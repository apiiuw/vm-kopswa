<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Anggaran Operasional Kendaraan Koperasi Swadharma</title>
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
        <div class="company-title">{{ $vendor ? $vendor->name : 'KOPERASI SWADHARMA' }}</div>
        @if(($tab ?? 'overview') === 'incomes')
            <div class="doc-title">REKAPITULASI PENAGIHAN SEWA ARMADA BULANAN (INCOMES)</div>
            <div class="sub-title">
                @if($vendor && $vendor->contract_number)
                    Surat Perjanjian Sewa No: {{ $vendor->contract_number }} | {{ $vendor->client_name }}
                @else
                    Daftar Invoice Penagihan Sewa Kendaraan Beserta Rincian PPN, PPh 2% & Penerimaan Bersih
                @endif
            </div>
        @elseif(($tab ?? 'overview') === 'expenses')
            <div class="doc-title">REKAPITULASI BUKU KAS PENGELUARAN OPERASIONAL (EXPENSES)</div>
            <div class="sub-title">
                Laporan Rincian Biaya Servis, Ban, Accu, Suku Cadang, dan Operasional Lapangan
            </div>
        @else
            <div class="doc-title">REKAPITULASI ALOKASI & REALISASI BIAYA OPERASIONAL KENDARAAN</div>
            <div class="sub-title">
                @if($vendor && $vendor->contract_number)
                    Surat Perjanjian Sewa No: {{ $vendor->contract_number }} | {{ $vendor->client_name }}
                @else
                    Laporan Monitoring Biaya Pemeliharaan & Operasional Armada Kendaraan (Plafon 36 Bulan)
                @endif
            </div>
        @endif
    </div>

    <!-- Summary Metrics Box for PDF -->
    <table style="margin-bottom: 12px; background-color: #f8fafc;">
        <tr>
            @if(($tab ?? 'overview') === 'incomes')
                <td style="text-align: center; width: 25%; padding: 8px;">
                    <div style="font-size: 8px; color: #64748b; font-weight: bold; text-transform: uppercase;">Total Bruto Sewa</div>
                    <div style="font-size: 11px; font-weight: bold; color: #015471; margin-top: 2px;">Rp {{ number_format($totalIncomeGross, 0, ',', '.') }}</div>
                </td>
                <td style="text-align: center; width: 25%; padding: 8px;">
                    <div style="font-size: 8px; color: #64748b; font-weight: bold; text-transform: uppercase;">Potongan PPh 2%</div>
                    <div style="font-size: 11px; font-weight: bold; color: #d97706; margin-top: 2px;">Rp {{ number_format($totalIncomePph, 0, ',', '.') }}</div>
                </td>
                <td style="text-align: center; width: 25%; padding: 8px;">
                    <div style="font-size: 8px; color: #64748b; font-weight: bold; text-transform: uppercase;">Penerimaan Bersih Riil</div>
                    <div style="font-size: 11px; font-weight: bold; color: #047857; margin-top: 2px;">Rp {{ number_format($totalIncomeNet, 0, ',', '.') }}</div>
                </td>
                <td style="text-align: center; width: 25%; padding: 8px;">
                    <div style="font-size: 8px; color: #64748b; font-weight: bold; text-transform: uppercase;">Status Lunas</div>
                    <div style="font-size: 11px; font-weight: bold; color: #015471; margin-top: 2px;">Rp {{ number_format($totalIncomePaid, 0, ',', '.') }}</div>
                </td>
            @elseif(($tab ?? 'overview') === 'expenses')
                <td style="text-align: center; width: 33.3%; padding: 8px;">
                    <div style="font-size: 8px; color: #64748b; font-weight: bold; text-transform: uppercase;">Total Pengeluaran Riil</div>
                    <div style="font-size: 11px; font-weight: bold; color: #dc2626; margin-top: 2px;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
                </td>
                <td style="text-align: center; width: 33.3%; padding: 8px;">
                    <div style="font-size: 8px; color: #64748b; font-weight: bold; text-transform: uppercase;">Total Transaksi</div>
                    <div style="font-size: 11px; font-weight: bold; color: #015471; margin-top: 2px;">{{ count($expenses) }} Catatan</div>
                </td>
                <td style="text-align: center; width: 33.3%; padding: 8px;">
                    <div style="font-size: 8px; color: #64748b; font-weight: bold; text-transform: uppercase;">Sisa Pagu Tersedia</div>
                    <div style="font-size: 11px; font-weight: bold; color: #047857; margin-top: 2px;">Rp {{ number_format($remainingBudget, 0, ',', '.') }}</div>
                </td>
            @else
                <td style="text-align: center; width: 33.3%; padding: 8px;">
                    <div style="font-size: 8px; color: #64748b; font-weight: bold; text-transform: uppercase;">Pagu Operasional 36 Bln</div>
                    <div style="font-size: 11px; font-weight: bold; color: #015471; margin-top: 2px;">Rp {{ number_format($totalBudget, 0, ',', '.') }}</div>
                </td>
                <td style="text-align: center; width: 33.3%; padding: 8px;">
                    <div style="font-size: 8px; color: #64748b; font-weight: bold; text-transform: uppercase;">Realisasi Biaya Riil</div>
                    <div style="font-size: 11px; font-weight: bold; color: #dc2626; margin-top: 2px;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
                </td>
                <td style="text-align: center; width: 33.3%; padding: 8px;">
                    <div style="font-size: 8px; color: #64748b; font-weight: bold; text-transform: uppercase;">Sisa Pagu Anggaran</div>
                    <div style="font-size: 11px; font-weight: bold; color: #047857; margin-top: 2px;">Rp {{ number_format($remainingBudget, 0, ',', '.') }}</div>
                </td>
            @endif
        </tr>
    </table>

    @if(($tab ?? 'overview') === 'incomes')
        <!-- Tabel Incomes / Tagihan Sewa -->
        <table>
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th>No. Invoice</th>
                    <th>Periode</th>
                    <th>Tgl Invoice</th>
                    <th class="text-right">Bruto (Rp)</th>
                    <th class="text-right">PPN 11% (Rp)</th>
                    <th class="text-right">Total Tagihan (Rp)</th>
                    <th class="text-right">PPh 2% (Rp)</th>
                    <th class="text-right">Keuntungan Bersih (Rp)</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($incomes as $inc)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td><strong>{{ $inc->invoice_number }}</strong></td>
                        <td>{{ $inc->period_month_year }}</td>
                        <td class="text-center">{{ $inc->invoice_date->format('d/m/Y') }}</td>
                        <td class="text-right">{{ number_format($inc->gross_amount, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($inc->ppn_amount, 0, ',', '.') }}</td>
                        <td class="text-right font-bold">{{ number_format($inc->total_amount_with_ppn, 0, ',', '.') }}</td>
                        <td class="text-right" style="color: #d97706;">{{ number_format($inc->pph23_amount, 0, ',', '.') }}</td>
                        <td class="text-right font-bold" style="color: #047857;">{{ number_format($inc->net_profit, 0, ',', '.') }}</td>
                        <td class="text-center"><strong>{{ strtoupper($inc->status) }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f1f5f9; font-weight: bold;">
                    <td colspan="4" class="text-right">TOTAL PENAGIHAN:</td>
                    <td class="text-right">Rp {{ number_format($totalIncomeGross, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($incomes->sum('ppn_amount'), 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($incomes->sum('total_amount_with_ppn'), 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #d97706;">Rp {{ number_format($totalIncomePph, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #047857;">Rp {{ number_format($totalIncomeNet, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $incomes->where('status', 'lunas')->count() }} Lunas</td>
                </tr>
            </tfoot>
        </table>

    @elseif(($tab ?? 'overview') === 'expenses')
        <!-- Tabel Expenses / Buku Kas Pengeluaran -->
        <table>
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th>No. Bukti / Memo</th>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Uraian / Keterangan</th>
                    <th>Unit Terkait</th>
                    <th class="text-right">Jumlah Pengeluaran (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($expenses as $exp)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td><strong>{{ $exp->expense_number }}</strong></td>
                        <td class="text-center">{{ $exp->transaction_date ? $exp->transaction_date->format('d/m/Y') : '-' }}</td>
                        <td>{{ strtoupper(str_replace('_', ' ', $exp->category)) }}</td>
                        <td>{{ $exp->title }}</td>
                        <td>{{ $exp->vehicle ? $exp->vehicle->plate_number : '-' }}</td>
                        <td class="text-right font-bold" style="color: #dc2626;">{{ number_format($exp->amount, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f1f5f9; font-weight: bold;">
                    <td colspan="6" class="text-right">TOTAL PENGELUARAN BUKU KAS:</td>
                    <td class="text-right" style="color: #dc2626;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

    @else
        <!-- Tabel Overview / Plafon Memo -->
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
            <em>Catatan: Sisa plafon anggaran operasional sebesar Rp {{ number_format($remainingBudget, 0, ',', '.') }} dihitung berdasarkan realisasi pengeluaran operasional tercatat terhadap total pagu anggaran aktif.</em>
        </div>
    @endif

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
                    <strong>Koperasi Swadharma</strong>
                    <br><br><br><br>
                    <u><strong>Siti Khodijah / Tim Operasional</strong></u><br>
                    Pengelola Armada Proyek
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
