@extends('layouts.app')

@section('title', 'Manajemen Pengajuan & Tiket Klien')
@section('page_title', 'Layanan & Permohonan Klien')

@section('content')
<div class="w-full space-y-6">

    <!-- Top Header Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#015471] to-[#023b4f] text-white flex items-center justify-center shadow-sm shrink-0">
                    <i data-lucide="inbox" class="w-6 h-6 text-[#FF7A3D]"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-xl font-black text-slate-900 tracking-tight">Manajemen Tiket & Pengajuan Klien Mitra</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#FF7A3D]/10 text-[#FF7A3D] border border-[#FF7A3D]/20">
                            Helpdesk & Servis
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar permohonan servis berkala, unit pengganti sementara, laporan kehilangan, dan verifikasi bukti perbaikan dari portal klien</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards (4 Columns) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-500">Total Permohonan</span>
                <span class="text-2xl font-black text-slate-900 block mt-1">{{ $stats['total'] }}</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Semua tiket masuk</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-[#015471]/10 text-[#015471] flex items-center justify-center">
                <i data-lucide="inbox" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-amber-600">Menunggu Review</span>
                <span class="text-2xl font-black text-amber-600 block mt-1">{{ $stats['pending'] }}</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Perlu verifikasi admin</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-blue-600">Sedang Ditangani</span>
                <span class="text-2xl font-black text-blue-600 block mt-1">{{ $stats['processing'] }}</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Proses bengkel / kirim</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <i data-lucide="refresh-cw" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-emerald-600">Selesai / Disetujui</span>
                <span class="text-2xl font-black text-emerald-600 block mt-1">{{ $stats['completed'] }}</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Kasus tuntas</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="check-circle-2" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        
        <!-- Table Toolbar & Filters -->
        <div class="p-4 sm:p-5 bg-slate-50/70 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <form id="filterForm" action="{{ route('admin.client-requests.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <!-- Search Input -->
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari plat / judul / kronologis..." class="text-xs font-semibold pl-9 pr-3.5 py-2 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 w-56 sm:w-64">
                </div>

                <!-- Status Filter -->
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-semibold text-slate-600">Status:</span>
                    <select name="status" onchange="document.getElementById('filterForm').submit()" class="text-xs font-bold bg-white border border-slate-300 text-slate-800 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-[#015471] focus:outline-hidden cursor-pointer shadow-2xs">
                        <option value="">Semua Status</option>
                        <option value="menunggu_review" {{ request('status') === 'menunggu_review' ? 'selected' : '' }}>Menunggu Review</option>
                        <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                        <option value="unit_dikirim" {{ request('status') === 'unit_dikirim' ? 'selected' : '' }}>Unit Dikirim</option>
                        <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai / Disetujui</option>
                        <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <!-- Filter Tipe -->
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-semibold text-slate-600">Jenis:</span>
                    <select name="request_type" onchange="document.getElementById('filterForm').submit()" class="text-xs font-bold bg-white border border-slate-300 text-slate-800 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-[#015471] focus:outline-hidden cursor-pointer shadow-2xs">
                        <option value="">Semua Jenis</option>
                        <option value="perbaikan_servis" {{ request('request_type') === 'perbaikan_servis' ? 'selected' : '' }}>Perbaikan / Servis</option>
                        <option value="unit_pengganti" {{ request('request_type') === 'unit_pengganti' ? 'selected' : '' }}>Unit Pengganti</option>
                        <option value="lapor_kehilangan" {{ request('request_type') === 'lapor_kehilangan' ? 'selected' : '' }}>Lapor Kehilangan</option>
                    </select>
                </div>

                <!-- Per-Page Selector -->
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-semibold text-slate-600">Tampilkan:</span>
                    <select name="per_page" onchange="document.getElementById('filterForm').submit()" class="text-xs font-bold bg-white border border-slate-300 text-slate-800 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-[#015471] focus:outline-hidden cursor-pointer shadow-2xs">
                        <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10 per halaman</option>
                        <option value="15" {{ ($perPage ?? 10) == 15 ? 'selected' : '' }}>15 per halaman</option>
                        <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25 per halaman</option>
                        <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50 per halaman</option>
                        <option value="100" {{ ($perPage ?? 10) == 100 ? 'selected' : '' }}>100 per halaman</option>
                    </select>
                </div>
            </form>

            <div class="text-xs text-slate-500 font-medium">
                Total: <strong class="text-slate-900">{{ $requests->total() }}</strong> Tiket Masuk
            </div>
        </div>

        <!-- Table Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#e6f0f3] border-b border-slate-200 text-[#015471] font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-6 py-4">Klien / Pemohon</th>
                        <th class="px-6 py-4">Armada Kendaraan</th>
                        <th class="px-6 py-4">Jenis & Judul Pengajuan</th>
                        <th class="px-6 py-4">Pengemudi / Bengkel</th>
                        <th class="px-6 py-4">Status Progres</th>
                        <th class="px-6 py-4">Waktu Pengajuan</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-800">
                    @forelse($requests as $req)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-bold text-slate-900">
                                <span class="block text-slate-900 font-bold">{{ $req->vendor ? $req->vendor->name : 'Klien Mitra' }}</span>
                                <span class="text-[11px] text-slate-500 font-medium">{{ $req->user ? $req->user->name : '-' }}</span>
                            </td>

                            <td class="px-6 py-4">
                                <span class="font-mono font-bold text-slate-900 block bg-slate-100 px-2 py-0.5 rounded w-max border border-slate-200">
                                    {{ $req->vehicle ? $req->vehicle->plate_number : '-' }}
                                </span>
                                <span class="text-[11px] text-slate-500">{{ $req->vehicle ? $req->vehicle->model_name : '' }}</span>
                            </td>

                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-900 block">{{ $req->title }}</span>
                                <span class="text-[11px] font-semibold text-[#015471]">{{ $req->request_type_label }}</span>
                            </td>

                            <td class="px-6 py-4 text-slate-600">
                                <span class="font-medium text-slate-900 block">{{ $req->driver_name ?: '-' }}</span>
                                <span class="text-[11px] text-slate-500">{{ $req->workshop_name ?: 'Bengkel Rekanan' }}</span>
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold border {{ $req->status_badge['bg'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $req->status_badge['dot'] }}"></span>
                                    {{ $req->status_badge['label'] }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-slate-500 font-medium">
                                {{ $req->created_at->format('d M Y, H:i') }}
                            </td>

                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('admin.client-requests.show', $req) }}" class="group inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#015471] hover:bg-[#FF7A3D] text-white text-xs font-bold transition-all duration-150 shadow-2xs hover:shadow-sm hover:-translate-y-0.5">
                                    <span>Tindak Lanjut</span>
                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-400">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                                Belum ada permohonan tiket pengajuan dari klien mitra.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-white">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
