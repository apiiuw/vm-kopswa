<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Location;
use App\Models\Vehicle;
use App\Models\VehicleDocument;
use App\Models\MaintenanceRecord;
use App\Models\InsuranceClaim;
use App\Models\Budget;
use App\Models\Income;
use App\Models\Expense;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Default Internal Users
        User::create([
            'name' => 'Tim Operasional PT SUP',
            'email' => 'operasional@ptsup.co.id',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Divisi Keuangan PT SUP',
            'email' => 'keuangan@ptsup.co.id',
            'password' => Hash::make('password123'),
        ]);

        // 2. Clients / Tenants (Multi-Client Contract Architecture: PT SUP as Vendor/Provider)
        $client1 = \App\Models\Vendor::create([
            'name' => 'PT Perkebunan Nusantara I Regional 7',
            'code' => 'PTPN-7',
            'contract_number' => 'TNP/TT/KTR/0075/2025',
            'client_name' => 'PT Swadharma Utama Prima (Penyedia)',
            'project_name' => 'Pengadaan Sewa 76 Unit Sepeda Motor Operasional Kebun',
            'pic_name' => 'Bambang Irawan (Pengawas Unit Sewa)',
            'phone' => '021-8293812 / 0812-3456-7890',
            'email' => 'operasional@ptpn1reg7.co.id',
            'contract_start_date' => '2025-05-01',
            'contract_end_date' => '2028-05-01',
            'status' => 'active',
            'notes' => 'Kontrak sewa 70 unit Honda CRF 150 L dan 6 unit Honda Supra X 125 periode 36 bulan.',
        ]);

        $client2 = \App\Models\Vendor::create([
            'name' => 'PT Agro Nusantara Bersama',
            'code' => 'AGRO',
            'contract_number' => 'KTR/ANB-SUP/2025/019',
            'client_name' => 'PT Swadharma Utama Prima (Penyedia)',
            'project_name' => 'Penyewaan Armada Patroli Perkebunan Sawit',
            'pic_name' => 'Hendra Setiawan',
            'phone' => '0813-8899-7711',
            'email' => 'procurement@agronusantara.co.id',
            'contract_start_date' => '2025-08-01',
            'contract_end_date' => '2027-08-01',
            'status' => 'active',
            'notes' => 'Kontrak sewa unit armada patroli perkebunan wilayah Sumatera.',
        ]);

        // 3. Locations (Wilayah Kebun & Area Operasional)
        $locationsData = [
            ['name' => 'Kebun Bergen', 'code' => 'BERGEN', 'manager_name' => 'Manajer Kebun Bergen', 'phone' => '081234567801'],
            ['name' => 'Kebun Kedaton', 'code' => 'KEDATON', 'manager_name' => 'Manajer Kebun Kedaton', 'phone' => '081234567802'],
            ['name' => 'Kebun Tulung Buyut', 'code' => 'TULUNG_BUYUT', 'manager_name' => 'Manajer Kebun Tulung Buyut', 'phone' => '081234567803'],
            ['name' => 'Kebun Way Berulu', 'code' => 'WAY_BERULU', 'manager_name' => 'Manajer Kebun Way Berulu', 'phone' => '081234567804'],
            ['name' => 'Kebun Beringin', 'code' => 'BERINGIN', 'manager_name' => 'Manajer Kebun Beringin', 'phone' => '081234567805'],
            ['name' => 'Kebun Musilandas', 'code' => 'MUSILANDAS', 'manager_name' => 'Manajer Kebun Musilandas', 'phone' => '081234567806'],
            ['name' => 'Kebun Pagar Alam', 'code' => 'PAGAR_ALAM', 'manager_name' => 'Manajer Kebun Pagar Alam', 'phone' => '081234567807'],
            ['name' => 'Kebun Ketahun', 'code' => 'KETAHUN', 'manager_name' => 'Manajer Kebun Ketahun', 'phone' => '081234567808'],
            ['name' => 'Kebun Padang Pelawi', 'code' => 'PADANG_PELAWI', 'manager_name' => 'Manajer Kebun Padang Pelawi', 'phone' => '081234567809'],
        ];

        $locationModels = [];
        foreach ($locationsData as $loc) {
            $locationModels[$loc['code']] = Location::create($loc);
        }

        // 4. Budgets (Sesuai Halaman 2 & 3 Memo Dokumen Master 2026)
        $budgetsData = [
            // Honda CRF 150 (70 unit)
            [
                'vehicle_group' => 'honda_crf_150',
                'category' => 'pajak_stnk',
                'label' => 'Pajak Kendaraan (STNK) - CRF 150',
                'qty_units' => 70,
                'unit_rate_monthly' => 62500,
                'unit_rate_yearly' => 750000,
                'total_budget_36m' => 157500000,
                'notes' => '70 unit x Rp 750.000 x 3 tahun',
            ],
            [
                'vehicle_group' => 'honda_crf_150',
                'category' => 'service_berkala',
                'label' => 'Service Berkala & Pelumas - CRF 150',
                'qty_units' => 70,
                'unit_rate_monthly' => 83333,
                'unit_rate_yearly' => 1000000,
                'total_budget_36m' => 210000000,
                'notes' => '70 unit x Rp 1.000.000 x 3 tahun',
            ],
            [
                'vehicle_group' => 'honda_crf_150',
                'category' => 'ban',
                'label' => 'Penggantian Ban - CRF 150',
                'qty_units' => 70,
                'unit_rate_monthly' => 62500,
                'unit_rate_yearly' => 750000,
                'total_budget_36m' => 157500000,
                'notes' => '70 unit x Rp 750.000 x 3 tahun',
            ],
            [
                'vehicle_group' => 'honda_crf_150',
                'category' => 'accu',
                'label' => 'Penggantian Accu - CRF 150',
                'qty_units' => 70,
                'unit_rate_monthly' => 41667,
                'unit_rate_yearly' => 500000,
                'total_budget_36m' => 105000000,
                'notes' => '70 unit x Rp 500.000 x 3 tahun',
            ],
            [
                'vehicle_group' => 'honda_crf_150',
                'category' => 'sparepart',
                'label' => 'Sparepart & Fast Moving - CRF 150',
                'qty_units' => 70,
                'unit_rate_monthly' => 20833,
                'unit_rate_yearly' => 250000,
                'total_budget_36m' => 52500000,
                'notes' => '70 unit x Rp 250.000 x 3 tahun',
            ],
            [
                'vehicle_group' => 'honda_crf_150',
                'category' => 'asuransi',
                'label' => 'Asuransi Kendaraan - CRF 150',
                'qty_units' => 70,
                'unit_rate_monthly' => 83333,
                'unit_rate_yearly' => 1000000,
                'total_budget_36m' => 210000000,
                'notes' => '70 unit x Rp 1.000.000 x 3 tahun',
            ],

            // Honda Supra X 125 (6 unit)
            [
                'vehicle_group' => 'honda_supra_x_125',
                'category' => 'pajak_stnk',
                'label' => 'Pajak Kendaraan (STNK) - Supra X 125',
                'qty_units' => 6,
                'unit_rate_monthly' => 29167,
                'unit_rate_yearly' => 350000,
                'total_budget_36m' => 6300000,
                'notes' => '6 unit x Rp 350.000 x 3 tahun',
            ],
            [
                'vehicle_group' => 'honda_supra_x_125',
                'category' => 'service_berkala',
                'label' => 'Service Berkala & Pelumas - Supra X 125',
                'qty_units' => 6,
                'unit_rate_monthly' => 41667,
                'unit_rate_yearly' => 500000,
                'total_budget_36m' => 9000000,
                'notes' => '6 unit x Rp 500.000 x 3 tahun',
            ],
            [
                'vehicle_group' => 'honda_supra_x_125',
                'category' => 'ban',
                'label' => 'Penggantian Ban - Supra X 125',
                'qty_units' => 6,
                'unit_rate_monthly' => 33333,
                'unit_rate_yearly' => 400000,
                'total_budget_36m' => 7200000,
                'notes' => '6 unit x Rp 400.000 x 3 tahun',
            ],
            [
                'vehicle_group' => 'honda_supra_x_125',
                'category' => 'accu',
                'label' => 'Penggantian Accu - Supra X 125',
                'qty_units' => 6,
                'unit_rate_monthly' => 41667,
                'unit_rate_yearly' => 500000,
                'total_budget_36m' => 9000000,
                'notes' => '6 unit x Rp 500.000 x 3 tahun',
            ],
            [
                'vehicle_group' => 'honda_supra_x_125',
                'category' => 'asuransi',
                'label' => 'Asuransi Kendaraan - Supra X 125',
                'qty_units' => 6,
                'unit_rate_monthly' => 41667,
                'unit_rate_yearly' => 500000,
                'total_budget_36m' => 9000000,
                'notes' => '6 unit x Rp 500.000 x 3 tahun',
            ],

            // Biaya Tak Terduga (Transportasi, Administrasi & Operasional)
            [
                'vehicle_group' => 'operasional_umum',
                'category' => 'operasional_tak_terduga',
                'label' => 'Biaya Tak Terduga (Transportasi, Administrasi & Unit Pengganti)',
                'qty_units' => 1,
                'unit_rate_monthly' => 2750000,
                'unit_rate_yearly' => 33000000,
                'total_budget_36m' => 99000000,
                'notes' => 'Alokasi Rp 33.000.000 / tahun x 3 tahun',
            ],
        ];

        foreach ($budgetsData as $b) {
            $b['vendor_id'] = $client1->id;
            Budget::create($b);
        }

        // 4. Seeding 76 Unit Kendaraan (70 CRF 150 + 6 Supra X 125)
        $locKeys = array_keys($locationModels);
        $vehiclesCreated = [];

        // 70 Honda CRF 150 L
        for ($i = 1; $i <= 70; $i++) {
            $locKey = $locKeys[($i - 1) % count($locKeys)];
            $loc = $locationModels[$locKey];
            
            // Contoh nomor plat nyata dari dokumen buku besar: BE 2402 AIT, BE 2443 AIT, BE 2364 AIT, dst.
            $plateNumber = match($i) {
                1 => 'BE 2402 AIT',
                2 => 'BE 2443 AIT',
                3 => 'BE 2364 AIT',
                default => 'BE ' . (2400 + $i) . ' A' . chr(65 + ($i % 26)) . 'T',
            };

            $holderIndex = (($i - 1) % 7) + 1;
            $userHolder = "Asisten Afdeling {$holderIndex} - {$loc->name}";

            $status = 'active';
            $replacementPlate = null;
            if ($i == 1) {
                $status = 'replaced';
                $replacementPlate = 'BE 6521 XY (Unit Pengganti Kebun)';
            } elseif ($i == 2) {
                $status = 'maintenance';
            } elseif ($i == 3) {
                $status = 'lost';
            }

            $v = Vehicle::create([
                'vendor_id' => $client1->id,
                'location_id' => $loc->id,
                'plate_number' => $plateNumber,
                'vehicle_type' => 'honda_crf_150',
                'model_name' => 'Honda CRF 150 L',
                'frame_number' => 'MH1KD111' . str_pad($i, 9, '0', STR_PAD_LEFT),
                'engine_number' => 'KD11E' . str_pad($i, 7, '0', STR_PAD_LEFT),
                'year' => 2025,
                'color' => ($i % 2 == 0) ? 'Extreme Red' : 'Extreme Black',
                'user_holder' => $userHolder,
                'rental_price_per_month' => 1570000,
                'status' => $status,
                'replacement_plate_number' => $replacementPlate,
                'notes' => 'Unit Sewa Kontrak PTPN I Regional 7 Periode 2025-2028',
            ]);

            $vehiclesCreated[] = $v;

            // Buat STNK & Pajak untuk masing-masing unit
            VehicleDocument::create([
                'vehicle_id' => $v->id,
                'document_type' => 'stnk',
                'document_number' => 'STNK-' . $v->plate_number,
                'issue_date' => '2025-05-01',
                'expiry_date' => Carbon::parse('2025-05-01')->addYears(1)->format('Y-m-d'),
                'cost' => 750000,
                'notes' => 'Pajak Tahunan STNK Unit ' . $v->plate_number,
            ]);

            // Asuransi TLO
            VehicleDocument::create([
                'vehicle_id' => $v->id,
                'document_type' => 'asuransi',
                'document_number' => 'POLIS-SUP-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'issue_date' => '2025-05-01',
                'expiry_date' => '2026-05-01',
                'cost' => 1000000,
                'notes' => 'Polis Asuransi Kerugian / TLO',
            ]);
        }

        // 6 Honda Supra X 125
        for ($j = 1; $j <= 6; $j++) {
            $loc = $locationModels['PAGAR_ALAM']; // Sesuai memo di kebun Pagar Alam
            $plateNumber = 'BE ' . (3500 + $j) . ' SP';
            $userHolder = "Asisten Afdeling {$j} - Kebun Pagar Alam";

            $v = Vehicle::create([
                'vendor_id' => $client1->id,
                'location_id' => $loc->id,
                'plate_number' => $plateNumber,
                'vehicle_type' => 'honda_supra_x_125',
                'model_name' => 'Honda Supra X 125 CW / Spoke',
                'frame_number' => 'MH1JB911' . str_pad($j, 9, '0', STR_PAD_LEFT),
                'engine_number' => 'JB91E' . str_pad($j, 7, '0', STR_PAD_LEFT),
                'year' => 2025,
                'color' => 'Quantum Black',
                'user_holder' => $userHolder,
                'rental_price_per_month' => 860000,
                'status' => 'active',
                'notes' => 'Unit Sewa CUB Kontrak PTPN I Reg 7',
            ]);

            $vehiclesCreated[] = $v;

            VehicleDocument::create([
                'vehicle_id' => $v->id,
                'document_type' => 'stnk',
                'document_number' => 'STNK-' . $v->plate_number,
                'issue_date' => '2025-05-01',
                'expiry_date' => Carbon::parse('2025-05-01')->addYears(1)->format('Y-m-d'),
                'cost' => 350000,
                'notes' => 'Pajak Tahunan STNK Supra X',
            ]);
        }

        // 5. Seeding Data Realisasi Biaya & Perawatan dari Dokumen Buku Besar (Hal 12 - 14)
        $historicalExpenses = [
            [
                'plate' => 'BE 2402 AIT',
                'type' => 'motor_pengganti',
                'category' => 'motor_pengganti',
                'date' => '2025-08-28',
                'cost' => 587500,
                'memo' => 'SUP/LPG/047/VIII/2025',
                'journal' => 'Journal Entry 10109',
                'desc' => 'Biaya Sewa Motor Pengganti Bulan Agustus Periode 1-31 Agustus 2025',
            ],
            [
                'plate' => 'BE 2402 AIT',
                'type' => 'motor_pengganti',
                'category' => 'motor_pengganti',
                'date' => '2025-09-28',
                'cost' => 650000,
                'memo' => 'SUP/LPG/049/IX/2025',
                'journal' => 'Journal Entry 10110',
                'desc' => 'Biaya Sewa Motor Pengganti Bulan September Periode 1-30 September 2025',
            ],
            [
                'plate' => 'BE 2402 AIT',
                'type' => 'motor_pengganti',
                'category' => 'motor_pengganti',
                'date' => '2025-10-28',
                'cost' => 650000,
                'memo' => 'SUP/LPG/058/X/2025',
                'journal' => 'Journal Entry 10111',
                'desc' => 'Biaya Sewa Motor Pengganti Bulan Oktober Periode 1-31 Oktober 2025',
            ],
            [
                'plate' => 'BE 2443 AIT',
                'type' => 'motor_pengganti',
                'category' => 'motor_pengganti',
                'date' => '2025-11-28',
                'cost' => 487500,
                'memo' => 'SUP/LPG/080/XI/2025',
                'journal' => 'Journal Entry 10599',
                'desc' => 'Biaya Sewa Motor Pengganti Bulan November Periode 07-30 November 2025',
            ],
            [
                'plate' => 'BE 2364 AIT',
                'type' => 'motor_pengganti',
                'category' => 'motor_pengganti',
                'date' => '2025-12-30',
                'cost' => 287500,
                'memo' => 'SUP/LPG/086/XII/2025',
                'journal' => 'Journal Entry 10600',
                'desc' => 'Biaya Sewa Motor Pengganti Bulan Desember Periode 16-31 Desember 2025',
            ],
            [
                'plate' => 'BE 2402 AIT',
                'type' => 'service_berkala',
                'category' => 'service_berkala',
                'date' => '2026-01-21',
                'cost' => 598000,
                'memo' => 'SUP/LPG/012/I/2026',
                'journal' => 'Journal Entry 11013',
                'desc' => 'Service Paket Lengkap & Ganti Oli Motor Sewa PTPN Reg 7',
            ],
            [
                'plate' => 'BE 2364 AIT',
                'type' => 'lainnya',
                'category' => 'legalitas_kepolisian',
                'date' => '2026-02-12',
                'cost' => 2800000,
                'memo' => 'SUP/LPG/035/II/2026',
                'journal' => 'Journal Entry 11141',
                'desc' => 'Biaya Blokir STNK dan Surat Keterangan Kehilangan Polda Motor Hilang',
            ],
            [
                'plate' => 'BE 2402 AIT',
                'type' => 'service_berkala',
                'category' => 'service_berkala',
                'date' => '2026-02-20',
                'cost' => 448500,
                'memo' => 'SUP/LPG/040/II/2026',
                'journal' => 'Journal Entry 11176',
                'desc' => 'Service 3 Unit Motor Sewa PTPN Reg 7 AHASS',
            ],
            [
                'plate' => 'BE 2443 AIT',
                'type' => 'service_berkala',
                'category' => 'service_berkala',
                'date' => '2026-04-14',
                'cost' => 762000,
                'memo' => 'SUP/LPG/062/IV/2026',
                'journal' => 'Journal Entry 11909',
                'desc' => 'Service 4 Unit Motor Sewa PTPN',
            ],
            [
                'plate' => 'BE 2402 AIT',
                'type' => 'service_berkala',
                'category' => 'service_berkala',
                'date' => '2026-04-30',
                'cost' => 495000,
                'memo' => 'SUP/LPG/071/IV/2026',
                'journal' => 'Journal Entry 12136',
                'desc' => 'Service 5 Unit Motor Sewa Kebun Bergen',
            ],
            [
                'plate' => 'BE 2443 AIT',
                'type' => 'service_berkala',
                'category' => 'service_berkala',
                'date' => '2026-04-30',
                'cost' => 890000,
                'memo' => 'SUP/LPG/072/IV/2026',
                'journal' => 'Journal Entry 12137',
                'desc' => 'Service 10 Unit Motor Sewa Kebun Padang Pelawi',
            ],
            [
                'plate' => 'BE 2402 AIT',
                'type' => 'ganti_ban',
                'category' => 'ban',
                'date' => '2026-06-26',
                'cost' => 731000,
                'memo' => 'SUP/LPG/095/VI/2026',
                'journal' => 'Journal Entry 12733',
                'desc' => 'Biaya Penggantian Ban Luar & Ban Dalam Motor Trail CRF PTPN',
            ],
        ];

        foreach ($historicalExpenses as $idx => $hexp) {
            $veh = Vehicle::where('plate_number', $hexp['plate'])->first() ?? $vehiclesCreated[0];

            $maint = MaintenanceRecord::create([
                'vehicle_id' => $veh->id,
                'type' => $hexp['type'],
                'date' => $hexp['date'],
                'workshop_name' => 'AHASS & Mitra Servis PTPN Regional 7',
                'description' => $hexp['desc'],
                'item_name' => $hexp['type'] === 'ganti_ban' ? 'Ban Trail CRF' : 'Pemeliharaan Unit',
                'cost' => $hexp['cost'],
                'memo_number' => $hexp['memo'],
            ]);

            Expense::create([
                'vendor_id' => $client1->id,
                'vehicle_id' => $veh->id,
                'maintenance_record_id' => $maint->id,
                'expense_number' => 'EXP-' . Carbon::parse($hexp['date'])->format('Ymd') . '-' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT),
                'journal_entry_number' => $hexp['journal'],
                'memo_number' => $hexp['memo'],
                'transaction_date' => $hexp['date'],
                'category' => $hexp['category'],
                'title' => $hexp['desc'],
                'amount' => $hexp['cost'],
                'recipient_vendor' => 'Mitra Rekanan Operasional Lampung',
                'description' => $hexp['desc'],
            ]);
        }

        // 6. Data Klaim Asuransi Unit Hilang (Kasus Motor BE 2364 AIT di Kebun Bergen)
        $lostVehicle = Vehicle::where('plate_number', 'BE 2364 AIT')->first();
        if ($lostVehicle) {
            InsuranceClaim::create([
                'vehicle_id' => $lostVehicle->id,
                'claim_type' => 'tlo_kehilangan',
                'incident_date' => '2025-12-15',
                'police_report_number' => 'LP/B/142/XII/2025/SPKT/POLRES_LPG',
                'polda_blocking_number' => 'SKET-BLOKIR/08/II/2026/DITLANTAS',
                'claim_amount' => 37800000,
                'settlement_amount' => 35000000,
                'status' => 'diverifikasi',
                'incident_chronology' => 'Unit hilang pada malam hari saat parkir di Pos Pengamanan Afdeling Kebun Bergen. Dilaporkan ke Polsek dan dilakukan blokir STNK di Polda Lampung.',
                'driver_name' => 'Asisten Afdeling Kebun Bergen',
                'driver_sim_number' => 'SIM-C-9801283921',
            ]);
        }

        // 7. Data Incomes (Penagihan Sewa Bulanan ke PTPN I Reg 7 dari Mei 2025 s.d. Juli 2026)
        $months = [
            ['May 2025', '2025-05-31', 'lunas'],
            ['Jun 2025', '2025-06-30', 'lunas'],
            ['Jul 2025', '2025-07-31', 'lunas'],
            ['Aug 2025', '2025-08-31', 'lunas'],
            ['Sep 2025', '2025-09-30', 'lunas'],
            ['Oct 2025', '2025-10-31', 'lunas'],
            ['Nov 2025', '2025-11-30', 'lunas'],
            ['Dec 2025', '2025-12-31', 'lunas'],
            ['Jan 2026', '2026-01-31', 'lunas'],
            ['Feb 2026', '2026-02-28', 'lunas'],
            ['Mar 2026', '2026-03-31', 'lunas'],
            ['Apr 2026', '2026-04-30', 'lunas'],
            ['May 2026', '2026-05-31', 'lunas'],
            ['Jun 2026', '2026-06-30', 'lunas'],
            ['Jul 2026', '2026-07-31', 'tertagih'],
        ];

        foreach ($months as $i => $m) {
            $invNumber = 'INV/SUP-PTPN7/' . Carbon::parse($m[1])->format('Ym') . '/' . str_pad($i + 1, 3, '0', STR_PAD_LEFT);
            Income::create([
                'vendor_id' => $client1->id,
                'invoice_number' => $invNumber,
                'period_month_year' => $m[0],
                'invoice_date' => $m[1],
                'client_name' => 'PT Perkebunan Nusantara I Regional 7',
                'crf_count' => 70,
                'crf_subtotal' => 109900000,
                'supra_count' => 6,
                'supra_subtotal' => 5160000,
                'gross_amount' => 115060000,
                'ppn_rate' => 11.00,
                'ppn_amount' => 12656600,
                'total_amount_with_ppn' => 127716600,
                'bast_number' => 'BAST/PTPN7-SUP/' . Carbon::parse($m[1])->format('Ym') . '/00' . ($i + 1),
                'status' => $m[2],
                'payment_date' => $m[2] === 'lunas' ? Carbon::parse($m[1])->addDays(15)->format('Y-m-d') : null,
                'notes' => 'Tagihan sewa 76 unit motor operasional',
            ]);
        }
    }
}
