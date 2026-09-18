<?php

namespace App\Console\Commands;

use App\Models\Expense;
use App\Models\InsuranceClaim;
use App\Models\Location;
use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use App\Models\VehicleDocument;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportArmadaExcelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'armada:import-excel 
                            {--file=Master-Data-Vehicle-Monitoring-SUP.xlsx : File Excel yang akan diimport} 
                            {--no-download : Lewati unduhan file foto STNK & BPKB}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kosongkan data armada lama dan import data armada dari file Excel (Baris 3 s/d selesai) beserta auto-download dokumen STNK/BPKB';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = base_path($this->option('file'));
        $shouldDownload = !$this->option('no-download');

        if (!file_exists($filePath)) {
            $this->error("File tidak ditemukan: {$filePath}");
            return 1;
        }

        $this->info("=================================================================");
        $this->info(" MEMULAI IMPORT DATA MASTER ARMADA SUP");
        $this->info(" File : {$filePath}");
        $this->info(" Unduh Dokumen : " . ($shouldDownload ? "YA (Google Drive -> Storage Lokal)" : "TIDAK"));
        $this->info("=================================================================\n");

        // 1. Baca spreadsheet
        $this->info("1. Membaca Sheet '1. Data Armada'...");
        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getSheetByName('1. Data Armada');
            if (!$sheet) {
                $this->error("Sheet '1. Data Armada' tidak ditemukan dalam file Excel!");
                return 1;
            }
            $rows = $sheet->toArray(null, true, true, true);
        } catch (\Throwable $e) {
            $this->error("Gagal membaca file Excel: " . $e->getMessage());
            return 1;
        }

        // Ambil baris valid (baris 3 sampai baris terakhir yang memiliki plat nomor)
        $validRows = [];
        $totalRows = count($rows);
        for ($i = 3; $i <= $totalRows; $i++) {
            $plate = trim($rows[$i]['A'] ?? '');
            if (!empty($plate)) {
                $validRows[$i] = $rows[$i];
            }
        }

        $countValid = count($validRows);
        $this->info("-> Ditemukan {$countValid} unit armada valid (Baris 3 s/d " . (array_key_last($validRows) ?? 3) . ").");

        if ($countValid === 0) {
            $this->warn("Tidak ada data armada valid untuk di-import.");
            return 0;
        }

        // 2. Kosongkan data armada existing
        $this->warn("\n2. Mengosongkan data armada existing dan riwayat terkait...");
        
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } elseif ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        VehicleDocument::truncate();
        MaintenanceRecord::truncate();
        InsuranceClaim::truncate();
        Expense::truncate();
        Vehicle::truncate();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } elseif ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $this->info("-> Seluruh data armada dan dokumen terdahulu berhasil dikosongkan.");

        // 3. Pastikan Vendor Default
        $vendor = Vendor::firstOrCreate(
            ['name' => 'PT Perkebunan Nusantara I Regional 7'],
            [
                'code' => 'PTPN-REG7',
                'contact_person' => 'PIC Logistik PTPN 1 Reg 7',
                'phone' => '0721-702233',
                'is_active' => true,
            ]
        );

        // 4. Pastikan direktori storage
        $stnkDir = storage_path('app/public/vehicles/stnk');
        $bpkbDir = storage_path('app/public/vehicles/bpkb');
        if (!File::isDirectory($stnkDir)) {
            File::makeDirectory($stnkDir, 0755, true);
        }
        if (!File::isDirectory($bpkbDir)) {
            File::makeDirectory($bpkbDir, 0755, true);
        }

        // Cache Master Locations
        $locationsCache = [];

        $this->info("\n3. Memproses & Mengimpor {$countValid} Unit Armada...\n");
        $bar = $this->output->createProgressBar($countValid);
        $bar->start();

        $imported = 0;
        $downloadedStnk = 0;
        $downloadedBpkb = 0;

        foreach ($validRows as $rowIndex => $row) {
            $plateRaw = preg_replace('/\s+/', ' ', trim($row['A'] ?? ''));
            $cleanPlateForFilename = Str::slug($plateRaw, '_');

            // Lokasi
            $locationName = trim($row['O'] ?? 'Pusat');
            if (empty($locationName)) {
                $locationName = 'Pusat';
            }
            if (!isset($locationsCache[$locationName])) {
                $loc = Location::firstOrCreate(
                    ['name' => $locationName],
                    ['code' => Str::slug($locationName)]
                );
                $locationsCache[$locationName] = $loc->id;
            }
            $locationId = $locationsCache[$locationName];

            // Mapping vehicle_type & brand
            $modelRaw = trim($row['D'] ?? '');
            $brandRaw = trim($row['C'] ?? 'Honda');
            $categoryRaw = trim($row['B'] ?? 'Roda 2 (Sepeda Motor)');
            $typeNameRaw = trim($row['E'] ?? '');

            $vehicleType = 'other';
            if (stripos($modelRaw, 'CRF') !== false) {
                $vehicleType = 'honda_crf_150';
            } elseif (stripos($modelRaw, 'SUPRA') !== false) {
                $vehicleType = 'honda_supra_x_125';
            }

            // Tarif sewa
            $rentalPrice = $this->parseCurrency($row['F'] ?? 0);

            // Parsing Tanggal Pajak
            $taxAnnual = $this->parseDate($row['K'] ?? null);
            $tax5Year = $this->parseDate($row['L'] ?? null);

            // Unduh STNK jika ada link
            $stnkPath = null;
            $stnkLink = trim($row['S'] ?? '');
            if (!empty($stnkLink) && $shouldDownload) {
                $stnkPath = $this->downloadFileFromGdrive($stnkLink, $stnkDir, 'stnk_' . $cleanPlateForFilename, 'vehicles/stnk');
                if ($stnkPath) {
                    $downloadedStnk++;
                }
            }

            // Unduh BPKB jika ada link
            $bpkbPath = null;
            $bpkbLink = trim($row['U'] ?? '');
            if (!empty($bpkbLink) && $shouldDownload) {
                $bpkbPath = $this->downloadFileFromGdrive($bpkbLink, $bpkbDir, 'bpkb_' . $cleanPlateForFilename, 'vehicles/bpkb');
                if ($bpkbPath) {
                    $downloadedBpkb++;
                }
            }

            // Status Operasional
            $statusRaw = trim($row['P'] ?? 'Aktif Beroperasi');
            $status = 'active';
            if (stripos($statusRaw, 'perbaikan') !== false || stripos($statusRaw, 'servis') !== false) {
                $status = 'maintenance';
            } elseif (stripos($statusRaw, 'pengganti') !== false) {
                $status = 'replaced';
            } elseif (stripos($statusRaw, 'hilang') !== false) {
                $status = 'lost';
            }

            // Simpan data kendaraan
            $vehicle = Vehicle::create([
                'plate_number' => $plateRaw,
                'category' => $categoryRaw ?: 'Roda 2 (Sepeda Motor)',
                'brand' => $brandRaw ?: 'Honda',
                'model_name' => $modelRaw ?: 'Honda',
                'type_name' => $typeNameRaw ?: null,
                'vehicle_type' => $vehicleType,
                'rental_price_per_month' => $rentalPrice,
                'frame_number' => trim($row['G'] ?? null) ?: null,
                'engine_number' => trim($row['H'] ?? null) ?: null,
                'year' => (int) trim($row['I'] ?? date('Y')) ?: date('Y'),
                'color' => trim($row['J'] ?? null) ?: null,
                'tax_annual_date' => $taxAnnual,
                'tax_5year_date' => $tax5Year,
                'user_holder' => $this->formatTitleCase(trim($row['M'] ?? null)),
                'driver_phone' => trim($row['N'] ?? null) ?: null,
                'location_id' => $locationId,
                'vendor_id' => $vendor->id,
                'status' => $status,
                'replacement_plate_number' => trim($row['Q'] ?? null) ?: null,
                'stnk_number' => trim($row['R'] ?? null) ?: null,
                'stnk_file' => $stnkPath,
                'bpkb_number' => trim($row['T'] ?? null) ?: null,
                'bpkb_file' => $bpkbPath,
                'notes' => trim($row['V'] ?? null) ?: null,
            ]);

            // Jika ada dokumen STNK tersimpan, buatkan record di vehicle_documents juga sebagai history
            if ($stnkPath || !empty($row['R'])) {
                VehicleDocument::create([
                    'vehicle_id' => $vehicle->id,
                    'document_type' => 'stnk',
                    'document_number' => trim($row['R'] ?? null),
                    'issue_date' => null,
                    'expiry_date' => $taxAnnual,
                    'file_path' => $stnkPath,
                    'notes' => 'Diimport dari Master Data Excel (Pajak 1 Th)',
                ]);
            }

            if ($bpkbPath || !empty($row['T'])) {
                VehicleDocument::create([
                    'vehicle_id' => $vehicle->id,
                    'document_type' => 'bpkb',
                    'document_number' => trim($row['T'] ?? null),
                    'issue_date' => null,
                    'expiry_date' => null,
                    'file_path' => $bpkbPath,
                    'notes' => 'Diimport dari Master Data Excel (Buku BPKB)',
                ]);
            }

            $imported++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("=================================================================");
        $this->info(" SUKSES IMPORT DATA ARMADA!");
        $this->info(" Total Armada Diimport   : {$imported} Unit");
        $this->info(" File STNK Terunduh      : {$downloadedStnk} File");
        $this->info(" File BPKB Terunduh      : {$downloadedBpkb} File");
        $this->info(" Folder Penyimpanan      : storage/app/public/vehicles/");
        $this->info("=================================================================\n");

        return 0;
    }

    /**
     * Parse string mata uang (Rp 1.570.000) menjadi integer/float
     */
    protected function parseCurrency($value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        // Hapus "Rp", spasi, titik ribuan
        $cleaned = preg_replace('/[^0-9]/', '', (string) $value);
        return (float) ($cleaned ?: 0);
    }

    /**
     * Parse format tanggal DD-MM-YYYY menjadi Carbon/Y-m-d
     */
    protected function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        try {
            $value = trim($value);
            // Cek jika format DD-MM-YYYY
            if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $value, $m)) {
                return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
            }

            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Format nama pengemudi / pengguna menjadi Title Case (huruf kapital di awal kata)
     */
    protected function formatTitleCase(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $value = trim(preg_replace('/\s+/', ' ', $value));
        // Tambahkan spasi setelah koma jika berdempetan: e.g. "SAPUTRA,S.T." -> "SAPUTRA, S.T."
        $value = preg_replace('/,([^\s])/', ', ${1}', $value);
        $words = explode(' ', $value);
        $result = [];

        foreach ($words as $w) {
            if (empty($w)) {
                continue;
            }
            // Pertahankan gelar akademis seperti S.T., S.TP, S.Pd, S.Si, S.Pt
            if (preg_match('/^S\.(T|TP|PD|E|KOM|H|SOS|SI|PT)\.?$/i', $w)) {
                $result[] = strtoupper($w);
            } else {
                $result[] = mb_convert_case(mb_strtolower($w), MB_CASE_TITLE, 'UTF-8');
            }
        }

        return implode(' ', $result);
    }

    /**
     * Download file dari link Google Drive ke direktori lokal
     */
    protected function downloadFileFromGdrive(string $url, string $targetDir, string $baseFilename, string $relativeStorageFolder): ?string
    {
        preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $url, $matches);
        $fileId = $matches[1] ?? null;

        if (!$fileId) {
            return null;
        }

        // Cek jika sudah ada file sebelumnya
        foreach (['pdf', 'jpg', 'jpeg', 'png', 'webp'] as $checkExt) {
            $existingPath = $targetDir . '/' . $baseFilename . '.' . $checkExt;
            if (file_exists($existingPath) && filesize($existingPath) > 1000) {
                return $relativeStorageFolder . '/' . $baseFilename . '.' . $checkExt;
            }
        }

        $tempPath = $targetDir . '/' . $baseFilename . '_temp.bin';
        $directUrl = 'https://drive.google.com/uc?export=download&id=' . $fileId;

        $maxAttempts = 3;
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $fp = @fopen($tempPath, 'wb');
            if (!$fp) {
                return null;
            }

            $ch = curl_init($directUrl);
            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            fclose($fp);

            if (file_exists($tempPath) && filesize($tempPath) > 1000 && $httpCode >= 200 && $httpCode < 400) {
                // Berhasil
                break;
            }

            // Gagal, coba jeda 1 detik lalu retry
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
            if ($attempt < $maxAttempts) {
                usleep(800000); // 0.8 detik
            }
        }

        if (!file_exists($tempPath) || filesize($tempPath) < 500) {
            @unlink($tempPath);
            return null;
        }

        // Deteksi MIME Type
        $mime = @mime_content_type($tempPath);
        $ext = 'pdf';
        if ($mime) {
            if (str_contains($mime, 'jpeg') || str_contains($mime, 'jpg')) {
                $ext = 'jpg';
            } elseif (str_contains($mime, 'png')) {
                $ext = 'png';
            } elseif (str_contains($mime, 'webp')) {
                $ext = 'webp';
            }
        }

        $finalFilename = $baseFilename . '.' . $ext;
        $finalPath = $targetDir . '/' . $finalFilename;

        if (file_exists($finalPath)) {
            @unlink($finalPath);
        }

        rename($tempPath, $finalPath);

        // Sedikit delay agar Google Drive tidak rate-limit
        usleep(300000); // 0.3 detik

        return $relativeStorageFolder . '/' . $finalFilename;
    }
}
