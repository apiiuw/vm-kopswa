<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use SimpleXMLElement;
use ZipArchive;

class ImportPersewaanExcel extends Command
{
    protected $signature = 'app:import-persewaan {--file=qryPersewaan.xlsx} {--fresh : Kosongkan data operasional lama sebelum import} {--with-clients : Buatkan akun login untuk setiap client}';

    protected $description = 'Import Data Kontrak dan Data Kdr Kontrak dari qryPersewaan.xlsx ke database';

    public function handle(): int
    {
        $filePath = base_path($this->option('file'));
        if (! file_exists($filePath)) {
            $this->error("File tidak ditemukan: {$filePath}");

            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            $this->clearOperationalData();
        }

        $zip = new ZipArchive;
        if ($zip->open($filePath) !== true) {
            $this->error('Gagal membuka file Excel.');

            return self::FAILURE;
        }

        $sharedStrings = $this->readSharedStrings($zip);
        $contractsRows = $this->readSheetRows($zip, 'xl/worksheets/sheet1.xml', $sharedStrings);
        $vehiclesRows = $this->readSheetRows($zip, 'xl/worksheets/sheet2.xml', $sharedStrings);
        $zip->close();

        $vendorMap = [];
        $contractCount = 0;

        // Sheet 1: Data Kontrak
        foreach (array_slice($contractsRows, 1) as $row) {
            $noKontrak = trim((string) ($row['A'] ?? ''));
            if ($noKontrak === '') {
                continue;
            }

            $penyewa = trim((string) ($row['L'] ?? '')) ?: 'Klien Mitra';
            $alamat = trim(str_replace('\n', "\n", (string) ($row['M'] ?? '')));
            $telp = trim((string) ($row['N'] ?? ''));

            $client = Client::where('name', $penyewa)->first();
            if (! $client) {
                $baseCode = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', substr($penyewa, 0, 6))) ?: 'CL';
                $code = $baseCode.'-'.str_pad((string) (Client::count() + 1), 3, '0', STR_PAD_LEFT);
                $client = Client::create([
                    'name' => $penyewa,
                    'code' => $code,
                    'address' => $alamat !== '-' ? $alamat : null,
                    'phone' => $telp !== '-' ? $telp : null,
                ]);
            }

            $caraBayar = trim((string) ($row['F'] ?? '1 Bulan'));
            $billingCycle = match ($caraBayar) {
                '3 Bulan' => 'triwulanan',
                '12 Bulan' => 'tahunan',
                '24 Bulan' => '2_tahunan',
                default => 'bulanan',
            };

            $vendor = Vendor::where('contract_number', $noKontrak)->first();
            $vendorCode = $vendor?->code ?: ('KTR-'.strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $noKontrak)));
            if (! $vendor && Vendor::where('code', $vendorCode)->exists()) {
                $vendorCode .= '-'.(Vendor::count() + 1);
            }

            $startDate = $this->excelDate($row['C'] ?? null);
            $endDate = $this->excelDate($row['D'] ?? null);
            $status = ($endDate && Carbon::parse($endDate)->isPast()) ? 'inactive' : 'active';

            $payload = [
                'client_id' => $client->id,
                'name' => $penyewa,
                'code' => substr($vendorCode, 0, 50),
                'contract_number' => $noKontrak,
                'contract_date' => $this->excelDate($row['B'] ?? null),
                'client_name' => 'Koperasi Swadharma (Penyedia)',
                'phone' => $telp !== '-' ? $telp : null,
                'contract_start_date' => $startDate,
                'contract_end_date' => $endDate,
                'duration_months' => is_numeric($row['E'] ?? null) ? (int) $row['E'] : null,
                'billing_cycle' => $billingCycle,
                'contract_value' => (float) ($row['G'] ?? 0),
                'main_contract_value' => (float) ($row['H'] ?? 0),
                'main_monthly_value' => (float) ($row['I'] ?? 0),
                'additional_contract_value' => (float) ($row['J'] ?? 0),
                'additional_monthly_value' => (float) ($row['K'] ?? 0),
                'status' => $status,
            ];

            if ($vendor) {
                $vendor->update($payload);
            } else {
                $vendor = Vendor::create($payload);
            }

            $vendorMap[$noKontrak] = $vendor;
            $contractCount++;
        }

        $vehicleCount = 0;

        // Sheet 2: Data Kdr Kontrak
        foreach (array_slice($vehiclesRows, 1) as $row) {
            $noKontrak = trim((string) ($row['A'] ?? ''));
            $nopol = trim((string) ($row['G'] ?? ''));
            if ($nopol === '') {
                continue;
            }

            $vendor = $vendorMap[$noKontrak] ?? Vendor::where('contract_number', $noKontrak)->first();
            $jenisRaw = strtolower(trim((string) ($row['C'] ?? 'motor')));
            $assetType = $jenisRaw === 'mobil' ? 'mobil' : 'motor';
            $brand = trim((string) ($row['D'] ?? ''));
            $tipe = trim((string) ($row['E'] ?? ''));
            $item = trim((string) ($row['F'] ?? ''));

            $acqValue = (float) ($row['O'] ?? 0);
            $residuPct = (float) ($row['Q'] ?? 0);
            $residuVal = $residuPct > 0 ? round($acqValue * ($residuPct / 100), 2) : 0;

            $existing = Vehicle::where('plate_number', $nopol)->first();

            $payload = [
                'vendor_id' => $vendor?->id,
                'user_holder' => trim((string) ($row['B'] ?? '')) ?: null,
                'asset_type' => $assetType,
                'category' => $assetType === 'mobil' ? 'Roda 4 (Mobil)' : 'Roda 2 (Motor)',
                'vehicle_type' => $assetType === 'mobil' ? 'mobil_operasional' : 'honda_crf_150',
                'brand' => $brand ?: 'Umum',
                'model_name' => $tipe ?: ($brand ?: 'Unit Operasional'),
                'type_name' => $item ?: null,
                'plate_number' => $nopol,
                'total_contract_value' => (float) ($row['H'] ?? 0),
                'rental_price_per_month' => (float) ($row['I'] ?? 0),
                'additional_contract_value' => (float) ($row['J'] ?? 0),
                'additional_monthly_value' => (float) ($row['K'] ?? 0),
                'engine_number' => trim((string) ($row['L'] ?? '')) ?: null,
                'frame_number' => trim((string) ($row['M'] ?? '')) ?: null,
                'invoice_value' => (float) ($row['N'] ?? 0),
                'acquisition_value' => $acqValue,
                'ppn_pm' => (float) ($row['P'] ?? 0),
                'residual_percentage' => $residuPct,
                'residual_value' => $residuVal,
                'useful_life_months' => is_numeric($row['R'] ?? null) ? (int) $row['R'] : 48,
                'billing_cycle' => $vendor?->billing_cycle ?? 'bulanan',
                'status' => $vendor?->status === 'active' ? 'active' : 'standby',
                'acquisition_date' => $vendor?->contract_start_date,
                'year' => $vendor?->contract_start_date ? (int) $vendor->contract_start_date->format('Y') : (int) date('Y'),
            ];

            if ($existing) {
                $existing->update($payload);
            } else {
                $payload['asset_code'] = Vehicle::generateNextAssetCode($assetType);
                Vehicle::create($payload);
            }

            $vehicleCount++;
        }

        $createdAccounts = 0;
        if ($this->option('with-clients')) {
            $clients = Client::with('contracts')->orderBy('id')->get();
            foreach ($clients as $client) {
                $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '.', $client->code ?: $client->name), '.'));
                $email = $slug.'@client.kopswa.id';
                if (User::where('email', $email)->exists()) {
                    $email = $slug.'.'.$client->id.'@client.kopswa.id';
                }

                User::create([
                    'name' => 'PIC '.$client->name,
                    'email' => $email,
                    'password' => Hash::make('kopswa123'),
                    'category' => 'klien',
                    'client_id' => $client->id,
                    'vendor_id' => $client->contracts->first()?->id,
                ]);
                $createdAccounts++;
            }
        }

        $this->info("Import selesai: {$contractCount} kontrak, {$vehicleCount} unit kendaraan, dan {$createdAccounts} akun client berhasil disinkronkan.");

        return self::SUCCESS;
    }

    private function clearOperationalData(): void
    {
        Schema::disableForeignKeyConstraints();

        $tables = [
            'handover_documents',
            'tax_invoices',
            'incomes',
            'expenses',
            'budgets',
            'maintenance_records',
            'vehicle_documents',
            'insurance_policies',
            'client_requests',
            'locations',
            'vehicles',
            'vendors',
            'clients',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->delete();
            }
        }

        // Hapus akun client lama, pertahankan akun internal/admin
        User::where('category', 'klien')->delete();

        Schema::enableForeignKeyConstraints();
        $this->info('Data operasional lama & akun client lama berhasil dikosongkan.');
    }

    private function readSharedStrings(ZipArchive $zip): array
    {
        $xmlContent = $zip->getFromName('xl/sharedStrings.xml');
        if ($xmlContent === false) {
            return [];
        }

        $xml = new SimpleXMLElement($xmlContent);
        $strings = [];
        foreach ($xml->si as $si) {
            $text = '';
            if (isset($si->t)) {
                $text = (string) $si->t;
            } elseif (isset($si->r)) {
                foreach ($si->r as $r) {
                    $text .= (string) $r->t;
                }
            }
            $strings[] = $text;
        }

        return $strings;
    }

    private function readSheetRows(ZipArchive $zip, string $entry, array $sharedStrings): array
    {
        $xmlContent = $zip->getFromName($entry);
        if ($xmlContent === false) {
            return [];
        }

        $xml = new SimpleXMLElement($xmlContent);
        $rows = [];
        foreach ($xml->sheetData->row as $row) {
            $rowData = [];
            foreach ($row->c as $c) {
                $ref = (string) $c['r'];
                $col = preg_replace('/[0-9]/', '', $ref);
                $type = (string) $c['t'];
                $val = isset($c->v) ? (string) $c->v : '';
                if ($type === 's' && $val !== '') {
                    $val = $sharedStrings[(int) $val] ?? '';
                }
                $rowData[$col] = $val;
            }
            $rows[] = $rowData;
        }

        return $rows;
    }

    private function excelDate(mixed $serial): ?string
    {
        if (! is_numeric($serial) || (int) $serial <= 0) {
            return null;
        }

        return Carbon::create(1899, 12, 30)->addDays((int) $serial)->toDateString();
    }
}
