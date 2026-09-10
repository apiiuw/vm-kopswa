@extends('layouts.app')

@section('title', 'Pemeliharaan, Servis & Penggantian Suku Cadang')
@section('page_title', 'Modul Pemeliharaan & Operasional Armada')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto bg-white text-slate-900">

    <!-- Top Summary Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-2xl bg-[#015471] text-white flex items-center justify-center shadow-xs">
                    <i data-lucide="wrench" class="w-5 h-5 text-[#FF7A3D]"></i>
                </span>
                Catatan Pemeliharaan Armada
            </h2>
            <p class="text-xs text-slate-500 mt-1">Pemantauan riwayat servis berkala, ganti ban, aki, dan suku cadang armada operasional</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('maintenance.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-sm transition active:scale-95">
                <i data-lucide="plus" class="w-4 h-4"></i> Catat Pemeliharaan / Servis
            </a>
        </div>
    </div>

    <!-- Category Stat Highlights (Refined: 5 Cards without Motor Pengganti) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 text-xs">
        <!-- 1. Total Biaya Riil -->
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-2xs hover:border-slate-300 hover:shadow-xs transition relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-slate-500 block text-[10px] uppercase font-bold tracking-wider">Total Biaya Riil</span>
                <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-xs">
                    <i data-lucide="coins" class="w-3.5 h-3.5"></i>
                </span>
            </div>
            <span class="text-base font-black text-slate-900 mt-2 block font-mono">Rp {{ number_format($stats['total_cost'], 0, ',', '.') }}</span>
            <span class="text-[10px] text-slate-400 mt-1 block">Seluruh jenis pemeliharaan</span>
        </div>

        <!-- 2. Service Berkala -->
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-2xs hover:border-[#015471]/30 hover:shadow-xs transition relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[#015471] block text-[10px] uppercase font-bold tracking-wider">Service Berkala</span>
                <span class="w-7 h-7 rounded-lg bg-[#015471]/10 text-[#015471] flex items-center justify-center text-xs">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#015471]"></i>
                </span>
            </div>
            <span class="text-base font-black text-[#015471] mt-2 block font-mono">Rp {{ number_format($stats['service_cost'], 0, ',', '.') }}</span>
            <span class="text-[10px] text-slate-400 mt-1 block">Tune up & ganti pelumas/oli</span>
        </div>

        <!-- 3. Ganti Ban -->
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-2xs hover:border-[#FF7A3D]/30 hover:shadow-xs transition relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[#FF7A3D] block text-[10px] uppercase font-bold tracking-wider">Ganti Ban</span>
                <span class="w-7 h-7 rounded-lg bg-[#FF7A3D]/10 text-[#FF7A3D] flex items-center justify-center text-xs">
                    <i data-lucide="disc" class="w-3.5 h-3.5 text-[#FF7A3D]"></i>
                </span>
            </div>
            <span class="text-base font-black text-[#FF7A3D] mt-2 block font-mono">Rp {{ number_format($stats['ban_cost'], 0, ',', '.') }}</span>
            <span class="text-[10px] text-slate-400 mt-1 block">Ban luar, dalam & tubeless</span>
        </div>

        <!-- 4. Ganti Accu -->
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-2xs hover:border-[#015471]/30 hover:shadow-xs transition relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[#015471] block text-[10px] uppercase font-bold tracking-wider">Ganti Accu / Aki</span>
                <span class="w-7 h-7 rounded-lg bg-[#015471]/10 text-[#015471] flex items-center justify-center text-xs">
                    <i data-lucide="battery-charging" class="w-3.5 h-3.5 text-[#015471]"></i>
                </span>
            </div>
            <span class="text-base font-black text-[#015471] mt-2 block font-mono">Rp {{ number_format($stats['accu_cost'], 0, ',', '.') }}</span>
            <span class="text-[10px] text-slate-400 mt-1 block">Penggantian baterai aki</span>
        </div>

        <!-- 5. Sparepart -->
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-2xs hover:border-slate-300 hover:shadow-xs transition relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-slate-700 block text-[10px] uppercase font-bold tracking-wider">Suku Cadang & Lainnya</span>
                <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-xs">
                    <i data-lucide="cog" class="w-3.5 h-3.5"></i>
                </span>
            </div>
            <span class="text-base font-black text-slate-900 mt-2 block font-mono">Rp {{ number_format($stats['sparepart_cost'], 0, ',', '.') }}</span>
            <span class="text-[10px] text-slate-400 mt-1 block">Fast moving & pergantian part</span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border-2 border-slate-100 shadow-2xs">
        <form action="{{ route('maintenance.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-1">Cari Keterangan / No Memo / Kendaraan</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="AHASS / BE 1234 / REQ-..." class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-slate-900">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-1">Jenis Pekerjaan</label>
                <select name="type" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-slate-900">
                    <option value="">Semua Jenis</option>
                    <option value="service_berkala" {{ request('type') == 'service_berkala' ? 'selected' : '' }}>Service Berkala & Pelumas</option>
                    <option value="ganti_ban" {{ request('type') == 'ganti_ban' ? 'selected' : '' }}>Penggantian Ban</option>
                    <option value="ganti_accu" {{ request('type') == 'ganti_accu' ? 'selected' : '' }}>Penggantian Accu</option>
                    <option value="sparepart" {{ request('type') == 'sparepart' ? 'selected' : '' }}>Sparepart & Fast Moving</option>
                    <option value="lainnya" {{ request('type') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-1">Kendaraan</label>
                <select name="vehicle_id" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 focus:outline-[#015471] text-slate-900">
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
                <a href="{{ route('maintenance.index') }}" class="py-2 px-3 text-xs font-bold text-slate-700 hover:bg-slate-100 rounded-xl border border-slate-200 text-center transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Main Container: Cards Layout -->
    <div class="bg-white rounded-3xl border-2 border-slate-100 shadow-sm overflow-hidden">
        
        <!-- Grid Control Toolbar -->
        <div class="px-6 py-4 bg-slate-50/70 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-2xl bg-[#015471]/10 text-[#015471] flex items-center justify-center font-bold text-xs">
                    <i data-lucide="layout-grid" class="w-4 h-4 text-[#015471]"></i>
                </span>
                <div>
                    <h3 class="text-xs font-black text-slate-900">Daftar Riwayat Servis & Suku Cadang</h3>
                    <p class="text-[11px] text-slate-500">Total ditemukan <strong class="text-[#015471] font-bold">{{ $records->total() }} catatan</strong> transaksi</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Per-Page Controls -->
                <form id="perPageForm" action="{{ route('maintenance.index') }}" method="GET" class="flex items-center gap-2">
                    @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                    @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
                    @if(request('vehicle_id')) <input type="hidden" name="vehicle_id" value="{{ request('vehicle_id') }}"> @endif

                    <label for="per_page_select" class="text-xs font-bold text-slate-500">Tampilkan:</label>
                    <select id="per_page_select" name="per_page" onchange="document.getElementById('perPageForm').submit()" class="text-xs font-bold px-3 py-1.5 rounded-xl border border-slate-300 focus:outline-[#015471] bg-white text-slate-800 cursor-pointer shadow-2xs">
                        <option value="9" {{ ($perPage ?? 15) == 9 ? 'selected' : '' }}>9 Kartu</option>
                        <option value="15" {{ ($perPage ?? 15) == 15 ? 'selected' : '' }}>15 Kartu</option>
                        <option value="24" {{ ($perPage ?? 15) == 24 ? 'selected' : '' }}>24 Kartu</option>
                        <option value="48" {{ ($perPage ?? 15) == 48 ? 'selected' : '' }}>48 Kartu</option>
                    </select>
                </form>
            </div>
        </div>

        <!-- KOTAK-KOTAK / GRID CARD VIEW -->
        <div class="p-6 bg-slate-50/40">
            @if($records->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($records as $rec)
                        @php
                            $complaints = $rec->parsed_complaints;
                            $cleanDesc = $rec->clean_description;
                        @endphp
                        <div class="bg-white rounded-2xl border-2 border-slate-100 shadow-2xs hover:border-[#015471]/40 hover:shadow-md transition-all flex flex-col justify-between overflow-hidden group">
                            
                            <!-- Card Header -->
                            <div class="p-4 pb-3 border-b border-slate-100 bg-linear-to-r from-slate-50 to-white flex items-start justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap mb-1.5">
                                        <a href="{{ $rec->vehicle ? route('vehicles.show', $rec->vehicle) : '#' }}" class="inline-flex items-center gap-1.5 text-xs font-black font-mono px-2.5 py-1 rounded-lg bg-[#015471] text-white hover:bg-[#013e54] transition">
                                            <i data-lucide="bike" class="w-3.5 h-3.5 text-[#FF7A3D]"></i>
                                            {{ $rec->vehicle ? $rec->vehicle->plate_number : '-' }}
                                        </a>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#015471]/10 text-[#015471] border border-[#015471]/20">
                                            {{ $rec->type_label }}
                                        </span>
                                    </div>
                                    <span class="text-[11px] text-slate-500 block">
                                        <i data-lucide="map-pin" class="w-3 h-3 inline-block text-slate-400 mr-0.5"></i>
                                        {{ $rec->vehicle && $rec->vehicle->location ? $rec->vehicle->location->name : 'Kebun / Lokasi Operasional' }}
                                    </span>
                                </div>

                                <div class="text-right shrink-0">
                                    <span class="text-xs font-bold text-slate-700 block">
                                        {{ $rec->date->translatedFormat('d M Y') }}
                                    </span>
                                    @if($rec->memo_number)
                                        <span class="text-[10px] font-mono text-[#015471] bg-[#015471]/5 px-2 py-0.5 rounded font-bold block mt-1">
                                            {{ $rec->memo_number }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Card Body: Structured Items & Description -->
                            <div class="p-4 space-y-3.5 flex-1">
                                <div>
                                    <a href="{{ route('maintenance.show', $rec) }}" class="font-black text-sm text-slate-900 group-hover:text-[#015471] transition line-clamp-1">
                                        {{ $rec->item_name ?: $rec->type_label }}
                                    </a>
                                </div>

                                <!-- Parsed Sub-items in neat individual boxes -->
                                @if(count($complaints) > 0)
                                    <div class="space-y-1.5">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Rincian Komponen / Perbaikan:</span>
                                        <div class="grid grid-cols-1 gap-1.5">
                                            @foreach($complaints as $c)
                                                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between gap-2 text-xs">
                                                    <div class="min-w-0 flex items-center gap-2">
                                                        <span class="w-5 h-5 rounded-md bg-[#015471] text-white text-[10px] font-black flex items-center justify-center shrink-0">
                                                            {{ $c['index'] }}
                                                        </span>
                                                        <div class="min-w-0">
                                                            <p class="font-bold text-slate-800 text-[11px] truncate">
                                                                {{ $c['title'] ?: $c['category'] }}
                                                            </p>
                                                            @if($c['description'])
                                                                <p class="text-[10px] text-slate-500 truncate">{{ $c['description'] }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @if($c['cost'])
                                                        <span class="text-[10px] font-mono font-bold text-[#FF7A3D] bg-white px-2 py-0.5 rounded-lg border border-slate-200 shrink-0">
                                                            Rp {{ $c['cost'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @elseif($cleanDesc)
                                    <p class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100 line-clamp-2 leading-relaxed">
                                        {{ $cleanDesc }}
                                    </p>
                                @endif

                                <!-- Workshop & Contacts info in micro badge -->
                                @if($rec->workshop_name || $rec->odometer_km)
                                    <div class="flex items-center gap-2 flex-wrap pt-2.5 border-t border-slate-100 text-[11px] text-slate-600">
                                        @if($rec->workshop_name)
                                            <span class="inline-flex items-center gap-1.5 bg-slate-100 px-2 py-1 rounded-lg text-slate-700 font-medium truncate max-w-[200px]">
                                                <i data-lucide="store" class="w-3 h-3 text-[#015471]"></i>
                                                {{ $rec->workshop_name }}
                                            </span>
                                        @endif
                                        @if($rec->odometer_km)
                                            <span class="inline-flex items-center gap-1.5 bg-slate-100 px-2 py-1 rounded-lg text-slate-700 font-mono">
                                                <i data-lucide="gauge" class="w-3 h-3 text-[#FF7A3D]"></i>
                                                {{ number_format($rec->odometer_km, 0, ',', '.') }} KM
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Card Footer: Cost & Action Buttons -->
                            <div class="px-4 py-3 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between gap-3">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">Total Biaya</span>
                                    <span class="text-base font-black font-mono text-[#FF7A3D]">
                                        Rp {{ number_format($rec->cost, 0, ',', '.') }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('maintenance.show', $rec) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#015471] hover:bg-[#013e54] text-white text-xs font-bold transition shadow-2xs">
                                        <i data-lucide="eye" class="w-3.5 h-3.5 text-[#FF7A3D]"></i> Detail
                                    </a>
                                    <form id="delete-maint-card-{{ $rec->id }}" action="{{ route('maintenance.destroy', $rec) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('delete-maint-card-{{ $rec->id }}', 'transaksi pemeliharaan ini')" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-xl hover:bg-rose-50 transition border border-transparent hover:border-rose-100" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-white rounded-2xl border-2 border-dashed border-slate-200">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                        <i data-lucide="inbox" class="w-6 h-6"></i>
                    </div>
                    <h4 class="text-xs font-bold text-slate-700">Belum Ada Data Pemeliharaan</h4>
                    <p class="text-[11px] text-slate-400 mt-1">Gunakan tombol "Catat Pemeliharaan / Servis" untuk menambahkan transaksi baru.</p>
                </div>
            @endif
        </div>

        @if($records->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-white">
                {{ $records->links() }}
            </div>
        @else
            <div class="px-6 py-3 border-t border-slate-100 bg-white text-xs text-slate-500 text-right">
                Menampilkan seluruh <strong class="text-slate-900">{{ $records->total() }}</strong> catatan
            </div>
        @endif
    </div>

</div>
@endsection

