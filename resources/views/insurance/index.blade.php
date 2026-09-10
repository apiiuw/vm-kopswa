@extends('layouts.app')

@section('title', 'Asuransi & Klaim Kehilangan Kendaraan')
@section('page_title', 'Modul Asuransi & Laporan Klaim (TLO / Partial Loss)')

@section('content')
<div class="space-y-6 w-full bg-white text-black">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-black tracking-tight">Klaim Asuransi Kendaraan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Penanganan kasus kehilangan unit (TLO), kecelakaan, laporan polisi, dan pencairan ganti rugi asuransi</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('insurance.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-sm transition">
                <i data-lucide="plus" class="w-4 h-4"></i> Catat Kasus / Klaim Asuransi
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-sm">
            <span class="text-xs font-bold text-[#015471]">Total Kasus / Klaim</span>
            <span class="text-2xl font-black text-black block mt-1">{{ $stats['total_claims'] }}</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-sm">
            <span class="text-xs font-bold text-[#FF7A3D]">Total Nilai Pengajuan Klaim</span>
            <span class="text-2xl font-black text-[#FF7A3D] block mt-1">Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-sm">
            <span class="text-xs font-bold text-[#015471]">Klaim Dicairkan</span>
            <span class="text-2xl font-black text-[#015471] block mt-1">Rp {{ number_format($stats['settled_amount'], 0, ',', '.') }}</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-sm">
            <span class="text-xs font-bold text-amber-700">Sedang Diproses</span>
            <span class="text-2xl font-black text-amber-700 block mt-1">{{ $stats['pending_count'] }} Kasus</span>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border-2 border-slate-100 shadow-sm overflow-hidden space-y-0">
        
        <!-- Table Control Toolbar (Search Info & Per Page Dropdown) -->
        <div class="px-6 py-4 bg-white border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-[#015471]/10 text-[#015471] flex items-center justify-center font-bold text-xs">
                    <i data-lucide="shield-alert" class="w-4 h-4"></i>
                </span>
                <div>
                    <h3 class="text-xs font-black text-black">Daftar Berkas Klaim Asuransi & Insiden</h3>
                    <p class="text-[11px] text-slate-400">Total ditemukan <strong class="text-[#015471]">{{ $claims->total() }} kasus</strong> klaim</p>
                </div>
            </div>

            <!-- Per-Page Controls -->
            <div class="flex items-center gap-3">
                <form id="perPageForm" action="{{ route('insurance.index') }}" method="GET" class="flex items-center gap-2">
                    <label for="per_page_select" class="text-xs font-bold text-slate-600">Tampilkan:</label>
                    <select id="per_page_select" name="per_page" onchange="document.getElementById('perPageForm').submit()" class="text-xs font-bold px-3 py-1.5 rounded-xl border border-slate-300 focus:outline-[#015471] bg-white text-black cursor-pointer shadow-2xs">
                        <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10 Baris</option>
                        <option value="15" {{ ($perPage ?? 10) == 15 ? 'selected' : '' }}>15 Baris</option>
                        <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25 Baris</option>
                        <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50 Baris</option>
                        <option value="100" {{ ($perPage ?? 10) == 100 ? 'selected' : '' }}>100 Baris</option>
                    </select>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#e6f0f3] border-b border-slate-200 text-[#015471] font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">Tanggal Kejadian</th>
                        <th class="px-5 py-3.5">Plat Motor</th>
                        <th class="px-5 py-3.5">Jenis Klaim</th>
                        <th class="px-5 py-3.5">Surat Laporan Polisi & Blokir</th>
                        <th class="px-5 py-3.5">Pengemudi / Pemegang</th>
                        <th class="px-5 py-3.5">Nilai Klaim (Rp)</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-black">
                    @forelse($claims as $c)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3.5 font-medium text-slate-600 whitespace-nowrap">
                                {{ $c->incident_date->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3.5 font-bold text-black">
                                <a href="{{ route('vehicles.show', $c->vehicle) }}" class="hover:text-[#FF7A3D]">
                                    {{ $c->vehicle ? $c->vehicle->plate_number : '-' }}
                                </a>
                                <span class="block text-[10px] text-slate-500">{{ $c->vehicle && $c->vehicle->location ? $c->vehicle->location->name : '' }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-[#FF7A3D]/20 text-[#FF7A3D] uppercase">
                                    {{ str_replace('_', ' ', $c->claim_type) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-bold text-black block text-[11px]">{{ $c->police_report_number ?: 'LP: -' }}</span>
                                <span class="text-[10px] text-[#015471] font-mono font-bold">{{ $c->polda_blocking_number ?: 'Blokir: -' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-black">
                                {{ $c->driver_name ?: '-' }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-bold text-black block">Rp {{ number_format($c->claim_amount, 0, ',', '.') }}</span>
                                @if($c->settlement_amount > 0)
                                    <span class="text-[10px] text-[#015471] font-bold block">Cair: Rp {{ number_format($c->settlement_amount, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#e6f0f3] text-[#015471] uppercase">
                                    {{ $c->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <form id="delete-claim-{{ $c->id }}" action="{{ route('insurance.destroy', $c) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete('delete-claim-{{ $c->id }}', 'catatan klaim ini')" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-slate-100 transition" title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-400">
                                Belum ada catatan klaim asuransi atau kehilangan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($claims->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-white">
                {{ $claims->links() }}
            </div>
        @else
            <div class="px-6 py-3 border-t border-slate-100 bg-white text-xs text-slate-500 text-right">
                Menampilkan seluruh <strong class="text-black">{{ $claims->total() }}</strong> berkas klaim
            </div>
        @endif
    </div>

</div>
@endsection
