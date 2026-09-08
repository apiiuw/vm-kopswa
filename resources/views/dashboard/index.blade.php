@extends('layouts.app')

@section('title', 'Dashboard Monitoring Armada & Anggaran')
@section('page_title', 'Dashboard Eksekutif Monitoring Proyek')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto bg-white text-black">

    <!-- Top Notice / Project Status Banner -->
    <div class="bg-gradient-to-r from-[#015471] via-[#01435b] to-[#002f40] rounded-3xl p-6 sm:p-7 text-white shadow-lg relative overflow-hidden border border-[#015471]">
        <div class="absolute -right-8 -bottom-8 w-60 h-60 bg-[#FF7A3D]/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#FF7A3D]/20 text-[#FF7A3D] text-xs font-bold border border-[#FF7A3D]/30 mb-2">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> 
                    @if($currentVendor)
                        Klien Sewa: {{ $currentVendor->name }} ({{ $currentVendor->code }})
                    @else
                        Platform Monitoring Armada Sewa PT SUP - Multi Klien
                    @endif
                </div>
                <h2 class="text-2xl font-black tracking-tight text-white">
                    @if($currentVendor)
                        {{ $currentVendor->project_name ?? 'Monitoring Operasional Armada Kendaraan' }}
                    @else
                        Monitoring Keseluruhan Armada Sewa PT SUP
                    @endif
                </h2>
                <p class="text-slate-200 text-xs sm:text-sm mt-1 max-w-2xl leading-relaxed">
                    @if($currentVendor)
                        Klien Mitra: <strong class="text-white">{{ $currentVendor->name }}</strong>
                        @if($currentVendor->contract_number)
                            | No. Kontrak: <strong class="text-[#FF7A3D]">{{ $currentVendor->contract_number }}</strong>
                        @endif
                        @if($currentVendor->contract_start_date && $currentVendor->contract_end_date)
                            | Periode: {{ $currentVendor->contract_start_date->format('d M Y') }} s.d. {{ $currentVendor->contract_end_date->format('d M Y') }}
                        @endif
                    @else
                        Mengawasi seluruh sepeda motor sewaan PT SUP, pemeliharaan berkala, dokumen STNK/pajak, klaim asuransi, dan realisasi anggaran operasional pada setiap klien mitra.
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('exports.budgetPdf') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white font-bold text-xs transition shadow-sm">
                    <i data-lucide="printer" class="w-4 h-4"></i> Cetak Rekap
                </a>
                <a href="{{ route('vehicles.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-[#015471] hover:bg-slate-100 font-bold text-xs transition shadow-sm">
                    <i data-lucide="plus" class="w-4 h-4 text-[#FF7A3D]"></i> Tambah Unit
                </a>
            </div>
        </div>
    </div>

    <!-- 4 Key Metric Cards (Vehicles & Finances) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Total Armada -->
        <div class="bg-white rounded-2xl p-5 border-2 border-slate-100 shadow-sm hover:border-[#015471]/40 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#015471] uppercase tracking-wider">Total Armada Motor</span>
                <div class="w-9 h-9 rounded-xl bg-[#e6f0f3] text-[#015471] flex items-center justify-center">
                    <i data-lucide="bike" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-black">{{ $totalVehicles }}</span>
                <span class="text-xs font-semibold text-slate-500">Unit</span>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-black">
                <span class="font-medium">{{ $crfCount }} Honda CRF 150</span>
                <span class="font-bold text-[#015471]">{{ $supraCount }} Supra X 125</span>
            </div>
        </div>

        <!-- Card 2: Status Operasional -->
        <div class="bg-white rounded-2xl p-5 border-2 border-slate-100 shadow-sm hover:border-[#015471]/40 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#015471] uppercase tracking-wider">Kondisi Operasional</span>
                <div class="w-9 h-9 rounded-xl bg-[#FF7A3D]/10 text-[#FF7A3D] flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-[#FF7A3D]">{{ $activeCount }}</span>
                <span class="text-xs font-semibold text-slate-500">Unit Normal</span>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-amber-700 font-bold">{{ $maintenanceCount }} Servis</span>
                <span class="text-[#015471] font-bold">{{ $replacedCount }} Pengganti</span>
                <span class="text-rose-700 font-bold">{{ $lostCount }} Hilang</span>
            </div>
        </div>

        <!-- Card 3: Realisasi Biaya vs Plafon -->
        <div class="bg-white rounded-2xl p-5 border-2 border-slate-100 shadow-sm hover:border-[#015471]/40 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#015471] uppercase tracking-wider">Plafon Anggaran Operasional</span>
                <div class="w-9 h-9 rounded-xl bg-[#e6f0f3] text-[#015471] flex items-center justify-center">
                    <i data-lucide="piggy-bank" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-1">
                <span class="text-2xl font-extrabold text-black">Rp {{ number_format($totalBudgetOperasional, 0, ',', '.') }}</span>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-600">Terpakai: <strong class="text-rose-600">Rp {{ number_format($totalExpense, 0, ',', '.') }}</strong></span>
                <span class="text-[#015471] font-bold">Sisa: Rp {{ number_format($sisaBudgetOperasional, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Card 4: Total Pendapatan Sewa -->
        <div class="bg-white rounded-2xl p-5 border-2 border-slate-100 shadow-sm hover:border-[#015471]/40 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-[#015471] uppercase tracking-wider">Pendapatan Sewa Tertagih</span>
                <div class="w-9 h-9 rounded-xl bg-[#FF7A3D]/10 text-[#FF7A3D] flex items-center justify-center">
                    <i data-lucide="banknote" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-1">
                <span class="text-2xl font-extrabold text-[#015471]">Rp {{ number_format($totalIncomeGross, 0, ',', '.') }}</span>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                <span>Lunas: <strong class="text-[#015471]">Rp {{ number_format($totalIncomePaid, 0, ',', '.') }}</strong></span>
                <span>Tagihan: <strong class="text-[#FF7A3D]">Rp {{ number_format($totalIncomePending, 0, ',', '.') }}</strong></span>
            </div>
        </div>
    </div>

    <!-- Section 1: Visual Multi-Chart Hub (Cashflow + Budget Plafon) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Cashflow Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 border-2 border-slate-100 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                <div>
                    <h3 class="font-bold text-black text-base">Tren Arus Kas Proyek (Pendapatan vs Pengeluaran)</h3>
                    <p class="text-xs text-slate-600">Penerimaan sewa bulanan vs pengeluaran operasional perawatan armada</p>
                </div>
                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-[#e6f0f3] text-[#015471] w-max">Buku Kas Aktual</span>
            </div>
            <div class="h-72">
                <canvas id="cashflowChart"></canvas>
            </div>
        </div>

        <!-- Category Expenses vs Pagu (Breakdown Dokumen Memo) -->
        <div class="bg-white rounded-2xl p-6 border-2 border-slate-100 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-black text-base">Realisasi Pos Anggaran</h3>
                    <a href="{{ route('financial.index') }}" class="text-xs text-[#FF7A3D] font-bold hover:underline">Detail Plafon &rarr;</a>
                </div>

                <div class="space-y-3.5 text-xs">
                    <!-- Servis -->
                    <div>
                        <div class="flex justify-between font-medium text-black mb-1">
                            <span>Service Berkala & Pelumas</span>
                            <span class="font-bold text-[#015471]">Rp {{ number_format($expensesByCategory['service_berkala'], 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-[#015471] h-2 rounded-full" style="width: {{ $budgetByCategory['service_berkala'] > 0 ? min(100, ($expensesByCategory['service_berkala'] / $budgetByCategory['service_berkala']) * 100) : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Ban -->
                    <div>
                        <div class="flex justify-between font-medium text-black mb-1">
                            <span>Penggantian Ban Luar/Dalam</span>
                            <span class="font-bold text-[#FF7A3D]">Rp {{ number_format($expensesByCategory['ban'], 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-[#FF7A3D] h-2 rounded-full" style="width: {{ $budgetByCategory['ban'] > 0 ? min(100, ($expensesByCategory['ban'] / $budgetByCategory['ban']) * 100) : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Accu -->
                    <div>
                        <div class="flex justify-between font-medium text-black mb-1">
                            <span>Penggantian Accu / Aki</span>
                            <span class="font-bold text-[#015471]">Rp {{ number_format($expensesByCategory['accu'], 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-[#015471] h-2 rounded-full" style="width: {{ $budgetByCategory['accu'] > 0 ? min(100, ($expensesByCategory['accu'] / $budgetByCategory['accu']) * 100) : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Sparepart -->
                    <div>
                        <div class="flex justify-between font-medium text-black mb-1">
                            <span>Suku Cadang / Fast Moving</span>
                            <span class="font-bold text-black">Rp {{ number_format($expensesByCategory['sparepart'], 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-[#FF7A3D] h-2 rounded-full" style="width: {{ $budgetByCategory['sparepart'] > 0 ? min(100, ($expensesByCategory['sparepart'] / $budgetByCategory['sparepart']) * 100) : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Motor Pengganti -->
                    <div>
                        <div class="flex justify-between font-medium text-black mb-1">
                            <span>Biaya Sewa Unit Pengganti</span>
                            <span class="font-bold text-[#FF7A3D]">Rp {{ number_format($expensesByCategory['motor_pengganti'], 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-[#015471] h-2 rounded-full" style="width: {{ $budgetByCategory['motor_pengganti'] > 0 ? min(100, ($expensesByCategory['motor_pengganti'] / $budgetByCategory['motor_pengganti']) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 p-3 bg-[#e6f0f3] rounded-xl border border-[#015471]/30 text-[11px] text-[#015471]">
                <i data-lucide="info" class="w-3.5 h-3.5 inline mr-1 text-[#FF7A3D]"></i>
                @if($currentVendor)
                    Pagu tersisa untuk klien <strong>{{ $currentVendor->code }}</strong> adalah <strong>Rp {{ number_format($sisaBudgetOperasional, 0, ',', '.') }}</strong>.
                @else
                    Total pagu operasional gabungan tersisa <strong>Rp {{ number_format($sisaBudgetOperasional, 0, ',', '.') }}</strong>.
                @endif
            </div>
        </div>
    </div>

    <!-- Section 2: Visual Distribution Analytics (Status Donut + Biaya Radar/Polar + Lokasi Horizontal Bar) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Chart A: Komposisi Status Armada (Doughnut) -->
        <div class="bg-white rounded-2xl p-6 border-2 border-slate-100 shadow-sm flex flex-col justify-between">
            <div class="mb-3">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-[#015471]/10 text-[#015471] flex items-center justify-center">
                        <i data-lucide="pie-chart" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-bold text-black text-sm">Status Armada Unit</h3>
                </div>
                <p class="text-[11px] text-slate-500 mt-0.5">Persentase kesiapan unit di lapangan</p>
            </div>
            <div class="h-56 relative">
                <canvas id="statusDonutChart"></canvas>
            </div>
            <div class="grid grid-cols-2 gap-2 text-[11px] pt-3 border-t border-slate-100 font-semibold">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#10b981]"></span> Aktif: {{ $activeCount }}</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#f59e0b]"></span> Servis: {{ $maintenanceCount }}</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#8b5cf6]"></span> Pengganti: {{ $replacedCount }}</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#ef4444]"></span> Hilang: {{ $lostCount }}</span>
            </div>
        </div>

        <!-- Chart B: Breakdown Biaya Pemeliharaan (Polar/Pie) -->
        <div class="bg-white rounded-2xl p-6 border-2 border-slate-100 shadow-sm flex flex-col justify-between">
            <div class="mb-3">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-[#FF7A3D]/10 text-[#FF7A3D] flex items-center justify-center">
                        <i data-lucide="sparkles" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-bold text-black text-sm">Komposisi Pengeluaran Operasional</h3>
                </div>
                <p class="text-[11px] text-slate-500 mt-0.5">Distribusi biaya servis, ban, aki, dan cadangan</p>
            </div>
            <div class="h-56 relative">
                <canvas id="expenseCategoryChart"></canvas>
            </div>
            <div class="text-center pt-3 border-t border-slate-100 text-[11px] text-slate-600 font-medium">
                Total Realisasi Biaya: <strong class="text-slate-900">Rp {{ number_format($totalExpense, 0, ',', '.') }}</strong>
            </div>
        </div>

        <!-- Chart C: Distribusi Unit per Lokasi Kebun / Pos (Horizontal Bar) -->
        <div class="bg-white rounded-2xl p-6 border-2 border-slate-100 shadow-sm flex flex-col justify-between">
            <div class="mb-3">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-[#015471]/10 text-[#015471] flex items-center justify-center">
                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                    </div>
                    <h3 class="font-bold text-black text-sm">Sebaran Unit per Lokasi</h3>
                </div>
                <p class="text-[11px] text-slate-500 mt-0.5">Jumlah sebaran motor pada setiap afdeling/pos</p>
            </div>
            <div class="h-56 relative">
                <canvas id="locationBarChart"></canvas>
            </div>
            <div class="text-center pt-3 border-t border-slate-100 text-[11px] text-slate-600 font-medium">
                Total Lokasi Aktif: <strong class="text-[#015471]">{{ count($locationLabels) }} Lokasi</strong>
            </div>
        </div>

    </div>

    <!-- Section 3: Permohonan & Tiket Masuk dari Klien Mitra (Realtime Client Requests) -->
    <div class="bg-white rounded-2xl p-6 border-2 border-slate-100 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#FF7A3D]/10 text-[#FF7A3D] flex items-center justify-center">
                    <i data-lucide="inbox" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-black text-base">Permohonan & Tiket Masuk dari Klien Mitra</h3>
                        @if(!empty($pendingClientRequestsCount) && $pendingClientRequestsCount > 0)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-[#FF7A3D] text-white animate-pulse">
                                {{ $pendingClientRequestsCount }} Perlu Tindak Lanjut
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500">Permohonan servis berkala, unit pengganti, laporan kehilangan (TLO), dan input bukti selesai pengerjaan</p>
                </div>
            </div>
            <a href="{{ route('admin.client-requests.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-[#015471] hover:underline shrink-0">
                <span>Buka Seluruh Tiket</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#e6f0f3] border-b border-slate-200 text-[#015471] font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-3">Klien / Vendor</th>
                        <th class="px-5 py-3">Kendaraan</th>
                        <th class="px-5 py-3">Jenis Permohonan</th>
                        <th class="px-5 py-3">Judul Pengajuan</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Waktu</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-800">
                    @forelse($recentClientRequests as $cr)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3 font-bold text-slate-900">
                                {{ $cr->vendor ? $cr->vendor->name : 'Klien Mitra' }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="font-mono font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                                    {{ $cr->vehicle ? $cr->vehicle->plate_number : '-' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-[#015471] font-semibold">
                                {{ $cr->request_type_label }}
                            </td>
                            <td class="px-5 py-3 font-medium text-slate-900">
                                {{ $cr->title }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $cr->status_badge['bg'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $cr->status_badge['dot'] }}"></span>
                                    {{ $cr->status_badge['label'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-slate-500 font-medium">
                                {{ $cr->created_at->diffForHumans() }}
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('admin.client-requests.show', $cr) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#015471] text-white hover:bg-[#013e54] text-[11px] font-bold transition">
                                    <span>Tindak Lanjut</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-6 text-slate-400">
                                Belum ada tiket atau pengajuan permohonan baru dari klien mitra.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 4: Alert STNK Expiring & Recent Maintenances -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Alert STNK / Asuransi Expiring Soon -->
        <div class="bg-white rounded-2xl p-6 border-2 border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-[#FF7A3D]/10 text-[#FF7A3D] flex items-center justify-center">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-black text-sm">Peringatan Jatuh Tempo Dokumen & Pajak</h3>
                        <p class="text-[11px] text-slate-500">STNK, Pajak Tahunan, dan Asuransi yang mendekati batas jatuh tempo</p>
                    </div>
                </div>
                <a href="{{ route('documents.index', ['filter' => 'expiring_soon']) }}" class="text-xs text-[#FF7A3D] font-bold hover:underline">Semua</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($expiringDocs as $doc)
                    <div class="py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-[#e6f0f3] text-[#015471] flex items-center justify-center text-xs font-bold font-mono">
                                {{ substr($doc->vehicle->plate_number, 0, 7) }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-black">{{ $doc->vehicle->plate_number }} - {{ $doc->type_label }}</p>
                                <p class="text-[11px] text-slate-500">{{ $doc->vehicle->location ? $doc->vehicle->location->name : 'Unit Operasional' }} | No: {{ $doc->document_number ?: '-' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-2.5 py-1 rounded-md text-[11px] font-bold bg-[#FF7A3D]/15 text-[#FF7A3D]">
                                {{ $doc->expiry_date ? $doc->expiry_date->format('d M Y') : '-' }}
                            </span>
                            <p class="text-[10px] text-slate-400 mt-0.5">{{ $doc->expiry_date ? $doc->expiry_date->diffForHumans() : '' }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-6 text-center">Tidak ada dokumen yang mendekati jatuh tempo dalam 60 hari ke depan.</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Maintenance Activities -->
        <div class="bg-white rounded-2xl p-6 border-2 border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-[#e6f0f3] text-[#015471] flex items-center justify-center">
                        <i data-lucide="wrench" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-black text-sm">Aktivitas Pemeliharaan Terkini</h3>
                        <p class="text-[11px] text-slate-500">Catatan servis berkala, ganti ban, dan unit pengganti</p>
                    </div>
                </div>
                <a href="{{ route('maintenance.index') }}" class="text-xs text-[#015471] font-bold hover:underline">Semua</a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($recentMaintenances as $maint)
                    <div class="py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-[#015471]">
                                <i data-lucide="settings" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-black">{{ $maint->vehicle->plate_number }} - {{ $maint->type_label }}</p>
                                <p class="text-[11px] text-slate-500 truncate max-w-xs">{{ $maint->description ?: $maint->item_name }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-black">Rp {{ number_format($maint->cost, 0, ',', '.') }}</span>
                            <p class="text-[10px] text-slate-400">{{ $maint->date->format('d M Y') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-6 text-center">Belum ada aktivitas pemeliharaan dicatat.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Cashflow Bar Chart
        const ctx = document.getElementById('cashflowChart').getContext('2d');
        const labels = {!! json_encode($chartLabels) !!};
        const incomeData = {!! json_encode($chartIncomeData) !!};
        const expenseData = {!! json_encode($chartExpenseData) !!};

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Pendapatan Sewa (Rp)',
                        data: incomeData,
                        backgroundColor: '#015471',
                        borderRadius: 6,
                    },
                    {
                        label: 'Pengeluaran Operasional (Rp)',
                        data: expenseData,
                        backgroundColor: '#FF7A3D',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: '#000000',
                            font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.y);
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#000000', font: { family: 'Plus Jakarta Sans', size: 10 } }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            color: '#000000',
                            font: { family: 'Plus Jakarta Sans', size: 10 },
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000).toFixed(0) + ' Jt';
                            }
                        }
                    }
                }
            }
        });

        // 2. Status Armada Doughnut Chart
        const statusCtx = document.getElementById('statusDonutChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Aktif', 'Servis', 'Pengganti', 'Hilang'],
                datasets: [{
                    data: [{{ $activeCount }}, {{ $maintenanceCount }}, {{ $replacedCount }}, {{ $lostCount }}],
                    backgroundColor: ['#10b981', '#f59e0b', '#8b5cf6', '#ef4444'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                cutout: '68%'
            }
        });

        // 3. Expense Composition Polar / Pie Chart
        const expCtx = document.getElementById('expenseCategoryChart').getContext('2d');
        new Chart(expCtx, {
            type: 'pie',
            data: {
                labels: ['Servis Berkala', 'Ganti Ban', 'Ganti Accu', 'Sparepart', 'Motor Pengganti'],
                datasets: [{
                    data: [
                        {{ $expensesByCategory['service_berkala'] }},
                        {{ $expensesByCategory['ban'] }},
                        {{ $expensesByCategory['accu'] }},
                        {{ $expensesByCategory['sparepart'] }},
                        {{ $expensesByCategory['motor_pengganti'] }}
                    ],
                    backgroundColor: ['#015471', '#FF7A3D', '#0284c7', '#334155', '#d97706'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            font: { family: 'Plus Jakarta Sans', size: 9 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed);
                            }
                        }
                    }
                }
            }
        });

        // 4. Location Distribution Horizontal Bar Chart
        const locCtx = document.getElementById('locationBarChart').getContext('2d');
        const locLabels = {!! json_encode($locationLabels) !!};
        const locCounts = {!! json_encode($locationCounts) !!};

        new Chart(locCtx, {
            type: 'bar',
            data: {
                labels: locLabels.length > 0 ? locLabels : ['Kantor Utama'],
                datasets: [{
                    label: 'Jumlah Unit',
                    data: locCounts.length > 0 ? locCounts : [{{ $totalVehicles }}],
                    backgroundColor: '#015471',
                    borderRadius: 5
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { color: '#f1f5f9' },
                        ticks: { stepSize: 5, font: { size: 9 } }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { size: 9, weight: '600' } }
                    }
                }
            }
        });
    });
</script>
@endpush
