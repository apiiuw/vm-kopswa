@extends('layouts.app')

@section('title', 'Tindak Lanjut Tiket Pengajuan Klien')
@section('page_title', 'Detail Permohonan & Tanggapan Admin')

@section('content')
<div class="w-full space-y-6">

    <!-- Top Action Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.client-requests.index') }}" class="p-2.5 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-lg font-black text-slate-900">Tiket Permohonan #REQ-{{ str_pad($clientRequest->id, 4, '0', STR_PAD_LEFT) }}</h1>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#015471]/10 text-[#015471]">
                        {{ $clientRequest->vendor ? $clientRequest->vendor->name : 'Klien Mitra' }}
                    </span>
                </div>
                <span class="text-xs text-slate-500">Diajukan oleh <strong>{{ $clientRequest->user ? $clientRequest->user->name : '-' }}</strong> pada {{ $clientRequest->created_at->format('d F Y, H:i') }}</span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border {{ $clientRequest->status_badge['bg'] }}">
                <span class="w-2 h-2 rounded-full {{ $clientRequest->status_badge['dot'] }}"></span>
                {{ $clientRequest->status_badge['label'] }}
            </span>
        </div>
    </div>

    <!-- 2 Column Master Split (Left: Data Permohonan & Bukti, Right: Form Update Status & Respon Admin) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- LEFT COLUMN: Rincian Pengajuan & Lampiran Bukti (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">

            <!-- Card 1: Data Pengajuan & Armada -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
                <div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#015471]/10 text-[#015471] mb-2">
                        {{ $clientRequest->request_type_label }}
                    </span>
                    <h2 class="text-lg font-black text-slate-900">{{ $clientRequest->title }}</h2>
                </div>

                <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                    <span class="text-xs font-bold text-slate-700 block mb-1">Uraian & Kronologis Masalah Lapangan:</span>
                    <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line">{{ $clientRequest->description }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/50">
                        <span class="text-slate-400 block text-[11px]">Pengemudi / Pemakai:</span>
                        <span class="font-bold text-slate-900 block mt-0.5">{{ $clientRequest->driver_name ?: '-' }}</span>
                        <span class="text-[11px] text-slate-500">{{ $clientRequest->driver_phone ?: '' }}</span>
                    </div>
                    <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/50">
                        <span class="text-slate-400 block text-[11px]">Bengkel Rekanan & Odometer:</span>
                        <span class="font-bold text-slate-900 block mt-0.5">{{ $clientRequest->workshop_name ?: 'Bengkel Rekanan' }}</span>
                        <span class="text-[11px] text-slate-500">{{ $clientRequest->odometer_km ? 'Odometer: ' . number_format($clientRequest->odometer_km) . ' KM' : '' }}</span>
                    </div>
                </div>

                @if($clientRequest->estimated_cost > 0)
                    <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-xs">
                        <span class="font-bold text-emerald-900">Estimasi / Biaya Riil Dilaporkan:</span>
                        <span class="font-mono font-black text-emerald-800 text-sm">Rp {{ number_format($clientRequest->estimated_cost, 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>

            <!-- Card 2: Berkas Lampiran Bukti & Foto -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center gap-2">
                    <i data-lucide="paperclip" class="w-4 h-4 text-[#FF7A3D]"></i>
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Berkas Lampiran Foto & Kuitansi Bukti</h3>
                </div>

                @if(!empty($clientRequest->proof_files) && count($clientRequest->proof_files) > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($clientRequest->proof_files as $file)
                            <a href="{{ asset('storage/' . $file['path']) }}" target="_blank" class="p-3.5 rounded-xl border border-slate-200 hover:border-[#015471] bg-slate-50 hover:bg-white transition flex items-center gap-3 group">
                                <div class="w-10 h-10 rounded-lg bg-[#015471]/10 text-[#015471] flex items-center justify-center shrink-0">
                                    <i data-lucide="file-image" class="w-5 h-5"></i>
                                </div>
                                <div class="overflow-hidden flex-1">
                                    <span class="text-xs font-bold text-slate-900 group-hover:text-[#015471] block truncate">{{ $file['name'] ?? 'Berkas Lampiran' }}</span>
                                    <span class="text-[10px] text-slate-400">Klik untuk buka berkas &rarr;</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400 italic">Klien tidak melampirkan berkas foto / nota pada tiket ini.</p>
                @endif
            </div>

        </div>

        <!-- RIGHT COLUMN: Form Respon & Tindak Lanjut Admin (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">

            <!-- Card 3: Form Tindak Lanjut Admin -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-[#FF7A3D] text-white text-xs font-black flex items-center justify-center shadow-2xs">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                        </span>
                        <div>
                            <h2 class="text-sm font-black text-slate-900">Tindak Lanjut & Respon Admin</h2>
                            <p class="text-[11px] text-slate-500">Perbarui status tiket & kirim tanggapan ke klien</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.client-requests.updateStatus', $clientRequest) }}" method="POST" class="p-6 space-y-4 text-xs">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">Status Penanganan Tiket <span class="text-[#FF7A3D]">*</span></label>
                        <select name="status" required class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer">
                            <option value="menunggu_review" {{ old('status', $clientRequest->status) === 'menunggu_review' ? 'selected' : '' }}>Menunggu Review</option>
                            <option value="diproses" {{ old('status', $clientRequest->status) === 'diproses' ? 'selected' : '' }}>Sedang Diproses (Koordinasi Bengkel / Tim)</option>
                            <option value="unit_dikirim" {{ old('status', $clientRequest->status) === 'unit_dikirim' ? 'selected' : '' }}>Unit Pengganti Dikirim ke Lokasi</option>
                            <option value="selesai" {{ old('status', $clientRequest->status) === 'selesai' ? 'selected' : '' }}>Selesai / Disetujui</option>
                            <option value="ditolak" {{ old('status', $clientRequest->status) === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">Catatan / Tanggapan untuk Klien</label>
                        <textarea name="admin_response" rows="4" placeholder="Tuliskan konfirmasi mekanik, nomor resi pengiriman unit pengganti, atau estimasi selesai servis..." class="w-full text-xs p-3.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 leading-relaxed">{{ old('admin_response', $clientRequest->admin_response) }}</textarea>
                    </div>

                    <!-- Auto Sync Option to Maintenance Module -->
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="checkbox" name="sync_to_maintenance" value="1" checked class="mt-0.5 rounded text-[#015471] focus:ring-[#015471]">
                            <div>
                                <span class="font-bold text-slate-900 block">Otomatis Sinkron ke Buku Servis</span>
                                <span class="text-[11px] text-slate-500">Jika status diset Selesai, catat otomatis ke riwayat Servis & Pemeliharaan dan pulihkan status motor jadi Aktif.</span>
                            </div>
                        </label>
                    </div>

                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-sm transition">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Simpan Status & Respon</span>
                    </button>
                </form>
            </div>

            <!-- Card 4: Info Armada Terkait -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-3 text-xs">
                <div class="flex items-center gap-2 text-[#015471]">
                    <i data-lucide="bike" class="w-4 h-4 text-[#FF7A3D]"></i>
                    <h3 class="font-bold uppercase tracking-wider text-xs">Armada Terkait</h3>
                </div>

                @if($clientRequest->vehicle)
                    <div class="space-y-2">
                        <div class="flex justify-between border-b border-slate-100 pb-2">
                            <span class="text-slate-400">Nomor Plat:</span>
                            <span class="font-bold font-mono text-slate-900">{{ $clientRequest->vehicle->plate_number }}</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-100 pb-2">
                            <span class="text-slate-400">Merk & Tipe:</span>
                            <span class="font-bold text-slate-900">{{ $clientRequest->vehicle->brand }} {{ $clientRequest->vehicle->model_name }}</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-100 pb-2">
                            <span class="text-slate-400">Lokasi Penempatan:</span>
                            <span class="font-bold text-slate-900">{{ $clientRequest->vehicle->location ? $clientRequest->vehicle->location->name : '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Status Kendaraan:</span>
                            <span class="font-bold uppercase {{ $clientRequest->vehicle->status === 'active' ? 'text-emerald-600' : 'text-[#FF7A3D]' }}">
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
