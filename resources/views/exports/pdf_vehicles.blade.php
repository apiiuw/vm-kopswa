<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar 76 Unit Armada Sepeda Motor Koperasi Swadharma</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8px;
            color: #000000;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #015471;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }
        .company-title {
            font-size: 13px;
            font-weight: bold;
            color: #015471;
        }
        .doc-title {
            font-size: 11px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 4px 5px;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 7.5px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <div class="company-title">{{ $vendor ? $vendor->name : 'KOPERASI SWADHARMA' }}</div>
        <div class="doc-title">DAFTAR INVENTARIS ARMADA KENDARAAN OPERASIONAL</div>
        <div style="font-size: 8px; color: #64748b;">
            @if($vendor && $vendor->contract_number)
                Kontrak No: {{ $vendor->contract_number }} | Klien: {{ $vendor->client_name }}
            @else
                Rekapitulasi Unit Armada Operasional Terdaftar
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 20px;">No</th>
                <th style="width: 70px;">Plat Nomor</th>
                <th style="width: 90px;">Tipe & Model</th>
                <th style="width: 100px;">Lokasi Kebun</th>
                <th>Pemegang / Pemakai Lapangan</th>
                <th style="width: 45px;" class="text-center">Tahun</th>
                <th style="width: 60px;">Warna</th>
                <th style="width: 75px;" class="text-right">Tarif Sewa/Bulan</th>
                <th style="width: 80px;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vehicles as $idx => $v)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-bold">{{ $v->plate_number }}</td>
                    <td>{{ $v->model_name }}</td>
                    <td>{{ $v->location ? $v->location->name : '-' }}</td>
                    <td>{{ $v->user_holder ?: '-' }}</td>
                    <td class="text-center">{{ $v->year }}</td>
                    <td>{{ $v->color }}</td>
                    <td class="text-right">Rp {{ number_format($v->rental_price_per_month, 0, ',', '.') }}</td>
                    <td class="text-center font-bold">{{ $v->status_badge['label'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
