@extends('layouts.app')

@section('title', 'Registrasi Unit Armada Baru')
@section('page_title', 'Registrasi Armada Baru')

@section('content')
<div class="w-full space-y-6">

    <!-- Form Tag Wrapping Everything -->
    <form action="{{ route('vehicles.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Top Header Action Bar -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#015471] to-[#023b4f] text-white flex items-center justify-center shadow-sm shrink-0">
                        <i data-lucide="plus-circle" class="w-6 h-6 text-[#FF7A3D]"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h1 class="text-xl font-black text-slate-900 tracking-tight">Formulir Tambah Armada Baru</h1>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#015471]/10 text-[#015471] border border-[#015471]/20">
                                Unit Baru
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Daftarkan kategori, merk, tipe, pajak tahunan & 5 tahunan, serta berkas legalitas STNK & BPKB</p>
                    </div>
                </div>

                <!-- Action CTA Buttons -->
                <div class="flex items-center gap-2.5 shrink-0">
                    <a href="{{ route('vehicles.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 hover:text-slate-900 transition shadow-2xs">
                        <i data-lucide="arrow-left" class="w-4 h-4 text-slate-400"></i> Batal
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-sm transition">
                        <i data-lucide="save" class="w-4 h-4"></i> Simpan Armada Baru
                    </button>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-500 shrink-0 mt-0.5"></i>
                <div>
                    <strong class="font-bold block mb-1">Perhatian: Mohon periksa kesalahan input berikut:</strong>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- 2-Column Split Master Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- LEFT COLUMN: Identitas, Spesifikasi & Pajak (7 Cols) -->
            <div class="lg:col-span-7 space-y-6">

                <!-- Card 1: Identitas & Spesifikasi Unit -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-[#015471] text-white text-xs font-black flex items-center justify-center shadow-2xs">1</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900">Identitas, Merk & Spesifikasi Kendaraan</h2>
                                <p class="text-[11px] text-slate-500">Kategori, pabrikan, tipe kendaraan, dan nomor identifikasi</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-200/60 px-2 py-0.5 rounded">Wajib Diisi</span>
                    </div>

                    <div class="p-6 space-y-5">
                        <!-- Visual Asset Type Selector -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-700">Pilih Jenis Aset yang Akan Diinput <span class="text-[#FF7A3D]">*</span></label>
                            <input type="hidden" name="asset_type" id="asset_type" value="{{ old('asset_type', 'motor') }}">
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" id="assetTypeCards">
                                <button type="button" onclick="selectAssetType('motor')" data-type="motor" class="asset-type-btn p-3 rounded-2xl border-2 text-left transition flex items-center gap-2.5 border-[#015471] bg-[#015471]/5">
                                    <div class="w-8 h-8 rounded-xl bg-[#015471] text-white flex items-center justify-center shrink-0"><i data-lucide="bike" class="w-4 h-4"></i></div>
                                    <div><span class="font-black text-xs text-slate-900 block">Motor</span><span class="text-[10px] text-slate-500">Roda 2 / Trail</span></div>
                                </button>
                                <button type="button" onclick="selectAssetType('mobil')" data-type="mobil" class="asset-type-btn p-3 rounded-2xl border-2 text-left transition flex items-center gap-2.5 border-slate-200 bg-white hover:border-slate-300">
                                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0"><i data-lucide="car" class="w-4 h-4"></i></div>
                                    <div><span class="font-black text-xs text-slate-900 block">Mobil</span><span class="text-[10px] text-slate-500">Roda 4 / Niaga</span></div>
                                </button>
                                <button type="button" onclick="selectAssetType('fotocopy')" data-type="fotocopy" class="asset-type-btn p-3 rounded-2xl border-2 text-left transition flex items-center gap-2.5 border-slate-200 bg-white hover:border-slate-300">
                                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0"><i data-lucide="printer" class="w-4 h-4"></i></div>
                                    <div><span class="font-black text-xs text-slate-900 block">Mesin Fotocopy</span><span class="text-[10px] text-slate-500">Multifungsi Kantor</span></div>
                                </button>
                                <button type="button" onclick="selectAssetType('dispenser')" data-type="dispenser" class="asset-type-btn p-3 rounded-2xl border-2 text-left transition flex items-center gap-2.5 border-slate-200 bg-white hover:border-slate-300">
                                    <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0"><i data-lucide="droplet" class="w-4 h-4"></i></div>
                                    <div><span class="font-black text-xs text-slate-900 block">Dispenser</span><span class="text-[10px] text-slate-500">Galon / Water Unit</span></div>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Plat Nomor (Khusus Motor & Mobil) -->
                            <div id="plateNumberGroup">
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Plat Nomor Kendaraan <span class="text-[#FF7A3D]">*</span>
                                </label>
                                <input type="text" name="plate_number" id="plate_number_input" value="{{ old('plate_number') }}" placeholder="Contoh: BE 2402 AIT" class="w-full text-xs font-mono font-bold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] focus:border-transparent text-slate-900 uppercase">
                                @error('plate_number') <span class="text-[#FF7A3D] text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                            </div>

                            <!-- Kode Aset Internal (Otomatis & Increment per Kategori) -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                                    <span>Kode Aset Internal</span>
                                    <span class="text-[10px] text-[#015471] font-bold">Auto Increment</span>
                                </label>
                                <input type="text" name="asset_code" id="asset_code_input" value="{{ old('asset_code', $nextAssetCodes['motor'] ?? 'KOPSWA-MTR-001') }}" class="w-full text-xs font-mono font-bold px-3.5 py-2.5 rounded-xl border border-slate-300 bg-slate-50 text-[#015471]">
                            </div>

                            <!-- Serial Number (Khusus Fotocopy & Dispenser) -->
                            <div id="serialNumberGroup" class="hidden">
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Serial Number (S/N) <span class="text-[#FF7A3D]">*</span></label>
                                <input type="text" name="serial_number" id="serial_number_input" value="{{ old('serial_number') }}" placeholder="Masukkan Serial Number Pabrikan" class="w-full text-xs font-mono px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900">
                            </div>

                            <!-- Kategori -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Kategori Aset <span class="text-[#FF7A3D]">*</span>
                                </label>
                                <select name="category" id="category" required class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer">
                                    <option value="Roda 2 (Motor)">Roda 2 (Sepeda Motor)</option>
                                    <option value="Roda 4 (Mobil/Truk)">Roda 4 (Mobil / Pick Up / Truk)</option>
                                    <option value="Mesin Fotocopy">Mesin Fotocopy</option>
                                    <option value="Dispenser">Dispenser</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Merk (Dinamis sesuai Jenis Aset) -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Merk <span class="text-[#FF7A3D]">*</span>
                                </label>
                                <select name="brand" id="brand" required class="w-full text-xs font-bold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer"></select>
                            </div>

                            <!-- Model / Nama Unit -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Nama Model / Varian <span class="text-[#FF7A3D]">*</span>
                                </label>
                                <input type="text" name="model_name" id="model_name" value="{{ old('model_name', 'Honda CRF 150 L') }}" required class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                            </div>

                            <!-- Tipe / Segmen (Dinamis sesuai Jenis Aset) -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5" id="typeNameLabel">
                                    Tipe / Segmen Unit
                                </label>
                                <select name="type_name" id="type_name" class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer"></select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Tarif Sewa -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Tarif Sewa / Bulan <span class="text-[#FF7A3D]">*</span>
                                </label>
                                <div class="flex rounded-xl shadow-2xs">
                                    <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-slate-300 bg-slate-50 text-slate-500 text-xs font-bold">Rp</span>
                                    <input type="number" name="rental_price_per_month" id="rental_price" value="{{ old('rental_price_per_month', 1570000) }}" required class="w-full text-xs font-bold px-3 py-2.5 rounded-r-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                                </div>
                            </div>

                            <!-- Shortcut Pilihan Model Cepat (Dinamis sesuai Jenis Aset) -->
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex flex-wrap items-center gap-1.5 text-xs" id="quickTemplatesContainer"></div>
                        </div>

                        <div id="vehicleFrameEngineRow" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Frame Number (VIN) -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor Rangka (VIN)</label>
                                <input type="text" name="frame_number" value="{{ old('frame_number') }}" placeholder="MH1KD111..." class="w-full text-xs font-mono px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                            </div>

                            <!-- Engine Number -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor Mesin</label>
                                <input type="text" name="engine_number" value="{{ old('engine_number') }}" placeholder="KD11E..." class="w-full text-xs font-mono px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Year -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tahun Perakitan</label>
                                <input type="number" name="year" value="{{ old('year', date('Y')) }}" class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                            </div>

                            <!-- Color -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Warna Unit</label>
                                <input type="text" name="color" value="{{ old('color', 'Merah Hitam') }}" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Pengingat Pajak Kendaraan (PKB Tahunan & Plat 5 Tahunan) -->
                <div id="taxScheduleCard" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-amber-500 text-white text-xs font-black flex items-center justify-center shadow-2xs">2</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900">Masa Berlaku Pajak & Plat Nomor</h2>
                                <p class="text-[11px] text-slate-500">Jadwal jatuh tempo pajak tahunan (PKB) & pajak plat 5 tahunan untuk pengingat otomatis</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-amber-700 uppercase tracking-wider bg-amber-50 border border-amber-200 px-2 py-0.5 rounded">Pengingat Samsat</span>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Pajak Tahunan PKB -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Jatuh Tempo Pajak Tahunan (PKB)
                                </label>
                                <input type="date" name="tax_annual_date" value="{{ old('tax_annual_date') }}" class="w-full text-xs font-mono font-bold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                                <span class="text-[11px] text-slate-400 block mt-1">Tanggal perpanjangan STNK tahunan berikutnya</span>
                            </div>

                            <!-- Pajak Plat 5 Tahunan -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Jatuh Tempo Pajak & Plat 5 Tahunan
                                </label>
                                <input type="date" name="tax_5year_date" value="{{ old('tax_5year_date') }}" class="w-full text-xs font-mono font-bold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                                <span class="text-[11px] text-slate-400 block mt-1">Tanggal penggantian plat kaleng / TNKB 5 tahunan</span>
                            </div>
                        </div>

                        <div class="p-3 bg-blue-50/70 rounded-xl border border-blue-100 flex items-start gap-2.5 text-xs text-slate-600">
                            <i data-lucide="info" class="w-4 h-4 text-[#015471] shrink-0 mt-0.5"></i>
                            <div>
                                Tanggal ini akan otomatis membuat entri pengingat pada halaman <strong>Dokumen & Pajak Kendaraan</strong> dan memicu notifikasi peringatan jika waktu jatuh tempo tersisa < 30 hari.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Kontrak Klien, Penempatan Kebun & Status Operasional -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-[#015471] text-white text-xs font-black flex items-center justify-center shadow-2xs">3</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900">Kontrak Klien & Penempatan Kebun</h2>
                                <p class="text-[11px] text-slate-500">Mitra penyewa, lokasi kerja operasional, dan pemegang unit</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-[#015471] uppercase tracking-wider bg-[#015471]/10 px-2 py-0.5 rounded">Distribusi</span>
                    </div>

                    <div class="p-6 space-y-5">
                        <!-- Status Operasional Awal (Menentukan alur Client & Kontrak) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Status Unit Operasional <span class="text-[#FF7A3D]">*</span></label>
                            <select name="status" id="status_select" onchange="toggleStatusContractFields()" class="w-full text-xs font-bold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer">
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Aktif Disewa (Terikat Kontrak Klien)</option>
                                <option value="standby" {{ old('status') == 'standby' ? 'selected' : '' }}>Tersedia / Standby (Pool Koperasi)</option>
                                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Dalam Perbaikan / Servis</option>
                                <option value="replaced" {{ old('status') == 'replaced' ? 'selected' : '' }}>Unit Pengganti Aktif</option>
                                <option value="sold" {{ old('status') == 'sold' ? 'selected' : '' }}>Dijual / Terjual</option>
                                <option value="lost" {{ old('status') == 'lost' ? 'selected' : '' }}>Hilang / Lapor Polisi</option>
                            </select>
                        </div>

                        <!-- Pilihan Klien -> Kontrak (Aktif jika status Aktif Disewa / Terikat Kontrak) -->
                        <div id="contractAssignmentBox" class="p-4 rounded-2xl bg-[#015471]/5 border border-[#015471]/20 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-800 mb-1.5">
                                    1. Pilih Perusahaan Klien <span class="text-[#FF7A3D] contract-req-star">*</span>
                                </label>
                                <select id="client_selector" onchange="filterContractsByClient()" class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900 cursor-pointer">
                                    <option value="">-- Pilih Perusahaan Klien --</option>
                                    @foreach($clients ?? [] as $cl)
                                        <option value="{{ $cl->id }}" {{ old('client_id') == $cl->id ? 'selected' : '' }}>{{ $cl->name }} ({{ $cl->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-800 mb-1.5">
                                    2. Pilih Nomor Kontrak Sewa <span class="text-[#FF7A3D] contract-req-star">*</span>
                                </label>
                                <select name="vendor_id" id="vendor_select" class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer">
                                    <option value="">-- Pilih Kontrak Sewa --</option>
                                    @foreach($vendors as $v)
                                        <option value="{{ $v->id }}" data-client-id="{{ $v->client_id }}" {{ (old('vendor_id', session('active_vendor_id')) == $v->id) ? 'selected' : '' }}>
                                            {{ $v->contract_number ?: $v->code }} — {{ $v->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Detail Penjualan (Muncul jika status = Dijual / Terjual) -->
                        <div id="soldFieldsBox" class="hidden p-4 rounded-2xl bg-slate-100 border border-slate-300 grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Dijual</label>
                                <input type="date" name="sold_date" value="{{ old('sold_date') }}" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 bg-white text-slate-900">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Harga Jual (Rp)</label>
                                <input type="number" name="sold_price" value="{{ old('sold_price') }}" min="0" class="w-full text-xs font-mono px-3 py-2 rounded-xl border border-slate-300 bg-white text-slate-900">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Pembeli / Keterangan</label>
                                <input type="text" name="sold_to" value="{{ old('sold_to') }}" placeholder="Nama Pembeli..." class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 bg-white text-slate-900">
                            </div>
                        </div>

                        <div id="driverPicBox" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Pemegang / Pengguna -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                                    <span id="userHolderLabel">Nama Pengemudi / Pemakai</span>
                                    <span class="text-[10px] text-slate-400 font-normal" id="userHolderBadge">PIC Driver</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                        <i data-lucide="user" class="w-4 h-4"></i>
                                    </div>
                                    <input type="text" name="user_holder" id="user_holder_input" value="{{ old('user_holder') }}" placeholder="Contoh: Bpk. Bambang / Asisten Afdeling 1" class="w-full text-xs font-semibold pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                                </div>
                            </div>

                            <!-- No HP / WhatsApp -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                                    <span id="driverPhoneLabel">No. HP / WhatsApp Pengemudi</span>
                                    <span class="text-[10px] text-emerald-600 font-bold">WA Aktif</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                        <i data-lucide="phone" class="w-4 h-4"></i>
                                    </div>
                                    <input type="text" name="driver_phone" value="{{ old('driver_phone') }}" placeholder="08xxxxxxxxxx" class="w-full text-xs font-semibold pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                                </div>
                            </div>
                        </div>

                        <!-- Valuasi, Penyusutan & Diskon Dealer -->
                        <div class="pt-4 border-t border-slate-100 space-y-3">
                            <span class="text-xs font-black text-[#015471] uppercase tracking-wider block">Valuasi Perolehan, Penyusutan & Diskon Dealer</span>
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Harga Beli / Faktur (Rp)</label>
                                    <input type="number" name="invoice_value" id="invoice_value_input" oninput="syncAcqAndCalculate()" value="{{ old('invoice_value', 0) }}" min="0" class="w-full text-xs font-mono px-3 py-2 rounded-xl border border-slate-300 text-slate-900">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Nilai Perolehan (Rp)</label>
                                    <input type="number" name="acquisition_value" id="acquisition_value_input" oninput="calculateLiveBookValue()" value="{{ old('acquisition_value', 0) }}" min="0" class="w-full text-xs font-mono px-3 py-2 rounded-xl border border-slate-300 text-slate-900">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">PPnPM (Rp)</label>
                                    <input type="number" name="ppn_pm" value="{{ old('ppn_pm', 0) }}" min="0" class="w-full text-xs font-mono px-3 py-2 rounded-xl border border-slate-300 text-slate-900">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Diskon Dealer (Rp)</label>
                                    <input type="number" name="dealer_discount" value="{{ old('dealer_discount', 0) }}" min="0" class="w-full text-xs font-mono px-3 py-2 rounded-xl border border-emerald-300 bg-emerald-50/40 text-slate-900">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Nilai Kontrak Unit (Rp)</label>
                                    <input type="number" name="total_contract_value" value="{{ old('total_contract_value', 0) }}" min="0" class="w-full text-xs font-mono px-3 py-2 rounded-xl border border-slate-300 text-slate-900">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Nilai Tambahan Total (Rp)</label>
                                    <input type="number" name="additional_contract_value" value="{{ old('additional_contract_value', 0) }}" min="0" class="w-full text-xs font-mono px-3 py-2 rounded-xl border border-slate-300 text-slate-900">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Nilai Tambahan / Bln (Rp)</label>
                                    <input type="number" name="additional_monthly_value" value="{{ old('additional_monthly_value', 0) }}" min="0" class="w-full text-xs font-mono px-3 py-2 rounded-xl border border-slate-300 text-slate-900">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-5 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Dealer / Supplier</label>
                                    <input type="text" name="dealer_name" value="{{ old('dealer_name') }}" placeholder="PT Dealer..." class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 text-slate-900">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Tgl Perolehan</label>
                                    <input type="date" name="acquisition_date" id="acquisition_date_input" onchange="calculateLiveBookValue()" value="{{ old('acquisition_date') }}" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 text-slate-900">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Umur Manfaat (Bln)</label>
                                    <input type="number" name="useful_life_months" id="useful_life_input" oninput="calculateLiveBookValue()" value="{{ old('useful_life_months', 48) }}" min="1" class="w-full text-xs font-mono px-3 py-2 rounded-xl border border-slate-300 text-slate-900">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Residu (%)</label>
                                    <input type="number" step="0.01" name="residual_percentage" value="{{ old('residual_percentage', 0) }}" min="0" max="100" class="w-full text-xs font-mono px-3 py-2 rounded-xl border border-slate-300 text-slate-900">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Siklus Sewa</label>
                                    <select name="billing_cycle" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 bg-white text-slate-900">
                                        <option value="bulanan" {{ old('billing_cycle', 'bulanan') == 'bulanan' ? 'selected' : '' }}>1 Bulan</option>
                                        <option value="triwulanan" {{ old('billing_cycle') == 'triwulanan' ? 'selected' : '' }}>3 Bulan</option>
                                        <option value="semesteran" {{ old('billing_cycle') == 'semesteran' ? 'selected' : '' }}>6 Bulan</option>
                                        <option value="tahunan" {{ old('billing_cycle') == 'tahunan' ? 'selected' : '' }}>12 Bulan</option>
                                        <option value="2_tahunan" {{ old('billing_cycle') == '2_tahunan' ? 'selected' : '' }}>24 Bulan</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Live Calculation: Penyusutan & Sisa Nilai Buku -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                                <div class="p-3 rounded-xl bg-amber-50/70 border border-amber-200">
                                    <span class="text-[10px] font-bold text-amber-800 uppercase block">Penyusutan / Bulan</span>
                                    <span id="liveMonthlyDep" class="text-sm font-black font-mono text-amber-900 block mt-0.5">Rp 0</span>
                                </div>
                                <div class="p-3 rounded-xl bg-rose-50/70 border border-rose-200">
                                    <span class="text-[10px] font-bold text-rose-800 uppercase block">Akum. Penyusutan (<span id="liveElapsedMonths">0</span> bln)</span>
                                    <span id="liveAccumDep" class="text-sm font-black font-mono text-rose-900 block mt-0.5">Rp 0</span>
                                </div>
                                <div class="p-3 rounded-xl bg-[#015471]/5 border border-[#015471]/25">
                                    <span class="text-[10px] font-black text-[#015471] uppercase block">Sisa Nilai Buku (Otomatis)</span>
                                    <span id="liveBookValue" class="text-sm font-black font-mono text-[#015471] block mt-0.5">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Dokumen Legalitas & Catatan Tambahan (5 Cols) -->
            <div class="lg:col-span-5 space-y-6">

                <!-- Card 4: Dokumen Legalitas (STNK & BPKB) -->
                <div id="stnkBpkbCard" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-[#FF7A3D] text-white text-xs font-black flex items-center justify-center shadow-2xs">4</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900">Dokumen STNK & BPKB</h2>
                                <p class="text-[11px] text-slate-500">Berkas fisik dan nomor registrasi legalitas</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-[#FF7A3D] uppercase tracking-wider bg-[#FF7A3D]/10 px-2 py-0.5 rounded">PDF / Foto</span>
                    </div>

                    <div class="p-6 space-y-4">
                        <!-- STNK Section -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <i data-lucide="file-badge" class="w-4 h-4 text-[#015471]"></i>
                                Surat Tanda Nomor Kendaraan (STNK)
                            </label>
                            <div>
                                <input type="text" name="stnk_number" value="{{ old('stnk_number') }}" placeholder="Nomor seri STNK..." class="w-full text-xs font-mono px-3.5 py-2 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                            </div>
                            
                            <div class="relative border-2 border-dashed border-slate-300 hover:border-[#015471] rounded-xl p-3.5 text-center cursor-pointer transition bg-slate-50/60 hover:bg-white group" onclick="document.getElementById('stnk_file').click()">
                                <input type="file" id="stnk_file" name="stnk_file" accept=".pdf,.jpg,.jpeg,.png,.webp" class="hidden" onchange="previewUpload(this, 'stnk-preview-text')">
                                <div class="w-8 h-8 rounded-xl bg-[#015471]/10 group-hover:bg-[#015471]/20 text-[#015471] flex items-center justify-center mx-auto mb-1.5 transition">
                                    <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-700 group-hover:text-[#015471] transition" id="stnk-preview-text">Pilih Berkas STNK</p>
                                <p class="text-[10px] text-slate-400">PDF, JPG, PNG (Maks 10MB)</p>
                            </div>
                            @error('stnk_file') <span class="text-[#FF7A3D] text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <div class="border-t border-slate-100"></div>

                        <!-- BPKB Section -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <i data-lucide="book-marked" class="w-4 h-4 text-[#FF7A3D]"></i>
                                Buku Pemilik Kendaraan Bermotor (BPKB)
                            </label>
                            <div>
                                <input type="text" name="bpkb_number" value="{{ old('bpkb_number') }}" placeholder="Nomor BPKB..." class="w-full text-xs font-mono px-3.5 py-2 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                            </div>
                            
                            <div class="relative border-2 border-dashed border-slate-300 hover:border-[#FF7A3D] rounded-xl p-3.5 text-center cursor-pointer transition bg-slate-50/60 hover:bg-white group" onclick="document.getElementById('bpkb_file').click()">
                                <input type="file" id="bpkb_file" name="bpkb_file" accept=".pdf,.jpg,.jpeg,.png,.webp" class="hidden" onchange="previewUpload(this, 'bpkb-preview-text')">
                                <div class="w-8 h-8 rounded-xl bg-[#FF7A3D]/10 group-hover:bg-[#FF7A3D]/20 text-[#FF7A3D] flex items-center justify-center mx-auto mb-1.5 transition">
                                    <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-700 group-hover:text-[#FF7A3D] transition" id="bpkb-preview-text">Pilih Berkas BPKB</p>
                                <p class="text-[10px] text-slate-400">PDF, JPG, PNG (Maks 10MB)</p>
                            </div>
                            @error('bpkb_file') <span class="text-[#FF7A3D] text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Card 5: Catatan & Submit Action -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-slate-700 text-white text-xs font-black flex items-center justify-center shadow-2xs">5</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900">Catatan Tambahan & Simpan</h2>
                                <p class="text-[11px] text-slate-500">Keterangan pengadaan atau catatan kondisi</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        <div>
                            <textarea name="notes" rows="3" class="w-full text-xs p-3.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900" placeholder="Keterangan pengadaan, kelengkapan unit, atau catatan riwayat...">{{ old('notes') }}</textarea>
                        </div>

                        <div class="pt-2 flex items-center justify-end gap-3 border-t border-slate-100">
                            <a href="{{ route('vehicles.index') }}" class="px-4 py-2.5 text-xs font-bold rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 transition">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-sm transition">
                                <i data-lucide="save" class="w-4 h-4"></i> Simpan Armada Baru
                            </button>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </form>

</div>

<!-- Modal Tambah Wilayah / Kebun Cepat -->
<div id="addLocationModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 transition-all">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#015471]/10 text-[#015471] flex items-center justify-center font-bold">
                    <i data-lucide="map-pin" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Tambah Wilayah / Kebun Baru</h3>
                    <p class="text-[11px] text-slate-500">Tambahkan lokasi kebun untuk klien terpilih</p>
                </div>
            </div>
            <button type="button" onclick="closeAddLocationModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="quickLocationForm" class="mt-4 space-y-4" onsubmit="submitQuickLocation(event)">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Klien Terpilih <span class="text-[#FF7A3D]">*</span></label>
                <input type="text" id="modal_vendor_name" readonly class="w-full text-xs font-semibold px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 cursor-not-allowed">
                <input type="hidden" id="modal_vendor_id" name="vendor_id">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Wilayah / Kebun <span class="text-[#FF7A3D]">*</span></label>
                <input type="text" id="modal_loc_name" name="name" required placeholder="Contoh: Kebun Bergen, Kebun Kedaton..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kode Kebun</label>
                    <input type="text" id="modal_loc_code" name="code" placeholder="Contoh: BERGEN" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 uppercase">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Manajer / PIC</label>
                    <input type="text" id="modal_loc_manager" name="manager_name" placeholder="Nama Manajer" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeAddLocationModal()" class="px-4 py-2 text-xs font-bold rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 transition">
                    Batal
                </button>
                <button type="submit" id="submitLocBtn" class="px-4 py-2 text-xs font-bold rounded-xl bg-[#015471] hover:bg-[#023e54] text-white transition flex items-center gap-1.5">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                    <span>Simpan Kebun</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function setTemplate(brand, model, type, price) {
        document.getElementById('brand').value = brand;
        document.getElementById('model_name').value = model;
        document.getElementById('type_name').value = type;
        document.getElementById('rental_price').value = price;
    }

    function previewUpload(input, targetId) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const fileName = file.name;
            const fileSize = (file.size / (1024 * 1024)).toFixed(2);
            document.getElementById(targetId).innerHTML = `
                <span class="text-[#015471] font-bold block truncate">✓ ${fileName}</span> 
                <span class="text-slate-400 text-[10px] block mt-0.5">${fileSize} MB • Siap diunggah</span>
            `;
        }
    }

    // Filter Lokasi Kebun sesuai Klien yang dipilih
    const vendorSelect = document.getElementById('vendor_select');
    const locationSelect = document.getElementById('location_select');

    function filterLocationsByVendor() {
        if (!vendorSelect || !locationSelect) return;
        const selectedVendorId = vendorSelect.value;
        const options = locationSelect.querySelectorAll('option');

        options.forEach((opt, idx) => {
            if (idx === 0) return; // Keep "-- Pilih Wilayah / Kebun --"
            const vId = opt.getAttribute('data-vendor-id');
            if (!selectedVendorId || !vId || vId === selectedVendorId) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
                if (opt.selected) {
                    locationSelect.value = '';
                }
            }
        });
    }

    if (vendorSelect) {
        vendorSelect.addEventListener('change', filterLocationsByVendor);
        // Initial run
        filterLocationsByVendor();
    }

    function openAddLocationModal() {
        const vId = vendorSelect.value;
        if (!vId) {
            alert('Silakan pilih Klien Penyewa / Mitra terlebih dahulu.');
            vendorSelect.focus();
            return;
        }
        const vText = vendorSelect.options[vendorSelect.selectedIndex].text;
        document.getElementById('modal_vendor_id').value = vId;
        document.getElementById('modal_vendor_name').value = vText;
        document.getElementById('modal_loc_name').value = '';
        document.getElementById('modal_loc_code').value = '';
        document.getElementById('modal_loc_manager').value = '';
        document.getElementById('addLocationModal').classList.remove('hidden');
        lucide.createIcons();
    }

    function closeAddLocationModal() {
        document.getElementById('addLocationModal').classList.add('hidden');
    }

    async function submitQuickLocation(e) {
        e.preventDefault();
        const btn = document.getElementById('submitLocBtn');
        btn.disabled = true;
        btn.innerText = 'Menyimpan...';

        const payload = {
            vendor_id: document.getElementById('modal_vendor_id').value,
            name: document.getElementById('modal_loc_name').value,
            code: document.getElementById('modal_loc_code').value,
            manager_name: document.getElementById('modal_loc_manager').value,
            _token: '{{ csrf_token() }}'
        };

        try {
            const res = await fetch('{{ route("locations.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            if (res.ok && data.success) {
                // Add new option to select
                const newOpt = document.createElement('option');
                newOpt.value = data.location.id;
                newOpt.text = data.location.name;
                newOpt.setAttribute('data-vendor-id', data.location.vendor_id);
                newOpt.selected = true;
                locationSelect.appendChild(newOpt);

                closeAddLocationModal();
                filterLocationsByVendor();
            } else {
                alert(data.message || 'Gagal menyimpan wilayah kebun.');
            }
        } catch (err) {
            alert('Terjadi kesalahan jaringan.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="check" class="w-3.5 h-3.5"></i><span>Simpan Kebun</span>';
            lucide.createIcons();
        }
    }

    const NEXT_ASSET_CODES = {!! json_encode($nextAssetCodes ?? ['motor' => 'KOPSWA-MTR-001', 'mobil' => 'KOPSWA-MBL-001', 'fotocopy' => 'KOPSWA-FC-001', 'dispenser' => 'KOPSWA-DSP-001']) !!};

    const ASSET_CONFIGS = {
        motor: {
            isVehicle: true,
            category: 'Roda 2 (Motor)',
            brands: ['Honda', 'Yamaha', 'Kawasaki', 'Suzuki', 'Lainnya'],
            types: ['Trail On/Off Road', 'Bebek / Underbone', 'Matic / Skuter', 'Sport', 'Lainnya'],
            modelPlaceholder: 'Contoh: Honda CRF 150 L / Supra X 125',
            defaultModel: 'Honda CRF 150 L',
            defaultPrice: 1570000,
            userLabel: 'Nama Pengemudi / Pemakai',
            userBadge: 'PIC Driver',
            userPlaceholder: 'Contoh: Bpk. Bambang / Asisten Afdeling 1',
            phoneLabel: 'No. HP / WhatsApp Pengemudi',
            replacementLabel: 'Motor Pengganti',
            templates: [
                { brand: 'Honda', model: 'Honda CRF 150 L', type: 'Trail On/Off Road', price: 1570000, label: 'Honda CRF 150 L' },
                { brand: 'Honda', model: 'Honda Supra X 125 CW FI', type: 'Bebek / Underbone', price: 980000, label: 'Supra X 125' },
                { brand: 'Kawasaki', model: 'Kawasaki KLX 150', type: 'Trail On/Off Road', price: 1600000, label: 'KLX 150' }
            ]
        },
        mobil: {
            isVehicle: true,
            category: 'Roda 4 (Mobil/Truk)',
            brands: ['Toyota', 'Mitsubishi', 'Daihatsu', 'Suzuki', 'Honda', 'Isuzu', 'Lainnya'],
            types: ['Double Cabin (4x4)', 'Pick Up / Bak Terbuka', 'Minibus / MPV', 'SUV', 'Sedan', 'Box / Niaga', 'Lainnya'],
            modelPlaceholder: 'Contoh: Toyota Hilux 4x4 / Avanza / Gran Max',
            defaultModel: 'Toyota Hilux 4x4',
            defaultPrice: 8500000,
            userLabel: 'Nama Driver / PIC Kendaraan',
            userBadge: 'Driver Mobil',
            userPlaceholder: 'Contoh: Bpk. Rudi / Operasional Kebun',
            phoneLabel: 'No. HP / WhatsApp Driver',
            replacementLabel: 'Mobil Pengganti',
            templates: [
                { brand: 'Toyota', model: 'Toyota Hilux Double Cabin 4x4', type: 'Double Cabin (4x4)', price: 11500000, label: 'Hilux 4x4' },
                { brand: 'Toyota', model: 'Toyota Avanza 1.5 G', type: 'Minibus / MPV', price: 6500000, label: 'Avanza MPV' },
                { brand: 'Daihatsu', model: 'Daihatsu Gran Max Pick Up', type: 'Pick Up / Bak Terbuka', price: 4800000, label: 'Gran Max PU' }
            ]
        },
        fotocopy: {
            isVehicle: false,
            category: 'Mesin Fotocopy',
            brands: ['Canon', 'Fuji Xerox', 'Ricoh', 'Sharp', 'Konica Minolta', 'Kyocera', 'Lainnya'],
            types: ['Monochrome A3/A4', 'Color Multifunction', 'High-Speed Production', 'Desktop Office', 'Lainnya'],
            modelPlaceholder: 'Contoh: Canon imageRUNNER 2625i / Fuji Xerox ApeosPort',
            defaultModel: 'Canon imageRUNNER 2625i',
            defaultPrice: 2500000,
            userLabel: 'PIC Ruangan / Divisi Pemakai',
            userBadge: 'PIC Kantor',
            userPlaceholder: 'Contoh: Bagian Umum / Ruang Sekretariat Lt. 2',
            phoneLabel: 'No. HP / Ext. PIC Ruangan',
            replacementLabel: 'Unit Pengganti',
            templates: [
                { brand: 'Canon', model: 'Canon imageRUNNER 2625i', type: 'Monochrome A3/A4', price: 2500000, label: 'Canon iR 2625i' },
                { brand: 'Fuji Xerox', model: 'Fuji Xerox ApeosPort C3070', type: 'Color Multifunction', price: 3800000, label: 'Xerox C3070' },
                { brand: 'Ricoh', model: 'Ricoh MP 3055', type: 'Monochrome A3/A4', price: 2750000, label: 'Ricoh MP 3055' }
            ]
        },
        dispenser: {
            isVehicle: false,
            category: 'Dispenser',
            brands: ['Modena', 'Sanken', 'Miyako', 'Polytron', 'Sharp', 'Cosmos', 'Lainnya'],
            types: ['Galon Bawah (Bottom Load)', 'Galon Atas (Standing)', 'Table Top / Meja', 'Hot & Cold Standard', 'Lainnya'],
            modelPlaceholder: 'Contoh: Modena DD 7106 L / Sanken HWD-C520IC',
            defaultModel: 'Modena DD 7106 L',
            defaultPrice: 250000,
            userLabel: 'PIC Ruangan / Area Penempatan',
            userBadge: 'PIC Pantry/Ruang',
            userPlaceholder: 'Contoh: Pantry Lt. 1 / Ruang Rapat Utama',
            phoneLabel: 'No. HP PIC Area',
            replacementLabel: 'Unit Pengganti',
            templates: [
                { brand: 'Modena', model: 'Modena DD 7106 L', type: 'Galon Bawah (Bottom Load)', price: 300000, label: 'Modena Galon Bawah' },
                { brand: 'Sanken', model: 'Sanken HWD-C520IC', type: 'Galon Bawah (Bottom Load)', price: 250000, label: 'Sanken C520IC' },
                { brand: 'Miyako', model: 'Miyako WDP-300', type: 'Galon Bawah (Bottom Load)', price: 200000, label: 'Miyako WDP-300' }
            ]
        }
    };

    function selectAssetType(type, resetDefaults = true) {
        document.getElementById('asset_type').value = type;
        document.querySelectorAll('.asset-type-btn').forEach(btn => {
            const active = btn.dataset.type === type;
            btn.className = 'asset-type-btn p-3 rounded-2xl border-2 text-left transition flex items-center gap-2.5 ' +
                (active ? 'border-[#015471] bg-[#015471]/5' : 'border-slate-200 bg-white hover:border-slate-300');
            const iconBox = btn.querySelector('div');
            if (iconBox) {
                iconBox.className = 'w-8 h-8 rounded-xl flex items-center justify-center shrink-0 ' +
                    (active ? 'bg-[#015471] text-white' : 'bg-slate-100 text-slate-700');
            }
        });
        toggleAssetTypeFields(resetDefaults);
    }

    function toggleAssetTypeFields(resetDefaults = false) {
        const type = document.getElementById('asset_type').value || 'motor';
        const cfg = ASSET_CONFIGS[type] || ASSET_CONFIGS.motor;

        const plateGroup = document.getElementById('plateNumberGroup');
        const serialGroup = document.getElementById('serialNumberGroup');
        const frameRow = document.getElementById('vehicleFrameEngineRow');
        const taxCard = document.getElementById('taxScheduleCard');
        const stnkCard = document.getElementById('stnkBpkbCard');
        const repSection = document.getElementById('replacementUnitSection');

        if (plateGroup) plateGroup.classList.toggle('hidden', !cfg.isVehicle);
        if (serialGroup) serialGroup.classList.toggle('hidden', cfg.isVehicle);
        if (frameRow) frameRow.classList.toggle('hidden', !cfg.isVehicle);
        if (taxCard) taxCard.classList.toggle('hidden', !cfg.isVehicle);
        if (stnkCard) stnkCard.classList.toggle('hidden', !cfg.isVehicle);
        if (repSection) repSection.classList.toggle('hidden', !cfg.isVehicle);

        const catSel = document.getElementById('category');
        if (catSel) catSel.value = cfg.category;

        const brandSel = document.getElementById('brand');
        if (brandSel) {
            brandSel.innerHTML = cfg.brands.map(b => `<option value="${b}">${b}</option>`).join('');
        }

        const typeSel = document.getElementById('type_name');
        if (typeSel) {
            typeSel.innerHTML = cfg.types.map(t => `<option value="${t}">${t}</option>`).join('');
        }

        const tplBox = document.getElementById('quickTemplatesContainer');
        if (tplBox) {
            tplBox.innerHTML = '<span class="font-bold text-slate-500 text-[11px] mr-1">Template Cepat:</span>' +
                cfg.templates.map(t => `<button type="button" onclick="setTemplate('${t.brand}', '${t.model}', '${t.type}', ${t.price})" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:border-[#015471] hover:text-[#015471] font-bold text-[11px] text-slate-700 shadow-2xs transition">${t.label}</button>`).join('');
        }

        const modelInp = document.getElementById('model_name');
        if (modelInp) {
            modelInp.placeholder = cfg.modelPlaceholder;
            if (resetDefaults) {
                modelInp.value = cfg.defaultModel;
                document.getElementById('rental_price').value = cfg.defaultPrice;
            }
        }

        const codeInp = document.getElementById('asset_code_input');
        if (codeInp && (resetDefaults || !codeInp.value)) {
            codeInp.value = NEXT_ASSET_CODES[type] || '';
        }

        document.getElementById('userHolderLabel').innerText = cfg.userLabel;
        document.getElementById('userHolderBadge').innerText = cfg.userBadge;
        document.getElementById('user_holder_input').placeholder = cfg.userPlaceholder;
        document.getElementById('driverPhoneLabel').innerText = cfg.phoneLabel;
    }
    function filterContractsByClient() {
        const clientId = document.getElementById('client_selector')?.value || '';
        const vendorSelect = document.getElementById('vendor_select');
        if (!vendorSelect) return;

        Array.from(vendorSelect.options).forEach(opt => {
            if (!opt.value) return;
            const match = !clientId || opt.dataset.clientId === clientId;
            opt.style.display = match ? '' : 'none';
            opt.disabled = !match;
        });

        const selectedOpt = vendorSelect.options[vendorSelect.selectedIndex];
        if (selectedOpt && selectedOpt.value && clientId && selectedOpt.dataset.clientId !== clientId) {
            vendorSelect.value = '';
        }
    }

    function toggleStatusContractFields() {
        const status = document.getElementById('status_select')?.value || 'active';
        const contractBox = document.getElementById('contractAssignmentBox');
        const soldBox = document.getElementById('soldFieldsBox');
        const driverPicBox = document.getElementById('driverPicBox');
        const vendorSelect = document.getElementById('vendor_select');

        const isActiveRented = (status === 'active');
        const needsContract = (status === 'active' || status === 'maintenance' || status === 'replaced' || status === 'lost');
        const needsDriverPic = (status === 'active' || status === 'maintenance');

        if (contractBox) contractBox.classList.toggle('hidden', !needsContract);
        if (soldBox) soldBox.classList.toggle('hidden', status !== 'sold');
        if (driverPicBox) driverPicBox.classList.toggle('hidden', !needsDriverPic);

        if (vendorSelect) {
            vendorSelect.required = isActiveRented;
            if (!needsContract) vendorSelect.value = '';
        }

        document.querySelectorAll('.contract-req-star').forEach(el => {
            el.classList.toggle('hidden', !isActiveRented);
        });
    }

    let acqManuallyEdited = false;
    document.getElementById('acquisition_value_input')?.addEventListener('input', () => { acqManuallyEdited = true; });

    function syncAcqAndCalculate() {
        const inv = parseFloat(document.getElementById('invoice_value_input')?.value || 0);
        const acqInp = document.getElementById('acquisition_value_input');
        if (acqInp && !acqManuallyEdited) {
            acqInp.value = inv;
        }
        calculateLiveBookValue();
    }

    function calculateLiveBookValue() {
        const inv = parseFloat(document.getElementById('invoice_value_input')?.value || 0);
        const acqRaw = parseFloat(document.getElementById('acquisition_value_input')?.value || 0);
        const acq = acqRaw > 0 ? acqRaw : inv;
        const life = Math.max(1, parseInt(document.getElementById('useful_life_input')?.value || 48, 10));
        const acqDateStr = document.getElementById('acquisition_date_input')?.value || '';

        const monthlyDep = Math.round(acq / life);
        let elapsed = 0;
        if (acqDateStr) {
            const start = new Date(acqDateStr);
            const now = new Date();
            const monthsDiff = (now.getFullYear() - start.getFullYear()) * 12 + (now.getMonth() - start.getMonth());
            elapsed = Math.max(0, Math.min(life, monthsDiff));
        }
        const accumDep = Math.min(acq, monthlyDep * elapsed);
        const bookValue = Math.max(0, acq - accumDep);

        const fmt = (n) => 'Rp ' + new Intl.NumberFormat('id-ID').format(n);
        document.getElementById('liveMonthlyDep').innerText = fmt(monthlyDep);
        document.getElementById('liveElapsedMonths').innerText = elapsed;
        document.getElementById('liveAccumDep').innerText = fmt(accumDep);
        document.getElementById('liveBookValue').innerText = fmt(bookValue);
    }

    document.addEventListener('DOMContentLoaded', () => {
        selectAssetType(document.getElementById('asset_type').value || 'motor', false);
        const vs = document.getElementById('vendor_select');
        if (vs && vs.value) {
            const selOpt = vs.options[vs.selectedIndex];
            if (selOpt && selOpt.dataset.clientId) {
                document.getElementById('client_selector').value = selOpt.dataset.clientId;
                filterContractsByClient();
            }
        }
        toggleStatusContractFields();
        calculateLiveBookValue();
    });
</script>
@endpush
