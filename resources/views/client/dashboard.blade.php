@extends('layouts.client')

@section('title', 'Dashboard Portal Klien')

@section('content')
<div class="space-y-8">

    <!-- Welcome Hero Banner -->
    <div class="bg-gradient-to-r from-[#015471] via-[#024157] to-[#015471] rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-[#FF7A3D]/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-white/10 text-cyan-200 border border-white/15">
                    <span class="w-2 h-2 rounded-full bg-[#FF7A3D] animate-ping"></span>
                    <span>Portal Layanan Klien Mandiri</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                    Selamat Datang, {{ Auth::user()->name }}
                </h1>
                <p class="text-xs sm:text-sm text-cyan-100 max-w-2xl leading-relaxed">
                    Ajukan permohonan servis berkala, klaim kehilangan (TLO), unit pengganti sementara, serta pantau status pengerjaan armada sewa Anda secara real-time.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <a href="{{ route('client.requests.create') }}" class="inline-flex items-center gap-2 px-5 py-3 text-xs font-extrabold rounded-2xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-md transition transform hover:-translate-y-0.5">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Buat Pengajuan / Laporan Baru</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Action Shortcut Cards (4 Action Tiles) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Shortcut 1: Servis / Perbaikan -->
        <a href="{{ route('client.requests.create', ['type' => 'perbaikan_servis']) }}" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs hover:shadow-md hover:border-[#015471] transition group">
            <div class="w-12 h-12 rounded-2xl bg-[#015471]/10 text-[#015471] group-hover:bg-[#015471] group-hover:text-white transition flex items-center justify-center mb-4">
                <i data-lucide="wrench" class="w-6 h-6 text-[#FF7A3D]"></i>
            </div>
            <h2 class="text-sm font-bold text-slate-900 group-hover:text-[#015471] transition">Ajukan Perbaikan / Servis</h2>
            <p class="text-xs text-slate-500 mt-1">Lapor kerusakan mesin, ganti oli, ban & sparepart motor sewa</p>
        </a>

        <!-- Shortcut 2: Unit Pengganti -->
        <a href="{{ route('client.requests.create', ['type' => 'unit_pengganti']) }}" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs hover:shadow-md hover:border-[#015471] transition group">
            <div class="w-12 h-12 rounded-2xl bg-[#FF7A3D]/10 text-[#FF7A3D] group-hover:bg-[#FF7A3D] group-hover:text-white transition flex items-center justify-center mb-4">
                <i data-lucide="refresh-cw" class="w-6 h-6"></i>
            </div>
            <h2 class="text-sm font-bold text-slate-900 group-hover:text-[#FF7A3D] transition">Unit Pengganti Sementara</h2>
            <p class="text-xs text-slate-500 mt-1">Permohonan drop motor cadangan jika armada servis > 24 jam</p>
        </a>

        <!-- Shortcut 3: Lapor Kehilangan (TLO) -->
        <a href="{{ route('client.requests.create', ['type' => 'lapor_kehilangan']) }}" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs hover:shadow-md hover:border-rose-500 transition group">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 group-hover:bg-rose-600 group-hover:text-white transition flex items-center justify-center mb-4">
                <i data-lucide="shield-alert" class="w-6 h-6"></i>
            </div>
            <h2 class="text-sm font-bold text-slate-900 group-hover:text-rose-600 transition">Lapor Kehilangan (TLO)</h2>
            <p class="text-xs text-slate-500 mt-1">Input insiden kehilangan unit & berkas LP Polsek/Polres</p>
        </a>

        <!-- Shortcut 4: Input Bukti Selesai Perbaikan -->
        <a href="{{ route('client.requests.create', ['type' => 'input_hasil_perbaikan']) }}" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs hover:shadow-md hover:border-emerald-500 transition group">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition flex items-center justify-center mb-4">
                <i data-lucide="file-check" class="w-6 h-6"></i>
            </div>
            <h2 class="text-sm font-bold text-slate-900 group-hover:text-emerald-600 transition">Input Bukti Selesai Servis</h2>
            <p class="text-xs text-slate-500 mt-1">Upload nota bengkel rekanan & foto motor siap beroperasi</p>
        </a>
    </div>

    <!-- Status Overview Counters (Armada & Pengajuan) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <span class="text-xs font-semibold text-slate-500">Total Armada Disewa</span>
            <span class="text-2xl font-black text-slate-900 block mt-1">{{ $totalVehicles }} Unit</span>
            <span class="text-[11px] text-emerald-600 font-bold mt-1 block">&bull; {{ $activeVehicles }} Unit Siap Pakai</span>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <span class="text-xs font-semibold text-slate-500">Sedang Diservis / Bengkel</span>
            <span class="text-2xl font-black text-[#FF7A3D] block mt-1">{{ $maintenanceVehicles }} Unit</span>
            <span class="text-[11px] text-slate-400 mt-1 block">Dalam pemeliharaan</span>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <span class="text-xs font-semibold text-slate-500">Pengajuan Menunggu Review</span>
            <span class="text-2xl font-black text-amber-600 block mt-1">{{ $pendingRequests }}</span>
            <span class="text-[11px] text-slate-400 mt-1 block">Tim SUP akan memproses</span>
        </div>

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-2xs">
            <span class="text-xs font-semibold text-slate-500">Pengajuan Selesai</span>
            <span class="text-2xl font-black text-emerald-600 block mt-1">{{ $completedRequests }}</span>
            <span class="text-[11px] text-slate-400 mt-1 block">Dari {{ $totalRequests }} permohonan</span>
        </div>
    </div>

    <!-- 2 Column Master Split (Left: Realtime Live Status Tracker, Right: Unit Dalam Perbaikan) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- LEFT: Realtime Status Pengajuan (7 Cols) -->
        <div class="lg:col-span-7 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></div>
                        <div>
                            <h2 class="text-sm font-black text-slate-900">Status Pengajuan Realtime</h2>
                            <p class="text-[11px] text-slate-500">Live monitoring respon penanganan dari PT SUP</p>
                        </div>
                    </div>
                    <a href="{{ route('client.requests.index') }}" class="text-xs font-bold text-[#015471] hover:underline flex items-center gap-1">
                        <span>Lihat Semua</span>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                <div id="realtimeRequestsContainer" class="divide-y divide-slate-100">
                    @forelse($recentRequests as $req)
                        <div class="p-5 hover:bg-slate-50/80 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-1.5 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-xs font-mono font-bold text-[#015471] bg-[#015471]/10 px-2 py-0.5 rounded">
                                        {{ $req->vehicle ? $req->vehicle->plate_number : 'Unit' }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-900">{{ $req->title }}</span>
                                </div>
                                <p class="text-xs text-slate-500 line-clamp-1">{{ $req->description }}</p>
                                <div class="flex items-center gap-3 text-[11px] text-slate-400">
                                    <span>{{ $req->request_type_label }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $req->created_at->diffForHumans() }}</span>
                                </div>
                            </div>

                            <div class="flex sm:flex-col items-center sm:items-end justify-between gap-2 shrink-0">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold border {{ $req->status_badge['bg'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $req->status_badge['dot'] }}"></span>
                                    {{ $req->status_badge['label'] }}
                                </span>
                                <a href="{{ route('client.requests.show', $req) }}" class="text-xs font-bold text-[#FF7A3D] hover:underline">
                                    Detail & Bukti &rarr;
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            <i data-lucide="inbox" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                            Belum ada pengajuan perbaikan atau laporan yang dibuat.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- RIGHT: Armada Motor Sedang Dalam Perbaikan / Bengkel (5 Cols) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-black text-slate-900">Armada Sedang Pemeliharaan</h2>
                        <p class="text-[11px] text-slate-500">Unit yang sedang berada di bengkel rekanan</p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#FF7A3D]/10 text-[#FF7A3D]">
                        {{ $vehiclesUnderRepair->count() }} Unit
                    </span>
                </div>

                <div class="divide-y divide-slate-100 max-h-[420px] overflow-y-auto">
                    @forelse($vehiclesUnderRepair as $v)
                        <div class="p-4 flex items-center justify-between gap-3 hover:bg-slate-50 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs shrink-0">
                                    <i data-lucide="wrench" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <span class="block text-xs font-bold text-slate-900">{{ $v->plate_number }}</span>
                                    <span class="text-[11px] text-slate-500">{{ $v->brand ? $v->brand . ' ' : '' }}{{ $v->model_name }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $v->location ? $v->location->name : 'Lokasi Kebun' }}</span>
                                </div>
                            </div>

                            <a href="{{ route('client.requests.create', ['vehicle_id' => $v->id, 'type' => 'input_hasil_perbaikan']) }}" class="px-3 py-1.5 text-[11px] font-bold rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition shrink-0">
                                + Input Bukti Selesai
                            </a>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            <i data-lucide="check-circle-2" class="w-8 h-8 mx-auto text-emerald-400 mb-2"></i>
                            Semua unit armada dalam kondisi aktif dan siap operasional.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    // Realtime polling update for client requests every 15 seconds
    setInterval(function() {
        fetch("{{ route('client.requests.realtime') }}")
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    // Update live indicator timestamp if needed
                }
            })
            .catch(err => console.log('Polling error', err));
    }, 15000);
</script>
@endpush
