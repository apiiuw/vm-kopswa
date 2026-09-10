@extends('layouts.app')

@section('title', 'Dokumen & Pajak Kendaraan')
@section('page_title', 'Modul Legalitas, STNK & Pajak Kendaraan')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto bg-white text-slate-900">

    <!-- Top Header & Action Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-2xl bg-[#015471] text-white flex items-center justify-center shadow-xs">
                    <i data-lucide="file-badge" class="w-5 h-5 text-[#FF7A3D]"></i>
                </span>
                Dokumen & Pajak STNK Armada
            </h2>
            <p class="text-xs text-slate-500 mt-1">Pemantauan masa berlaku STNK tahunan, plat 5 tahunan, BPKB, dan pencatatan bayar pajak langsung ke kas</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('documents.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-sm transition active:scale-95">
                <i data-lucide="plus" class="w-4 h-4"></i> Catat Dokumen Baru
            </a>
        </div>
    </div>

    <!-- Summary Metrics Cards (5 Cards Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 text-xs">
        <!-- 1. Total Dokumen / Arsip -->
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-2xs hover:border-slate-300 hover:shadow-xs transition relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-slate-500 block text-[10px] uppercase font-bold tracking-wider">Total Dokumen</span>
                <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-xs">
                    <i data-lucide="files" class="w-3.5 h-3.5"></i>
                </span>
            </div>
            <span class="text-xl font-black text-slate-900 mt-2 block font-mono">{{ $stats['total_docs'] }} <span class="text-xs font-semibold text-slate-400">Berkas</span></span>
            <span class="text-[10px] text-slate-400 mt-1 block">STNK, PKB, BPKB & Asuransi</span>
        </div>

        <!-- 2. Status Aktif & Aman -->
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-2xs hover:border-emerald-200 hover:shadow-xs transition relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-emerald-700 block text-[10px] uppercase font-bold tracking-wider">Aktif & Aman</span>
                <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-xs">
                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i>
                </span>
            </div>
            <span class="text-xl font-black text-emerald-700 mt-2 block font-mono">{{ $stats['active_docs'] }} <span class="text-xs font-semibold text-slate-400">Unit</span></span>
            <span class="text-[10px] text-slate-400 mt-1 block">Masa berlaku > 30 hari</span>
        </div>

        <!-- 3. Jatuh Tempo < 30 Hari -->
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-2xs hover:border-amber-200 hover:shadow-xs transition relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[#FF7A3D] block text-[10px] uppercase font-bold tracking-wider">Jatuh Tempo Segera</span>
                <span class="w-7 h-7 rounded-lg bg-[#FF7A3D]/10 text-[#FF7A3D] flex items-center justify-center text-xs">
                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                </span>
            </div>
            <span class="text-xl font-black text-[#FF7A3D] mt-2 block font-mono">{{ $stats['expiring_soon'] }} <span class="text-xs font-semibold text-slate-400">Unit</span></span>
            <span class="text-[10px] text-slate-400 mt-1 block">Perlu disiapkan dana/Samsat</span>
        </div>

        <!-- 4. Lewat Jatuh Tempo / Telat -->
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-2xs hover:border-rose-200 hover:shadow-xs transition relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-rose-600 block text-[10px] uppercase font-bold tracking-wider">Lewat Jatuh Tempo</span>
                <span class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-xs">
                    <i data-lucide="alert-octagon" class="w-3.5 h-3.5"></i>
                </span>
            </div>
            <span class="text-xl font-black text-rose-600 mt-2 block font-mono">{{ $stats['expired'] }} <span class="text-xs font-semibold text-slate-400">Unit</span></span>
            <span class="text-[10px] text-slate-400 mt-1 block">Masa berlaku telah habis</span>
        </div>

        <!-- 5. Total Biaya Pajak & Asuransi -->
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-2xs hover:border-[#015471]/30 hover:shadow-xs transition relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[#015471] block text-[10px] uppercase font-bold tracking-wider">Total Biaya Legalitas</span>
                <span class="w-7 h-7 rounded-lg bg-[#015471]/10 text-[#015471] flex items-center justify-center text-xs">
                    <i data-lucide="coins" class="w-3.5 h-3.5 text-[#015471]"></i>
                </span>
            </div>
            <span class="text-base font-black text-[#015471] mt-2 block font-mono">Rp {{ number_format($stats['total_cost'], 0, ',', '.') }}</span>
            <span class="text-[10px] text-slate-400 mt-1 block">Pajak PKB: Rp {{ number_format($stats['tax_cost'], 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Alert Filter Banner if Expiring Soon -->
    @if($expiringCount > 0)
        <div class="p-4 sm:p-5 rounded-2xl bg-amber-50/90 border-2 border-amber-200 text-amber-950 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-2xs">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs font-bold">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="text-xs font-black text-amber-950">Perhatian: Ada {{ $expiringCount }} Dokumen / Pajak Jatuh Tempo Dalam 30 Hari!</h4>
                    <p class="text-[11px] text-amber-800 mt-0.5">Segera lakukan perpanjangan STNK dan pembayaran PKB di Samsat. Gunakan tombol hijau <strong>"Bayar Pajak"</strong> untuk pelunasan.</p>
                </div>
            </div>
            <a href="{{ route('documents.index', ['filter' => 'expiring_soon']) }}" class="inline-flex items-center justify-center px-4 py-2 text-xs font-bold bg-amber-600 text-white rounded-xl hover:bg-amber-700 transition shrink-0 shadow-2xs">
                Filter Unit Jatuh Tempo ({{ $expiringCount }})
            </a>
        </div>
    @endif

    <!-- Table Card Container -->
    <div class="bg-white rounded-3xl border-2 border-slate-100 shadow-sm overflow-hidden space-y-0">
        
        <!-- Filter Toolbar -->
        <div class="p-5 border-b border-slate-200/80 bg-slate-50/70 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            
            <!-- Quick Status Filter Pills -->
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('documents.index') }}" class="px-3.5 py-2 text-xs font-bold rounded-xl transition shadow-2xs {{ !request('filter') ? 'bg-[#015471] text-white shadow-xs' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}">
                    Semua Dokumen
                </a>
                <a href="{{ route('documents.index', ['filter' => 'expiring_soon']) }}" class="px-3.5 py-2 text-xs font-bold rounded-xl transition shadow-2xs {{ request('filter') == 'expiring_soon' ? 'bg-[#FF7A3D] text-white shadow-xs' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}">
                    Jatuh Tempo < 30 Hari @if($expiringCount > 0)<span class="ml-1 px-1.5 py-0.2 rounded-full text-[10px] bg-white/20 text-white font-mono">{{ $expiringCount }}</span>@endif
                </a>
                <a href="{{ route('documents.index', ['filter' => 'expired']) }}" class="px-3.5 py-2 text-xs font-bold rounded-xl transition shadow-2xs {{ request('filter') == 'expired' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50' }}">
                    Lewat Jatuh Tempo
                </a>
            </div>

            <!-- Filter By Document Type & Per Page -->
            <form action="{{ route('documents.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                @if(request('filter')) <input type="hidden" name="filter" value="{{ request('filter') }}"> @endif
                
                <div>
                    <select name="type" onchange="this.form.submit()" class="text-xs font-bold px-3.5 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] bg-white text-slate-800 cursor-pointer shadow-2xs">
                        <option value="">Semua Jenis Dokumen</option>
                        <option value="stnk" {{ request('type') == 'stnk' ? 'selected' : '' }}>STNK / Pajak Tahunan</option>
                        <option value="pajak_tahunan" {{ request('type') == 'pajak_tahunan' ? 'selected' : '' }}>PKB & SWDKLLJ Tahunan</option>
                        <option value="plat_5_tahunan" {{ request('type') == 'plat_5_tahunan' ? 'selected' : '' }}>Pajak & Ganti Plat 5 Tahunan</option>
                        <option value="bpkb" {{ request('type') == 'bpkb' ? 'selected' : '' }}>BPKB</option>
                        <option value="asuransi" {{ request('type') == 'asuransi' ? 'selected' : '' }}>Polis Asuransi</option>
                        <option value="blokir_polda" {{ request('type') == 'blokir_polda' ? 'selected' : '' }}>Surat Blokir Polda</option>
                    </select>
                </div>

                <div class="flex items-center gap-1.5">
                    <label for="per_page" class="text-xs font-bold text-slate-500">Tampilkan:</label>
                    <select name="per_page" onchange="this.form.submit()" class="text-xs font-bold px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] bg-white text-slate-800 cursor-pointer shadow-2xs">
                        <option value="10" {{ ($perPage ?? 15) == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ ($perPage ?? 15) == 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ ($perPage ?? 15) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ ($perPage ?? 15) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ ($perPage ?? 15) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Table View -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#e6f0f3] border-b border-slate-200 text-[#015471] font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">Plat Nomor & Unit</th>
                        <th class="px-5 py-3.5">Jenis Dokumen</th>
                        <th class="px-5 py-3.5">No. Dokumen / Seri</th>
                        <th class="px-5 py-3.5">Tgl Pembayaran</th>
                        <th class="px-5 py-3.5">Jatuh Tempo (Masa Berlaku)</th>
                        <th class="px-5 py-3.5 text-right">Biaya Terakhir</th>
                        <th class="px-5 py-3.5 text-right">Aksi & Bayar Pajak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-800">
                    @forelse($documents as $doc)
                        @php
                            $diffDays = $doc->expiry_date ? (int) round(now()->startOfDay()->diffInDays($doc->expiry_date->startOfDay(), false)) : null;
                            $isRenewableDoc = in_array($doc->document_type, ['stnk', 'pajak_tahunan', 'plat_5_tahunan', 'asuransi']);
                            $isInsurance = ($doc->document_type === 'asuransi');
                            $needsPayment = $doc->expiry_date && $diffDays <= 30;
                            $actionBtnLabel = $isInsurance ? 'Bayar Premi' : 'Bayar Pajak';
                            $actionIcon = $isInsurance ? 'shield-check' : 'credit-card';
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- 1. Plat Nomor & Unit -->
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <a href="{{ $doc->vehicle ? route('vehicles.show', $doc->vehicle) : '#' }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#015471] text-white font-mono font-black text-xs hover:bg-[#013e54] transition shadow-2xs">
                                        <i data-lucide="bike" class="w-3.5 h-3.5 text-[#FF7A3D]"></i>
                                        {{ $doc->vehicle ? $doc->vehicle->plate_number : '-' }}
                                    </a>
                                </div>
                                <div class="text-[11px] text-slate-500 mt-1">
                                    {{ $doc->vehicle ? ($doc->vehicle->brand . ' ' . $doc->vehicle->model_name) : '' }}
                                    @if($doc->vehicle && $doc->vehicle->location)
                                        <span class="text-slate-400">&bull; {{ $doc->vehicle->location->name }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- 2. Jenis Dokumen -->
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $isInsurance ? 'bg-indigo-50 text-indigo-700 border border-indigo-200/60' : 'bg-[#015471]/10 text-[#015471] border border-[#015471]/20' }}">
                                    {{ $doc->type_label }}
                                </span>
                            </td>

                            <!-- 3. Nomor Registrasi / Seri -->
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-xs font-bold text-slate-800 bg-slate-100 px-2 py-1 rounded-md">
                                    {{ $doc->document_number ?: '-' }}
                                </span>
                            </td>

                            <!-- 4. Tgl Pembayaran Terakhir -->
                            <td class="px-5 py-3.5 text-slate-600 font-mono text-[11px] whitespace-nowrap">
                                {{ $doc->issue_date ? $doc->issue_date->format('d/m/Y') : '-' }}
                            </td>

                            <!-- 5. Jatuh Tempo Status Badge -->
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @if($doc->expiry_date)
                                    @if($diffDays < 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold font-mono bg-rose-50 text-rose-700 border border-rose-200">
                                            <i data-lucide="alert-circle" class="w-3 h-3 text-rose-600"></i>
                                            {{ $doc->expiry_date->format('d M Y') }}
                                            <span class="text-rose-600 font-extrabold">(Telat {{ abs($diffDays) }} hr)</span>
                                        </span>
                                    @elseif($diffDays <= 30)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold font-mono bg-[#FF7A3D]/10 text-[#FF7A3D] border border-[#FF7A3D]/30">
                                            <i data-lucide="clock" class="w-3 h-3"></i>
                                            {{ $doc->expiry_date->format('d M Y') }}
                                            <span>({{ $diffDays == 0 ? 'Hari Ini' : $diffDays . ' hr lagi' }})</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold font-mono bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            <i data-lucide="check-circle" class="w-3 h-3 text-emerald-600"></i>
                                            {{ $doc->expiry_date->format('d M Y') }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Tidak diset</span>
                                @endif
                            </td>

                            <!-- 6. Biaya Terakhir -->
                            <td class="px-5 py-3.5 text-right font-bold text-slate-900 whitespace-nowrap">
                                <span class="font-mono text-[#FF7A3D] font-bold">Rp {{ number_format($doc->cost, 0, ',', '.') }}</span>
                            </td>

                            <!-- 7. Aksi & Bayar Pajak / Premi -->
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($isRenewableDoc && $needsPayment)
                                        <!-- Tombol Bayar Pajak / Bayar Premi Asuransi (Jatuh Tempo <= 30 Hari atau Telat) -->
                                        <button type="button" onclick="openPayTaxModal({{ $doc->id }}, '{{ $doc->type_label }}', '{{ $doc->vehicle ? $doc->vehicle->plate_number : '' }}', '{{ $doc->document_number }}', '{{ $doc->expiry_date ? $doc->expiry_date->format('Y-m-d') : '' }}', '{{ $doc->cost }}', '{{ $doc->document_type }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl {{ $isInsurance ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-emerald-600 hover:bg-emerald-700' }} text-white font-bold text-xs shadow-2xs transition active:scale-95">
                                            <i data-lucide="{{ $actionIcon }}" class="w-3.5 h-3.5"></i> {{ $actionBtnLabel }}
                                        </button>
                                    @elseif($isRenewableDoc && !$needsPayment && $doc->expiry_date)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[10px]">
                                            <i data-lucide="check" class="w-3 h-3 text-emerald-600"></i> Aktif
                                        </span>
                                    @endif

                                    @if($doc->file_path)
                                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="p-1.5 text-[#015471] hover:bg-[#015471]/10 rounded-xl transition" title="Lihat Lampiran Berkas">
                                            <i data-lucide="paperclip" class="w-4 h-4"></i>
                                        </a>
                                    @endif

                                    <form id="delete-doc-{{ $doc->id }}" action="{{ route('documents.destroy', $doc) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('delete-doc-{{ $doc->id }}', 'dokumen ini')" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-xl hover:bg-rose-50 transition border border-transparent hover:border-rose-100" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                    <i data-lucide="folder-x" class="w-6 h-6"></i>
                                </div>
                                <h4 class="text-xs font-bold text-slate-700">Tidak Ada Data Dokumen atau Pajak</h4>
                                <p class="text-[11px] text-slate-400 mt-1">Gunakan tombol "Catat Dokumen Baru" untuk menambahkan arsip dokumen baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($documents->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-white">
                {{ $documents->links() }}
            </div>
        @else
            <div class="px-6 py-3 border-t border-slate-100 bg-white text-xs text-slate-500 text-right">
                Menampilkan seluruh <strong class="text-slate-900">{{ $documents->total() }}</strong> arsip dokumen
            </div>
        @endif
    </div>

</div>

<!-- Modal Dialog Set Bayar Pajak / Perpanjangan Asuransi -->
<div id="payTaxModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-xl border border-slate-200 max-w-lg w-full overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shadow-2xs" id="modalTaxIconWrapper">
                    <i data-lucide="credit-card" class="w-5 h-5" id="modalTaxIcon"></i>
                </div>
                <div>
                    <h3 class="text-xs font-black text-slate-900" id="modalTaxTitle">Pelunasan & Perpanjangan Pajak</h3>
                    <p class="text-[11px] text-slate-500" id="modalTaxSubtitle">Unit Kendaraan</p>
                </div>
            </div>
            <button type="button" onclick="closePayTaxModal()" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-200 transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="payTaxForm" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5" id="modalLabelCost">Nominal Biaya Pajak (Rp) <span class="text-[#FF7A3D]">*</span></label>
                    <div class="flex rounded-xl shadow-2xs">
                        <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-slate-300 bg-slate-50 text-slate-600 text-xs font-bold font-mono">
                            Rp
                        </span>
                        <input type="number" name="cost" id="modalTaxCost" required placeholder="Contoh: 350000" class="w-full text-xs font-mono font-bold px-3 py-2.5 rounded-r-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5" id="modalLabelDocNumber">Nomor Registrasi / NTPN</label>
                    <input type="text" name="document_number" id="modalTaxDocNumber" placeholder="Nomor seri / NTPN / No. Polis..." class="w-full text-xs font-mono px-3 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Pembayaran <span class="text-[#FF7A3D]">*</span></label>
                    <input type="date" name="paid_date" id="modalTaxPaidDate" value="{{ date('Y-m-d') }}" required class="w-full text-xs font-mono px-3 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Jatuh Tempo Berikutnya <span class="text-[#FF7A3D]">*</span></label>
                    <input type="date" name="next_expiry_date" id="modalTaxNextExpiry" required class="w-full text-xs font-mono font-bold px-3 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5" id="modalLabelReceipt">Unggah Struk / Bukti Bayar (PDF/Foto)</label>
                <input type="file" name="receipt_file" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" id="modalTaxNotes" rows="2" placeholder="Catatan loket / polis asuransi jika ada..." class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900"></textarea>
            </div>

            <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-100 text-[11px] text-emerald-800" id="modalInfoNote">
                <i data-lucide="check-circle" class="w-3.5 h-3.5 inline mr-1 text-emerald-600"></i>
                Nominal pembayaran ini akan <strong>otomatis tercatat di Buku Kas Pengeluaran</strong> dan masa berlaku unit diperbarui.
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closePayTaxModal()" class="px-4 py-2.5 text-xs font-bold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" id="modalSubmitBtn" class="px-5 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition active:scale-95 flex items-center gap-1.5">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openPayTaxModal(docId, docType, plateNumber, currentDocNum, currentExpiry, currentCost, rawDocType) {
        const modal = document.getElementById('payTaxModal');
        const form = document.getElementById('payTaxForm');
        const isInsurance = (rawDocType === 'asuransi');
        
        form.action = `/documents/${docId}/pay-tax`;
        document.getElementById('modalTaxTitle').textContent = isInsurance ? `Pembayaran Premi & Perpanjangan ${docType}` : `Pelunasan & Perpanjang ${docType}`;
        document.getElementById('modalTaxSubtitle').textContent = `Unit Armada: ${plateNumber}`;
        document.getElementById('modalLabelCost').innerHTML = isInsurance ? `Nominal Premi Asuransi (Rp) <span class="text-[#FF7A3D]">*</span>` : `Nominal Biaya Pajak (Rp) <span class="text-[#FF7A3D]">*</span>`;
        document.getElementById('modalLabelDocNumber').textContent = isInsurance ? `Nomor Polis Asuransi Baru / Seri` : `Nomor Registrasi / NTPN`;
        document.getElementById('modalLabelReceipt').textContent = isInsurance ? `Unggah Polis / Bukti Bayar Premi (PDF/Foto)` : `Unggah Struk / Bukti Bayar Pajak (PDF/Foto)`;
        
        const submitBtn = document.getElementById('modalSubmitBtn');
        if (isInsurance) {
            submitBtn.className = "px-5 py-2.5 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition active:scale-95 flex items-center gap-1.5";
            submitBtn.innerHTML = `<i data-lucide="shield-check" class="w-4 h-4"></i> Simpan Pembayaran Premi`;
        } else {
            submitBtn.className = "px-5 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition active:scale-95 flex items-center gap-1.5";
            submitBtn.innerHTML = `<i data-lucide="save" class="w-4 h-4"></i> Simpan Pembayaran Pajak`;
        }

        document.getElementById('modalTaxCost').value = currentCost || '';
        document.getElementById('modalTaxDocNumber').value = currentDocNum || '';
        
        if (currentExpiry) {
            let nextDate = new Date(currentExpiry);
            nextDate.setFullYear(nextDate.getFullYear() + 1);
            let yyyy = nextDate.getFullYear();
            let mm = String(nextDate.getMonth() + 1).padStart(2, '0');
            let dd = String(nextDate.getDate()).padStart(2, '0');
            document.getElementById('modalTaxNextExpiry').value = `${yyyy}-${mm}-${dd}`;
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        if (window.lucide) { lucide.createIcons(); }
    }

    function closePayTaxModal() {
        const modal = document.getElementById('payTaxModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endpush
@endsection
