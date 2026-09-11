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
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Plat Nomor -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Plat Nomor <span class="text-[#FF7A3D]">*</span>
                                </label>
                                <input type="text" name="plate_number" value="{{ old('plate_number') }}" placeholder="Contoh: BE 2402 AIT" required class="w-full text-xs font-mono font-bold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] focus:border-transparent text-slate-900 uppercase">
                                @error('plate_number') <span class="text-[#FF7A3D] text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                            </div>

                            <!-- Kategori Kendaraan -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Kategori Kendaraan <span class="text-[#FF7A3D]">*</span>
                                </label>
                                <select name="category" id="category" required class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer">
                                    <option value="Roda 2 (Motor)" {{ old('category', 'Roda 2 (Motor)') == 'Roda 2 (Motor)' ? 'selected' : '' }}>Roda 2 (Sepeda Motor)</option>
                                    <option value="Roda 4 (Mobil/Truk)" {{ old('category') == 'Roda 4 (Mobil/Truk)' ? 'selected' : '' }}>Roda 4 (Mobil / Pick Up / Truk)</option>
                                    <option value="Alat Berat" {{ old('category') == 'Alat Berat' ? 'selected' : '' }}>Alat Berat / Traktor</option>
                                    <option value="Lainnya" {{ old('category') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>

                            <!-- Merk Kendaraan -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Merk Kendaraan <span class="text-[#FF7A3D]">*</span>
                                </label>
                                <select name="brand" id="brand" required class="w-full text-xs font-bold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer">
                                    <option value="Honda" {{ old('brand', 'Honda') == 'Honda' ? 'selected' : '' }}>Honda</option>
                                    <option value="Yamaha" {{ old('brand') == 'Yamaha' ? 'selected' : '' }}>Yamaha</option>
                                    <option value="Kawasaki" {{ old('brand') == 'Kawasaki' ? 'selected' : '' }}>Kawasaki</option>
                                    <option value="Suzuki" {{ old('brand') == 'Suzuki' ? 'selected' : '' }}>Suzuki</option>
                                    <option value="Toyota" {{ old('brand') == 'Toyota' ? 'selected' : '' }}>Toyota</option>
                                    <option value="Mitsubishi" {{ old('brand') == 'Mitsubishi' ? 'selected' : '' }}>Mitsubishi</option>
                                    <option value="Daihatsu" {{ old('brand') == 'Daihatsu' ? 'selected' : '' }}>Daihatsu</option>
                                    <option value="Isuzu" {{ old('brand') == 'Isuzu' ? 'selected' : '' }}>Isuzu</option>
                                    <option value="Lainnya" {{ old('brand') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Model / Nama Unit -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Nama Model / Varian <span class="text-[#FF7A3D]">*</span>
                                </label>
                                <input type="text" name="model_name" id="model_name" value="{{ old('model_name', 'Honda CRF 150 L') }}" placeholder="Contoh: CRF 150 L / Supra X 125" required class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                            </div>

                            <!-- Tipe Kendaraan / Bentuk -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Tipe / Segmen Kendaraan
                                </label>
                                <select name="type_name" id="type_name" class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer">
                                    <option value="Trail On/Off Road" {{ old('type_name', 'Trail On/Off Road') == 'Trail On/Off Road' ? 'selected' : '' }}>Trail On/Off Road</option>
                                    <option value="Bebek / Underbone" {{ old('type_name') == 'Bebek / Underbone' ? 'selected' : '' }}>Bebek / Underbone</option>
                                    <option value="Matic / Skuter" {{ old('type_name') == 'Matic / Skuter' ? 'selected' : '' }}>Matic / Skuter</option>
                                    <option value="Sport" {{ old('type_name') == 'Sport' ? 'selected' : '' }}>Sport</option>
                                    <option value="Pick Up / Bak Terbuka" {{ old('type_name') == 'Pick Up / Bak Terbuka' ? 'selected' : '' }}>Pick Up / Bak Terbuka</option>
                                    <option value="Double Cabin (4x4)" {{ old('type_name') == 'Double Cabin (4x4)' ? 'selected' : '' }}>Double Cabin (4x4)</option>
                                    <option value="Minibus / MPV" {{ old('type_name') == 'Minibus / MPV' ? 'selected' : '' }}>Minibus / MPV</option>
                                    <option value="Lainnya" {{ old('type_name') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>

                            <!-- Tarif Sewa -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Tarif Sewa / Bulan <span class="text-[#FF7A3D]">*</span>
                                </label>
                                <div class="flex rounded-xl shadow-2xs">
                                    <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-slate-300 bg-slate-50 text-slate-500 text-xs font-bold">
                                        Rp
                                    </span>
                                    <input type="number" name="rental_price_per_month" id="rental_price" value="{{ old('rental_price_per_month', 1570000) }}" required class="w-full text-xs font-bold px-3 py-2.5 rounded-r-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                                </div>
                            </div>
                        </div>

                        <!-- Shortcut Pilihan Model Cepat -->
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex flex-wrap items-center gap-2 text-xs">
                            <span class="font-bold text-slate-500 text-[11px] mr-1">Pilihan Cepat Template:</span>
                            <button type="button" onclick="setTemplate('Honda', 'Honda CRF 150 L', 'Trail On/Off Road', 1570000)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:border-[#015471] hover:text-[#015471] font-bold text-[11px] text-slate-700 shadow-2xs transition">
                                Honda CRF 150 L (Rp 1.570.000)
                            </button>
                            <button type="button" onclick="setTemplate('Honda', 'Honda Supra X 125 CW FI', 'Bebek / Underbone', 980000)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:border-[#015471] hover:text-[#015471] font-bold text-[11px] text-slate-700 shadow-2xs transition">
                                Honda Supra X 125 (Rp 980.000)
                            </button>
                            <button type="button" onclick="setTemplate('Kawasaki', 'Kawasaki KLX 150', 'Trail On/Off Road', 1600000)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:border-[#015471] hover:text-[#015471] font-bold text-[11px] text-slate-700 shadow-2xs transition">
                                KLX 150
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
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
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Vendor / Klien -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Klien Penyewa / Mitra <span class="text-[#FF7A3D]">*</span>
                                </label>
                                <select name="vendor_id" required class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer">
                                    <option value="">-- Pilih Klien / Kontrak Sewa --</option>
                                    @foreach($vendors as $v)
                                        <option value="{{ $v->id }}" {{ (old('vendor_id', session('active_vendor_id')) == $v->id) ? 'selected' : '' }}>
                                            {{ $v->name }} ({{ $v->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Wilayah Kebun -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Wilayah Kebun / Lokasi</label>
                                <select name="location_id" class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer">
                                    <option value="">-- Pilih Wilayah / Kebun --</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Pemegang / Pengguna Motor -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                                    <span>Nama Pengemudi / Pemakai</span>
                                    <span class="text-[10px] text-slate-400 font-normal">PIC Driver</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                        <i data-lucide="user" class="w-4 h-4"></i>
                                    </div>
                                    <input type="text" name="user_holder" value="{{ old('user_holder') }}" placeholder="Contoh: Bpk. Bambang / Asisten Afdeling 1" class="w-full text-xs font-semibold pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                                </div>
                            </div>

                            <!-- No HP / WhatsApp Driver -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                                    <span>No. HP / WhatsApp Pengemudi</span>
                                    <span class="text-[10px] text-emerald-600 font-bold">WA Aktif</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                        <i data-lucide="phone" class="w-4 h-4"></i>
                                    </div>
                                    <input type="text" name="driver_phone" value="{{ old('driver_phone') }}" placeholder="08xxxxxxxxxx" class="w-full text-xs font-semibold pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                                </div>
                            </div>

                            <!-- Status Operasional -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Status Operasional Awal</label>
                                <select name="status" class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer">
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Aktif Beroperasi</option>
                                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Dalam Servis / Bengkel</option>
                                    <option value="replaced" {{ old('status') == 'replaced' ? 'selected' : '' }}>Unit Pengganti Aktif</option>
                                    <option value="lost" {{ old('status') == 'lost' ? 'selected' : '' }}>Hilang / Lapor Polisi</option>
                                </select>
                            </div>
                        </div>

                        <!-- Motor Pengganti Dropdown -->
                        <div class="pt-3 border-t border-slate-100">
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-700">
                                    Motor Pengganti
                                </label>
                                <span class="text-[10px] text-slate-400">Pilihan dari unit berstatus cadangan</span>
                            </div>
                            <select name="replacement_plate_number" class="w-full text-xs font-bold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer">
                                <option value="">-- Tidak Menggunakan Unit Pengganti (Unit Beroperasi Normal) --</option>
                                @foreach($replacementVehicles as $rep)
                                    <option value="{{ $rep->plate_number }}" {{ old('replacement_plate_number') == $rep->plate_number ? 'selected' : '' }}>
                                        {{ $rep->plate_number }} • {{ $rep->model_name }} ({{ $rep->location ? $rep->location->name : 'Pool Cadangan' }})
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-[11px] text-slate-400 block mt-1">Pilih unit motor pengganti jika unit ini sedang dalam perbaikan dan digantikan unit cadangan.</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Dokumen Legalitas & Catatan Tambahan (5 Cols) -->
            <div class="lg:col-span-5 space-y-6">

                <!-- Card 4: Dokumen Legalitas (STNK & BPKB) -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
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
</script>
@endpush
