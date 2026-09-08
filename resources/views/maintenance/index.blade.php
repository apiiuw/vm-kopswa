@extends('layouts.app')

@section('title', 'Pemeliharaan, Servis & Penggantian Suku Cadang')
@section('page_title', 'Modul Pemeliharaan & Operasional Armada')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto bg-white text-black">

    <!-- Top Summary Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-black tracking-tight">Catatan Pemeliharaan Armada</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pemantauan biaya servis berkala, ganti ban, aki, sparepart, dan motor pengganti</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('maintenance.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-sm transition">
                <i data-lucide="plus" class="w-4 h-4"></i> Catat Pemeliharaan / Servis
            </a>
        </div>
    </div>

    <!-- Category Stat Highlights -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 text-xs">
        <div class="bg-white p-3.5 rounded-xl border-2 border-slate-100 shadow-2xs">
            <span class="text-slate-500 block text-[10px] uppercase font-bold">Total Biaya Riil</span>
            <span class="text-sm font-black text-black mt-1 block">Rp {{ number_format($stats['total_cost'], 0, ',', '.') }}</span>
        </div>
        <div class="bg-white p-3.5 rounded-xl border-2 border-slate-100 shadow-2xs">
            <span class="text-[#015471] block text-[10px] uppercase font-bold">Service Berkala</span>
            <span class="text-sm font-black text-[#015471] mt-1 block">Rp {{ number_format($stats['service_cost'], 0, ',', '.') }}</span>
        </div>
        <div class="bg-white p-3.5 rounded-xl border-2 border-slate-100 shadow-2xs">
            <span class="text-[#FF7A3D] block text-[10px] uppercase font-bold">Ganti Ban</span>
            <span class="text-sm font-black text-[#FF7A3D] mt-1 block">Rp {{ number_format($stats['ban_cost'], 0, ',', '.') }}</span>
        </div>
        <div class="bg-white p-3.5 rounded-xl border-2 border-slate-100 shadow-2xs">
            <span class="text-[#015471] block text-[10px] uppercase font-bold">Ganti Accu</span>
            <span class="text-sm font-black text-[#015471] mt-1 block">Rp {{ number_format($stats['accu_cost'], 0, ',', '.') }}</span>
        </div>
        <div class="bg-white p-3.5 rounded-xl border-2 border-slate-100 shadow-2xs">
            <span class="text-black block text-[10px] uppercase font-bold">Sparepart</span>
            <span class="text-sm font-black text-black mt-1 block">Rp {{ number_format($stats['sparepart_cost'], 0, ',', '.') }}</span>
        </div>
        <div class="bg-white p-3.5 rounded-xl border-2 border-slate-100 shadow-2xs">
            <span class="text-[#FF7A3D] block text-[10px] uppercase font-bold">Motor Pengganti</span>
            <span class="text-sm font-black text-[#FF7A3D] mt-1 block">Rp {{ number_format($stats['motor_pengganti_cost'], 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-sm">
        <form action="{{ route('maintenance.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-black mb-1">Cari Keterangan / No Memo</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="AHASS / SUP/LPG/..." class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-black mb-1">Jenis Pekerjaan</label>
                <select name="type" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
                    <option value="">Semua Jenis</option>
                    <option value="service_berkala" {{ request('type') == 'service_berkala' ? 'selected' : '' }}>Service Berkala & Pelumas</option>
                    <option value="ganti_ban" {{ request('type') == 'ganti_ban' ? 'selected' : '' }}>Penggantian Ban</option>
                    <option value="ganti_accu" {{ request('type') == 'ganti_accu' ? 'selected' : '' }}>Penggantian Accu</option>
                    <option value="sparepart" {{ request('type') == 'sparepart' ? 'selected' : '' }}>Sparepart & Fast Moving</option>
                    <option value="motor_pengganti" {{ request('type') == 'motor_pengganti' ? 'selected' : '' }}>Sewa Motor Pengganti</option>
                    <option value="lainnya" {{ request('type') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-black mb-1">Kendaraan</label>
                <select name="vehicle_id" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
                    <option value="">Semua Kendaraan</option>
                    @foreach($vehicles as $v)
                        <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>{{ $v->plate_number }} ({{ $v->model_name }})</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full text-xs font-bold py-2 px-3 rounded-xl bg-[#015471] hover:bg-[#013e54] text-white transition">
                    Filter Data
                </button>
                <a href="{{ route('maintenance.index') }}" class="py-2 px-3 text-xs font-bold text-black hover:bg-slate-100 rounded-xl border border-slate-200 text-center transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border-2 border-slate-100 shadow-sm overflow-hidden space-y-0">
        
        <!-- Table Control Toolbar (Search Info & Per Page Dropdown) -->
        <div class="px-6 py-4 bg-white border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-[#015471]/10 text-[#015471] flex items-center justify-center font-bold text-xs">
                    <i data-lucide="wrench" class="w-4 h-4"></i>
                </span>
                <div>
                    <h3 class="text-xs font-black text-black">Daftar Riwayat Servis & Suku Cadang</h3>
                    <p class="text-[11px] text-slate-400">Total ditemukan <strong class="text-[#015471]">{{ $records->total() }} catatan</strong> transaksi</p>
                </div>
            </div>

            <!-- Per-Page Controls -->
            <div class="flex items-center gap-3">
                <form id="perPageForm" action="{{ route('maintenance.index') }}" method="GET" class="flex items-center gap-2">
                    @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                    @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
                    @if(request('vehicle_id')) <input type="hidden" name="vehicle_id" value="{{ request('vehicle_id') }}"> @endif

                    <label for="per_page_select" class="text-xs font-bold text-slate-600">Tampilkan:</label>
                    <select id="per_page_select" name="per_page" onchange="document.getElementById('perPageForm').submit()" class="text-xs font-bold px-3 py-1.5 rounded-xl border border-slate-300 focus:outline-[#015471] bg-white text-black cursor-pointer shadow-2xs">
                        <option value="10" {{ ($perPage ?? 15) == 10 ? 'selected' : '' }}>10 Baris</option>
                        <option value="15" {{ ($perPage ?? 15) == 15 ? 'selected' : '' }}>15 Baris</option>
                        <option value="25" {{ ($perPage ?? 15) == 25 ? 'selected' : '' }}>25 Baris</option>
                        <option value="50" {{ ($perPage ?? 15) == 50 ? 'selected' : '' }}>50 Baris</option>
                        <option value="100" {{ ($perPage ?? 15) == 100 ? 'selected' : '' }}>100 Baris</option>
                    </select>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#e6f0f3] border-b border-slate-200 text-[#015471] font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5">Kendaraan</th>
                        <th class="px-5 py-3.5">Kategori</th>
                        <th class="px-5 py-3.5">Rincian Item & Deskripsi</th>
                        <th class="px-5 py-3.5">Bengkel / Rekanan</th>
                        <th class="px-5 py-3.5">No. Memo</th>
                        <th class="px-5 py-3.5 text-right">Biaya (Rp)</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-black">
                    @forelse($records as $rec)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3.5 font-medium text-slate-600 whitespace-nowrap">
                                {{ $rec->date->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3.5">
                                <a href="{{ route('vehicles.show', $rec->vehicle) }}" class="font-bold text-black hover:text-[#FF7A3D] transition">
                                    {{ $rec->vehicle ? $rec->vehicle->plate_number : '-' }}
                                </a>
                                <span class="block text-[10px] text-slate-500">{{ $rec->vehicle && $rec->vehicle->location ? $rec->vehicle->location->name : '' }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-[#e6f0f3] text-[#015471]">
                                    {{ $rec->type_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-bold text-black block">{{ $rec->item_name ?: '-' }}</span>
                                <span class="text-[11px] text-slate-600">{{ $rec->description }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-700 font-medium">
                                {{ $rec->workshop_name ?: '-' }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="text-[11px] font-mono text-[#015471] font-bold">{{ $rec->memo_number ?: '-' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-right font-bold text-black whitespace-nowrap">
                                Rp {{ number_format($rec->cost, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <form id="delete-maint-{{ $rec->id }}" action="{{ route('maintenance.destroy', $rec) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete('delete-maint-{{ $rec->id }}', 'transaksi pemeliharaan ini')" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-slate-100 transition" title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-400">
                                Belum ada data pemeliharaan yang cocok dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-white">
                {{ $records->links() }}
            </div>
        @else
            <div class="px-6 py-3 border-t border-slate-100 bg-white text-xs text-slate-500 text-right">
                Menampilkan seluruh <strong class="text-black">{{ $records->total() }}</strong> catatan
            </div>
        @endif
    </div>

</div>
@endsection
