<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu Riwayat Kendaraan {{ $vehicle->plate_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #000000;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #015471;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .company-title {
            font-size: 14px;
            font-weight: bold;
            color: #015471;
        }
        .doc-title {
            font-size: 12px;
            font-weight: bold;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 4px 6px;
            border: 1px solid #e2e8f0;
        }
        .table-section-title {
            font-size: 11px;
            font-weight: bold;
            margin: 12px 0 6px 0;
            color: #0f172a;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 3px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 5px;
        }
        table.data-table th {
            background-color: #f1f5f9;
            font-size: 9px;
            text-transform: uppercase;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <div class="company-title">{{ $vehicle->vendor ? $vehicle->vendor->name : 'PT SWADHARMA UTAMA PRIMA' }}</div>
        <div class="doc-title">KARTU RIWAYAT PEMELIHARAAN KENDARAAN (VEHICLE HISTORY CARD)</div>
        <div style="font-size: 9px; color: #64748b;">
            @if($vehicle->vendor && $vehicle->vendor->contract_number)
                Kontrak Sewa No: {{ $vehicle->vendor->contract_number }} | {{ $vehicle->vendor->client_name }}
            @else
                Monitoring Pemeliharaan & Riwayat Operasional Armada Kendaraan
            @endif
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td style="width: 25%; background: #f8fafc;"><strong>Plat Nomor:</strong></td>
            <td style="width: 25%; font-weight: bold;">{{ $vehicle->plate_number }}</td>
            <td style="width: 25%; background: #f8fafc;"><strong>Lokasi Kebun:</strong></td>
            <td style="width: 25%;">{{ $vehicle->location ? $vehicle->location->name : '-' }}</td>
        </tr>
        <tr>
            <td style="background: #f8fafc;"><strong>Tipe & Model:</strong></td>
            <td>{{ $vehicle->model_name }}</td>
            <td style="background: #f8fafc;"><strong>Pemegang Unit:</strong></td>
            <td>{{ $vehicle->user_holder ?: '-' }}</td>
        </tr>
        <tr>
            <td style="background: #f8fafc;"><strong>Nomor Rangka:</strong></td>
            <td style="font-family: monospace;">{{ $vehicle->frame_number ?: '-' }}</td>
            <td style="background: #f8fafc;"><strong>Tahun / Warna:</strong></td>
            <td>{{ $vehicle->year }} / {{ $vehicle->color }}</td>
        </tr>
        <tr>
            <td style="background: #f8fafc;"><strong>Status Operasional:</strong></td>
            <td style="font-weight: bold;">{{ $vehicle->status_badge['label'] }}</td>
            <td style="background: #f8fafc;"><strong>Tarif Sewa/Bulan:</strong></td>
            <td style="font-weight: bold;">Rp {{ number_format($vehicle->rental_price_per_month, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="table-section-title">A. Riwayat Pemeliharaan, Servis & Suku Cadang (Ban / Accu / Sparepart)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 65px;">Tanggal</th>
                <th style="width: 100px;">Jenis</th>
                <th>Deskripsi / Item Suku Cadang</th>
                <th style="width: 100px;">Bengkel / Rekanan</th>
                <th style="width: 80px;" class="text-right">Biaya (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalMaint = 0; @endphp
            @forelse($vehicle->maintenanceRecords as $mr)
                @php $totalMaint += $mr->cost; @endphp
                <tr>
                    <td class="text-center">{{ $mr->date->format('d/m/Y') }}</td>
                    <td>{{ $mr->type_label }}</td>
                    <td>{{ $mr->item_name ?: $mr->description }}</td>
                    <td>{{ $mr->workshop_name ?: '-' }}</td>
                    <td class="text-right font-bold">{{ number_format($mr->cost, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="color: #94a3b8;">Belum ada catatan pemeliharaan.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background: #f8fafc; font-weight: bold;">
                <td colspan="4" class="text-right">TOTAL BIAYA PEMELIHARAAN:</td>
                <td class="text-right" style="color: #dc2626;">Rp {{ number_format($totalMaint, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="table-section-title">B. Dokumen Legalitas (STNK & Pajak)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Jenis Dokumen</th>
                <th>Nomor Dokumen</th>
                <th style="width: 80px;" class="text-center">Jatuh Tempo</th>
                <th style="width: 80px;" class="text-right">Biaya (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vehicle->documents as $doc)
                <tr>
                    <td>{{ $doc->type_label }}</td>
                    <td>{{ $doc->document_number ?: '-' }}</td>
                    <td class="text-center">{{ $doc->expiry_date ? $doc->expiry_date->format('d/m/Y') : '-' }}</td>
                    <td class="text-right">{{ number_format($doc->cost, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="color: #94a3b8;">Belum ada dokumen tercatat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
