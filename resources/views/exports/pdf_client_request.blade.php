<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tiket Permohonan #REQ-{{ str_pad($clientRequest->id, 4, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            margin: 20px 25px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.45;
        }
        .header-table {
            width: 100%;
            border-bottom: 2.5px solid #015471;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .company-name {
            font-size: 13px;
            font-weight: bold;
            color: #015471;
            text-transform: uppercase;
        }
        .doc-title {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 3px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-type {
            background-color: #015471;
            color: #ffffff;
        }
        .badge-status {
            background-color: #f1f5f9;
            color: #015471;
            border: 1px solid #cbd5e1;
        }
        .section-box {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            margin-bottom: 12px;
            background-color: #ffffff;
        }
        .section-header {
            background-color: #f8fafc;
            padding: 5px 10px;
            font-size: 10px;
            font-weight: bold;
            color: #015471;
            border-bottom: 1px solid #cbd5e1;
            text-transform: uppercase;
        }
        .section-body {
            padding: 8px 10px;
        }
        .grid-table {
            width: 100%;
            border-collapse: collapse;
        }
        .grid-table td {
            padding: 4px 6px;
            vertical-align: top;
            border: 1px solid #f1f5f9;
        }
        .label {
            color: #64748b;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            display: block;
            margin-bottom: 2px;
        }
        .value {
            font-weight: bold;
            color: #0f172a;
            font-size: 10px;
        }
        .complaint-item {
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 8px;
            margin-bottom: 8px;
            background-color: #fafbfc;
        }
        .photo-cell {
            width: 25%;
            padding: 4px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }
        .photo-img {
            max-width: 100%;
            height: 90px;
            object-fit: cover;
            border-radius: 4px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: monospace; }
        .font-bold { font-weight: bold; }
        .footer-sign-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .footer-sign-table td {
            text-align: center;
            padding: 6px;
            vertical-align: top;
        }
    </style>
</head>
<body>

    <!-- Header Dokumen -->
    <table class="header-table">
        <tr>
            <td style="width: 65%;">
                <div class="company-name">{{ $clientRequest->vendor ? $clientRequest->vendor->name : 'KOPERASI SWADHARMA' }}</div>
                <div class="doc-title">FORMULIR PERMOHONAN LAYANAN ARMADA</div>
                <div style="font-size: 9px; color: #64748b; margin-top: 2px;">
                    Nomor Tiket: <strong style="color: #015471;">#REQ-{{ str_pad($clientRequest->id, 4, '0', STR_PAD_LEFT) }}</strong> &bull; 
                    Tanggal Pengajuan: {{ $clientRequest->created_at->isoFormat('D MMMM Y, HH:mm') }} WIB
                </div>
            </td>
            <td style="width: 35%; text-align: right; vertical-align: middle;">
                <span class="badge badge-type">{{ $clientRequest->request_type_label }}</span>
                <span class="badge badge-status">{{ $clientRequest->status_badge['label'] }}</span>
            </td>
        </tr>
    </table>

    <!-- TAHAP 1: Kategori & Identitas Armada Motor -->
    <div class="section-box">
        <div class="section-header">1. Kategori Pengajuan & Identitas Armada Motor</div>
        <div class="section-body">
            <table class="grid-table">
                <tr>
                    <td style="width: 30%;">
                        <span class="label">Plat Nomor Kendaraan</span>
                        <span class="value font-mono" style="font-size: 12px; color: #015471;">{{ $clientRequest->vehicle ? $clientRequest->vehicle->plate_number : '-' }}</span>
                    </td>
                    <td style="width: 35%;">
                        <span class="label">Tipe & Model Kendaraan</span>
                        <span class="value">{{ $clientRequest->vehicle ? $clientRequest->vehicle->brand . ' ' . $clientRequest->vehicle->model_name : '-' }}</span>
                    </td>
                    <td style="width: 35%;">
                        <span class="label">Lokasi Operasional / Kebun</span>
                        <span class="value">{{ $clientRequest->vehicle && $clientRequest->vehicle->location ? $clientRequest->vehicle->location->name : '-' }}</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label">Tahun / Warna Unit</span>
                        <span class="value">{{ $clientRequest->vehicle ? $clientRequest->vehicle->year . ' / ' . $clientRequest->vehicle->color : '-' }}</span>
                    </td>
                    <td>
                        <span class="label">Status Armada</span>
                        <span class="value">{{ strtoupper($clientRequest->vehicle ? $clientRequest->vehicle->status : '-') }}</span>
                    </td>
                    <td>
                        <span class="label">Mitra Pengguna (Klien)</span>
                        <span class="value">{{ $clientRequest->vendor ? $clientRequest->vendor->name : '-' }}</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- TAHAP 2: Rincian Permohonan / Daftar Keluhan Perbaikan -->
    <div class="section-box">
        <div class="section-header">2. Rincian Permohonan & Keluhan Kerusakan</div>
        <div class="section-body">
            @if($clientRequest->request_type === 'perbaikan_servis' && count($complaintBlocks) > 0)
                @foreach($complaintBlocks as $idx => $comp)
                    <div class="complaint-item">
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 70%;">
                                    <strong style="color: #015471; font-size: 11px;">#{{ $comp['num'] ?? ($idx + 1) }}. {{ $comp['title'] }}</strong>
                                    <span style="font-size: 8.5px; color: #64748b; background: #e2e8f0; padding: 1px 5px; border-radius: 3px; margin-left: 5px;">{{ $comp['category'] }}</span>
                                </td>
                                <td style="width: 30%; text-align: right;">
                                    @if(!empty($comp['cost']))
                                        <strong style="color: #059669; font-size: 10px;">Estimasi: {{ $comp['cost'] }}</strong>
                                    @endif
                                </td>
                            </tr>
                        </table>
                        <div style="font-size: 9.5px; color: #334155; margin-top: 4px; line-height: 1.4;">
                            {!! nl2br(e($comp['desc'])) !!}
                        </div>
                    </div>
                @endforeach
            @else
                <div style="font-size: 10px; color: #1e293b; line-height: 1.5;">
                    <strong style="display: block; margin-bottom: 4px; color: #015471;">Judul: {{ $clientRequest->title }}</strong>
                    {!! nl2br(e($remainingDescription ?: $clientRequest->description)) !!}
                </div>
            @endif
        </div>
    </div>

    <!-- TAHAP 3: Informasi Unit Motor, PIC & Bengkel -->
    <div class="section-box">
        <div class="section-header">3. Informasi Lapangan, Kontak PIC & Bengkel Rujukan</div>
        <div class="section-body">
            <table class="grid-table">
                <tr>
                    <td style="width: 33%;">
                        <span class="label">Speedometer Terkini</span>
                        <span class="value font-mono" style="color: #015471; font-size: 11px;">
                            {{ $clientRequest->odometer_km ? number_format($clientRequest->odometer_km, 0, ',', '.') . ' KM' : '-' }}
                        </span>
                    </td>
                    <td style="width: 33%;">
                        <span class="label">Kondisi Operasional</span>
                        <span class="value">{{ $structuredData['operational_status'] ?: 'Masih Bisa Jalan Normal' }}</span>
                    </td>
                    <td style="width: 34%;">
                        <span class="label">Posisi / Lokasi Motor</span>
                        <span class="value">{{ $structuredData['unit_position'] ?: '-' }}</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="label">Pengemudi Terdaftar</span>
                        <span class="value">{{ $clientRequest->driver_name ?: '-' }}</span>
                        @if(!empty($clientRequest->driver_phone))
                            <div style="font-size: 8.5px; color: #64748b; font-family: monospace; margin-top: 2px;">Telp: {{ $clientRequest->driver_phone }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="label">PIC Penanggung Jawab</span>
                        <span class="value">{{ $structuredData['pic_name'] ?: ($clientRequest->user ? $clientRequest->user->name : '-') }}</span>
                        @if(!empty($structuredData['pic_phone']))
                            <div style="font-size: 8.5px; color: #015471; font-family: monospace; font-weight: bold; margin-top: 2px;">Telp: {{ $structuredData['pic_phone'] }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="label">Catatan Fisik Lapangan</span>
                        <span class="value">{{ $structuredData['physical_notes'] ?: '-' }}</span>
                    </td>
                </tr>
                @if($clientRequest->request_type === 'perbaikan_servis' || $clientRequest->workshop_name || $clientRequest->estimated_cost > 0)
                <tr>
                    <td>
                        <span class="label">Nama Bengkel / AHASS</span>
                        <span class="value">{{ $clientRequest->workshop_name ?: 'Bengkel Rekanan' }}</span>
                        @if(!empty($structuredData['workshop_phone']))
                            <div style="font-size: 8.5px; color: #64748b; font-family: monospace; margin-top: 2px;">Telp: {{ $structuredData['workshop_phone'] }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="label">Mekanik / Teknisi</span>
                        <span class="value">{{ $structuredData['mechanic_name'] ?: '-' }}</span>
                        @if(!empty($structuredData['mechanic_phone']))
                            <div style="font-size: 8.5px; color: #64748b; font-family: monospace; margin-top: 2px;">Telp: {{ $structuredData['mechanic_phone'] }}</div>
                        @endif
                    </td>
                    <td style="background-color: #f0fdf4;">
                        <span class="label" style="color: #166534;">Total Estimasi Biaya</span>
                        <span class="value font-mono" style="color: #15803d; font-size: 12px;">
                            Rp {{ number_format($clientRequest->estimated_cost ?: 0, 0, ',', '.') }}
                        </span>
                    </td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    <!-- TAHAP 4: Foto Fisik Kendaraan & Bukti Keluhan (Jika Ada) -->
    @php
        $hasAnyAnglePhoto = !empty($vehicleAnglePhotos['front']) || !empty($vehicleAnglePhotos['right']) || !empty($vehicleAnglePhotos['left']) || !empty($vehicleAnglePhotos['behind']);
    @endphp
    @if($hasAnyAnglePhoto)
    <div class="section-box" style="page-break-inside: avoid;">
        <div class="section-header">4. Lampiran Foto Fisik 4 Sudut Kendaraan</div>
        <div class="section-body">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td class="photo-cell">
                        <span class="label">1. Tampak Depan</span>
                        @if(isset($vehicleAnglePhotos['front']) && file_exists(public_path('storage/' . $vehicleAnglePhotos['front']['path'])))
                            <img src="{{ public_path('storage/' . $vehicleAnglePhotos['front']['path']) }}" class="photo-img">
                        @else
                            <div style="height: 70px; line-height: 70px; color: #94a3b8; font-size: 8.5px;">Foto Depan</div>
                        @endif
                    </td>
                    <td class="photo-cell">
                        <span class="label">2. Samping Kanan</span>
                        @if(isset($vehicleAnglePhotos['right']) && file_exists(public_path('storage/' . $vehicleAnglePhotos['right']['path'])))
                            <img src="{{ public_path('storage/' . $vehicleAnglePhotos['right']['path']) }}" class="photo-img">
                        @else
                            <div style="height: 70px; line-height: 70px; color: #94a3b8; font-size: 8.5px;">Foto Kanan</div>
                        @endif
                    </td>
                    <td class="photo-cell">
                        <span class="label">3. Samping Kiri</span>
                        @if(isset($vehicleAnglePhotos['left']) && file_exists(public_path('storage/' . $vehicleAnglePhotos['left']['path'])))
                            <img src="{{ public_path('storage/' . $vehicleAnglePhotos['left']['path']) }}" class="photo-img">
                        @else
                            <div style="height: 70px; line-height: 70px; color: #94a3b8; font-size: 8.5px;">Foto Kiri</div>
                        @endif
                    </td>
                    <td class="photo-cell">
                        <span class="label">4. Tampak Belakang</span>
                        @if(isset($vehicleAnglePhotos['behind']) && file_exists(public_path('storage/' . $vehicleAnglePhotos['behind']['path'])))
                            <img src="{{ public_path('storage/' . $vehicleAnglePhotos['behind']['path']) }}" class="photo-img">
                        @else
                            <div style="height: 70px; line-height: 70px; color: #94a3b8; font-size: 8.5px;">Foto Belakang</div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
    @endif

    <!-- Catatan Tanggapan Admin / Internal Koperasi Swadharma -->
    @if($clientRequest->admin_response)
    <div class="section-box" style="border-left: 3.5px solid #015471; background: #f8fafc;">
        <div class="section-header" style="background: transparent;">Tanggapan / Verifikasi Admin Koperasi Swadharma</div>
        <div class="section-body" style="font-size: 9.5px; color: #1e293b;">
            {!! nl2br(e($clientRequest->admin_response)) !!}
        </div>
    </div>
    @endif

</body>
</html>
