@extends('layouts.client')

@section('title', 'Status Pengajuan Realtime')

@section('content')
<div class="w-full space-y-6">

    <!-- Top Header -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#015471] to-[#023b4f] text-white flex items-center justify-center shadow-sm shrink-0">
                    <i data-lucide="activity" class="w-6 h-6 text-[#FF7A3D]"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-xl font-black text-slate-900 tracking-tight">Status Pengajuan & Laporan Realtime</h1>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Live Update
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Monitoring permohonan servis, unit pengganti, laporan kehilangan & bukti perbaikan armada</p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('client.requests.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-sm transition">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i> Buat Pengajuan Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Table & Filters Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        
        <!-- Table Toolbar & Filters -->
        <div class="p-4 sm:p-5 bg-slate-50/70 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <form id="filterForm" action="{{ route('client.requests.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <!-- Filter Status -->
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

                <!-- Filter Tipe Pengajuan -->
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
                        <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10 baris</option>
                        <option value="15" {{ ($perPage ?? 10) == 15 ? 'selected' : '' }}>15 baris</option>
                        <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25 baris</option>
                        <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50 baris</option>
                    </select>
                </div>
            </form>

            <div class="text-xs text-slate-500 font-medium">
                Total: <strong class="text-slate-900">{{ $requests->total() }}</strong> Pengajuan
            </div>
        </div>

        <!-- Table Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#e6f0f3] border-b border-slate-200 text-[#015471] font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-6 py-4">Nomor & Kendaraan</th>
                        <th class="px-6 py-4">Jenis & Judul Pengajuan</th>
                        <th class="px-6 py-4">Pengemudi / Bengkel</th>
                        <th class="px-6 py-4">Lampiran Bukti</th>
                        <th class="px-6 py-4">Status Progres</th>
                        <th class="px-6 py-4">Tanggal Pengajuan</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-800">
                    @forelse($requests as $req)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4">
                                <span class="font-mono font-bold text-slate-900 block">
                                    {{ $req->vehicle ? $req->vehicle->plate_number : '-' }}
                                </span>
                                <span class="text-[11px] text-slate-500">
                                    {{ $req->vehicle ? $req->vehicle->model_name : '' }}
                                </span>
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
                                @if(!empty($req->proof_files) && count($req->proof_files) > 0)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-[#015471] bg-[#015471]/10 px-2 py-1 rounded-lg">
                                        <i data-lucide="paperclip" class="w-3.5 h-3.5"></i>
                                        {{ count($req->proof_files) }} Berkas
                                    </span>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Tanpa Berkas</span>
                                @endif
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

                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('client.requests.show', $req) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-[#015471] text-white hover:bg-[#013e54] text-[11px] font-bold transition shadow-2xs">
                                    <span>Lihat Detail</span>
                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-slate-400">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                                Belum ada riwayat pengajuan permohonan.
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
