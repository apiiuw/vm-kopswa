@extends('layouts.app')

@section('title', 'Detail Kendaraan ' . $vehicle->plate_number)
@section('page_title', 'Kartu Riwayat Kendaraan')

@section('content')
<div class="w-full space-y-6">

    <!-- Top Navigation Bar (Luar Div Putih / Atas Card) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <!-- Tombol Kembali di Pojok Kiri Atas -->
        <a href="{{ route('vehicles.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 hover:text-slate-900 transition shadow-2xs w-fit">
            <i data-lucide="arrow-left" class="w-4 h-4 text-slate-500"></i>
            <span>Kembali</span>
        </a>

        <!-- Action CTA Toolbar di Pojok Kanan Atas -->
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('exports.vehicleCardPdf', $vehicle) }}" class="inline-flex items-center gap-2 px-3.5 py-2.5 text-xs font-bold rounded-xl border border-slate-300 text-slate-800 bg-white hover:bg-slate-50 transition shadow-2xs">
                <i data-lucide="file-text" class="w-4 h-4 text-[#FF7A3D]"></i> Kartu Riwayat PDF
            </a>
            <a href="{{ route('maintenance.create', ['vehicle_id' => $vehicle->id]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-sm transition">
                <i data-lucide="wrench" class="w-4 h-4"></i> Catat Servis/Ban
            </a>
            <a href="{{ route('vehicles.edit', $vehicle) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 text-xs font-bold rounded-xl border border-slate-300 text-slate-800 bg-white hover:bg-slate-50 hover:text-[#015471] transition shadow-2xs">
                <i data-lucide="pencil" class="w-4 h-4 text-[#015471]"></i> Edit Unit
            </a>
        </div>
    </div>

    <!-- Top Master Header Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <!-- Unit Identitas Utama -->
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#015471] to-[#023b4f] text-white flex items-center justify-center shrink-0 shadow-sm">
                    <i data-lucide="{{ $vehicle->icon_name }}" class="w-7 h-7 text-[#FF7A3D]"></i>
                </div>
                <div class="space-y-1.5">
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-2xl font-black text-slate-900 font-mono tracking-tight">{{ preg_replace('/\s+/', ' ', trim($vehicle->plate_number)) }}</h1>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $vehicle->status_badge['class'] }}">
                            {{ $vehicle->status_badge['label'] }}
                        </span>
                        @if($vehicle->replacement_plate_number)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                <i data-lucide="repeat" class="w-3.5 h-3.5 text-amber-600"></i>
                                Motor Pengganti: <strong class="font-mono">{{ preg_replace('/\s+/', ' ', trim($vehicle->replacement_plate_number)) }}</strong>
                            </span>
                        @endif
                    </div>
                    
                    @php
                        // Clean model name to avoid double brand (e.g. "Honda Honda CRF 150 L" -> "Honda CRF 150 L")
                        $displayBrand = $vehicle->brand ?: 'Honda';
                        $cleanModel = $vehicle->model_name;
                        if (stripos($cleanModel, $displayBrand) === 0) {
                            $displayName = $cleanModel;
                        } else {
                            $displayName = $displayBrand . ' ' . $cleanModel;
                        }
                    @endphp

                    <!-- Baris 1: Spesifikasi Unit -->
                    <div class="flex flex-wrap items-center gap-2 text-xs text-slate-600">
                        <span class="font-extrabold text-slate-900">{{ $displayName }} ({{ $vehicle->year }})</span>
                        <span class="text-slate-300">&bull;</span>
                        <span>{{ $vehicle->category ?: 'Roda 2' }}</span>
                        @if($vehicle->type_name)
                            <span class="text-slate-300">&bull;</span>
                            <span>{{ $vehicle->type_name }}</span>
                        @endif
                        <span class="text-slate-300">&bull;</span>
                        <span>Warna: <strong class="text-slate-800">{{ $vehicle->color ?: '-' }}</strong></span>
                    </div>

                    <!-- Catatan Tambahan Unit (Tepat di Bawah Nama Motor / Spesifikasi) -->
                    @if($vehicle->notes)
                        <div class="pt-1">
                            <div class="inline-flex items-start gap-2 px-3 py-1.5 rounded-xl bg-amber-50/80 border border-amber-200/80 text-amber-900 text-xs">
                                <i data-lucide="info" class="w-3.5 h-3.5 text-amber-600 shrink-0 mt-0.5"></i>
                                <div>
                                    <span class="font-bold text-[11px] uppercase tracking-wider text-amber-800 block">Catatan Unit:</span>
                                    <p class="text-xs text-amber-900 font-medium leading-relaxed">{{ $vehicle->notes }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Baris 2: Klien & Lokasi Kebun & Pengemudi (Structured Badges) -->
                    <div class="flex flex-wrap items-center gap-2.5 pt-1 text-xs">
                        <!-- Klien / Mitra -->
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#015471]/5 border border-[#015471]/15 text-[#015471] font-bold">
                            <i data-lucide="building-2" class="w-3.5 h-3.5 text-[#FF7A3D]"></i>
                            {{ $vehicle->vendor ? $vehicle->vendor->name : 'Klien Belum Ditentukan' }}
                        </span>

                        <!-- Lokasi Kebun -->
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200 text-slate-700 font-semibold">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-[#FF7A3D]"></i>
                            Kebun: <strong class="text-slate-900 font-bold ml-0.5">{{ $vehicle->location ? $vehicle->location->name : 'Belum Ditentukan' }}</strong>
                        </span>

                        <!-- Pengemudi & WhatsApp Badge -->
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200 text-slate-700">
                            <i data-lucide="user" class="w-3.5 h-3.5 text-[#015471]"></i>
                            <span class="text-slate-500 font-medium">Pengemudi:</span>
                            <strong class="text-slate-900 font-bold">{{ $vehicle->user_holder ?: 'Asisten Kebun' }}</strong>
                            
                            @if($vehicle->driver_phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $vehicle->driver_phone) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 hover:text-emerald-800 bg-emerald-50 hover:bg-emerald-100/80 border border-emerald-200/80 px-2 py-0.5 rounded-md transition shadow-2xs ml-1" title="Hubungi WhatsApp">
                                    <i data-lucide="phone" class="w-3 h-3 text-emerald-600"></i>
                                    <span>{{ $vehicle->driver_phone }}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6 Quick Stats / Spec Metric Tiles -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-3 mt-6 pt-6 border-t border-slate-100 text-xs">
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-slate-500 block text-[10px] font-bold uppercase tracking-wider">No. Rangka</span>
                <span class="font-bold text-slate-900 font-mono text-xs block mt-1 truncate" title="{{ $vehicle->frame_number }}">
                    {{ $vehicle->frame_number ?: '-' }}
                </span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-slate-500 block text-[10px] font-bold uppercase tracking-wider">No. Mesin</span>
                <span class="font-bold text-slate-900 font-mono text-xs block mt-1 truncate" title="{{ $vehicle->engine_number }}">
                    {{ $vehicle->engine_number ?: '-' }}
                </span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-slate-500 block text-[10px] font-bold uppercase tracking-wider">Pajak Tahunan (PKB)</span>
                <span class="font-bold font-mono text-xs block mt-1 {{ $vehicle->tax_annual_date ? 'text-[#015471]' : 'text-slate-400' }}">
                    {{ $vehicle->tax_annual_date ? $vehicle->tax_annual_date->format('d M Y') : '-' }}
                </span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-slate-500 block text-[10px] font-bold uppercase tracking-wider">Pajak Plat 5 Th</span>
                <span class="font-bold font-mono text-xs block mt-1 {{ $vehicle->tax_5year_date ? 'text-[#015471]' : 'text-slate-400' }}">
                    {{ $vehicle->tax_5year_date ? $vehicle->tax_5year_date->format('d M Y') : '-' }}
                </span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-slate-500 block text-[10px] font-bold uppercase tracking-wider">Sewa Kontrak</span>
                <span class="font-black text-[#015471] text-xs block mt-1">
                    Rp {{ number_format($vehicle->rental_price_per_month, 0, ',', '.') }}
                </span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-slate-500 block text-[10px] font-bold uppercase tracking-wider">Total Servis</span>
                <span class="font-black text-[#FF7A3D] text-xs block mt-1">
                    Rp {{ number_format($totalMaintenanceCost, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <!-- STACKED GRID SECTIONS -->
    <div class="space-y-6">

        <!-- ========================================================================= -->
        <!-- BARIS 1: LEGALITAS UTAMA (STNK & BPKB) & PAJAK (PKB) / POLIS LAINNYA (1 BARIS 2 KOLOM) -->
        <!-- ========================================================================= -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Card 1A: Dokumen Legalitas Utama (STNK & BPKB) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
                <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-[#FF7A3D]/10 text-[#FF7A3D] flex items-center justify-center shrink-0">
                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-black text-slate-900">Legalitas Utama (STNK & BPKB)</h2>
                            <p class="text-[10px] text-slate-500">Dokumen identitas resmi dan legalitas kepemilikan unit</p>
                        </div>
                    </div>
                    <a href="{{ route('vehicles.edit', $vehicle) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-slate-300 text-[11px] font-bold text-slate-700 hover:text-[#015471] hover:bg-slate-50 transition shadow-2xs">
                        <i data-lucide="upload" class="w-3.5 h-3.5"></i> Ganti/Unggah
                    </a>
                </div>

                <div class="p-5 space-y-3.5 flex-1">
                    <!-- STNK Compact Card Row -->
                    <div class="rounded-xl border border-slate-200 bg-slate-50/40 p-3.5 space-y-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-800 flex items-center justify-center shrink-0">
                                    <i data-lucide="file-badge" class="w-4 h-4"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <h3 class="text-xs font-black text-slate-900 truncate">Surat STNK</h3>
                                        <span class="text-[10px] text-slate-400 font-normal">(Pajak Tahunan)</span>
                                    </div>
                                    <p class="text-xs font-mono font-bold truncate {{ $vehicle->stnk_number ? 'text-[#015471]' : 'text-slate-400 italic' }}">
                                        {{ $vehicle->stnk_number ?: 'Nomor belum dicatat' }}
                                    </p>
                                </div>
                            </div>

                            @if($vehicle->stnk_file)
                                @php
                                    $stnkExt = strtolower(pathinfo($vehicle->stnk_file, PATHINFO_EXTENSION));
                                    $isStnkPdf = $stnkExt === 'pdf';
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold shrink-0 {{ $isStnkPdf ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $isStnkPdf ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                                    {{ strtoupper($stnkExt) }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-200 text-slate-600 shrink-0">
                                    Belum Ada File
                                </span>
                            @endif
                        </div>

                        <!-- Action STNK: Inline Right -->
                        <div class="pt-2 border-t border-slate-200/70 flex items-center justify-between text-xs">
                            @if($vehicle->stnk_file)
                                <span class="text-[11px] text-emerald-700 font-bold flex items-center gap-1">
                                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Tersimpan
                                </span>
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ asset('storage/' . $vehicle->stnk_file) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#015471] hover:bg-[#013e54] text-white text-[11px] font-bold transition shadow-2xs">
                                        <i data-lucide="eye" class="w-3 h-3"></i> Buka
                                    </a>
                                    <a href="{{ asset('storage/' . $vehicle->stnk_file) }}" download class="p-1 rounded-lg border border-slate-300 text-slate-600 hover:text-black hover:bg-white transition" title="Unduh">
                                        <i data-lucide="download" class="w-3 h-3"></i>
                                    </a>
                                </div>
                            @else
                                <span class="text-[11px] text-slate-400">File belum diunggah</span>
                                <a href="{{ route('vehicles.edit', $vehicle) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#FF7A3D] hover:bg-[#e56b32] text-white text-[11px] font-bold transition shadow-2xs">
                                    <i data-lucide="upload" class="w-3 h-3"></i> Upload
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- BPKB Compact Card Row -->
                    <div class="rounded-xl border border-slate-200 bg-slate-50/40 p-3.5 space-y-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                                    <i data-lucide="book-marked" class="w-4 h-4"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <h3 class="text-xs font-black text-slate-900 truncate">Buku BPKB</h3>
                                        <span class="text-[10px] text-slate-400 font-normal">(Kepemilikan)</span>
                                    </div>
                                    <p class="text-xs font-mono font-bold truncate {{ $vehicle->bpkb_number ? 'text-[#015471]' : 'text-slate-400 italic' }}">
                                        {{ $vehicle->bpkb_number ?: 'Nomor belum dicatat' }}
                                    </p>
                                </div>
                            </div>

                            @if($vehicle->bpkb_file)
                                @php
                                    $bpkbExt = strtolower(pathinfo($vehicle->bpkb_file, PATHINFO_EXTENSION));
                                    $isBpkbPdf = $bpkbExt === 'pdf';
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold shrink-0 {{ $isBpkbPdf ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $isBpkbPdf ? 'bg-rose-500' : 'bg-emerald-500' }}"></span>
                                    {{ strtoupper($bpkbExt) }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-200 text-slate-600 shrink-0">
                                    Belum Ada File
                                </span>
                            @endif
                        </div>

                        <!-- Action BPKB: Inline Right -->
                        <div class="pt-2 border-t border-slate-200/70 flex items-center justify-between text-xs">
                            @if($vehicle->bpkb_file)
                                <span class="text-[11px] text-emerald-700 font-bold flex items-center gap-1">
                                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Tersimpan
                                </span>
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ asset('storage/' . $vehicle->bpkb_file) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#015471] hover:bg-[#013e54] text-white text-[11px] font-bold transition shadow-2xs">
                                        <i data-lucide="eye" class="w-3 h-3"></i> Buka
                                    </a>
                                    <a href="{{ asset('storage/' . $vehicle->bpkb_file) }}" download class="p-1 rounded-lg border border-slate-300 text-slate-600 hover:text-black hover:bg-white transition" title="Unduh">
                                        <i data-lucide="download" class="w-3 h-3"></i>
                                    </a>
                                </div>
                            @else
                                <span class="text-[11px] text-slate-400">File belum diunggah</span>
                                <a href="{{ route('vehicles.edit', $vehicle) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#FF7A3D] hover:bg-[#e56b32] text-white text-[11px] font-bold transition shadow-2xs">
                                    <i data-lucide="upload" class="w-3 h-3"></i> Upload
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 1B: Riwayat Pajak (PKB) & Polis Dokumen Lainnya -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
                <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center shrink-0">
                            <i data-lucide="file-stack" class="w-4 h-4 text-[#015471]"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-black text-slate-900">Pajak (PKB) & Polis Lainnya</h2>
                            <p class="text-[10px] text-slate-500">Masa berlaku pajak tahunan, plat 5 tahunan dan polis asuransi</p>
                        </div>
                    </div>
                    <a href="{{ route('documents.create', ['vehicle_id' => $vehicle->id]) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-[#FF7A3D]/10 text-[#FF7A3D] hover:bg-[#FF7A3D] hover:text-white text-xs font-bold transition">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah
                    </a>
                </div>

                <div class="p-5 space-y-3 flex-1">
                    @forelse($vehicle->documents as $doc)
                        <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-bold text-slate-900 text-xs truncate">{{ $doc->type_label }}</span>
                                <span class="text-[10px] px-2 py-0.5 rounded-md font-mono font-bold bg-white border border-slate-200 text-[#015471] shrink-0">
                                    {{ $doc->document_number ?: 'Tercatat' }}
                                </span>
                            </div>
                            <div class="mt-2 text-[11px] text-slate-600 space-y-1">
                                <p>Masa Berlaku: <strong class="text-[#015471]">{{ $doc->expiry_date ? $doc->expiry_date->format('d M Y') : 'Tidak ditentukan' }}</strong></p>
                                <p>Biaya: <strong class="text-[#FF7A3D]">Rp {{ number_format($doc->cost, 0, ',', '.') }}</strong></p>
                                @if($doc->notes)
                                    <p class="text-slate-400 text-[10px] mt-0.5 italic truncate">{{ $doc->notes }}</p>
                                @endif
                                @if($doc->file_path)
                                    <div class="mt-2 pt-1.5 border-t border-slate-200">
                                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#015471] hover:underline">
                                            <i data-lucide="paperclip" class="w-3.5 h-3.5"></i> Lihat Berkas Lampiran
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-slate-400">
                            <i data-lucide="folder-x" class="w-7 h-7 mx-auto text-slate-300 mb-1.5 opacity-70"></i>
                            <p class="text-xs">Belum ada riwayat dokumen tambahan untuk unit ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- BARIS 2: RIWAYAT SERVIS, BAN & SUKU CADANG (HANYA RIWAYAT SERVIS - FULL WIDTH) -->
        <!-- ========================================================================= -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#015471]/10 text-[#015471] flex items-center justify-center">
                        <i data-lucide="wrench" class="w-4 h-4 text-[#FF7A3D]"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-900">Riwayat Servis, Ban & Suku Cadang</h2>
                        <p class="text-[11px] text-slate-500">Catatan perawatan rutin, penggantian ban, aki, dan suku cadang unit</p>
                    </div>
                </div>
                <a href="{{ route('maintenance.create', ['vehicle_id' => $vehicle->id]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-2xs transition">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Catat Pemeliharaan Baru
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="px-5 py-3.5 w-28">Tanggal</th>
                            <th class="px-5 py-3.5 w-36">Jenis</th>
                            <th class="px-5 py-3.5">Rincian Komponen / Perbaikan</th>
                            <th class="px-5 py-3.5 w-44">Bengkel</th>
                            <th class="px-5 py-3.5 text-right w-36">Biaya (Rp)</th>
                            <th class="px-4 py-3.5 text-center w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-900">
                        @forelse($vehicle->maintenanceRecords as $mr)
                            @php
                                $complaints = $mr->parsed_complaints;
                                $cleanDesc = $mr->clean_description;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-5 py-3.5 text-slate-600 whitespace-nowrap font-mono text-[11px]">
                                    {{ $mr->date->format('d/m/Y') }}
                                </td>
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-[#015471]/10 text-[#015471] border border-[#015471]/20">
                                        {{ $mr->type_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <a href="{{ route('maintenance.show', $mr) }}" class="text-slate-900 hover:text-[#015471] block font-bold text-xs transition">
                                        {{ $mr->item_name ?: $mr->type_label }}
                                    </a>

                                    @if(count($complaints) > 0)
                                        <div class="flex items-center gap-1.5 flex-wrap mt-1.5">
                                            @foreach($complaints as $c)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[10px] font-medium border border-slate-200">
                                                    <span class="w-3.5 h-3.5 rounded bg-[#015471] text-white text-[8px] font-black flex items-center justify-center">{{ $c['index'] }}</span>
                                                    {{ $c['title'] ?: $c['category'] }}
                                                    @if($c['cost'])
                                                        <span class="text-[#FF7A3D] font-bold">({{ $c['cost'] }})</span>
                                                    @endif
                                                </span>
                                            @endforeach
                                        </div>
                                    @elseif($cleanDesc)
                                        <span class="text-[11px] text-slate-500 block mt-0.5 line-clamp-1">{{ $cleanDesc }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-slate-600 text-[11px]">
                                    <span class="truncate block max-w-[170px]" title="{{ $mr->workshop_name ?: '-' }}">{{ $mr->workshop_name ?: '-' }}</span>
                                </td>
                                <td class="px-5 py-3.5 text-right font-bold text-[#FF7A3D] whitespace-nowrap font-mono text-xs">
                                    Rp {{ number_format($mr->cost, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                    <a href="{{ route('maintenance.show', $mr) }}" class="p-1.5 text-slate-400 hover:text-[#015471] rounded-lg hover:bg-slate-100 transition inline-block" title="Lihat Detail">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-10 text-slate-400">
                                    <i data-lucide="wrench" class="w-8 h-8 mx-auto text-slate-300 mb-2 opacity-50"></i>
                                    <p class="text-xs">Belum ada riwayat pemeliharaan atau servis pada unit ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($vehicle->maintenanceRecords->count() > 0)
                        <tfoot class="bg-slate-50/80 border-t border-slate-200 font-bold text-xs">
                            <tr>
                                <td colspan="4" class="px-5 py-3.5 text-slate-700">Total Akumulasi Biaya</td>
                                <td class="px-5 py-3.5 text-right text-[#FF7A3D] font-black font-mono text-sm">
                                    Rp {{ number_format($totalMaintenanceCost, 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- BARIS 3: RIWAYAT KLAIM ASURANSI & INSIDEN (HANYA KLAIM ASURANSI - FULL WIDTH) -->
        <!-- ========================================================================= -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                        <i data-lucide="shield-alert" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-900">Riwayat Klaim Asuransi & Insiden Kehilangan</h2>
                        <p class="text-[10px] text-slate-500">Status laporan kehilangan, blokir Polda & proses pencairan klaim asuransi</p>
                    </div>
                </div>
            </div>

            <div class="p-6 divide-y divide-slate-100">
                @forelse($vehicle->insuranceClaims as $claim)
                    <div class="py-4 first:pt-0 last:pb-0 space-y-2.5">
                        <div class="flex items-center justify-between gap-3 flex-wrap">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 text-rose-800 uppercase tracking-wider">
                                    {{ str_replace('_', ' ', $claim->claim_type) }}
                                </span>
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-[#015471]/10 text-[#015471] uppercase">
                                    Status: {{ strtoupper($claim->status) }}
                                </span>
                            </div>
                            <span class="text-xs font-semibold text-slate-500 font-mono">
                                Tanggal Insiden: {{ $claim->incident_date->format('d M Y') }}
                            </span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs text-slate-700 bg-slate-50/60 p-3.5 rounded-xl border border-slate-100">
                            <p>Laporan Polisi: <strong class="font-mono text-slate-900">{{ $claim->police_report_number ?: 'Belum diisi' }}</strong></p>
                            <p>Surat Blokir STNK: <strong class="font-mono text-slate-900">{{ $claim->polda_blocking_number ?: 'Belum diisi' }}</strong></p>
                            @if($claim->incident_chronology)
                                <div class="md:col-span-2 text-[11px] text-slate-600 italic pt-1">
                                    Kronologi: "{{ $claim->incident_chronology }}"
                                </div>
                            @endif
                        </div>
                        <div class="pt-2 flex items-center justify-between text-xs">
                            <div>
                                <span class="text-slate-500">Nilai Klaim:</span> <strong class="font-mono text-slate-900">Rp {{ number_format($claim->claim_amount, 0, ',', '.') }}</strong>
                            </div>
                            <div>
                                <span class="text-slate-500">Pencairan Asuransi:</span> <strong class="font-mono text-emerald-700 font-bold">Rp {{ number_format($claim->settlement_amount, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400">
                        <i data-lucide="shield-check" class="w-8 h-8 mx-auto text-emerald-500 mb-1.5 opacity-80"></i>
                        <p class="text-xs font-semibold text-slate-700">Unit Aman</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Tidak ada riwayat klaim asuransi maupun insiden kehilangan pada kendaraan ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection

