@extends('layouts.client')

@section('title', 'Daftar Armada Motor Disewa')

@section('content')
<div class="w-full space-y-6">

    <!-- Top Header -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#015471] to-[#023b4f] text-white flex items-center justify-center shadow-sm shrink-0">
                    <i data-lucide="bike" class="w-6 h-6 text-[#FF7A3D]"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-xl font-black text-slate-900 tracking-tight">Daftar Armada Motor Disewa</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#015471]/10 text-[#015471] border border-[#015471]/20">
                            Unit Mitra
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Monitoring unit motor yang sedang beroperasi di wilayah kerja Anda</p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('client.requests.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-sm transition">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i> Buat Pengajuan Servis
                </a>
            </div>
        </div>
    </div>

    <!-- Table & Search Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        
        <!-- Table Toolbar & Search Filters -->
        <div class="p-4 sm:p-5 bg-slate-50/70 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <form id="filterForm" action="{{ route('client.vehicles.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                <!-- Search Input -->
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor plat / tipe motor..." class="text-xs font-semibold pl-9 pr-3.5 py-2 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 w-56 sm:w-64">
                </div>

                <!-- Status Filter -->
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-semibold text-slate-600">Status:</span>
                    <select name="status" onchange="document.getElementById('filterForm').submit()" class="text-xs font-bold bg-white border border-slate-300 text-slate-800 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-[#015471] focus:outline-hidden cursor-pointer shadow-2xs">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif / Siap Pakai</option>
                        <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Dalam Perbaikan / Servis</option>
                        <option value="replaced" {{ request('status') === 'replaced' ? 'selected' : '' }}>Unit Pengganti Aktif</option>
                        <option value="lost" {{ request('status') === 'lost' ? 'selected' : '' }}>Hilang (TLO)</option>
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
                Total: <strong class="text-slate-900">{{ $vehicles->total() }}</strong> Unit Armada
            </div>
        </div>

        <!-- Table Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#e6f0f3] border-b border-slate-200 text-[#015471] font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-6 py-4">Nomor Polisi</th>
                        <th class="px-6 py-4">Merk & Tipe Kendaraan</th>
                        <th class="px-6 py-4">Lokasi Penempatan</th>
                        <th class="px-6 py-4">Tahun / Warna</th>
                        <th class="px-6 py-4">Status Kendaraan</th>
                        <th class="px-6 py-4 text-right">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-800">
                    @forelse($vehicles as $v)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4">
                                <span class="font-mono font-bold text-slate-900 block text-xs bg-slate-100 px-2.5 py-1 rounded-lg w-max border border-slate-200">
                                    {{ $v->plate_number }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-900 block">{{ $v->brand ? $v->brand . ' ' : '' }}{{ $v->model_name }}</span>
                                <span class="text-[11px] text-slate-500">{{ $v->category ? ucfirst($v->category) : 'Motor' }}</span>
                            </td>

                            <td class="px-6 py-4 text-slate-600 font-medium">
                                <div class="flex items-center gap-1.5">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                                    <span>{{ $v->location ? $v->location->name : 'Kantor/Kebun' }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-slate-600">
                                <span>{{ $v->year ?: '-' }}</span> &bull; <span class="capitalize">{{ $v->color ?: '-' }}</span>
                            </td>

                            <td class="px-6 py-4">
                                @if($v->status === 'active')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Siap Pakai
                                    </span>
                                @elseif($v->status === 'maintenance')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Sedang Servis
                                    </span>
                                @elseif($v->status === 'replaced')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span> Unit Pengganti
                                    </span>
                                @elseif($v->status === 'lost')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Hilang (TLO)
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                @if($v->status === 'lost')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-400 text-[11px] font-bold cursor-not-allowed border border-slate-200" title="Unit ini berstatus hilang dan tidak dapat diajukan servis/lapor">
                                        <i data-lucide="ban" class="w-3.5 h-3.5 text-rose-400"></i>
                                        <span>Unit Hilang (TLO)</span>
                                    </span>
                                @else
                                    <a href="{{ route('client.requests.create', ['vehicle_id' => $v->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#015471] text-white hover:bg-[#013e54] text-[11px] font-bold transition shadow-2xs">
                                        <i data-lucide="wrench" class="w-3.5 h-3.5 text-[#FF7A3D]"></i>
                                        <span>Ajukan Servis / Lapor</span>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-slate-400">
                                <i data-lucide="bike" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                                Tidak ditemukan data armada kendaraan yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vehicles->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-white">
                {{ $vehicles->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
