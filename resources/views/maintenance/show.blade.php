@extends('layouts.app')

@section('title', 'Detail Catatan Pemeliharaan Armada')
@section('page_title', 'Rincian Transaksi Pemeliharaan & Servis')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto bg-white text-black">

    <!-- Top Navigation & Action Header -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-[#015471] text-white flex items-center justify-center shadow-sm shrink-0">
                    <i data-lucide="wrench" class="w-6 h-6 text-[#FF7A3D]"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-xl font-black text-slate-900 tracking-tight">
                            {{ $maintenance->item_name ?: $maintenance->type_label }}
                        </h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#015471]/10 text-[#015471] border border-[#015471]/20">
                            {{ $maintenance->type_label }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-2 flex-wrap">
                        <span><i data-lucide="calendar" class="w-3.5 h-3.5 inline-block text-slate-400 mr-1"></i>Tanggal: <strong class="text-slate-700 font-semibold">{{ $maintenance->date->translatedFormat('d F Y') }}</strong></span>
                        <span>&bull;</span>
                        <span><i data-lucide="clock" class="w-3.5 h-3.5 inline-block text-slate-400 mr-1"></i>Dicatat: <strong class="text-slate-700 font-semibold">{{ $maintenance->created_at->format('d/m/Y H:i') }} WIB</strong></span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('maintenance.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition shadow-2xs">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-slate-400"></i> Kembali
                </a>

                <form id="delete-maint-detail-{{ $maintenance->id }}" action="{{ route('maintenance.destroy', $maintenance) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmDelete('delete-maint-detail-{{ $maintenance->id }}', 'catatan pemeliharaan ini')" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 hover:text-rose-700 transition border border-rose-200">
                        <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Overview Highlights Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Armada Kendaraan -->
        <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-2xs space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Armada Kendaraan</span>
            <div class="flex items-center justify-between">
                <div>
                    <a href="{{ $maintenance->vehicle ? route('vehicles.show', $maintenance->vehicle) : '#' }}" class="text-base font-black font-mono text-slate-900 hover:text-[#015471] transition">
                        {{ $maintenance->vehicle ? $maintenance->vehicle->plate_number : '-' }}
                    </a>
                    <span class="block text-xs font-bold text-slate-600 mt-0.5">
                        {{ $maintenance->vehicle ? ($maintenance->vehicle->brand . ' ' . $maintenance->vehicle->model_name) : 'Non-Unit' }}
                    </span>
                </div>
                <div class="w-9 h-9 rounded-xl bg-[#015471]/10 text-[#015471] flex items-center justify-center">
                    <i data-lucide="bike" class="w-5 h-5 text-[#FF7A3D]"></i>
                </div>
            </div>
            <span class="text-[11px] text-slate-500 block pt-1 border-t border-slate-100 mt-2">
                <i data-lucide="map-pin" class="w-3 h-3 inline-block text-slate-400 mr-0.5"></i>
                {{ $maintenance->vehicle && $maintenance->vehicle->location ? $maintenance->vehicle->location->name : 'Kebun / Lokasi Operasional' }}
            </span>
        </div>

        <!-- 2. Total Biaya Riil -->
        <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-2xs space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Biaya Riil</span>
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-lg font-black font-mono text-[#FF7A3D] block">
                        Rp {{ number_format($maintenance->cost, 0, ',', '.') }}
                    </span>
                    <span class="text-[11px] font-semibold text-emerald-600 block mt-0.5">
                        <i data-lucide="check-circle-2" class="w-3 h-3 inline-block mr-0.5"></i> Masuk Buku Kas
                    </span>
                </div>
                <div class="w-9 h-9 rounded-xl bg-[#FF7A3D]/10 text-[#FF7A3D] flex items-center justify-center">
                    <i data-lucide="coins" class="w-5 h-5"></i>
                </div>
            </div>
            <span class="text-[11px] font-mono text-slate-500 block pt-1 border-t border-slate-100 mt-2">
                {{ $expense ? $expense->expense_number : 'EXP-AUTO' }}
            </span>
        </div>

        <!-- 3. Odometer Speedometer -->
        <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-2xs space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Odometer Speedometer</span>
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-base font-black font-mono text-[#015471] block">
                        {{ $maintenance->odometer_km ? number_format($maintenance->odometer_km, 0, ',', '.') . ' KM' : 'Tidak Dicatat' }}
                    </span>
                    <span class="text-[11px] text-slate-500 block mt-0.5">
                        Posisi KM Terkini
                    </span>
                </div>
                <div class="w-9 h-9 rounded-xl bg-[#015471]/10 text-[#015471] flex items-center justify-center">
                    <i data-lucide="gauge" class="w-5 h-5 text-[#FF7A3D]"></i>
                </div>
            </div>
            <span class="text-[11px] text-slate-500 block pt-1 border-t border-slate-100 mt-2 truncate">
                Status: <strong class="text-slate-800 font-bold capitalize">{{ str_replace('_', ' ', $structuredData['operational_status'] ?: 'Normal Operasional') }}</strong>
            </span>
        </div>

        <!-- 4. Bengkel / Vendor Rekanan -->
        <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-2xs space-y-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Bengkel Rekanan</span>
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <span class="text-sm font-black text-slate-900 block truncate" title="{{ $maintenance->workshop_name ?: 'Bengkel Mandiri / Lapangan' }}">
                        {{ $maintenance->workshop_name ?: 'Bengkel Mandiri / Lapangan' }}
                    </span>
                    <span class="text-[11px] text-slate-500 block mt-0.5 truncate">
                        {{ $structuredData['workshop_phone'] ? 'Telp: ' . $structuredData['workshop_phone'] : 'Tanpa No. Kontak' }}
                    </span>
                </div>
                <div class="w-9 h-9 rounded-xl bg-[#015471]/10 text-[#015471] flex items-center justify-center shrink-0">
                    <i data-lucide="store" class="w-5 h-5 text-[#FF7A3D]"></i>
                </div>
            </div>
            <span class="text-[11px] text-slate-500 block pt-1 border-t border-slate-100 mt-2 truncate">
                Mekanik: <strong class="text-slate-800 font-bold">{{ $structuredData['mechanic_name'] ?: '-' }}</strong>
            </span>
        </div>
    </div>

    <!-- Main Content 2 Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Kolom Kiri: Rincian Suku Cadang & Pekerjaan (8 Kolom) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Card Rincian Sub Kategori Suku Cadang / Keluhan -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-[#015471] text-white flex items-center justify-center text-xs shadow-2xs">
                            <i data-lucide="layers" class="w-4 h-4 text-[#FF7A3D]"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-black text-slate-900">Rincian Komponen & Suku Cadang Diganti</h2>
                            <p class="text-[11px] text-slate-500">Daftar item pekerjaan atau suku cadang yang dicatat pada pemeliharaan ini</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-[#015471] bg-[#015471]/10 px-2.5 py-1 rounded-full">
                        {{ count($complaintBlocks) > 0 ? count($complaintBlocks) . ' Item Tercatat' : '1 Item Pekerjaan' }}
                    </span>
                </div>

                <div class="p-6 space-y-4">
                    @if(count($complaintBlocks) > 0)
                        <div class="space-y-4">
                            @foreach($complaintBlocks as $cb)
                                @php
                                    $itemPhotos = array_filter($complaintPhotos, function($p) use ($cb) {
                                        return (isset($p['complaint_index']) && $p['complaint_index'] === ($cb['index'] - 1))
                                            || str_contains($p['name'] ?? '', "Foto Keluhan #{$cb['index']}")
                                            || str_contains($p['name'] ?? '', "Foto Item #{$cb['index']}");
                                    });
                                @endphp
                                <div class="p-5 rounded-2xl border border-slate-200 bg-white space-y-3.5 shadow-2xs hover:border-[#015471]/40 transition">
                                    <div class="flex items-start justify-between border-b border-slate-100 pb-3 gap-3">
                                        <div class="flex items-center gap-2.5">
                                            <span class="w-6 h-6 rounded-lg bg-[#015471] text-white text-[11px] font-black flex items-center justify-center shrink-0">
                                                {{ $cb['index'] }}
                                            </span>
                                            <div>
                                                <h3 class="font-bold text-xs text-slate-900">
                                                    {{ $cb['title'] ?: 'Item Pekerjaan #' . $cb['index'] }}
                                                </h3>
                                                <span class="text-[10px] font-semibold text-[#015471] bg-[#015471]/10 px-2 py-0.5 rounded">
                                                    {{ $cb['category'] ?: 'Pemeliharaan' }}
                                                </span>
                                            </div>
                                        </div>
                                        @if($cb['cost'])
                                            <span class="text-xs font-black font-mono text-[#FF7A3D] bg-[#FF7A3D]/10 px-2.5 py-1 rounded-lg shrink-0">
                                                Rp {{ $cb['cost'] }}
                                            </span>
                                        @endif
                                    </div>

                                    @if($cb['description'])
                                        <div>
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Deskripsi Teknis Pengerjaan:</span>
                                            <p class="text-xs text-slate-700 bg-slate-50/80 p-3.5 rounded-xl border border-slate-100 whitespace-pre-line leading-relaxed">
                                                {{ $cb['description'] }}
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Foto Bukti Khusus Item Ini -->
                                    @if(count($itemPhotos) > 0)
                                        <div class="pt-2 border-t border-dashed border-slate-100">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Foto Fisik Item / Kerusakan:</span>
                                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                                                @foreach($itemPhotos as $photo)
                                                    <a href="{{ asset('storage/' . $photo['path']) }}" target="_blank" class="group relative rounded-xl border border-slate-200 overflow-hidden bg-slate-100 hover:border-[#015471] transition block shadow-2xs">
                                                        <img src="{{ asset('storage/' . $photo['path']) }}" alt="{{ $photo['name'] }}" class="w-full h-24 object-cover group-hover:scale-105 transition-transform duration-200">
                                                        <div class="p-1.5 bg-white text-left">
                                                            <span class="block truncate text-[10px] font-bold text-slate-700">{{ $photo['name'] }}</span>
                                                        </div>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Tampilan Default Single Record -->
                        <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200 space-y-3">
                            <div class="flex items-center justify-between">
                                <h3 class="font-black text-sm text-slate-900">
                                    {{ $maintenance->item_name ?: $maintenance->type_label }}
                                </h3>
                                <span class="text-xs font-black font-mono text-[#FF7A3D]">
                                    Rp {{ number_format($maintenance->cost, 0, ',', '.') }}
                                </span>
                            </div>
                            @if($cleanDescription)
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Uraian / Deskripsi:</span>
                                    <p class="text-xs text-slate-700 bg-white p-3.5 rounded-xl border border-slate-200 whitespace-pre-line leading-relaxed">
                                        {{ $cleanDescription }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card 4 Sudut Foto Fisik Armada -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-[#015471] text-white flex items-center justify-center text-xs shadow-2xs">
                            <i data-lucide="camera" class="w-4 h-4 text-[#FF7A3D]"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-black text-slate-900">Foto Fisik 4 Sudut Unit Kendaraan</h2>
                            <p class="text-[11px] text-slate-500">Dokumentasi fisik unit motor saat proses pemeliharaan</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-[#015471] bg-[#015471]/10 px-2.5 py-1 rounded-full">4 Sisi Lengkap</span>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Depan -->
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-3 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-black text-slate-800">1. Tampak Depan</span>
                                <span class="text-[9px] font-bold text-[#015471] bg-[#015471]/10 px-1.5 py-0.5 rounded">Nopol</span>
                            </div>
                            @if(isset($vehicleAnglePhotos['front']))
                                <a href="{{ asset('storage/' . $vehicleAnglePhotos['front']['path']) }}" target="_blank" class="block group relative rounded-xl overflow-hidden border border-slate-200 bg-white">
                                    <img src="{{ asset('storage/' . $vehicleAnglePhotos['front']['path']) }}" class="w-full h-28 object-cover group-hover:scale-105 transition-transform duration-200" alt="Tampak Depan">
                                </a>
                            @else
                                <div class="w-full h-28 rounded-xl bg-slate-100 border border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-400 text-center p-2">
                                    <i data-lucide="image-off" class="w-6 h-6 mb-1 text-slate-300"></i>
                                    <span class="text-[10px]">Tidak Dilampirkan</span>
                                </div>
                            @endif
                        </div>

                        <!-- Samping Kanan -->
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-3 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-black text-slate-800">2. Samping Kanan</span>
                                <span class="text-[9px] font-bold text-[#015471] bg-[#015471]/10 px-1.5 py-0.5 rounded">Body Kanan</span>
                            </div>
                            @if(isset($vehicleAnglePhotos['right']))
                                <a href="{{ asset('storage/' . $vehicleAnglePhotos['right']['path']) }}" target="_blank" class="block group relative rounded-xl overflow-hidden border border-slate-200 bg-white">
                                    <img src="{{ asset('storage/' . $vehicleAnglePhotos['right']['path']) }}" class="w-full h-28 object-cover group-hover:scale-105 transition-transform duration-200" alt="Samping Kanan">
                                </a>
                            @else
                                <div class="w-full h-28 rounded-xl bg-slate-100 border border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-400 text-center p-2">
                                    <i data-lucide="image-off" class="w-6 h-6 mb-1 text-slate-300"></i>
                                    <span class="text-[10px]">Tidak Dilampirkan</span>
                                </div>
                            @endif
                        </div>

                        <!-- Samping Kiri -->
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-3 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-black text-slate-800">3. Samping Kiri</span>
                                <span class="text-[9px] font-bold text-[#015471] bg-[#015471]/10 px-1.5 py-0.5 rounded">Body Kiri</span>
                            </div>
                            @if(isset($vehicleAnglePhotos['left']))
                                <a href="{{ asset('storage/' . $vehicleAnglePhotos['left']['path']) }}" target="_blank" class="block group relative rounded-xl overflow-hidden border border-slate-200 bg-white">
                                    <img src="{{ asset('storage/' . $vehicleAnglePhotos['left']['path']) }}" class="w-full h-28 object-cover group-hover:scale-105 transition-transform duration-200" alt="Samping Kiri">
                                </a>
                            @else
                                <div class="w-full h-28 rounded-xl bg-slate-100 border border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-400 text-center p-2">
                                    <i data-lucide="image-off" class="w-6 h-6 mb-1 text-slate-300"></i>
                                    <span class="text-[10px]">Tidak Dilampirkan</span>
                                </div>
                            @endif
                        </div>

                        <!-- Belakang -->
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-3 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-black text-slate-800">4. Tampak Belakang</span>
                                <span class="text-[9px] font-bold text-[#015471] bg-[#015471]/10 px-1.5 py-0.5 rounded">Nopol</span>
                            </div>
                            @if(isset($vehicleAnglePhotos['behind']))
                                <a href="{{ asset('storage/' . $vehicleAnglePhotos['behind']['path']) }}" target="_blank" class="block group relative rounded-xl overflow-hidden border border-slate-200 bg-white">
                                    <img src="{{ asset('storage/' . $vehicleAnglePhotos['behind']['path']) }}" class="w-full h-28 object-cover group-hover:scale-105 transition-transform duration-200" alt="Tampak Belakang">
                                </a>
                            @else
                                <div class="w-full h-28 rounded-xl bg-slate-100 border border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-400 text-center p-2">
                                    <i data-lucide="image-off" class="w-6 h-6 mb-1 text-slate-300"></i>
                                    <span class="text-[10px]">Tidak Dilampirkan</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Dokumen Pendukung & Kwitansi Nota (Jika Ada) -->
            @if(count($generalDocs) > 0 || $maintenance->receipt_file)
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-[#015471] text-white flex items-center justify-center text-xs shadow-2xs">
                                <i data-lucide="paperclip" class="w-4 h-4 text-[#FF7A3D]"></i>
                            </div>
                            <div>
                                <h2 class="text-sm font-black text-slate-900">Dokumen Nota, Kwitansi & Berkas Pendukung</h2>
                                <p class="text-[11px] text-slate-500">Berkas fisik invoice atau bukti pembayaran bengkel</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @if($maintenance->receipt_file)
                                <div class="p-3.5 rounded-2xl bg-white border border-slate-200 flex items-center justify-between gap-3 shadow-2xs">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-9 h-9 rounded-xl bg-[#FF7A3D]/10 text-[#FF7A3D] flex items-center justify-center shrink-0">
                                            <i data-lucide="receipt" class="w-5 h-5"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="font-bold text-xs text-slate-900 block truncate">Kwitansi / Nota Pembayaran</span>
                                            <span class="text-[10px] text-slate-400 font-mono">Berkas Bukti Resmi</span>
                                        </div>
                                    </div>
                                    <a href="{{ asset('storage/' . $maintenance->receipt_file) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-[#015471] hover:bg-[#013e54] text-white text-[11px] font-bold transition shadow-2xs shrink-0">
                                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i> Buka
                                    </a>
                                </div>
                            @endif

                            @foreach($generalDocs as $doc)
                                <div class="p-3.5 rounded-2xl bg-white border border-slate-200 flex items-center justify-between gap-3 shadow-2xs">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-9 h-9 rounded-xl bg-[#015471]/10 text-[#015471] flex items-center justify-center shrink-0">
                                            <i data-lucide="file-text" class="w-5 h-5"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <span class="font-bold text-xs text-slate-900 block truncate" title="{{ $doc['name'] }}">{{ $doc['name'] }}</span>
                                            <span class="text-[10px] text-slate-400 uppercase font-mono">{{ pathinfo($doc['name'], PATHINFO_EXTENSION) }} Document</span>
                                        </div>
                                    </div>
                                    <a href="{{ asset('storage/' . $doc['path']) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-[#015471] hover:bg-[#013e54] text-white text-[11px] font-bold transition shadow-2xs shrink-0">
                                        <i data-lucide="download" class="w-3.5 h-3.5"></i> Unduh
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <!-- Kolom Kanan: Rincian Teknis, Personil & Kas Buku (4 Kolom) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Card Personil Lapangan -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center gap-2.5">
                    <i data-lucide="users" class="w-4 h-4 text-[#FF7A3D]"></i>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-900">Kontak & Personil Lapangan</h3>
                </div>

                <div class="p-5 space-y-4 text-xs">
                    <!-- Pengemudi -->
                    <div class="p-3 bg-slate-50/80 rounded-2xl border border-slate-100 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">Pengemudi / Pemegang Unit:</span>
                        <span class="font-bold text-slate-900 block text-xs">
                            {{ $maintenance->driver_name ?: ($maintenance->vehicle ? $maintenance->vehicle->user_holder : 'Pengemudi Lapangan') }}
                        </span>
                        @if($maintenance->driver_phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $maintenance->driver_phone) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-emerald-600 font-semibold hover:underline">
                                <i data-lucide="phone" class="w-3 h-3"></i> {{ $maintenance->driver_phone }}
                            </a>
                        @endif
                    </div>

                    <!-- PIC Pengaju -->
                    <div class="p-3 bg-slate-50/80 rounded-2xl border border-slate-100 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase block">PIC Pengaju / Penanggung Jawab:</span>
                        <span class="font-bold text-slate-900 block text-xs">
                            {{ $structuredData['pic_name'] ?: ($maintenance->pic_name ?: '-') }}
                        </span>
                        @if($structuredData['pic_phone'] || $maintenance->pic_phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $structuredData['pic_phone'] ?: $maintenance->pic_phone) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-emerald-600 font-semibold hover:underline">
                                <i data-lucide="phone" class="w-3 h-3"></i> {{ $structuredData['pic_phone'] ?: $maintenance->pic_phone }}
                            </a>
                        @endif
                    </div>

                    <!-- Lokasi Keberadaan & Catatan Fisik -->
                    @if($structuredData['unit_position'] || $structuredData['physical_notes'])
                        <div class="pt-3 border-t border-slate-100 space-y-2">
                            @if($structuredData['unit_position'])
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Posisi / Lokasi Motor:</span>
                                    <span class="font-bold text-slate-800 text-xs">{{ $structuredData['unit_position'] }}</span>
                                </div>
                            @endif

                            @if($structuredData['physical_notes'])
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase block">Catatan Fisik Tambahan:</span>
                                    <span class="font-medium text-slate-700 text-xs">{{ $structuredData['physical_notes'] }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card Informasi Buku Kas & No. Memo -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center gap-2.5">
                    <i data-lucide="book-open" class="w-4 h-4 text-[#FF7A3D]"></i>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-900">Sinkronisasi Kas Operasional</h3>
                </div>

                <div class="p-5 space-y-3.5 text-xs">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <span class="text-slate-500">No. Transaksi Kas:</span>
                        <span class="font-mono font-bold text-[#015471]">
                            {{ $expense ? $expense->expense_number : 'EXP-' . date('Ymd', strtotime($maintenance->date)) . '-' . str_pad($maintenance->id, 4, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <span class="text-slate-500">Nomor Memo:</span>
                        <span class="font-mono font-bold text-slate-800">
                            {{ $maintenance->memo_number ?: '-' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <span class="text-slate-500">Kategori Kas:</span>
                        <span class="font-bold text-slate-800 capitalize">
                            {{ $expense ? str_replace('_', ' ', $expense->category) : str_replace('_', ' ', $maintenance->type) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                        <span class="text-slate-500">Klien / Vendor Terkait:</span>
                        <span class="font-bold text-[#FF7A3D]">
                            {{ $maintenance->vehicle && $maintenance->vehicle->vendor ? $maintenance->vehicle->vendor->name : 'PT SUP Internal' }}
                        </span>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-[#015471]/5 border border-[#015471]/15 flex items-center justify-between mt-2">
                        <div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase block">Total Pengeluaran Kas:</span>
                            <span class="text-base font-black font-mono text-[#FF7A3D]">
                                Rp {{ number_format($maintenance->cost, 0, ',', '.') }}
                            </span>
                        </div>
                        <a href="{{ route('financial.index') }}" class="px-2.5 py-1.5 rounded-xl bg-white border border-slate-200 text-[#015471] hover:bg-[#015471] hover:text-white text-[11px] font-bold transition shadow-2xs">
                            Buku Kas &rarr;
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
