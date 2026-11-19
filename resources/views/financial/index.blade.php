@extends('layouts.app')

@section('title', 'Pendapatan, Pengeluaran & Anggaran Operasional')
@section('page_title', 'Keuangan Proyek & Realisasi Anggaran')

@section('content')
<div class="space-y-6 w-full bg-white text-black">

    <!-- Top Action & Tabs Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-black tracking-tight">Monitoring Anggaran & Buku Kas</h2>
            <p class="text-xs text-slate-500 mt-0.5">Penagihan sewa per kontrak, pengeluaran riil, penyusutan aset, & faktur pajak Coretax</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
            <a href="{{ route('exports.financialExcel') }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold rounded-xl bg-white hover:bg-slate-50 text-black border-2 border-slate-200 shadow-2xs transition">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-[#015471]"></i> Ekspor Excel Lengkap
            </a>
            <a href="{{ route('exports.budgetPdf', ['tab' => $tab]) }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-sm transition">
                <i data-lucide="printer" class="w-4 h-4"></i> Cetak Rekap PDF
            </a>
        </div>
    </div>

    <!-- Summary Ceiling & Cash Cards (Dynamic per Tab) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        @if($tab === 'incomes')
            <!-- Incomes Tab Summary Cards -->
            <div class="bg-white p-5 rounded-2xl border-2 border-slate-100 shadow-sm">
                <span class="text-xs font-bold text-[#015471]">Total Bruto Tagihan Sewa</span>
                <span class="text-2xl font-black text-black block mt-1 font-mono">Rp {{ number_format($totalIncomeGross, 0, ',', '.') }}</span>
                <span class="text-[11px] text-slate-500 mt-1 block">Akumulasi seluruh tagihan DPP riil</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border-2 border-slate-100 shadow-sm">
                <span class="text-xs font-bold text-amber-600">Total Potongan PPh 2%</span>
                <span class="text-2xl font-black text-amber-600 block mt-1 font-mono">Rp {{ number_format($totalIncomePph, 0, ',', '.') }}</span>
                <span class="text-[11px] text-slate-500 mt-1 block">Pasal 23 atas jasa sewa kendaraan</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border-2 border-slate-100 shadow-sm">
                <span class="text-xs font-bold text-emerald-700">Penerimaan / Keuntungan Bersih</span>
                <span class="text-2xl font-black text-emerald-700 block mt-1 font-mono">Rp {{ number_format($totalIncomeNet, 0, ',', '.') }}</span>
                <span class="text-[11px] text-emerald-700 font-bold mt-1 block">Lunas: Rp {{ number_format($totalIncomePaid, 0, ',', '.') }} | Belum: Rp {{ number_format($totalIncomeUnpaid, 0, ',', '.') }}</span>
            </div>
        @elseif($tab === 'expenses')
            <!-- Expenses Tab Summary Cards -->
            <div class="bg-white p-5 rounded-2xl border-2 border-slate-100 shadow-sm">
                <span class="text-xs font-bold text-[#FF7A3D]">Total Pengeluaran Kas Riil</span>
                <span class="text-2xl font-black text-[#FF7A3D] block mt-1 font-mono">Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
                <span class="text-[11px] text-slate-500 mt-1 block">Pemeliharaan, Ban, Accu, Sparepart & Cadangan</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border-2 border-slate-100 shadow-sm">
                <span class="text-xs font-bold text-[#015471]">Total Catatan Transaksi</span>
                <span class="text-2xl font-black text-[#015471] block mt-1 font-mono">{{ $totalExpensesCount }} Transaksi</span>
                <span class="text-[11px] text-slate-500 mt-1 block">Seluruh riwayat buku kas pengeluaran tercatat</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border-2 border-slate-100 shadow-sm">
                <span class="text-xs font-bold text-[#015471]">Sisa Pagu Anggaran Operasional</span>
                <span class="text-2xl font-black text-[#015471] block mt-1 font-mono">Rp {{ number_format($remainingBudget, 0, ',', '.') }}</span>
                <span class="text-[11px] text-[#015471] font-bold mt-1 block">Dari Pagu 36 Bulan (Rp {{ number_format($totalBudget, 0, ',', '.') }})</span>
            </div>
        @else
            <!-- Overview / Default Tab Summary Cards -->
            <div class="bg-white p-5 rounded-2xl border-2 border-slate-100 shadow-sm">
                <span class="text-xs font-bold text-[#015471]">Total Pagu Operasional 36 Bulan</span>
                <span class="text-2xl font-black text-black block mt-1 font-mono">Rp {{ number_format($totalBudget, 0, ',', '.') }}</span>
                <span class="text-[11px] text-slate-500 mt-1 block">Berdasarkan Memo Anggaran Internal 2026</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border-2 border-slate-100 shadow-sm">
                <span class="text-xs font-bold text-[#FF7A3D]">Realisasi Biaya Operasional Riil</span>
                <span class="text-2xl font-black text-[#FF7A3D] block mt-1 font-mono">Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
                <span class="text-[11px] text-slate-500 mt-1 block">Pemeliharaan, Ban, Accu, Sparepart, Unit Pengganti</span>
            </div>
            <div class="bg-white p-5 rounded-2xl border-2 border-slate-100 shadow-sm">
                <span class="text-xs font-bold text-[#015471]">Sisa Pagu Tersedia</span>
                <span class="text-2xl font-black text-[#015471] block mt-1 font-mono">Rp {{ number_format($remainingBudget, 0, ',', '.') }}</span>
                <span class="text-[11px] text-[#015471] font-bold mt-1 block">Selisih Pagu terhadap Realisasi</span>
            </div>
        @endif
    </div>

    <!-- Modern Segmented Tab Selector -->
    <div class="bg-slate-100/80 p-1.5 rounded-2xl border border-slate-200/80 shadow-2xs">
        <nav class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-1.5 text-xs font-bold" aria-label="Tabs">
            <!-- Tab 1: Plafon Anggaran -->
            <a href="{{ route('financial.index', ['tab' => 'overview']) }}" 
               class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl transition-all duration-200 {{ $tab === 'overview' ? 'bg-[#015471] text-white shadow-md font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-white/80' }}">
                <i data-lucide="pie-chart" class="w-3.5 h-3.5 shrink-0"></i>
                <span>Anggaran</span>
            </a>

            <!-- Tab 2: Tagihan Sewa (Incomes) -->
            <a href="{{ route('financial.index', ['tab' => 'incomes']) }}" 
               class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl transition-all duration-200 {{ $tab === 'incomes' ? 'bg-[#015471] text-white shadow-md font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-white/80' }}">
                <i data-lucide="arrow-down-left" class="w-3.5 h-3.5 shrink-0"></i>
                <span>Tagihan ({{ $incomes->total() }})</span>
            </a>

            <!-- Tab 3: Buku Kas Pengeluaran -->
            <a href="{{ route('financial.index', ['tab' => 'expenses']) }}" 
               class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl transition-all duration-200 {{ $tab === 'expenses' ? 'bg-[#015471] text-white shadow-md font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-white/80' }}">
                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 shrink-0"></i>
                <span>Pengeluaran ({{ $expenses->total() }})</span>
            </a>

            <!-- Tab 4: Pendapatan Lain-lain (Diskon Dealer) -->
            <a href="{{ route('financial.index', ['tab' => 'other_incomes']) }}" 
               class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl transition-all duration-200 {{ $tab === 'other_incomes' ? 'bg-[#015471] text-white shadow-md font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-white/80' }}">
                <i data-lucide="tag" class="w-3.5 h-3.5 shrink-0"></i>
                <span>Pend. Lain2 ({{ $otherIncomes->count() }})</span>
            </a>

            <!-- Tab 5: Penyusutan & Nilai Buku -->
            <a href="{{ route('financial.index', ['tab' => 'depreciation']) }}" 
               class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl transition-all duration-200 {{ $tab === 'depreciation' ? 'bg-[#015471] text-white shadow-md font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-white/80' }}">
                <i data-lucide="trending-down" class="w-3.5 h-3.5 shrink-0"></i>
                <span>Penyusutan Aset</span>
            </a>

            <!-- Tab 6: Faktur Pajak (Coretax) -->
            <a href="{{ route('financial.index', ['tab' => 'tax']) }}" 
               class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl transition-all duration-200 {{ $tab === 'tax' ? 'bg-[#FF7A3D] text-white shadow-md font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-white/80' }}">
                <i data-lucide="file-badge" class="w-3.5 h-3.5 shrink-0"></i>
                <span>Faktur Pajak ({{ $taxInvoices->count() }})</span>
            </a>
        </nav>
    </div>

    @if($tab === 'overview')
        <!-- Tab 1: Plafon Anggaran (Breakdown Dokumen Memo Master Halaman 2-3) -->
        <div class="bg-white rounded-2xl border-2 border-slate-100 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-200 bg-[#e6f0f3] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-[#015471]">Tabel Rincian Alokasi Biaya Operasional Selama Masa Kontrak (36 Bulan)</h3>
                    <p class="text-xs text-slate-600">Data perbandingan alokasi pagu anggaran tender vs pengeluaran operasional aktual</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('financial.budget.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold rounded-xl bg-[#015471] hover:bg-[#013e54] text-white shadow-xs transition">
                        <i data-lucide="plus" class="w-4 h-4 text-[#FF7A3D]"></i>
                        <span>Input Plafon Baru</span>
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-black font-bold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="px-5 py-3.5">Pos Anggaran Operasional</th>
                            <th class="px-5 py-3.5">Target Kendaraan</th>
                            <th class="px-5 py-3.5 text-right">Plafon 36 Bulan (Rp)</th>
                            <th class="px-5 py-3.5 text-right">Realisasi Riil (Rp)</th>
                            <th class="px-5 py-3.5 text-right">Sisa Anggaran (Rp)</th>
                            <th class="px-5 py-3.5 text-center">Persentase Terpakai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-black">
                        @foreach($categoriesReport as $k => $c)
                            @php
                                $percent = $c['allocated'] > 0 ? ($c['spent'] / $c['allocated']) * 100 : 0;
                            @endphp
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-5 py-3.5 font-bold text-black">
                                    {{ $c['label'] }}
                                </td>
                                <td class="px-5 py-3.5 text-slate-600 font-medium">
                                    {{ in_array($k, ['overhead_admin', 'motor_pengganti', 'legalitas_kepolisian']) ? 'Operasional Lapangan' : 'CRF 150 & Supra X' }}
                                </td>
                                <td class="px-5 py-3.5 text-right font-bold text-black">
                                    Rp {{ number_format($c['allocated'], 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3.5 text-right font-bold text-[#FF7A3D]">
                                    Rp {{ number_format($c['spent'], 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3.5 text-right font-bold text-[#015471]">
                                    Rp {{ number_format($c['remaining'], 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-center gap-2">
                                        <div class="w-16 bg-slate-100 rounded-full h-2">
                                            <div class="bg-[#015471] h-2 rounded-full" style="width: {{ min(100, $percent) }}%"></div>
                                        </div>
                                        <span class="text-[10px] font-bold text-black">{{ number_format($percent, 1) }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-[#e6f0f3] font-black text-black border-t-2 border-[#015471]">
                        <tr>
                            <td colspan="2" class="px-5 py-3.5 text-right uppercase text-[10px] text-[#015471]">Total Anggaran & Biaya:</td>
                            <td class="px-5 py-3.5 text-right">Rp {{ number_format($totalBudget, 0, ',', '.') }}</td>
                            <td class="px-5 py-3.5 text-right text-[#FF7A3D]">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                            <td class="px-5 py-3.5 text-right text-[#015471]">Rp {{ number_format($remainingBudget, 0, ',', '.') }}</td>
                            <td class="px-5 py-3.5 text-center">{{ number_format(($totalExpense / max(1, $totalBudget)) * 100, 1) }}%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    @elseif($tab === 'incomes')
        <!-- Tab 2: Penagihan Sewa Bulanan (Rp 115.060.000 / bln) -->
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-bold text-black">
                        Daftar Tagihan Sewa
                        @if($currentVendor)
                            ke {{ $currentVendor->client_name ?: 'Klien' }}
                        @endif
                    </h3>
                    <p class="text-xs text-slate-500">Rekapitulasi invoice penagihan sewa armada bulanan beserta status pelunasan</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <form action="{{ route('financial.income.generate') }}" method="POST" class="inline-flex items-center gap-1.5 bg-slate-50 border border-slate-200 rounded-xl p-1">
                        @csrf
                        <input type="month" name="period" value="{{ date('Y-m') }}" required class="text-xs font-bold bg-white border border-slate-200 rounded-lg px-2.5 py-1.5 text-slate-800">
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-lg bg-[#015471] hover:bg-[#01435b] text-white transition">
                            <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#FF7A3D]"></i> Generate Tagihan Kontrak
                        </button>
                    </form>
                    <a href="{{ route('financial.income.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-sm transition">
                        <i data-lucide="plus" class="w-4 h-4"></i> Buat Invoice Manual
                    </a>
                </div>
            </div>

            <!-- Toolbar Dropdown & Total Info -->
            <div class="bg-white p-3.5 rounded-2xl border-2 border-slate-100 shadow-2xs flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-slate-600">Tampilkan:</span>
                    <form method="GET" action="{{ route('financial.index') }}" class="inline">
                        <input type="hidden" name="tab" value="incomes">
                        <select name="per_page_incomes" onchange="this.form.submit()" class="text-xs font-bold bg-slate-50 border border-slate-200 text-slate-800 rounded-lg px-2.5 py-1.5 focus:ring-2 focus:ring-[#015471] focus:outline-hidden">
                            @foreach([10, 15, 25, 50, 100] as $size)
                                <option value="{{ $size }}" {{ (request('per_page_incomes', $perPageIncomes ?? 15) == $size) ? 'selected' : '' }}>{{ $size }} per halaman</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="text-xs text-slate-500 font-medium">
                    Total: <span class="font-bold text-slate-800">{{ $incomes->total() }}</span> Invoice Tagihan
                </div>
            </div>

            <div class="bg-white rounded-2xl border-2 border-slate-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto scrollbar-thin">
                    <table class="w-full text-left text-xs min-w-[1200px]">
                        <thead class="bg-[#e6f0f3] border-b border-slate-200 text-[#015471] font-bold uppercase tracking-wider text-[10px] whitespace-nowrap">
                            <tr>
                                <th class="px-5 py-3.5">No. Invoice</th>
                                <th class="px-5 py-3.5">Kontrak & Klien</th>
                                <th class="px-5 py-3.5">Periode Sewa</th>
                                <th class="px-5 py-3.5">Tanggal Invoice</th>
                                <th class="px-5 py-3.5 text-right">Bruto (Rp)</th>
                                <th class="px-5 py-3.5 text-right">PPN 11% (Rp)</th>
                                <th class="px-5 py-3.5 text-right">Total Tagihan (Rp)</th>
                                <th class="px-5 py-3.5 text-right">PPh 2% (Rp)</th>
                                <th class="px-5 py-3.5 text-center">Status</th>
                                <th class="px-5 py-3.5 text-right">Keuntungan Bersih (Rp)</th>
                                <th class="px-5 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-black whitespace-nowrap">
                            @forelse($incomes as $inc)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-5 py-3.5 font-mono font-bold text-black">{{ $inc->invoice_number }}</td>
                                    <td class="px-5 py-3.5">
                                        <div class="font-bold text-slate-900">{{ $inc->vendor?->name ?: $inc->client_name }}</div>
                                        <div class="text-[10px] font-mono text-slate-500">
                                            {{ $inc->contract_number ?: ($inc->vendor?->contract_number ?: '-') }}
                                            @if($inc->vendor?->payment_method)
                                                <span class="ml-1 px-1.5 py-0.5 rounded bg-slate-100 text-[#015471] font-sans font-bold">{{ $inc->vendor->payment_method_label }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 font-bold text-[#015471]">{{ $inc->period_month_year }}</td>
                                    <td class="px-5 py-3.5 text-slate-600">{{ $inc->invoice_date->format('d/m/Y') }}</td>
                                    <td class="px-5 py-3.5 font-medium text-right">Rp {{ number_format($inc->gross_amount, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3.5 text-slate-500 text-right">Rp {{ number_format($inc->ppn_amount, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3.5 font-bold text-black text-right">Rp {{ number_format($inc->total_amount_with_ppn, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3.5 font-bold text-amber-700 text-right">Rp {{ number_format($inc->pph23_amount, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3.5 text-center">
                                        @php
                                            $paidAmt = (float) ($inc->paid_amount ?? 0);
                                            $totalTagihan = (float) $inc->total_amount_with_ppn;
                                            $isPartial = $inc->status !== 'lunas' && $paidAmt > 0 && $paidAmt < $totalTagihan;
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $inc->status === 'lunas' ? 'bg-[#e6f0f3] text-[#015471]' : ($isPartial ? 'bg-amber-100 text-amber-800' : 'bg-[#FF7A3D]/20 text-[#FF7A3D]') }}">
                                            {{ $isPartial ? 'DIBAYAR SEBAGIAN' : strtoupper($inc->status) }}
                                        </span>
                                        @if($isPartial)
                                            <div class="text-[10px] font-mono text-slate-500 mt-1">
                                                Terbayar: Rp {{ number_format($paidAmt, 0, ',', '.') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 font-black text-emerald-700 font-mono text-right bg-emerald-50/40">
                                        Rp {{ number_format($inc->net_profit, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <!-- Tombol Lihat PDF (Icon Mata) -->
                                            <a href="{{ route('exports.singleInvoicePdf', ['income' => $inc->id, 'action' => 'stream']) }}" 
                                               target="_blank" 
                                               title="Lihat Invoice PDF"
                                               class="p-1.5 rounded-lg border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 hover:text-[#015471] transition shadow-2xs inline-flex items-center justify-center">
                                                <i data-lucide="eye" class="w-3.5 h-3.5 text-[#015471]"></i>
                                            </a>

                                            <!-- Tombol Download PDF (Arrow ke Bawah) -->
                                            <a href="{{ route('exports.singleInvoicePdf', ['income' => $inc->id, 'action' => 'download']) }}" 
                                               title="Download Invoice PDF"
                                               class="p-1.5 rounded-lg border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 hover:text-[#FF7A3D] transition shadow-2xs inline-flex items-center justify-center">
                                                <i data-lucide="download" class="w-3.5 h-3.5 text-[#FF7A3D]"></i>
                                            </a>

                                            @if($inc->status === 'draft')
                                                <form action="{{ route('financial.income.updateStatus', $inc) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="tertagih">
                                                    <button type="submit" class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-amber-500 text-white hover:bg-amber-600 shadow-2xs transition">
                                                        Set Tertagih
                                                    </button>
                                                </form>
                                            @endif

                                            @if($inc->status !== 'lunas')
                                                <button type="button" onclick="openPayIncomeModal({{ $inc->id }}, '{{ $inc->invoice_number }}', {{ (float) $inc->total_amount_with_ppn }}, {{ (float) ($inc->paid_amount ?? 0) }})" class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 shadow-2xs transition">
                                                    Catat Bayar
                                                </button>
                                                <form action="{{ route('financial.income.updateStatus', $inc) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="lunas">
                                                    <input type="hidden" name="payment_date" value="{{ date('Y-m-d') }}">
                                                    <button type="submit" class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-[#015471] text-white hover:bg-[#013e54] shadow-2xs transition">
                                                        Set Lunas
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                                    Lunas
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-8 text-slate-400">Belum ada invoice tagihan sewa.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($incomes->hasPages())
                    <div class="px-5 py-3 border-t border-slate-200 bg-white">
                        {{ $incomes->links() }}
                    </div>
                @endif
            </div>
        </div>

    @elseif($tab === 'expenses')
        <!-- Tab 3: Buku Kas Pengeluaran -->
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-bold text-black">Buku Kas Operasional Pekerjaan Sewa Kendaraan</h3>
                    <p class="text-xs text-slate-500">Mencatat pengeluaran servis, suku cadang, sewa unit pengganti, dan dokumen kepolisian</p>
                </div>
                <a href="{{ route('financial.expense.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-xl bg-[#015471] hover:bg-[#013e54] text-white shadow-sm transition">
                    <i data-lucide="plus" class="w-4 h-4"></i> Catat Pengeluaran Kas Baru
                </a>
            </div>

            <!-- Toolbar Dropdown & Total Info -->
            <div class="bg-white p-3.5 rounded-2xl border-2 border-slate-100 shadow-2xs flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-slate-600">Tampilkan:</span>
                    <form method="GET" action="{{ route('financial.index') }}" class="inline">
                        <input type="hidden" name="tab" value="expenses">
                        <select name="per_page_expenses" onchange="this.form.submit()" class="text-xs font-bold bg-slate-50 border border-slate-200 text-slate-800 rounded-lg px-2.5 py-1.5 focus:ring-2 focus:ring-[#015471] focus:outline-hidden">
                            @foreach([10, 15, 25, 50, 100] as $size)
                                <option value="{{ $size }}" {{ (request('per_page_expenses', $perPageExpenses ?? 15) == $size) ? 'selected' : '' }}>{{ $size }} per halaman</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="text-xs text-slate-500 font-medium">
                    Total: <span class="font-bold text-slate-800">{{ $expenses->total() }}</span> Transaksi Pengeluaran
                </div>
            </div>

            <div class="bg-white rounded-2xl border-2 border-slate-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#e6f0f3] border-b border-slate-200 text-[#015471] font-bold uppercase tracking-wider text-[10px]">
                            <tr>
                                <th class="px-5 py-3.5">Tanggal</th>
                                <th class="px-5 py-3.5">No. Bukti / Kas</th>
                                <th class="px-5 py-3.5">Kategori</th>
                                <th class="px-5 py-3.5">Uraian Transaksi</th>
                                <th class="px-5 py-3.5">No. Memo</th>
                                <th class="px-5 py-3.5 text-right">Nominal (Rp)</th>
                                <th class="px-5 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-black">
                            @forelse($expenses as $e)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-5 py-3.5 font-medium text-slate-600 whitespace-nowrap">{{ $e->transaction_date->format('d/m/Y') }}</td>
                                    <td class="px-5 py-3.5 font-mono text-[#015471] font-bold">{{ $e->expense_number }}</td>
                                    <td class="px-5 py-3.5">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#e6f0f3] text-[#015471] uppercase">
                                            {{ str_replace('_', ' ', $e->category) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="font-bold text-black block">{{ $e->title }}</span>
                                        <span class="text-[10px] text-slate-500">{{ $e->recipient_vendor }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-600 font-mono text-[11px]">{{ $e->memo_number ?: '-' }}</td>
                                    <td class="px-5 py-3.5 text-right font-bold text-[#FF7A3D] whitespace-nowrap">
                                        Rp {{ number_format($e->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <form id="delete-exp-{{ $e->id }}" action="{{ route('financial.expense.destroy', $e) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete('delete-exp-{{ $e->id }}', 'transaksi pengeluaran ini')" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-slate-100 transition" title="Hapus">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-8 text-slate-400">Belum ada pengeluaran dicatat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($expenses->hasPages())
                    <div class="px-5 py-3 border-t border-slate-200 bg-white">
                        {{ $expenses->links() }}
                    </div>
                @endif
            </div>
        </div>

    @elseif($tab === 'other_incomes')
        <!-- TAB 4: PENDAPATAN LAIN-LAIN (DISKON DEALER) -->
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border-2 border-slate-100 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Pendapatan Lain-lain (Diskon Dealer Pengadaan & Lainnya)</h3>
                    <p class="text-xs text-slate-500">Total Tercatat: <strong class="text-emerald-700 font-mono">Rp {{ number_format($totalOtherIncome, 0, ',', '.') }}</strong></p>
                </div>
                <button type="button" onclick="document.getElementById('otherIncomeModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold">+ Catat Pendapatan Lain2</button>
            </div>
            <div class="bg-white rounded-2xl border-2 border-slate-100 overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase text-[10px] font-extrabold">
                        <tr>
                            <th class="px-5 py-3">No. Bukti</th>
                            <th class="px-5 py-3">Tanggal</th>
                            <th class="px-5 py-3">Sumber / Dealer</th>
                            <th class="px-5 py-3">Unit Terkait</th>
                            <th class="px-5 py-3">Keterangan</th>
                            <th class="px-5 py-3 text-right">Nominal (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($otherIncomes as $oi)
                            <tr>
                                <td class="px-5 py-3 font-mono font-bold text-[#015471]">{{ $oi->invoice_number }}</td>
                                <td class="px-5 py-3 font-mono">{{ $oi->invoice_date?->format('d/m/Y') }}</td>
                                <td class="px-5 py-3 font-bold">{{ $oi->source_name ?: $oi->client_name }}</td>
                                <td class="px-5 py-3">{{ $oi->vehicle?->plate_number ?: '-' }}</td>
                                <td class="px-5 py-3 text-slate-600">{{ $oi->notes }}</td>
                                <td class="px-5 py-3 text-right font-mono font-bold text-emerald-700">Rp {{ number_format($oi->gross_amount, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-8 text-center text-slate-400">Belum ada pendapatan lain-lain / diskon dealer tercatat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($tab === 'depreciation')
        <!-- TAB 5: PENYUSUTAN & NILAI BUKU ASET -->
        <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border-2 border-slate-100">
                    <span class="text-xs font-bold text-slate-500">Total Nilai Perolehan Aset</span>
                    <span class="text-xl font-black font-mono text-slate-900 block mt-1">Rp {{ number_format($totalAcquisition, 0, ',', '.') }}</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border-2 border-slate-100">
                    <span class="text-xs font-bold text-rose-600">Total Akumulasi Penyusutan</span>
                    <span class="text-xl font-black font-mono text-rose-600 block mt-1">Rp {{ number_format($totalAccumDepreciation, 0, ',', '.') }}</span>
                </div>
                <div class="bg-white p-5 rounded-2xl border-2 border-slate-100">
                    <span class="text-xs font-bold text-[#015471]">Total Sisa Nilai Buku</span>
                    <span class="text-xl font-black font-mono text-[#015471] block mt-1">Rp {{ number_format($totalBookValue, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="bg-white rounded-2xl border-2 border-slate-100 overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase text-[10px] font-extrabold">
                        <tr>
                            <th class="px-4 py-3">Unit / Kode Aset</th>
                            <th class="px-4 py-3">Jenis</th>
                            <th class="px-4 py-3">Tgl Perolehan</th>
                            <th class="px-4 py-3 text-right">Nilai Faktur</th>
                            <th class="px-4 py-3 text-right">Nilai Perolehan</th>
                            <th class="px-4 py-3 text-right">Penyusutan/Bln</th>
                            <th class="px-4 py-3 text-right">Akum. Penyusutan</th>
                            <th class="px-4 py-3 text-right">Sisa Nilai Buku</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($depreciationAssets as $da)
                            <tr>
                                <td class="px-4 py-3 font-bold">{{ $da->plate_number }} <span class="text-slate-400 font-normal">({{ $da->model_name }})</span></td>
                                <td class="px-4 py-3">{{ $da->asset_type_label }}</td>
                                <td class="px-4 py-3 font-mono">{{ $da->acquisition_date?->format('d/m/Y') ?: '-' }}</td>
                                <td class="px-4 py-3 text-right font-mono">Rp {{ number_format($da->invoice_value, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-mono">Rp {{ number_format($da->acquisition_value, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-mono text-amber-700">Rp {{ number_format($da->monthly_depreciation, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-mono text-rose-600">Rp {{ number_format($da->accumulated_depreciation, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-mono font-black text-[#015471]">Rp {{ number_format($da->book_value, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">Belum ada data aset.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($tab === 'tax')
        <!-- TAB 6: FAKTUR PAJAK & EKSPOR CORETAX -->
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border-2 border-slate-100 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Faktur Pajak & Ekspor Coretax DJP</h3>
                    <p class="text-xs text-slate-500">Buat Faktur Pajak (NSFP, Kode Transaksi, NPWP/NITKU) dan unduh file XML siap impor ke Coretax.</p>
                </div>
                <button type="button" onclick="document.getElementById('taxModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-[#FF7A3D] text-white text-xs font-bold">+ Create Faktur Pajak</button>
            </div>
            <div class="bg-white rounded-2xl border-2 border-slate-100 overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase text-[10px] font-extrabold">
                        <tr>
                            <th class="px-4 py-3">No. Faktur Pajak</th>
                            <th class="px-4 py-3">Tgl / Masa</th>
                            <th class="px-4 py-3">Pembeli / NPWP</th>
                            <th class="px-4 py-3 text-right">DPP (Rp)</th>
                            <th class="px-4 py-3 text-right">PPN 11% (Rp)</th>
                            <th class="px-4 py-3">Status Coretax</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($taxInvoices as $ti)
                            <tr>
                                <td class="px-4 py-3 font-mono font-bold text-[#015471]">{{ $ti->faktur_number }}</td>
                                <td class="px-4 py-3 font-mono">{{ $ti->faktur_date->format('d/m/Y') }} ({{ $ti->tax_period }})</td>
                                <td class="px-4 py-3">
                                    <div class="font-bold">{{ $ti->buyer_name }}</div>
                                    <div class="text-[10px] font-mono text-slate-500">NPWP: {{ $ti->buyer_npwp ?: '-' }}</div>
                                </td>
                                <td class="px-4 py-3 text-right font-mono">Rp {{ number_format($ti->dpp_amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-[#FF7A3D]">Rp {{ number_format($ti->ppn_amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3"><span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100">{{ strtoupper($ti->coretax_status) }}</span></td>
                                <td class="px-4 py-3 text-right space-x-1 whitespace-nowrap">
                                    <a href="{{ route('financial.tax.xml', $ti) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#015471] text-white text-[11px] font-bold">XML Coretax</a>
                                    <form action="{{ route('financial.tax.destroy', $ti) }}" method="POST" class="inline" onsubmit="return confirm('Hapus faktur pajak?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-slate-400 hover:text-rose-600"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">Belum ada faktur pajak dibuat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

<!-- Modal Catat Pendapatan Lain-lain -->
<div id="otherIncomeModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4 text-xs">
        <div class="flex justify-between items-center border-b pb-2">
            <h3 class="font-bold text-sm">Catat Pendapatan Lain-lain / Diskon Dealer</h3>
            <button type="button" onclick="document.getElementById('otherIncomeModal').classList.add('hidden')"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
        <form action="{{ route('financial.income.store') }}" method="POST" class="space-y-3">
            @csrf
            <input type="hidden" name="income_type" value="lain_lain">
            <input type="hidden" name="status" value="lunas">
            <div>
                <label class="block font-bold mb-1">Nomor Bukti / Referensi *</label>
                <input type="text" name="invoice_number" value="LAIN/{{ date('Ym') }}/{{ rand(100,999) }}" required class="w-full px-3 py-2 rounded-xl border font-mono">
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block font-bold mb-1">Periode *</label>
                    <input type="text" name="period_month_year" value="{{ date('Y-m') }}" required class="w-full px-3 py-2 rounded-xl border">
                </div>
                <div>
                    <label class="block font-bold mb-1">Tanggal *</label>
                    <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl border">
                </div>
            </div>
            <div>
                <label class="block font-bold mb-1">Sumber / Nama Dealer *</label>
                <input type="text" name="client_name" required placeholder="PT Dealer Utama / Cashback Pengadaan" class="w-full px-3 py-2 rounded-xl border">
            </div>
            <div>
                <label class="block font-bold mb-1">Nominal Pendapatan (Rp) *</label>
                <input type="number" name="custom_gross_amount" required min="1" class="w-full px-3 py-2 rounded-xl border font-mono">
            </div>
            <div>
                <label class="block font-bold mb-1">Keterangan</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 rounded-xl border" placeholder="Diskon pembelian unit..."></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('otherIncomeModal').classList.add('hidden')" class="px-3 py-2 rounded-xl border">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Create Faktur Pajak Coretax -->
<div id="taxModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4 text-xs">
        <div class="flex justify-between items-center border-b pb-2">
            <h3 class="font-bold text-sm">Create Faktur Pajak (Coretax DJP)</h3>
            <button type="button" onclick="document.getElementById('taxModal').classList.add('hidden')"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
        <form action="{{ route('financial.tax.store') }}" method="POST" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold mb-1">Nomor Seri Faktur Pajak *</label>
                    <input type="text" name="faktur_number" value="040.001-{{ date('y') }}.{{ rand(10000000,99999999) }}" required class="w-full px-3 py-2 rounded-xl border font-mono">
                </div>
                <div>
                    <label class="block font-bold mb-1">Kode Transaksi</label>
                    <select name="transaction_code" class="w-full px-3 py-2 rounded-xl border bg-white">
                        <option value="04">04 - DPP Nilai Lain</option>
                        <option value="01">01 - Penyerahan Kena PPN</option>
                        <option value="02">02 - Pemungut Bendahara</option>
                        <option value="03">03 - Pemungut BUMN</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold mb-1">Tanggal Faktur *</label>
                    <input type="date" name="faktur_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl border">
                </div>
                <div>
                    <label class="block font-bold mb-1">Masa Pajak (MM-YYYY) *</label>
                    <input type="text" name="tax_period" value="{{ date('m-Y') }}" required class="w-full px-3 py-2 rounded-xl border font-mono">
                </div>
            </div>
            <div>
                <label class="block font-bold mb-1">Referensi Invoice Sewa (Opsional)</label>
                <select name="income_id" class="w-full px-3 py-2 rounded-xl border bg-white">
                    <option value="">-- Input Manual --</option>
                    @foreach($allIncomesForTax as $inc)
                        <option value="{{ $inc->id }}">{{ $inc->invoice_number }} — {{ $inc->client_name }} (Rp {{ number_format($inc->gross_amount, 0, ',', '.') }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold mb-1">Nama Lawan Transaksi *</label>
                    <input type="text" name="buyer_name" required placeholder="PT Perkebunan Nusantara..." class="w-full px-3 py-2 rounded-xl border">
                </div>
                <div>
                    <label class="block font-bold mb-1">NPWP / NIK Pembeli</label>
                    <input type="text" name="buyer_npwp" placeholder="16 digit NPWP" class="w-full px-3 py-2 rounded-xl border font-mono">
                </div>
            </div>
            <div>
                <label class="block font-bold mb-1">Nominal DPP (Rp) *</label>
                <input type="number" name="dpp_amount" required min="0" class="w-full px-3 py-2 rounded-xl border font-mono">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('taxModal').classList.add('hidden')" class="px-3 py-2 rounded-xl border">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-[#FF7A3D] text-white font-bold">Simpan Faktur Pajak</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Invoice Tagihan -->
<div id="addIncomeModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border-2 border-[#015471] space-y-4 text-black">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-black text-sm">Buat Invoice Penagihan Sewa Bulanan</h3>
            <button onclick="document.getElementById('addIncomeModal').classList.add('hidden')" class="text-slate-400 hover:text-black">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('financial.income.store') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-black mb-1">Nomor Kontrak</label>
                    <select name="vendor_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-black">
                        @foreach($vendors as $v)
                            <option value="{{ $v->id }}">{{ $v->contract_number ?: $v->code }} — {{ $v->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-black mb-1">Siklus Termin</label>
                    <select name="billing_cycle" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-black">
                        <option value="bulanan">Bulanan (1 Bln)</option>
                        <option value="triwulanan">3 Bulanan (Triwulan)</option>
                        <option value="semesteran">6 Bulanan (Semester)</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block font-bold text-black mb-1">Nomor Invoice <span class="text-[#FF7A3D]">*</span></label>
                <input type="text" name="invoice_number" value="INV/RENT/{{ date('Ym') }}/0{{ rand(10, 99) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono focus:outline-[#015471] text-black">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-black mb-1">Periode Bulan/Tahun <span class="text-[#FF7A3D]">*</span></label>
                    <input type="text" name="period_month_year" value="{{ date('F Y') }}" placeholder="Contoh: Agustus 2026" required class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
                </div>
                <div>
                    <label class="block font-bold text-black mb-1">Tanggal Invoice <span class="text-[#FF7A3D]">*</span></label>
                    <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
                </div>
            </div>

            <div>
                <label class="block font-bold text-black mb-1">Nama Klien / Instansi <span class="text-[#FF7A3D]">*</span></label>
                <input type="text" name="client_name" value="{{ $currentVendor ? ($currentVendor->client_name ?? 'Mitra Penyewa') : 'Mitra Penyewa' }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-black mb-1">Jumlah Unit Tipe 1 (e.g. CRF 150)</label>
                    <input type="number" name="crf_count" value="70" required class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
                </div>
                <div>
                    <label class="block font-bold text-black mb-1">Jumlah Unit Tipe 2 (e.g. Supra X)</label>
                    <input type="number" name="supra_count" value="6" required class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
                </div>
            </div>

            <div>
                <label class="block font-bold text-black mb-1">Nomor BAST (Berita Acara Serah Terima)</label>
                <input type="text" name="bast_number" placeholder="BAST/..." class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono focus:outline-[#015471] text-black">
            </div>

            <div>
                <label class="block font-bold text-black mb-1">Status Penagihan</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
                    <option value="tertagih">Tertagih (Belum Dibayar)</option>
                    <option value="lunas">Lunas (Telah Ditransfer)</option>
                    <option value="draft">Draft</option>
                </select>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addIncomeModal').classList.add('hidden')" class="px-3.5 py-2 rounded-xl border-2 border-slate-200 text-black font-bold">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white font-bold">Simpan Invoice</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Biaya Kas Pengeluaran -->
<div id="addExpenseModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border-2 border-[#015471] space-y-4 text-black">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-black text-sm">Catat Transaksi Pengeluaran Kas</h3>
            <button onclick="document.getElementById('addExpenseModal').classList.add('hidden')" class="text-slate-400 hover:text-black">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('financial.expense.store') }}" method="POST" class="space-y-3 text-xs">
            @csrf

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-black mb-1">Tanggal Transaksi <span class="text-[#FF7A3D]">*</span></label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
                </div>
                <div>
                    <label class="block font-bold text-black mb-1">Kategori Biaya <span class="text-[#FF7A3D]">*</span></label>
                    <select name="category" required class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
                        <option value="service_berkala">Service Berkala & Pelumas</option>
                        <option value="ban">Penggantian Ban</option>
                        <option value="accu">Penggantian Accu</option>
                        <option value="sparepart">Sparepart & Fast Moving</option>
                        <option value="motor_pengganti">Sewa Motor Pengganti</option>
                        <option value="pajak_stnk">Pajak STNK & Plat</option>
                        <option value="asuransi">Asuransi Kendaraan</option>
                        <option value="legalitas_kepolisian">Legalitas Kepolisian / Blokir Polda</option>
                        <option value="overhead_admin">Transportasi, Administrasi & Operasional</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-black mb-1">Pilih Kendaraan (Opsional bila terkait unit)</label>
                <select name="vehicle_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
                    <option value="">-- Biaya Umum / Non-Spesifik --</option>
                    @foreach($vehicles as $v)
                        <option value="{{ $v->id }}">{{ $v->plate_number }} ({{ $v->model_name }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-black mb-1">Judul / Uraian Biaya <span class="text-[#FF7A3D]">*</span></label>
                <input type="text" name="title" placeholder="Contoh: Pembayaran Sewa Motor Pengganti Bulan Agustus" required class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-black mb-1">Nominal Biaya (Rp) <span class="text-[#FF7A3D]">*</span></label>
                    <input type="number" name="amount" placeholder="Contoh: 650000" required class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
                </div>
                <div>
                    <label class="block font-bold text-black mb-1">Penerima / Vendor / Bengkel</label>
                    <input type="text" name="recipient_vendor" placeholder="Nama rekanan / AHASS" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-black mb-1">Nomor Memo</label>
                    <input type="text" name="memo_number" placeholder="KOPSWA/LPG/..." class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono focus:outline-[#015471] text-black">
                </div>
                <div>
                    <label class="block font-bold text-black mb-1">Journal Entry (Buku Besar)</label>
                    <input type="text" name="journal_entry_number" placeholder="Journal Entry 10..." class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono focus:outline-[#015471] text-black">
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addExpenseModal').classList.add('hidden')" class="px-3.5 py-2 rounded-xl border-2 border-slate-200 text-black font-bold">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-[#015471] hover:bg-[#013e54] text-white font-bold">Simpan Pengeluaran</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Input Plafon Anggaran Baru -->
<div id="addBudgetModal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 border border-slate-200 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-[#015471]/10 text-[#015471] flex items-center justify-center">
                    <i data-lucide="piggy-bank" class="w-4 h-4 text-[#FF7A3D]"></i>
                </div>
                <h3 class="text-base font-extrabold text-black">Input Plafon Pos Anggaran Baru</h3>
            </div>
            <button onclick="document.getElementById('addBudgetModal').classList.add('hidden')" class="text-slate-400 hover:text-black">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('financial.budget.store') }}" method="POST" class="space-y-3.5 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-black mb-1">Kategori Pos Anggaran <span class="text-[#FF7A3D]">*</span></label>
                <select name="category" required class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
                    <option value="service_berkala">Service Berkala & Pelumas</option>
                    <option value="ban">Penggantian Ban</option>
                    <option value="accu">Penggantian Accu</option>
                    <option value="sparepart">Sparepart & Fast Moving</option>
                    <option value="motor_pengganti">Biaya Motor Pengganti</option>
                    <option value="pajak_stnk">Pajak Kendaraan (STNK)</option>
                    <option value="asuransi">Asuransi Kendaraan</option>
                    <option value="legalitas_kepolisian">Legalitas & Kepolisian</option>
                    <option value="overhead_admin">Transportasi, Administrasi & Operasional</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-black mb-1">Nama / Label Pos Anggaran <span class="text-[#FF7A3D]">*</span></label>
                <input type="text" name="label" placeholder="Contoh: Service Berkala & Pelumas - CRF 150" required class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-black mb-1">Target Kendaraan</label>
                    <input type="text" name="vehicle_group" placeholder="honda_crf_150 / Semua Unit" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
                </div>
                <div>
                    <label class="block font-bold text-black mb-1">Jumlah Unit <span class="text-[#FF7A3D]">*</span></label>
                    <input type="number" name="qty_units" id="budgetQty" value="70" required class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-black mb-1">Tarif / Unit / Bulan (Rp) <span class="text-[#FF7A3D]">*</span></label>
                    <input type="number" name="unit_rate_monthly" id="budgetRateMonthly" placeholder="83333" required oninput="calcBudgetTotal()" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
                </div>
                <div>
                    <label class="block font-bold text-black mb-1">Tarif / Unit / Tahun (Rp)</label>
                    <input type="number" name="unit_rate_yearly" id="budgetRateYearly" placeholder="1000000" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
                </div>
            </div>

            <div>
                <label class="block font-bold text-black mb-1">Total Plafon Anggaran 36 Bulan (Rp) <span class="text-[#FF7A3D]">*</span></label>
                <input type="number" name="total_budget_36m" id="budgetTotal36m" placeholder="Contoh: 210000000" required class="w-full px-3 py-2 rounded-xl border border-slate-300 font-bold focus:outline-[#015471] text-[#015471]">
                <p class="text-[10px] text-slate-500 mt-1">Dihitung otomatis: Qty Unit x Tarif Bulanan x 36 Bulan (atau dapat diedit manual).</p>
            </div>

            <div>
                <label class="block font-bold text-black mb-1">Catatan / Dasar Memo Anggaran</label>
                <textarea name="notes" rows="2" placeholder="Dasar perhitungan acuan dokumen tender memo 2026..." class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addBudgetModal').classList.add('hidden')" class="px-3.5 py-2 rounded-xl border-2 border-slate-200 text-black font-bold">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-[#015471] hover:bg-[#013e54] text-white font-bold">Simpan Plafon</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddBudgetModal() {
        document.getElementById('addBudgetModal').classList.remove('hidden');
    }

    function calcBudgetTotal() {
        const qty = parseFloat(document.getElementById('budgetQty').value) || 0;
        const monthly = parseFloat(document.getElementById('budgetRateMonthly').value) || 0;
        if (monthly > 0) {
            document.getElementById('budgetRateYearly').value = Math.round(monthly * 12);
            document.getElementById('budgetTotal36m').value = Math.round(qty * monthly * 36);
        }
    }

    @if(session('auto_download_invoice_id'))
        document.addEventListener('DOMContentLoaded', function() {
            // Trigger automatic PDF download immediately after redirect
            const downloadUrl = "{{ route('exports.singleInvoicePdf', ['income' => session('auto_download_invoice_id'), 'action' => 'download']) }}";
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = downloadUrl;
            document.body.appendChild(iframe);
        });
    @endif
</script>
<!-- Modal Catat Pembayaran Invoice (Sebagian / Lunas) -->
<div id="payIncomeModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Catat Pembayaran Tagihan</h3>
                <p class="text-xs text-slate-500 mt-0.5">Invoice: <span id="payModalInvNum" class="font-mono font-bold text-[#015471]"></span></p>
            </div>
            <button type="button" onclick="document.getElementById('payIncomeModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="payIncomeForm" method="POST" class="space-y-4 text-xs">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="tertagih">
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex justify-between items-center">
                <span class="text-slate-600 font-semibold">Total Tagihan (Inc. PPN):</span>
                <span id="payModalTotalLabel" class="font-mono font-black text-slate-900"></span>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Total Nominal Sudah Dibayar (Rp) *</label>
                <input type="number" step="0.01" min="0" name="paid_amount" id="payModalAmount" required class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono font-bold text-slate-900">
                <p class="text-[11px] text-slate-400 mt-1">Jika nominal ≥ total tagihan, status otomatis menjadi <strong>Lunas</strong>.</p>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Tanggal Pembayaran</label>
                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-900">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('payIncomeModal').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 font-semibold text-slate-600">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white font-bold">Simpan Pembayaran</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPayIncomeModal(id, invNum, totalAmt, currentPaid) {
        document.getElementById('payIncomeForm').action = `/financial/income/${id}/status`;
        document.getElementById('payModalInvNum').textContent = invNum;
        document.getElementById('payModalTotalLabel').textContent = 'Rp ' + Math.round(totalAmt).toLocaleString('id-ID');
        document.getElementById('payModalAmount').value = currentPaid > 0 ? currentPaid : Math.round(totalAmt);
        document.getElementById('payIncomeModal').classList.remove('hidden');
    }
</script>
@endsection
