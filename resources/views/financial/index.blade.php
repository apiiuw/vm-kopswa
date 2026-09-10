@extends('layouts.app')

@section('title', 'Pendapatan, Pengeluaran & Anggaran Operasional')
@section('page_title', 'Keuangan Proyek & Realisasi Anggaran')

@section('content')
<div class="space-y-6 w-full bg-white text-black">

    <!-- Top Action & Tabs Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-black tracking-tight">Monitoring Anggaran & Buku Kas</h2>
            <p class="text-xs text-slate-500 mt-0.5">Penagihan sewa, pengeluaran riil, dan pemantauan pagu anggaran Rp 1.032.000.000</p>
        </div>
        <div class="flex items-center gap-2.5">
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
                <span class="text-[11px] text-[#015471] font-bold mt-1 block">Aman & Terkendali s/d Mei 2028</span>
            </div>
        @endif
    </div>

    <!-- Modern Segmented Tab Selector -->
    <div class="bg-slate-100/80 p-1.5 rounded-2xl border border-slate-200/80 shadow-2xs">
        <nav class="grid grid-cols-1 sm:grid-cols-3 gap-1.5 text-xs font-bold" aria-label="Tabs">
            <!-- Tab 1: Plafon Anggaran -->
            <a href="{{ route('financial.index', ['tab' => 'overview']) }}" 
               class="flex items-center justify-center gap-2.5 px-4 py-3 rounded-xl transition-all duration-200 {{ $tab === 'overview' ? 'bg-[#015471] text-white shadow-md shadow-[#015471]/20 font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-white/80' }}">
                <div class="w-6 h-6 rounded-lg flex items-center justify-center {{ $tab === 'overview' ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-[#015471]' }}">
                    <i data-lucide="pie-chart" class="w-3.5 h-3.5 {{ $tab === 'overview' ? 'text-[#FF7A3D]' : '' }}"></i>
                </div>
                <span>1. Plafon Anggaran vs Realisasi</span>
                <span class="hidden md:inline-flex px-1.5 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider {{ $tab === 'overview' ? 'bg-[#FF7A3D] text-white' : 'bg-slate-200 text-slate-500' }}">Memo</span>
            </a>

            <!-- Tab 2: Tagihan Sewa Bulanan (Incomes) -->
            <a href="{{ route('financial.index', ['tab' => 'incomes']) }}" 
               class="flex items-center justify-center gap-2.5 px-4 py-3 rounded-xl transition-all duration-200 {{ $tab === 'incomes' ? 'bg-[#015471] text-white shadow-md shadow-[#015471]/20 font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-white/80' }}">
                <div class="w-6 h-6 rounded-lg flex items-center justify-center {{ $tab === 'incomes' ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-emerald-700]' }}">
                    <i data-lucide="arrow-down-left" class="w-3.5 h-3.5 {{ $tab === 'incomes' ? 'text-[#FF7A3D]' : 'text-emerald-600' }}"></i>
                </div>
                <span>2. Tagihan Sewa Bulanan</span>
                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-black {{ $tab === 'incomes' ? 'bg-[#FF7A3D] text-white' : 'bg-slate-200 text-slate-700' }}">
                    {{ $incomes->total() }}
                </span>
            </a>

            <!-- Tab 3: Buku Kas Pengeluaran (Expenses) -->
            <a href="{{ route('financial.index', ['tab' => 'expenses']) }}" 
               class="flex items-center justify-center gap-2.5 px-4 py-3 rounded-xl transition-all duration-200 {{ $tab === 'expenses' ? 'bg-[#015471] text-white shadow-md shadow-[#015471]/20 font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-white/80' }}">
                <div class="w-6 h-6 rounded-lg flex items-center justify-center {{ $tab === 'expenses' ? 'bg-white/20 text-white' : 'bg-slate-200/70 text-rose-600' }}">
                    <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 {{ $tab === 'expenses' ? 'text-[#FF7A3D]' : 'text-rose-600' }}"></i>
                </div>
                <span>3. Buku Kas Pengeluaran</span>
                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-black {{ $tab === 'expenses' ? 'bg-[#FF7A3D] text-white' : 'bg-slate-200 text-slate-700' }}">
                    {{ $expenses->total() }}
                </span>
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
                <button onclick="document.getElementById('addIncomeModal').classList.remove('hidden')" class="px-3.5 py-2 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-sm transition">
                    + Buat Invoice Tagihan Baru
                </button>
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
                                    <td class="px-5 py-3.5 font-bold text-[#015471]">{{ $inc->period_month_year }}</td>
                                    <td class="px-5 py-3.5 text-slate-600">{{ $inc->invoice_date->format('d/m/Y') }}</td>
                                    <td class="px-5 py-3.5 font-medium text-right">Rp {{ number_format($inc->gross_amount, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3.5 text-slate-500 text-right">Rp {{ number_format($inc->ppn_amount, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3.5 font-bold text-black text-right">Rp {{ number_format($inc->total_amount_with_ppn, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3.5 font-bold text-amber-700 text-right">Rp {{ number_format($inc->pph23_amount, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $inc->status === 'lunas' ? 'bg-[#e6f0f3] text-[#015471]' : 'bg-[#FF7A3D]/20 text-[#FF7A3D]' }}">
                                            {{ strtoupper($inc->status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 font-black text-emerald-700 font-mono text-right bg-emerald-50/40">
                                        Rp {{ number_format($inc->net_profit, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        @if($inc->status !== 'lunas')
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
                                            <span class="text-[10px] text-slate-500 font-medium">Dibayar {{ $inc->payment_date ? $inc->payment_date->format('d/m/Y') : '-' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-8 text-slate-400">Belum ada invoice tagihan sewa.</td>
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
                <button onclick="document.getElementById('addExpenseModal').classList.remove('hidden')" class="px-3.5 py-2 text-xs font-bold rounded-xl bg-[#015471] hover:bg-[#013e54] text-white shadow-sm transition">
                    + Catat Pengeluaran Kas Baru
                </button>
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
    @endif

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
                    <input type="text" name="memo_number" placeholder="SUP/LPG/..." class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono focus:outline-[#015471] text-black">
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
</script>
@endsection
