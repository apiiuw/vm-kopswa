<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Anggaran Operasional Kendaraan PT SUP</title>
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
            padding-bottom: 8px;
            margin-bottom: 14px;
        }
        .company-title {
            font-size: 14px;
            font-weight: bold;
            color: #015471;
            text-transform: uppercase;
        }
        .doc-title {
            font-size: 12px;
            font-weight: bold;
            margin-top: 2px;
        }
        .sub-title {
            font-size: 9px;
            color: #64748b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
        }
        th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .bg-gray { background-color: #f8fafc; }
        .footer {
            margin-top: 25px;
        }
        .signature-table {
            width: 100%;
            border: none;
        }
        .signature-table td {
            border: none;
            text-align: center;
            padding: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="company-title">{{ $vendor ? $vendor->name : 'PT SWADHARMA UTAMA PRIMA' }}</div>
        <div class="doc-title">REKAPITULASI ALOKASI & REALISASI BIAYA OPERASIONAL KENDARAAN</div>
        <div class="sub-title">
            @if($vendor && $vendor->contract_number)
                Surat Perjanjian Sewa No: {{ $vendor->contract_number }} | {{ $vendor->client_name }}
            @else
                Laporan Monitoring Biaya Pemeliharaan & Operasional Armada Kendaraan
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th>Pos Biaya Operasional</th>
                <th style="width: 50px;" class="text-center">Qty Unit</th>
                <th style="width: 90px;" class="text-right">Plafon Anggaran 36 Bulan (Rp)</th>
                <th style="width: 85px;" class="text-right">Realisasi Biaya (Rp)</th>
                <th style="width: 85px;" class="text-right">Sisa Anggaran (Rp)</th>
                <th style="width: 50px;" class="text-center">% Pakai</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($budgets as $b)
                @php
                    $spent = \App\Models\Expense::where('category', $b->category)->sum('amount');
                    $rem = $b->total_budget_36m - $spent;
                    $pct = $b->total_budget_36m > 0 ? ($spent / $b->total_budget_36m) * 100 : 0;
                @endphp
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>
                        <strong>{{ $b->label }}</strong>
                        <div style="font-size: 8px; color: #64748b;">{{ $b->notes }}</div>
                    </td>
                    <td class="text-center">{{ $b->qty_units }}</td>
                    <td class="text-right font-bold">{{ number_format($b->total_budget_36m, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #dc2626;">{{ number_format($spent, 0, ',', '.') }}</td>
                    <td class="text-right font-bold" style="color: #047857;">{{ number_format($rem, 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($pct, 1) }}%</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="3" class="text-right">TOTAL BIAYA OPERASIONAL:</td>
                <td class="text-right">Rp {{ number_format($totalBudget, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #dc2626;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #047857;">Rp {{ number_format($remainingBudget, 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format(($totalExpense / max(1, $totalBudget)) * 100, 1) }}%</td>
            </tr>
        </tfoot>
    </table>

    <div style="font-size: 8px; color: #475569; margin-top: 10px;">
        <em>Catatan: Sisa plafon anggaran operasional sebesar Rp {{ number_format($remainingBudget, 0, ',', '.') }} mencukupi rata-rata pengeluaran operasional lapangan hingga akhir masa kontrak Mei 2028 sesuai persetujuan Direksi PT SUP.</em>
    </div>

    <div class="footer">
        <table class="signature-table">
            <tr>
                <td style="width: 50%;">
                    Mengetahui,<br>
                    <strong>PT Perkebunan Nusantara I Regional 7</strong>
                    <br><br><br><br>
                    <u><strong>W I Y O S O</strong></u><br>
                    Senior Executive Vice President (SEVP)
                </td>
                <td style="width: 50%;">
                    Dibuat Oleh,<br>
                    <strong>PT Swadharma Utama Prima</strong>
                    <br><br><br><br>
                    <u><strong>Siti Khodijah / Tim Operasional</strong></u><br>
                    Pengelola Armada Proyek
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
