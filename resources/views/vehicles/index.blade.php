@extends('layouts.app')

@section('title', 'Data Armada Kendaraan (' . $vehicles->total() . ' Unit)')
@section('page_title', 'Manajemen Armada Kendaraan')

@section('content')
<div class="space-y-6 w-full bg-white text-black">

    <!-- Top Header & Action Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-2xl bg-[#015471] text-white flex items-center justify-center shadow-xs">
                    <i data-lucide="truck" class="w-5 h-5 text-[#FF7A3D]"></i>
                </span>
                Armada Kendaraan Operasional
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                @if($currentVendor)
                    Vendor: <strong class="text-[#015471]">{{ $currentVendor->name }}</strong> ({{ $vehicles->total() }} Unit Terdaftar)
                @else
                    Seluruh Armada Terdaftar dari Multi Vendor ({{ $vehicles->total() }} Unit)
                @endif
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
            <a href="{{ route('exports.vehiclesPdf') }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold rounded-xl bg-white hover:bg-slate-50 text-slate-800 border-2 border-slate-200 shadow-2xs transition">
                <i data-lucide="file-text" class="w-4 h-4 text-[#FF7A3D]"></i> Ekspor PDF
            </a>
            <a href="{{ route('exports.vehiclesExcel') }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold rounded-xl bg-white hover:bg-slate-50 text-slate-800 border-2 border-slate-200 shadow-2xs transition">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-[#015471]"></i> Ekspor Excel
            </a>
            <a href="{{ route('vehicles.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-sm transition active:scale-95">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Motor
            </a>
        </div>
    </div>

    <!-- Summary Metrics Cards (5 Cards Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 text-xs">
        <!-- 1. Total Armada -->
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-2xs hover:border-slate-300 hover:shadow-xs transition relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-slate-500 block text-[10px] uppercase font-bold tracking-wider">Total Armada</span>
                <span class="w-7 h-7 rounded-lg bg-slate-100 text-[#015471] flex items-center justify-center text-xs">
                    <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                </span>
            </div>
            <span class="text-xl font-black text-slate-900 mt-2 block font-mono">{{ $stats['total'] }} <span class="text-xs font-semibold text-slate-400">Unit</span></span>
            <span class="text-[10px] text-slate-400 mt-1 block">Seluruh unit terdaftar</span>
        </div>

        <!-- 2. Aktif Beroperasi -->
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-2xs hover:border-emerald-200 hover:shadow-xs transition relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-emerald-700 block text-[10px] uppercase font-bold tracking-wider">Aktif Beroperasi</span>
                <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-xs">
                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i>
                </span>
            </div>
            <span class="text-xl font-black text-emerald-700 mt-2 block font-mono">{{ $stats['active'] }} <span class="text-xs font-semibold text-slate-400">Unit</span></span>
            <span class="text-[10px] text-slate-400 mt-1 block">Unit siap jalan di lapangan</span>
        </div>

        <!-- 3. Dalam Servis / Bengkel -->
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-2xs hover:border-orange-200 hover:shadow-xs transition relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[#FF7A3D] block text-[10px] uppercase font-bold tracking-wider">Dalam Perbaikan</span>
                <span class="w-7 h-7 rounded-lg bg-[#FF7A3D]/10 text-[#FF7A3D] flex items-center justify-center text-xs">
                    <i data-lucide="wrench" class="w-3.5 h-3.5"></i>
                </span>
            </div>
            <span class="text-xl font-black text-[#FF7A3D] mt-2 block font-mono">{{ $stats['maintenance'] }} <span class="text-xs font-semibold text-slate-400">Unit</span></span>
            <span class="text-[10px] text-slate-400 mt-1 block">Sedang dalam proses servis</span>
        </div>

        <!-- 4. Unit Pengganti / Klaim -->
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-2xs hover:border-amber-200 hover:shadow-xs transition relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-amber-700 block text-[10px] uppercase font-bold tracking-wider">Pengganti / Hilang</span>
                <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center text-xs">
                    <i data-lucide="repeat" class="w-3.5 h-3.5"></i>
                </span>
            </div>
            <span class="text-xl font-black text-amber-700 mt-2 block font-mono">
                {{ $stats['replaced'] }} <span class="text-xs font-normal text-slate-400">ganti</span> 
                @if($stats['lost'] > 0)
                    <span class="text-xs font-semibold text-rose-600">/ {{ $stats['lost'] }} hilang</span>
                @endif
            </span>
            <span class="text-[10px] text-slate-400 mt-1 block">Backup aktif & klaim asuransi</span>
        </div>

        <!-- 5. Estimasi Nilai Sewa / Bln -->
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-2xs hover:border-blue-200 hover:shadow-xs transition relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[#015471] block text-[10px] uppercase font-bold tracking-wider">Nilai Kontrak / Bln</span>
                <span class="w-7 h-7 rounded-lg bg-[#015471]/10 text-[#015471] flex items-center justify-center text-xs">
                    <i data-lucide="coins" class="w-3.5 h-3.5"></i>
                </span>
            </div>
            <span class="text-base sm:text-lg font-black text-[#015471] mt-2 block font-mono">
                Rp {{ number_format($stats['total_rental'], 0, ',', '.') }}
            </span>
            <span class="text-[10px] text-slate-400 mt-1 block">Total billing sewa unit/bulan</span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-sm">
        <form action="{{ route('vehicles.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-7 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-black mb-1">Cari Plat/Model/Pemakai</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Plat / Model / Nama..." class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-black mb-1">Klien Penyewa</label>
                <select name="vendor_id" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
                    <option value="">Semua Klien</option>
                    @foreach($vendors as $v)
                        <option value="{{ $v->id }}" {{ request('vendor_id', $activeVendorId) == $v->id ? 'selected' : '' }}>
                            {{ $v->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-black mb-1">Kategori</label>
                <select name="category" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
                    <option value="">Semua Kategori</option>
                    <option value="Roda 2 (Motor)" {{ request('category') == 'Roda 2 (Motor)' ? 'selected' : '' }}>Roda 2 (Motor)</option>
                    <option value="Roda 4 (Mobil/Truk)" {{ request('category') == 'Roda 4 (Mobil/Truk)' ? 'selected' : '' }}>Roda 4 (Mobil/Truk)</option>
                    <option value="Alat Berat" {{ request('category') == 'Alat Berat' ? 'selected' : '' }}>Alat Berat</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-black mb-1">Merk</label>
                <select name="brand" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
                    <option value="">Semua Merk</option>
                    @foreach($brands as $b)
                        <option value="{{ $b }}" {{ request('brand') == $b ? 'selected' : '' }}>{{ $b }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-black mb-1">Status Unit</label>
                <select name="status" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif Beroperasi</option>
                    <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Dalam Servis</option>
                    <option value="replaced" {{ request('status') == 'replaced' ? 'selected' : '' }}>Unit Pengganti Aktif</option>
                    <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Hilang / Polis</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-black mb-1">Lokasi Kebun</label>
                <select name="location_id" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-black">
                    <option value="">Semua Kebun</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full text-xs font-bold py-2 px-3 rounded-xl bg-[#015471] hover:bg-[#013e54] text-white transition">
                    Filter
                </button>
                <a href="{{ route('vehicles.index') }}" class="py-2 px-3 text-xs font-bold text-black hover:bg-slate-100 rounded-xl border border-slate-200 text-center transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table Card Container -->
    <div class="bg-white rounded-2xl border-2 border-slate-100 shadow-sm overflow-hidden space-y-0">
        
        <!-- Table Control Toolbar (Search Info & Per Page Dropdown) -->
        <div class="px-6 py-4 bg-white border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-[#015471]/10 text-[#015471] flex items-center justify-center font-bold text-xs">
                    <i data-lucide="layers" class="w-4 h-4"></i>
                </span>
                <div>
                    <h3 class="text-xs font-black text-black">Daftar Unit Armada & Pajak</h3>
                    <p class="text-[11px] text-slate-400">Total ditemukan <strong class="text-[#015471]">{{ $vehicles->total() }} unit</strong> armada</p>
                </div>
            </div>

            <!-- Per-Page & View Controls -->
            <div class="flex items-center gap-3">
                <form id="perPageForm" action="{{ route('vehicles.index') }}" method="GET" class="flex items-center gap-2">
                    @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                    @if(request('vendor_id')) <input type="hidden" name="vendor_id" value="{{ request('vendor_id') }}"> @endif
                    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                    @if(request('brand')) <input type="hidden" name="brand" value="{{ request('brand') }}"> @endif
                    @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                    @if(request('location_id')) <input type="hidden" name="location_id" value="{{ request('location_id') }}"> @endif

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

        <!-- Vehicles Table with Horizontal Scroll -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-gradient-to-r from-[#e6f0f3] to-[#f4f8fa] border-b border-slate-200 text-[#015471] font-black uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5 w-12 text-center">No</th>
                        <th class="px-5 py-3.5">Plat Nomor</th>
                        <th class="px-5 py-3.5">Kategori & Merk</th>
                        <th class="px-5 py-3.5">Model & Tipe</th>
                        <th class="px-5 py-3.5">Pengguna Motor</th>
                        <th class="px-5 py-3.5">Klien & Penempatan</th>
                        <th class="px-5 py-3.5">KM Terakhir Servis</th>
                        <th class="px-5 py-3.5">Jatuh Tempo Pajak</th>
                        <th class="px-5 py-3.5">Tarif Sewa</th>
                        <th class="px-5 py-3.5 text-center">Status & Legalitas</th>
                        <th class="px-5 py-3.5 text-right w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-black">
                    @forelse($vehicles as $index => $v)
                        <tr class="hover:bg-[#f8fafc] transition-colors duration-150">
                            <td class="px-5 py-4 text-center font-bold text-slate-400">
                                {{ $vehicles->firstItem() + $index }}
                            </td>
                            <td class="px-5 py-4">
                                <a href="{{ route('vehicles.show', $v) }}" class="inline-flex items-center gap-1.5 group">
                                    <span class="px-2.5 py-1 rounded-lg bg-[#e6f0f3] border border-[#015471]/30 font-mono text-xs text-[#015471] font-black group-hover:bg-[#015471] group-hover:text-white transition">
                                        {{ $v->plate_number }}
                                    </span>
                                </a>
                                @if($v->replacement_plate_number)
                                    <span class="block text-[10px] text-[#FF7A3D] font-bold mt-1">
                                        <i data-lucide="repeat" class="w-3 h-3 inline"></i> Pengganti: {{ $v->replacement_plate_number }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-bold text-slate-900 block">{{ $v->brand ?: 'Honda' }}</span>
                                <span class="text-[10px] text-slate-500">{{ $v->category ?: 'Roda 2 (Motor)' }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-black text-black block">{{ $v->model_name }}</span>
                                <span class="text-[10px] text-slate-400">{{ $v->type_name ?: 'Standar' }} • Th. {{ $v->year }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-[#015471]/10 text-[#015471] flex items-center justify-center font-black text-[11px] shrink-0">
                                        <i data-lucide="user" class="w-3.5 h-3.5"></i>
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 block text-xs">{{ $v->user_holder ?: 'Belum Ditentukan' }}</span>
                                        <span class="text-[10px] text-slate-400 font-medium">Pemakai / Driver Lapangan</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-bold text-black block">{{ $v->vendor ? $v->vendor->name : '-' }}</span>
                                <span class="inline-flex items-center gap-1 text-[11px] text-slate-600">
                                    <i data-lucide="map-pin" class="w-3 h-3 text-[#FF7A3D]"></i>
                                    {{ $v->location ? $v->location->name : 'Belum Ditentukan' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                @if($v->latest_service_km)
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 font-mono font-bold text-xs inline-flex items-center gap-1">
                                            <i data-lucide="gauge" class="w-3 h-3 text-emerald-600"></i>
                                            {{ number_format($v->latest_service_km, 0, ',', '.') }} KM
                                        </span>
                                    </div>
                                    @if($v->latest_service_record && $v->latest_service_record->date)
                                        <span class="text-[10px] text-slate-400 block mt-0.5">
                                            Servis: {{ $v->latest_service_record->date->format('d/m/Y') }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Belum servis</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($v->tax_annual_date)
                                    @php
                                        $diffAnnual = now()->diffInDays($v->tax_annual_date, false);
                                    @endphp
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-1">
                                            <span class="text-[10px] text-slate-400">1 Th:</span>
                                            <span class="inline-block px-1.5 py-0.2 rounded text-[10px] font-mono font-bold {{ $diffAnnual < 0 ? 'bg-rose-100 text-rose-700' : ($diffAnnual <= 30 ? 'bg-[#FF7A3D]/20 text-[#FF7A3D]' : 'text-slate-700') }}">
                                                {{ $v->tax_annual_date->format('d/m/Y') }}
                                            </span>
                                        </div>
                                        @if($v->tax_5year_date)
                                            <div class="flex items-center gap-1">
                                                <span class="text-[10px] text-slate-400">5 Th:</span>
                                                <span class="text-[10px] font-mono text-slate-600 font-medium">
                                                    {{ $v->tax_5year_date->format('d/m/Y') }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Belum diset</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-black text-black block">Rp {{ number_format($v->rental_price_per_month, 0, ',', '.') }}</span>
                                <span class="text-[10px] text-slate-400">/ bulan</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $v->status_badge['class'] }}">
                                    {{ $v->status_badge['label'] }}
                                </span>
                                <!-- Document mini indicators -->
                                <div class="flex items-center justify-center gap-1.5 mt-1.5">
                                    <span class="inline-flex items-center gap-1 text-[9px] font-bold px-1.5 py-0.5 rounded {{ $v->stnk_file ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-400' }}" title="{{ $v->stnk_file ? 'STNK Tersedia' : 'STNK Belum Diunggah' }}">
                                        <i data-lucide="file-check" class="w-2.5 h-2.5"></i> STNK
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-[9px] font-bold px-1.5 py-0.5 rounded {{ $v->bpkb_file ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-400' }}" title="{{ $v->bpkb_file ? 'BPKB Tersedia' : 'BPKB Belum Diunggah' }}">
                                        <i data-lucide="book-open" class="w-2.5 h-2.5"></i> BPKB
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('vehicles.show', $v) }}" class="p-1.5 text-[#015471] hover:text-white hover:bg-[#015471] rounded-lg transition" title="Lihat Kartu Unit">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('vehicles.edit', $v) }}" class="p-1.5 text-slate-600 hover:text-white hover:bg-[#FF7A3D] rounded-lg transition" title="Edit Data">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </a>
                                    <form id="delete-vehicle-{{ $v->id }}" action="{{ route('vehicles.destroy', $v) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('delete-vehicle-{{ $v->id }}', 'kendaraan {{ $v->plate_number }}')" class="p-1.5 text-slate-400 hover:text-white hover:bg-rose-600 rounded-lg transition" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-12 text-slate-400">
                                <i data-lucide="inbox" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                                <p class="font-medium text-xs">Tidak ada data unit yang cocok dengan filter pencarian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Themed Pagination Footer -->
        @if($vehicles->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-white">
                {{ $vehicles->links() }}
            </div>
        @else
            <div class="px-6 py-3 border-t border-slate-100 bg-white text-xs text-slate-500 text-right">
                Menampilkan seluruh <strong class="text-black">{{ $vehicles->total() }}</strong> unit armada
            </div>
        @endif
    </div>

</div>
@endsection
