@extends('layouts.app')

@section('title', 'Catat Pemeliharaan & Suku Cadang')
@section('page_title', 'Catat Pemeliharaan Kendaraan')

@section('content')
<div class="w-full space-y-6">

    <!-- Form Tag Wrapping Everything -->
    <form action="{{ route('maintenance.store') }}" method="POST" enctype="multipart/form-data" id="maintenanceForm">
        @csrf

        <!-- Top Header Action Bar -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#015471] to-[#023b4f] text-white flex items-center justify-center shadow-sm shrink-0">
                        <i data-lucide="wrench" class="w-6 h-6 text-[#FF7A3D]"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h1 class="text-xl font-black text-slate-900 tracking-tight">Formulir Catat Pemeliharaan & Servis</h1>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#FF7A3D]/10 text-[#FF7A3D] border border-[#FF7A3D]/20">
                                Transaksi Operasional
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Pencatatan riwayat servis atau penggantian suku cadang yang otomatis terhubung ke Buku Kas Pengeluaran</p>
                    </div>
                </div>

                <!-- Action CTA Buttons -->
                <div class="flex items-center gap-2.5 shrink-0">
                    <a href="{{ route('maintenance.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 hover:text-slate-900 transition shadow-2xs">
                        <i data-lucide="arrow-left" class="w-4 h-4 text-slate-400"></i> Batal
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-sm transition">
                        <i data-lucide="save" class="w-4 h-4"></i> Simpan & Masukkan ke Kas
                    </button>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-500 shrink-0 mt-0.5"></i>
                <div>
                    <strong class="font-bold block mb-1">Mohon periksa kesalahan input berikut:</strong>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- 2-Column Split Master Grid (7 Cols Left, 5 Cols Right) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- LEFT COLUMN: Unit & Rincian Pekerjaan (7 Cols) -->
            <div class="lg:col-span-7 space-y-6">

                <!-- Card 1: Kendaraan & Klasifikasi Pekerjaan -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-[#015471] text-white text-xs font-black flex items-center justify-center shadow-2xs">1</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900">Kendaraan & Kategori Servis</h2>
                                <p class="text-[11px] text-slate-500">Pilih unit armada target dan jenis pemeliharaan yang dilakukan</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-200/60 px-2 py-0.5 rounded">Wajib Diisi</span>
                    </div>

                    <div class="p-6 space-y-5">
                        <!-- Pilih Kendaraan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Unit Armada Kendaraan <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <div class="relative">
                                <select name="vehicle_id" id="vehicle_id" required class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer">
                                    <option value="">-- Pilih Armada Motor --</option>
                                    @foreach($vehicles as $v)
                                        <option value="{{ $v->id }}" 
                                            data-model="{{ $v->model_name }}"
                                            data-location="{{ $v->location ? $v->location->name : 'Non-Lokasi' }}"
                                            {{ (old('vehicle_id', $selectedVehicleId) == $v->id) ? 'selected' : '' }}>
                                            {{ $v->plate_number }} — {{ $v->model_name }} ({{ $v->location ? $v->location->name : 'Belum Ada Lokasi' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('vehicle_id') <span class="text-[#FF7A3D] text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Jenis Pekerjaan -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Jenis / Kategori Pekerjaan <span class="text-[#FF7A3D]">*</span>
                                </label>
                                <select name="type" id="type" required class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer">
                                    <option value="service_berkala" {{ old('type', $defaultType) == 'service_berkala' ? 'selected' : '' }}>Service Berkala & Pelumas</option>
                                    <option value="ganti_ban" {{ old('type', $defaultType) == 'ganti_ban' ? 'selected' : '' }}>Penggantian Ban (Depan / Belakang)</option>
                                    <option value="ganti_accu" {{ old('type', $defaultType) == 'ganti_accu' ? 'selected' : '' }}>Penggantian Accu / Aki</option>
                                    <option value="sparepart" {{ old('type', $defaultType) == 'sparepart' ? 'selected' : '' }}>Sparepart & Fast Moving</option>
                                    <option value="motor_pengganti" {{ old('type', $defaultType) == 'motor_pengganti' ? 'selected' : '' }}>Biaya Sewa Motor Pengganti</option>
                                    <option value="lainnya" {{ old('type', $defaultType) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>

                            <!-- Tanggal Pekerjaan -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Tanggal Pengerjaan / Nota <span class="text-[#FF7A3D]">*</span>
                                </label>
                                <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="w-full text-xs font-mono px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                                @error('date') <span class="text-[#FF7A3D] text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Nama Item & Quantity -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Nama Komponen / Item Servis
                                </label>
                                <input type="text" name="item_name" value="{{ old('item_name') }}" placeholder="Contoh: Paket Servis AHASS / Ban Luar IRC / Oli Mesin" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Kuantitas (Qty) <span class="text-[#FF7A3D]">*</span>
                                </label>
                                <div class="flex rounded-xl shadow-2xs">
                                    <input type="number" name="qty" id="qty" value="{{ old('qty', 1) }}" min="1" required class="w-full text-xs font-bold text-center px-3 py-2.5 rounded-l-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                                    <span class="inline-flex items-center px-3 rounded-r-xl border border-l-0 border-slate-300 bg-slate-50 text-slate-500 text-[11px] font-bold">
                                        Pcs/Set
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Rincian / Catatan Pengerjaan
                            </label>
                            <textarea name="description" rows="3" placeholder="Tuliskan catatan teknis detail kerusakan, suku cadang tambahan, atau alasan penggantian unit..." class="w-full text-xs p-3.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 leading-relaxed">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Bengkel, Odometer & Administrasi Internal -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-[#015471] text-white text-xs font-black flex items-center justify-center shadow-2xs">2</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900">Bengkel, Rekanan & Odometer</h2>
                                <p class="text-[11px] text-slate-500">Pencatatan tempat pengerjaan dan jarak tempuh kendaraan</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-200/60 px-2 py-0.5 rounded">Operasional</span>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Bengkel / Rekanan -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Nama Bengkel / Toko / Vendor
                                </label>
                                <div class="relative">
                                    <input type="text" name="workshop_name" value="{{ old('workshop_name') }}" placeholder="Contoh: AHASS Astra / Bengkel Sentosa" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                                </div>
                            </div>

                            <!-- Odometer KM -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Odometer (KM Saat Servis)
                                </label>
                                <div class="flex rounded-xl shadow-2xs">
                                    <input type="number" name="odometer_km" value="{{ old('odometer_km') }}" placeholder="12500" class="w-full text-xs font-mono font-bold px-3.5 py-2.5 rounded-l-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                                    <span class="inline-flex items-center px-3 rounded-r-xl border border-l-0 border-slate-300 bg-slate-50 text-slate-500 text-xs font-bold">
                                        KM
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Nomor Memo Internal -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Nomor Memo / SPK Internal (Bila Ada)
                            </label>
                            <input type="text" name="memo_number" value="{{ old('memo_number') }}" placeholder="Contoh: MEMO/SUP/WS/2026/089" class="w-full text-xs font-mono px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Biaya, Berkas Nota & Ringkasan Transaksi (5 Cols) -->
            <div class="lg:col-span-5 space-y-6">

                <!-- Card 3: Nilai Biaya & Sinkronisasi Kas -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-[#FF7A3D] text-white text-xs font-black flex items-center justify-center shadow-2xs">3</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900">Biaya Riil & Kas Keluar</h2>
                                <p class="text-[11px] text-slate-500">Nominal pengeluaran yang ditagihkan</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-[#FF7A3D] uppercase tracking-wider bg-[#FF7A3D]/10 px-2 py-0.5 rounded">Kas Otomatis</span>
                    </div>

                    <div class="p-6 space-y-4">
                        <!-- Input Biaya -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Total Biaya Riil (Rp) <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <div class="flex rounded-xl shadow-2xs">
                                <span class="inline-flex items-center px-3.5 rounded-l-xl border border-r-0 border-slate-300 bg-slate-50 text-slate-600 text-xs font-black font-mono">
                                    Rp
                                </span>
                                <input type="number" name="cost" id="cost" value="{{ old('cost') }}" placeholder="0" required class="w-full text-sm font-black font-mono px-3.5 py-2.5 rounded-r-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#FF7A3D] text-slate-900">
                            </div>
                            @error('cost') <span class="text-[#FF7A3D] text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <!-- Banner Info Kas Terhubung -->
                        <div class="p-3.5 rounded-xl bg-gradient-to-r from-blue-50/70 to-indigo-50/70 border border-blue-100 flex items-start gap-3">
                            <div class="w-7 h-7 rounded-lg bg-[#015471] text-white flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                            </div>
                            <div class="text-[11px] text-slate-600 leading-relaxed">
                                <strong class="text-slate-900 font-bold block mb-0.5">Integrasi Finansial Otomatis</strong>
                                Nilai biaya ini akan otomatis terdata pada <strong>Laporan Pengeluaran Finansial</strong> unit terkait sesuai kategori yang dipilih.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Upload Bukti Kuitansi / Faktur Nota -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white text-xs font-black flex items-center justify-center shadow-2xs">4</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900">Lampiran Bukti Nota / Faktur</h2>
                                <p class="text-[11px] text-slate-500">Kuitansi bengkel, struk sparepart, atau bukti transfer</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-200/60 px-2 py-0.5 rounded">Opsional</span>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="relative border-2 border-dashed border-slate-200 hover:border-[#015471] rounded-2xl p-5 text-center transition bg-slate-50/50 hover:bg-slate-50">
                            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 mx-auto flex items-center justify-center mb-2.5">
                                <i data-lucide="file-up" class="w-5 h-5 text-[#015471]"></i>
                            </div>
                            <label class="block text-xs font-bold text-slate-800 cursor-pointer">
                                <span>Pilih atau seret berkas nota di sini</span>
                                <input type="file" name="receipt_file" id="receipt_file" accept=".pdf,.jpg,.jpeg,.png" onchange="showReceiptFileName(this)" class="sr-only">
                            </label>
                            <p class="text-[11px] text-slate-400 mt-1">Mendukung format PDF, JPG, PNG (Maks 4MB)</p>
                            <div id="receipt_file_preview" class="hidden mt-3 p-2 bg-white rounded-lg border border-slate-200 text-xs text-slate-700 font-bold items-center justify-center gap-2">
                                <i data-lucide="paperclip" class="w-3.5 h-3.5 text-[#015471]"></i>
                                <span id="receipt_file_text" class="truncate max-w-[200px]"></span>
                            </div>
                        </div>
                        @error('receipt_file') <span class="text-[#FF7A3D] text-[11px] block font-semibold">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Bottom Sticky-like Action Buttons on Mobile / Desktop Assist -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-2">
                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i> Simpan Transaksi Pemeliharaan
                    </button>
                    <a href="{{ route('maintenance.index') }}" class="w-full py-2.5 px-4 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition flex items-center justify-center gap-1.5">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i> Batal & Kembali
                    </a>
                </div>

            </div>

        </div>
    </form>
</div>

<script>
function showReceiptFileName(input) {
    const preview = document.getElementById('receipt_file_preview');
    const text = document.getElementById('receipt_file_text');
    if (input.files && input.files[0]) {
        text.innerText = input.files[0].name;
        preview.classList.remove('hidden');
        preview.classList.add('flex');
    } else {
        preview.classList.add('hidden');
        preview.classList.remove('flex');
    }
}
</script>
@endsection
