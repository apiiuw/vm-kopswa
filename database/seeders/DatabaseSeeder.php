<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Vendor;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Klien / Vendor: PT Perkebunan Nusantara I Regional 7
        $vendor = Vendor::create([
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

        // 2. Akun Login 1: Admin Internal PT SUP
        User::create([
            'name' => 'Administrator PT SUP',
            'email' => 'info@ptsup.co.id',
            'password' => Hash::make('password123'),
            'category' => 'internal',
            'vendor_id' => null,
        ]);

        // 3. Akun Login 2: Klien PT Perkebunan Nusantara I Regional 7
        User::create([
            'name' => 'PIC PTPN I Regional 7',
            'email' => 'pic.reg7@ptpn.id',
            'password' => Hash::make('password123'),
            'category' => 'klien',
            'vendor_id' => $vendor->id,
        ]);

        // 4. Data Master Lokasi / Kebun PTPN I Regional 7
        $locations = [
            ['name' => 'Kebun Bergen', 'code' => 'BERGEN'],
            ['name' => 'Kebun Kedaton', 'code' => 'KEDATON'],
            ['name' => 'Kebun Tulung Buyut', 'code' => 'TULUNG_BUYUT'],
            ['name' => 'Kebun Way Berulu', 'code' => 'WAY_BERULU'],
            ['name' => 'Kebun Beringin', 'code' => 'BERINGIN'],
            ['name' => 'Kebun Musilandas', 'code' => 'MUSILANDAS'],
            ['name' => 'Kebun Pagar Alam', 'code' => 'PAGAR_ALAM'],
            ['name' => 'Kebun Ketahun', 'code' => 'KETAHUN'],
            ['name' => 'Kebun Padang Pelawi', 'code' => 'PADANG_PELAWI'],
        ];

        foreach ($locations as $loc) {
            \App\Models\Location::create([
                'vendor_id' => $vendor->id,
                'name' => $loc['name'],
                'code' => $loc['code'],
                'manager_name' => 'Manajer ' . $loc['name'],
            ]);
        }
    }
}

