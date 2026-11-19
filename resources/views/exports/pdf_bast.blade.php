<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $bast->bast_number }}</title>
    <style>
        @page {
            margin: 24px 32px 28px 32px;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
            font-size: 9.5px;
            color: #1e293b;
            line-height: 1.45;
            margin: 0;
            padding: 0;
        }
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2.5px solid #015471;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .kop-table td {
            vertical-align: middle;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 15px;
            font-weight: bold;
            color: #015471;
            letter-spacing: 0.4px;
            margin: 0 0 2px 0;
        }
        .company-sub {
            font-size: 8.5px;
            color: #64748b;
        }
        .doc-badge {
            display: inline-block;
            background: #FF7A3D;
            color: #ffffff;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .doc-meta-right {
            font-size: 8px;
            color: #475569;
            margin-top: 4px;
        }
        .title-block {
            text-align: center;
            margin: 8px 0 14px 0;
            padding-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        .doc-title {
            font-size: 13px;
            font-weight: bold;
            color: #015471;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin: 0 0 3px 0;
        }
        .doc-number {
            font-size: 9.5px;
            color: #334155;
        }
        .intro-text {
            text-align: justify;
            margin: 0 0 12px 0;
            font-size: 9.5px;
            color: #334155;
        }
        .section-heading {
            background: #015471;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 5px 9px;
            margin: 10px 0 0 0;
            border-radius: 3px 3px 0 0;
        }
        table.grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.grid th,
        table.grid td {
            border: 1px solid #cbd5e1;
            padding: 5.5px 8px;
            text-align: left;
            vertical-align: top;
        }
        table.grid th {
            background: #f8fafc;
            color: #475569;
            font-size: 8.5px;
            font-weight: bold;
            width: 28%;
        }
        table.grid td {
            color: #0f172a;
            font-size: 9px;
        }
        .notes-box {
            border: 1px solid #cbd5e1;
            border-top: none;
            background: #ffffff;
            padding: 8px 10px;
            margin-bottom: 10px;
            font-size: 9px;
            color: #1e293b;
            min-height: 36px;
        }
        .clause-box {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-left: 3.5px solid #FF7A3D;
            padding: 8px 11px;
            margin: 10px 0 14px 0;
            font-size: 8.8px;
            color: #334155;
            text-align: justify;
        }
        .sig-wrapper {
            page-break-inside: avoid;
            margin-top: 14px;
        }
        .sig-place-date {
            text-align: right;
            font-size: 9px;
            color: #334155;
            margin-bottom: 8px;
        }
        table.signatures {
            width: 100%;
            border-collapse: collapse;
        }
        table.signatures td {
            width: 50%;
            border: 1px solid #e2e8f0;
            padding: 10px 14px 12px 14px;
            text-align: center;
            vertical-align: top;
        }
        .sig-role {
            font-size: 8.5px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .sig-org {
            font-size: 9.5px;
            font-weight: bold;
            color: #015471;
            margin-top: 2px;
        }
        .sig-space {
            height: 54px;
        }
        .sig-name {
            display: inline-block;
            border-top: 1px solid #334155;
            padding-top: 3px;
            min-width: 150px;
            font-weight: bold;
            font-size: 9.5px;
            color: #0f172a;
        }
        .sig-pos {
            font-size: 8px;
            color: #64748b;
            margin-top: 2px;
        }
        .footer-note {
            margin-top: 14px;
            padding-top: 6px;
            border-top: 1px dashed #cbd5e1;
            font-size: 7.5px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>
    @php
        $clientObj = $bast->vendor?->client ?? $bast->vehicle?->vendor?->client;
        $clientName = $clientObj?->name ?: ($bast->vendor?->name ?: 'Klien Mitra');
        $clientCode = $clientObj?->code;
        $contractNumber = $bast->vendor?->contract_number ?: '-';
        $projectName = $bast->vendor?->project_name;
        $logoPath = public_path('img/icon/kopswa-icon.png');
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;
    @endphp

    <!-- Kop Dokumen -->
    <table class="kop-table">
        <tr>
            <td style="width: 62%;">
                <table style="border-collapse: collapse;">
                    <tr>
                        @if($logoBase64)
                            <td style="padding: 0 10px 0 0; vertical-align: middle;">
                                <img src="{{ $logoBase64 }}" alt="Logo Koperasi Swadharma" style="height: 38px; width: auto; display: block;">
                            </td>
                        @endif
                        <td style="padding: 0; vertical-align: middle;">
                            <div class="company-name">KOPERASI SWADHARMA</div>
                            <div class="company-sub">Sistem Monitoring &amp; Pengelolaan Aset Sewa Operasional</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 38%; text-align: right;">
                <span class="doc-badge">{{ $bast->type_label }}</span>
                <div class="doc-meta-right">Tanggal Terbit: <strong>{{ $bast->handover_date->translatedFormat('d F Y') }}</strong></div>
            </td>
        </tr>
    </table>

    <!-- Judul Dokumen -->
    <div class="title-block">
        <div class="doc-title">BERITA ACARA SERAH TERIMA ({{ strtoupper($bast->type_label) }})</div>
        <div class="doc-number">Nomor Dokumen: <strong>{{ $bast->bast_number }}</strong></div>
    </div>

    <!-- Paragraf Pembuka -->
    <p class="intro-text">
        Pada hari ini, tanggal <strong>{{ $bast->handover_date->translatedFormat('d F Y') }}</strong>, kami yang bertanda tangan di bawah ini telah melaksanakan serah terima unit aset operasional dengan rincian para pihak, kontrak sewa, serta spesifikasi unit sebagai berikut:
    </p>

    <!-- Bagian 1: Para Pihak & Kontrak -->
    <div class="section-heading">I. Data Para Pihak &amp; Kontrak Sewa</div>
    <table class="grid">
        <tr>
            <th>Pihak Pertama (Penyerah)</th>
            <td><strong>{{ $bast->handed_over_by ?: 'Tim Operasional' }}</strong> — Koperasi Swadharma</td>
        </tr>
        <tr>
            <th>Client / Perusahaan Mitra</th>
            <td>
                <strong>{{ $clientName }}</strong>
                @if($clientCode) <span style="color: #64748b;">({{ $clientCode }})</span> @endif
                @if($clientObj?->address)
                    <br><span style="font-size: 8.5px; color: #475569;">{{ $clientObj->address }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>Pihak Kedua (Penerima)</th>
            <td>
                <strong>{{ $bast->received_by ?: '-' }}</strong>
                @if($bast->recipient_position) — {{ $bast->recipient_position }} @endif
            </td>
        </tr>
        <tr>
            <th>Nomor Kontrak Sewa</th>
            <td>
                <strong>{{ $contractNumber }}</strong>
                @if($projectName) — {{ $projectName }} @endif
            </td>
        </tr>
        <tr>
            <th>Masa Berlaku Kontrak</th>
            <td>
                @if($bast->contract_start_date && $bast->contract_end_date)
                    <strong>{{ $bast->contract_start_date->translatedFormat('d F Y') }}</strong> s.d. <strong>{{ $bast->contract_end_date->translatedFormat('d F Y') }}</strong>
                @else
                    Sesuai masa berlaku kontrak aktif
                @endif
            </td>
        </tr>
    </table>

    <!-- Bagian 2: Spesifikasi Unit -->
    <div class="section-heading">II. Spesifikasi Unit Aset</div>
    <table class="grid">
        <tr>
            <th>Jenis &amp; Model Aset</th>
            <td>
                {{ $bast->vehicle?->asset_type_label ?: '-' }} —
                <strong>{{ trim(($bast->vehicle?->brand ?? '').' '.($bast->vehicle?->model_name ?? '')) }}</strong>
                @if($bast->vehicle?->year) (Tahun {{ $bast->vehicle->year }}) @endif
            </td>
        </tr>
        <tr>
            <th>Plat / Kode Unit</th>
            <td>
                <strong>{{ $bast->vehicle?->plate_number ?: '-' }}</strong>
                @if($bast->vehicle?->asset_code) &nbsp;|&nbsp; Kode Internal: <strong>{{ $bast->vehicle->asset_code }}</strong> @endif
            </td>
        </tr>
        @if($bast->vehicle?->frame_number || $bast->vehicle?->engine_number || $bast->vehicle?->serial_number)
        <tr>
            <th>Nomor Identitas Teknis</th>
            <td>
                @if($bast->vehicle?->frame_number) No. Rangka: <strong>{{ $bast->vehicle->frame_number }}</strong> @endif
                @if($bast->vehicle?->engine_number) &nbsp;|&nbsp; No. Mesin: <strong>{{ $bast->vehicle->engine_number }}</strong> @endif
                @if($bast->vehicle?->serial_number)
                    @if($bast->vehicle?->frame_number || $bast->vehicle?->engine_number)<br>@endif
                    Serial Number: <strong>{{ $bast->vehicle->serial_number }}</strong>
                @endif
            </td>
        </tr>
        @endif
        @if($bast->vehicle?->location)
        <tr>
            <th>Lokasi Penempatan</th>
            <td>{{ $bast->vehicle->location->name }}</td>
        </tr>
        @endif
        @if($bast->odometer_km)
        <tr>
            <th>Odometer / KM Serah Terima</th>
            <td><strong>{{ number_format($bast->odometer_km, 0, ',', '.') }} KM</strong></td>
        </tr>
        @endif
        @if($bast->replacementVehicle)
        <tr>
            <th>Unit Pengganti Diserahkan</th>
            <td>
                <strong>{{ $bast->replacementVehicle->plate_number }}</strong> — {{ $bast->replacementVehicle->model_name }}
                @if((float) $bast->replacement_cost > 0)
                    <br>Biaya Unit Pengganti: <strong>Rp {{ number_format($bast->replacement_cost, 0, ',', '.') }}</strong>
                @endif
            </td>
        </tr>
        @endif
    </table>

    <!-- Bagian 3: Kondisi & Kelengkapan -->
    <div class="section-heading">III. Catatan Kondisi &amp; Kelengkapan Unit</div>
    <div class="notes-box">
        {!! nl2br(e($bast->condition_notes ?: 'Unit diserahkan dan diterima dalam kondisi baik, normal, dan lengkap sesuai standar operasional yang berlaku.')) !!}
    </div>

    <!-- Klausul -->
    <div class="clause-box">
        <strong>Klausul Serah Terima:</strong> Berita Acara Serah Terima ini merupakan bagian yang tidak terpisahkan dari Perjanjian Sewa Nomor <strong>{{ $contractNumber }}</strong> dengan masa berlaku kontrak tanggal <strong>{{ $bast->contract_start_date?->format('d/m/Y') ?: '-' }}</strong> sampai dengan <strong>{{ $bast->contract_end_date?->format('d/m/Y') ?: '-' }}</strong>. Sejak ditandatangani, tanggung jawab pemakaian dan pemeliharaan unit beralih sesuai ketentuan kontrak yang berlaku.
    </div>

    <!-- Tanda Tangan -->
    <div class="sig-wrapper">
        <div class="sig-place-date">
            Tanggal Serah Terima: <strong>{{ $bast->handover_date->translatedFormat('d F Y') }}</strong>
        </div>
        <table class="signatures">
            <tr>
                <td>
                    <div class="sig-role">Pihak Pertama (Yang Menyerahkan)</div>
                    <div class="sig-org">Koperasi Swadharma</div>
                    <div class="sig-space"></div>
                    <div class="sig-name">{{ $bast->handed_over_by ?: 'Tim Operasional' }}</div>
                    <div class="sig-pos">Perwakilan Koperasi Swadharma</div>
                </td>
                <td>
                    <div class="sig-role">Pihak Kedua (Yang Menerima)</div>
                    <div class="sig-org">{{ $clientName }}</div>
                    <div class="sig-space"></div>
                    <div class="sig-name">{{ $bast->received_by ?: 'Perwakilan Klien' }}</div>
                    <div class="sig-pos">{{ $bast->recipient_position ?: 'Penerima Unit' }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer-note">
        Dokumen resmi diterbitkan melalui Sistem Monitoring Aset Koperasi Swadharma &bull; {{ $bast->bast_number }}
    </div>
</body>
</html>
