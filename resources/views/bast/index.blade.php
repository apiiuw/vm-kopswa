@extends('layouts.app')

@section('title', 'BAST & Serah Terima Unit')
@section('header_title', 'BAST & Serah Terima Unit')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between gap-2.5">
            <div class="flex items-center gap-2.5">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
            @if(session('preview_bast_id'))
                <button type="button" onclick="openBastPreview({{ session('preview_bast_id') }})" class="px-3 py-1.5 rounded-xl bg-emerald-600 text-white text-[11px] font-bold hover:bg-emerald-700">
                    Lihat Overview & PDF
                </button>
            @endif
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
            <strong class="font-bold block mb-1">Periksa kembali input BAST:</strong>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Top Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs font-bold text-slate-500">Total BAST Terbit</span>
            <span class="text-2xl font-black text-slate-900 block mt-1">{{ $stats['total'] }}</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs font-bold text-slate-500">BAST Unit Baru</span>
            <span class="text-2xl font-black text-[#015471] block mt-1">{{ $stats['unit_baru'] }}</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs font-bold text-slate-500">BAST Unit Pengganti</span>
            <span class="text-2xl font-black text-[#FF7A3D] block mt-1">{{ $stats['unit_pengganti'] }}</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-xs font-bold text-slate-500">BAST Pengembalian</span>
            <span class="text-2xl font-black text-emerald-600 block mt-1">{{ $stats['pengembalian'] }}</span>
        </div>
    </div>

    <!-- Header & Action -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-black text-slate-900">Berita Acara Serah Terima (BAST)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Terbitkan BAST Unit Baru, Unit Pengganti, dan Pengembalian lengkap dengan live overview & generate PDF otomatis.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <form action="{{ route('bast.index') }}" method="GET" class="flex items-center gap-2">
                <select name="bast_type" onchange="this.form.submit()" class="text-xs font-bold border border-slate-300 rounded-xl px-3 py-2 bg-white text-slate-800">
                    <option value="">Semua Tipe BAST</option>
                    <option value="unit_baru" {{ request('bast_type') === 'unit_baru' ? 'selected' : '' }}>Unit Baru</option>
                    <option value="unit_pengganti" {{ request('bast_type') === 'unit_pengganti' ? 'selected' : '' }}>Unit Pengganti</option>
                    <option value="pengembalian" {{ request('bast_type') === 'pengembalian' ? 'selected' : '' }}>Pengembalian</option>
                </select>
            </form>
            <button type="button" onclick="openCreateBastModal()" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-xs transition">
                <i data-lucide="plus" class="w-4 h-4"></i> Buat BAST Baru
            </button>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1320px] text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase text-[10px] font-extrabold whitespace-nowrap">
                    <tr>
                        <th class="px-5 py-3.5">No. BAST</th>
                        <th class="px-5 py-3.5">Tipe</th>
                        <th class="px-5 py-3.5">Client</th>
                        <th class="px-5 py-3.5">Unit & Kontrak</th>
                        <th class="px-5 py-3.5">Masa Berlaku Kontrak</th>
                        <th class="px-5 py-3.5">Tgl Serah Terima</th>
                        <th class="px-5 py-3.5">Penerima</th>
                        <th class="px-5 py-3.5 text-right">Biaya Pengganti</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($basts as $b)
                        @php
                            $clientObj = $b->vendor?->client ?? $b->vehicle?->vendor?->client;
                            $clientName = $clientObj?->name ?: ($b->vendor?->name ?: '-');
                        @endphp
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-3.5 font-mono font-bold text-[#015471] whitespace-nowrap">{{ $b->bast_number }}</td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @php
                                    $badge = match($b->bast_type) {
                                        'unit_pengganti' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'pengembalian' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        default => 'bg-sky-100 text-sky-800 border-sky-200',
                                    };
                                @endphp
                                <span class="inline-block whitespace-nowrap px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $badge }}">{{ $b->type_label }}</span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="font-bold text-slate-900">{{ $clientName }}</div>
                                @if($clientObj?->code)
                                    <div class="text-[10px] font-mono text-slate-400">{{ $clientObj->code }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 min-w-[240px]">
                                <div class="font-bold text-slate-900 whitespace-nowrap">{{ $b->vehicle?->plate_number }} — {{ $b->vehicle?->model_name }}</div>
                                <div class="text-[11px] text-slate-500 whitespace-nowrap">Kontrak: {{ $b->vendor?->contract_number ?: $b->vendor?->name ?: '-' }}</div>
                                @if($b->replacementVehicle)
                                    <div class="text-[11px] text-[#FF7A3D] font-semibold whitespace-nowrap">Pengganti: {{ $b->replacementVehicle->plate_number }} ({{ $b->replacementVehicle->model_name }})</div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 font-mono text-[11px] whitespace-nowrap">
                                @if($b->contract_start_date && $b->contract_end_date)
                                    {{ $b->contract_start_date->format('d/m/Y') }} s.d. {{ $b->contract_end_date->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-5 py-3.5 font-mono whitespace-nowrap">{{ $b->handover_date->format('d/m/Y') }}</td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="font-bold text-slate-800">{{ $b->received_by ?: '-' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $b->recipient_position }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-right font-mono font-bold whitespace-nowrap">
                                {{ $b->replacement_cost > 0 ? 'Rp ' . number_format($b->replacement_cost, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                                @if($b->signed_file)
                                    <a href="{{ asset('storage/' . $b->signed_file) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-[11px] font-bold hover:bg-emerald-100" title="Lihat Scan BAST TTD Basah">
                                        <i data-lucide="file-check-2" class="w-3.5 h-3.5"></i> TTD Basah
                                    </a>
                                    <button type="button" onclick="openUploadSignedModal({{ $b->id }}, '{{ $b->bast_number }}')" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-amber-50 text-amber-700 text-[11px] font-bold hover:bg-amber-100" title="Upload Ulang Scan TTD Basah">
                                        <i data-lucide="upload" class="w-3.5 h-3.5"></i> Upload Ulang
                                    </button>
                                @else
                                    <button type="button" onclick="openUploadSignedModal({{ $b->id }}, '{{ $b->bast_number }}')" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-amber-50 text-amber-700 text-[11px] font-bold hover:bg-amber-100" title="Upload Scan TTD Basah">
                                        <i data-lucide="upload" class="w-3.5 h-3.5"></i> Upload TTD
                                    </button>
                                @endif
                                <button type="button" onclick="openBastPreview({{ $b->id }})" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 text-slate-800 text-[11px] font-bold hover:bg-slate-200">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i> Overview
                                </button>
                                <a href="{{ route('bast.pdf', $b) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-[#015471] text-white text-[11px] font-bold hover:bg-[#01435b]">
                                    <i data-lucide="file-down" class="w-3.5 h-3.5"></i> PDF
                                </a>
                                <form action="{{ route('bast.destroy', $b) }}" method="POST" class="inline" onsubmit="return confirm('Hapus BAST ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-10 text-center text-slate-400">Belum ada dokumen BAST. Klik "Buat BAST Baru" untuk menerbitkan BAST unit baru, pengganti, atau pengembalian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($basts->hasPages())
            <div class="p-4 border-t border-slate-100">{{ $basts->links() }}</div>
        @endif
    </div>
</div>

<!-- Modal Create BAST (2 Columns: Form + Live Overview Preview) -->
<div id="bastModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-6xl w-full p-6 shadow-2xl border border-slate-200 max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-5">
            <div>
                <h3 class="font-black text-slate-900 text-base">Terbitkan Dokumen BAST & Live Overview</h3>
                <p class="text-[11px] text-slate-500">Setelah disimpan, sistem otomatis membuat PDF dan menampilkan pratinjau dokumennya.</p>
            </div>
            <button type="button" onclick="document.getElementById('bastModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('bast.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-6 text-xs">
            @csrf

            <!-- LEFT: Form Inputs (7 cols) -->
            <div class="lg:col-span-7 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jenis BAST *</label>
                        <select name="bast_type" id="bast_type" onchange="toggleReplacementFields(); updateBastOverview();" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 font-semibold">
                            <option value="unit_baru">BAST Unit Baru</option>
                            <option value="unit_pengganti">BAST Unit Pengganti</option>
                            <option value="pengembalian">BAST Pengembalian Unit</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Unit Aset Utama *</label>
                        <select name="vehicle_id" id="bast_vehicle_id" onchange="syncVehicleContract(); updateBastOverview();" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 font-semibold">
                            @foreach($vehicles as $veh)
                                <option value="{{ $veh->id }}"
                                    data-label="{{ $veh->plate_number }} — {{ $veh->model_name }}"
                                    data-type="{{ $veh->asset_type_label }}"
                                    data-vendor-id="{{ $veh->vendor_id }}"
                                    data-contract-start="{{ $veh->vendor?->contract_start_date?->format('Y-m-d') }}"
                                    data-contract-end="{{ $veh->vendor?->contract_end_date?->format('Y-m-d') }}">
                                    {{ $veh->plate_number }} — {{ $veh->model_name }} ({{ $veh->asset_type_label }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kontrak Sewa Terkait</label>
                        <select name="vendor_id" id="bast_vendor_id" onchange="syncContractDates(); updateBastOverview();" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 font-semibold">
                            <option value="">-- Sesuai Kontrak Unit --</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->id }}"
                                    data-label="{{ $v->contract_number ?: $v->code }} — {{ $v->name }}"
                                    data-client="{{ $v->name }}"
                                    data-start="{{ $v->contract_start_date?->format('Y-m-d') }}"
                                    data-end="{{ $v->contract_end_date?->format('Y-m-d') }}">
                                    {{ $v->contract_number ?: $v->code }} — {{ $v->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tanggal Serah Terima *</label>
                        <input type="date" name="handover_date" id="bast_handover_date" onchange="updateBastOverview()" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-slate-900">
                    </div>
                </div>

                <div id="replacementFields" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-3.5 p-3.5 rounded-xl bg-amber-50 border border-amber-200">
                    <div>
                        <label class="block font-bold text-amber-900 mb-1">Unit Pengganti yang Diberikan</label>
                        <select name="replacement_vehicle_id" id="bast_replacement_id" onchange="updateBastOverview()" class="w-full px-3 py-2.5 rounded-xl border border-amber-300 bg-white text-slate-900">
                            <option value="">-- Pilih Unit Pengganti (Opsional) --</option>
                            @foreach($vehicles as $veh)
                                <option value="{{ $veh->id }}" data-label="{{ $veh->plate_number }} — {{ $veh->model_name }}">{{ $veh->plate_number }} — {{ $veh->model_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-amber-900 mb-1">Biaya Kendaraan Pengganti (Rp)</label>
                        <input type="number" name="replacement_cost" id="bast_replacement_cost" oninput="updateBastOverview()" value="0" min="0" class="w-full px-3 py-2.5 rounded-xl border border-amber-300 bg-white text-slate-900 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Mulai Berlaku Kontrak (Dalam BAST)</label>
                        <input type="date" name="contract_start_date" id="bast_contract_start" onchange="updateBastOverview()" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-slate-900">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Berakhir Kontrak (Dalam BAST)</label>
                        <input type="date" name="contract_end_date" id="bast_contract_end" onchange="updateBastOverview()" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-slate-900">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Diserahkan Oleh</label>
                        <input type="text" name="handed_over_by" id="bast_handed_by" oninput="updateBastOverview()" value="{{ Auth::user()->name }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-900">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Diterima Oleh *</label>
                        <input type="text" name="received_by" id="bast_received_by" oninput="updateBastOverview()" required placeholder="Nama penerima" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-900">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jabatan Penerima</label>
                        <input type="text" name="recipient_position" id="bast_position" oninput="updateBastOverview()" placeholder="Asisten / PIC" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-900">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Odometer (KM)</label>
                        <input type="number" name="odometer_km" id="bast_km" oninput="updateBastOverview()" placeholder="0" min="0" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-slate-900 font-mono">
                    </div>
                </div>

                <!-- Checklist Kelengkapan -->
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                    <span class="font-bold text-slate-800 block">Checklist Kelengkapan Serah Terima (Otomatis masuk ke Catatan PDF):</span>
                    <div class="flex flex-wrap gap-3 text-[11px]">
                        @foreach(['STNK Asli', 'Kunci Kontak & Cadangan', 'Helm SNI', 'Toolkit Standar', 'Kondisi Bodi Baik', 'Lampu & Kelistrikan Normal', 'Kabel Power / Aksesoris Lengkap'] as $chk)
                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                <input type="checkbox" class="bast-chk rounded text-[#015471]" value="{{ $chk }}" onchange="syncChecklistNotes()">
                                <span>{{ $chk }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Catatan Kondisi Unit & Kelengkapan</label>
                    <textarea name="condition_notes" id="bast_notes" oninput="updateBastOverview()" rows="2" placeholder="Detail kelengkapan atau kondisi fisik saat serah terima..." class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-slate-900"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Upload Scan BAST Bertanda Tangan (Opsional)</label>
                    <input type="file" name="signed_file" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-slate-600">
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('bastModal').classList.add('hidden')" class="px-4 py-2.5 rounded-xl border border-slate-200 font-bold text-slate-700">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white font-bold shadow-sm">Simpan, Terbitkan & Preview PDF</button>
                </div>
            </div>

            <!-- RIGHT: Live Overview Preview (5 cols) -->
            <div class="lg:col-span-5">
                <div class="bg-slate-50 border-2 border-slate-200 rounded-2xl p-5 space-y-4 sticky top-4">
                    <div class="flex items-center justify-between border-b-2 border-[#015471] pb-3">
                        <div>
                            <div class="text-sm font-black text-[#015471]">KOPERASI SWADHARMA</div>
                            <div class="text-[10px] text-slate-500">Live Preview Dokumen BAST (Siap Cetak PDF)</div>
                        </div>
                        <span id="ov_badge" class="px-2.5 py-1 rounded-md bg-[#FF7A3D] text-white text-[10px] font-black uppercase">BAST Unit Baru</span>
                    </div>

                    <div class="text-center py-1">
                        <div class="font-black text-slate-900 text-xs uppercase" id="ov_title">BERITA ACARA SERAH TERIMA (UNIT BARU)</div>
                        <div class="text-[10px] text-slate-500 font-mono">Nomor Otomatis: BAST-XXXX/KOPSWA/{{ date('Ym') }}</div>
                    </div>

                    <div class="bg-white rounded-xl border border-slate-200 p-3.5 space-y-2 text-[11px]">
                        <div class="flex justify-between"><span class="text-slate-500">Tanggal Serah Terima:</span><strong id="ov_date">{{ date('Y-m-d') }}</strong></div>
                        <div class="flex justify-between"><span class="text-slate-500">Kontrak Sewa:</span><strong id="ov_contract" class="text-right">-</strong></div>
                        <div class="flex justify-between"><span class="text-slate-500">Masa Berlaku Kontrak:</span><strong id="ov_period" class="font-mono text-[#015471]">-</strong></div>
                        <div class="flex justify-between"><span class="text-slate-500">Unit Aset:</span><strong id="ov_unit" class="text-right">-</strong></div>
                        <div id="ov_rep_row" class="hidden flex justify-between text-[#FF7A3D]"><span>Unit Pengganti:</span><strong id="ov_rep">-</strong></div>
                        <div id="ov_cost_row" class="hidden flex justify-between text-amber-700"><span>Biaya Pengganti:</span><strong id="ov_cost" class="font-mono">Rp 0</strong></div>
                        <div class="flex justify-between"><span class="text-slate-500">Odometer:</span><strong id="ov_km">-</strong></div>
                    </div>

                    <div class="bg-amber-50 border-l-4 border-[#FF7A3D] p-3 rounded-r-xl text-[11px] text-slate-700">
                        <strong>Klausul Kontrak:</strong> Berlaku sejak <span id="ov_clause_start" class="font-bold">-</span> s.d. <span id="ov_clause_end" class="font-bold">-</span>.
                    </div>

                    <div class="bg-white rounded-xl border border-slate-200 p-3 text-[11px]">
                        <span class="font-bold text-slate-500 block mb-1">Catatan Kondisi & Kelengkapan:</span>
                        <p id="ov_notes" class="text-slate-800 whitespace-pre-line">Unit diterima dalam kondisi baik dan lengkap.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-3 text-center text-[10px]">
                        <div>
                            <span class="text-slate-500 block">Yang Menyerahkan,</span>
                            <div class="h-10"></div>
                            <strong id="ov_handed" class="underline text-slate-900">{{ Auth::user()->name }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Yang Menerima,</span>
                            <div class="h-10"></div>
                            <strong id="ov_received" class="underline text-slate-900">-</strong>
                            <span id="ov_position" class="block text-slate-500"></span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Preview PDF Otomatis (Setelah Simpan / Klik Overview) -->
<div id="bastPreviewModal" class="hidden fixed inset-0 bg-black/75 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-5xl w-full h-[90vh] flex flex-col shadow-2xl overflow-hidden">
        <div class="px-5 py-3.5 bg-[#015471] text-white flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <i data-lucide="file-text" class="w-5 h-5 text-[#FF7A3D]"></i>
                <h3 class="font-bold text-sm">Overview & Pratinjau PDF Dokumen BAST</h3>
            </div>
            <div class="flex items-center gap-2">
                <a id="bastPreviewDownloadBtn" href="#" class="px-3.5 py-1.5 rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white text-xs font-bold inline-flex items-center gap-1.5">
                    <i data-lucide="download" class="w-3.5 h-3.5"></i> Unduh PDF
                </a>
                <button type="button" onclick="document.getElementById('bastPreviewModal').classList.add('hidden')" class="p-1.5 rounded-lg hover:bg-white/10">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        <iframe id="bastPreviewIframe" src="" class="flex-1 w-full bg-slate-100 border-0"></iframe>
    </div>
</div>

<!-- Modal Upload Ulang Scan BAST TTD Basah -->
<div id="uploadSignedModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Upload Scan BAST TTD Basah</h3>
                <p class="text-xs text-slate-500 mt-0.5">Nomor BAST: <span id="uploadSignedBastNumber" class="font-mono font-bold text-[#015471]"></span></p>
            </div>
            <button type="button" onclick="document.getElementById('uploadSignedModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="uploadSignedForm" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">File Scan BAST (PDF / JPG / PNG, maks. 10 MB) *</label>
                <input type="file" name="signed_file" accept=".pdf,.jpg,.jpeg,.png" required class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#015471]/10 file:text-[#015471] hover:file:bg-[#015471]/20 border border-slate-200 rounded-xl p-1.5">
                <p class="text-[11px] text-slate-400 mt-1.5">Jika sudah ada file sebelumnya, file lama akan otomatis diganti dengan file baru.</p>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('uploadSignedModal').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white text-xs font-bold inline-flex items-center gap-1.5">
                    <i data-lucide="upload" class="w-3.5 h-3.5"></i> Simpan Scan TTD
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateBastModal() {
        document.getElementById('bastModal').classList.remove('hidden');
        syncVehicleContract();
        updateBastOverview();
    }

    function openBastPreview(id) {
        document.getElementById('bastPreviewIframe').src = `/bast/${id}/preview`;
        document.getElementById('bastPreviewDownloadBtn').href = `/bast/${id}/pdf`;
        document.getElementById('bastPreviewModal').classList.remove('hidden');
    }

    function openUploadSignedModal(id, bastNumber) {
        document.getElementById('uploadSignedForm').action = `/bast/${id}/upload-signed`;
        document.getElementById('uploadSignedBastNumber').textContent = bastNumber;
        document.getElementById('uploadSignedModal').classList.remove('hidden');
    }

    function toggleReplacementFields() {
        const type = document.getElementById('bast_type').value;
        const box = document.getElementById('replacementFields');
        if (type === 'unit_pengganti') {
            box.classList.remove('hidden');
        } else {
            box.classList.add('hidden');
        }
    }

    function syncVehicleContract() {
        const sel = document.getElementById('bast_vehicle_id');
        const opt = sel.options[sel.selectedIndex];
        if (!opt) return;
        const vId = opt.dataset.vendorId;
        if (vId) {
            document.getElementById('bast_vendor_id').value = vId;
        }
        if (opt.dataset.contractStart) {
            document.getElementById('bast_contract_start').value = opt.dataset.contractStart;
        }
        if (opt.dataset.contractEnd) {
            document.getElementById('bast_contract_end').value = opt.dataset.contractEnd;
        }
    }

    function syncContractDates() {
        const sel = document.getElementById('bast_vendor_id');
        const opt = sel.options[sel.selectedIndex];
        if (opt && opt.dataset.start) {
            document.getElementById('bast_contract_start').value = opt.dataset.start;
        }
        if (opt && opt.dataset.end) {
            document.getElementById('bast_contract_end').value = opt.dataset.end;
        }
    }

    function syncChecklistNotes() {
        const checked = Array.from(document.querySelectorAll('.bast-chk:checked')).map(c => '✓ ' + c.value);
        const notesEl = document.getElementById('bast_notes');
        if (checked.length > 0) {
            notesEl.value = 'Kelengkapan Unit:\n' + checked.join('\n');
        }
        updateBastOverview();
    }

    function updateBastOverview() {
        const type = document.getElementById('bast_type').value;
        const typeLabels = {
            unit_baru: 'BAST Unit Baru',
            unit_pengganti: 'BAST Unit Pengganti',
            pengembalian: 'BAST Pengembalian Unit'
        };
        document.getElementById('ov_badge').innerText = typeLabels[type] || 'BAST';
        document.getElementById('ov_title').innerText = 'BERITA ACARA SERAH TERIMA (' + (typeLabels[type] || '').toUpperCase() + ')';

        document.getElementById('ov_date').innerText = document.getElementById('bast_handover_date').value || '-';

        const vSel = document.getElementById('bast_vendor_id');
        const vOpt = vSel.options[vSel.selectedIndex];
        document.getElementById('ov_contract').innerText = (vOpt && vOpt.value) ? vOpt.dataset.label : 'Sesuai Kontrak Unit';

        const cStart = document.getElementById('bast_contract_start').value || '-';
        const cEnd = document.getElementById('bast_contract_end').value || '-';
        document.getElementById('ov_period').innerText = `${cStart} s.d. ${cEnd}`;
        document.getElementById('ov_clause_start').innerText = cStart;
        document.getElementById('ov_clause_end').innerText = cEnd;

        const uSel = document.getElementById('bast_vehicle_id');
        const uOpt = uSel.options[uSel.selectedIndex];
        document.getElementById('ov_unit').innerText = uOpt ? uOpt.dataset.label : '-';

        const repSel = document.getElementById('bast_replacement_id');
        const repOpt = repSel?.options[repSel.selectedIndex];
        const repCost = parseFloat(document.getElementById('bast_replacement_cost')?.value || 0);
        document.getElementById('ov_rep_row').classList.toggle('hidden', type !== 'unit_pengganti' || !repOpt?.value);
        document.getElementById('ov_rep').innerText = repOpt?.dataset.label || '-';
        document.getElementById('ov_cost_row').classList.toggle('hidden', type !== 'unit_pengganti' || repCost <= 0);
        document.getElementById('ov_cost').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(repCost);

        const km = document.getElementById('bast_km').value;
        document.getElementById('ov_km').innerText = km ? new Intl.NumberFormat('id-ID').format(km) + ' KM' : '-';
        document.getElementById('ov_notes').innerText = document.getElementById('bast_notes').value || 'Unit diterima dalam kondisi baik dan lengkap.';
        document.getElementById('ov_handed').innerText = document.getElementById('bast_handed_by').value || 'Tim Operasional';
        document.getElementById('ov_received').innerText = document.getElementById('bast_received_by').value || 'Penerima Unit';
        document.getElementById('ov_position').innerText = document.getElementById('bast_position').value || '';
    }

    @if(session('preview_bast_id'))
        document.addEventListener('DOMContentLoaded', () => openBastPreview({{ session('preview_bast_id') }}));
    @elseif(request()->filled('vehicle_id'))
        document.addEventListener('DOMContentLoaded', () => {
            const vSel = document.getElementById('bast_vehicle_id');
            if (vSel) {
                vSel.value = '{{ request('vehicle_id') }}';
                openCreateBastModal();
            }
        });
    @endif
</script>
@endsection
