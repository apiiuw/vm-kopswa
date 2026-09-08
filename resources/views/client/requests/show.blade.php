@extends('layouts.client')

@section('title', 'Detail Pengajuan & Respon Tim SUP')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('client.requests.index') }}" class="p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-base font-black text-slate-900">Rincian Pengajuan #REQ-{{ str_pad($clientRequest->id, 4, '0', STR_PAD_LEFT) }}</h1>
                <span class="text-xs text-slate-500">Diajukan pada {{ $clientRequest->created_at->format('d F Y, H:i') }}</span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border {{ $clientRequest->status_badge['bg'] }}">
                <span class="w-2 h-2 rounded-full {{ $clientRequest->status_badge['dot'] }}"></span>
                {{ $clientRequest->status_badge['label'] }}
            </span>
        </div>
    </div>

    <!-- 2 Column Master Card -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Column: Rincian & Deskripsi (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Card 1: Detail Pengajuan -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
                <div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#015471]/10 text-[#015471] mb-2">
                        {{ $clientRequest->request_type_label }}
                    </span>
                    <h2 class="text-lg font-black text-slate-900">{{ $clientRequest->title }}</h2>
                </div>

                <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                    <span class="text-xs font-bold text-slate-700 block mb-1">Uraian & Kronologis Masalah:</span>
                    <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line">{{ $clientRequest->description }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/50">
                        <span class="text-slate-400 block text-[11px]">Pengemudi / Pemakai:</span>
                        <span class="font-bold text-slate-900 block mt-0.5">{{ $clientRequest->driver_name ?: '-' }}</span>
                        <span class="text-[11px] text-slate-500">{{ $clientRequest->driver_phone ?: '' }}</span>
                    </div>
                    <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/50">
                        <span class="text-slate-400 block text-[11px]">Bengkel / Lokasi:</span>
                        <span class="font-bold text-slate-900 block mt-0.5">{{ $clientRequest->workshop_name ?: 'Bengkel Rekanan' }}</span>
                        <span class="text-[11px] text-slate-500">{{ $clientRequest->odometer_km ? 'Odo: ' . number_format($clientRequest->odometer_km) . ' KM' : '' }}</span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Bukti & Lampiran Berkas -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center gap-2">
                    <i data-lucide="paperclip" class="w-4 h-4 text-[#FF7A3D]"></i>
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Lampiran Bukti & Foto</h3>
                </div>

                @if(!empty($clientRequest->proof_files) && count($clientRequest->proof_files) > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($clientRequest->proof_files as $file)
                            <a href="{{ asset('storage/' . $file['path']) }}" target="_blank" class="p-3 rounded-xl border border-slate-200 hover:border-[#015471] bg-slate-50 hover:bg-white transition flex items-center gap-3 group">
                                <div class="w-9 h-9 rounded-lg bg-[#015471]/10 text-[#015471] flex items-center justify-center shrink-0">
                                    <i data-lucide="file-image" class="w-4 h-4"></i>
                                </div>
                                <div class="overflow-hidden flex-1">
                                    <span class="text-xs font-bold text-slate-900 group-hover:text-[#015471] block truncate">{{ $file['name'] ?? 'Berkas Lampiran' }}</span>
                                    <span class="text-[10px] text-slate-400">Klik untuk melihat file</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400 italic">Tidak ada berkas lampiran yang diunggah.</p>
                @endif
            </div>

        </div>

        <!-- Right Column: Status Timeline & Respon Admin PT SUP (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Card 3: Respon & Tanggapan PT SUP -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center gap-2 text-[#015471]">
                    <i data-lucide="message-square" class="w-4 h-4 text-[#FF7A3D]"></i>
                    <h3 class="text-xs font-bold uppercase tracking-wider">Respon Tim Operasional PT SUP</h3>
                </div>

                @if($clientRequest->admin_response)
                    <div class="p-4 rounded-xl bg-blue-50/70 border border-blue-200 text-xs text-blue-900 space-y-1.5">
                        <span class="font-bold block">Tanggapan Admin:</span>
                        <p class="leading-relaxed">{{ $clientRequest->admin_response }}</p>
                    </div>
                @else
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 flex items-start gap-2.5">
                        <i data-lucide="clock" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"></i>
                        <div>
                            <span class="font-bold block">Sedang Menunggu Review:</span>
                            Tim helpdesk & teknis PT SUP akan segera memeriksa laporan ini dan memberikan status tindak lanjut.
                        </div>
                    </div>
                @endif
            </div>

            <!-- Card 4: Informasi Armada Motor -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center gap-2 text-[#015471]">
                    <i data-lucide="bike" class="w-4 h-4 text-[#FF7A3D]"></i>
                    <h3 class="text-xs font-bold uppercase tracking-wider">Informasi Kendaraan</h3>
                </div>

                @if($clientRequest->vehicle)
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between border-b border-slate-100 pb-2">
                            <span class="text-slate-400">Nomor Plat:</span>
                            <span class="font-bold font-mono text-slate-900">{{ $clientRequest->vehicle->plate_number }}</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-100 pb-2">
                            <span class="text-slate-400">Model Kendaraan:</span>
                            <span class="font-bold text-slate-900">{{ $clientRequest->vehicle->model_name }}</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-100 pb-2">
                            <span class="text-slate-400">Lokasi Penempatan:</span>
                            <span class="font-bold text-slate-900">{{ $clientRequest->vehicle->location ? $clientRequest->vehicle->location->name : '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Status Terkini:</span>
                            <span class="font-bold uppercase text-xs {{ $clientRequest->vehicle->status === 'active' ? 'text-emerald-600' : 'text-[#FF7A3D]' }}">
                                {{ $clientRequest->vehicle->status }}
                            </span>
                        </div>
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>
@endsection
