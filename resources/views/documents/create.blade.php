@extends('layouts.app')

@section('title', 'Catat Dokumen & Pajak Kendaraan')
@section('page_title', 'Catat Dokumen & Pajak Armada')

@section('content')
<div class="w-full space-y-6">

    <!-- Form Tag Wrapping Everything -->
    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" id="documentForm">
        @csrf

        <!-- Top Header Action Bar -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#015471] to-[#023b4f] text-white flex items-center justify-center shadow-sm shrink-0">
                        <i data-lucide="file-check" class="w-6 h-6 text-[#FF7A3D]"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h1 class="text-xl font-black text-slate-900 tracking-tight">Formulir Catat Dokumen & Pajak Legalitas</h1>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#015471]/10 text-[#015471] border border-[#015471]/20">
                                Legalitas & Pajak
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Catat perpanjangan STNK tahunan, ganti plat 5 tahunan, BPKB, atau asuransi unit armada</p>
                    </div>
                </div>

                <!-- Action CTA Buttons -->
                <div class="flex items-center gap-2.5 shrink-0">
                    <a href="{{ route('documents.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 hover:text-slate-900 transition shadow-2xs">
                        <i data-lucide="arrow-left" class="w-4 h-4 text-slate-400"></i> Batal
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-sm transition">
                        <i data-lucide="save" class="w-4 h-4"></i> Simpan Dokumen & Sinkron Kas
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

        <!-- 2-Column Split Master Grid (7 Cols Left, 5 Cols Right) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- LEFT COLUMN: Unit & Rincian Dokumen (7 Cols) -->
            <div class="lg:col-span-7 space-y-6">

                <!-- Card 1: Kendaraan & Klasifikasi Dokumen -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-[#015471] text-white text-xs font-black flex items-center justify-center shadow-2xs">1</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900">Kendaraan & Jenis Dokumen</h2>
                                <p class="text-[11px] text-slate-500">Pilih unit armada target dan jenis legalitas / surat izin</p>
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
                            <select name="vehicle_id" id="vehicle_id" required class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer">
                                <option value="">-- Pilih Armada Motor / Mobil --</option>
                                @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}" {{ (old('vehicle_id', $selectedVehicleId) == $v->id) ? 'selected' : '' }}>
                                        {{ $v->plate_number }} — {{ $v->brand ? $v->brand . ' ' : '' }}{{ $v->model_name }} ({{ $v->location ? $v->location->name : 'Non-Lokasi' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('vehicle_id') <span class="text-[#FF7A3D] text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Jenis Dokumen -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Jenis Dokumen / Legalitas <span class="text-[#FF7A3D]">*</span>
                                </label>
                                <select name="document_type" id="document_type" required class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer">
                                    <option value="stnk" {{ old('document_type') == 'stnk' ? 'selected' : '' }}>STNK & Pajak Tahunan (PKB)</option>
                                    <option value="plat_5_tahunan" {{ old('document_type') == 'plat_5_tahunan' ? 'selected' : '' }}>Pajak & Ganti Plat 5 Tahunan</option>
                                    <option value="bpkb" {{ old('document_type') == 'bpkb' ? 'selected' : '' }}>Buku BPKB Kendaraan</option>
                                    <option value="asuransi" {{ old('document_type') == 'asuransi' ? 'selected' : '' }}>Polis Asuransi Kerugian</option>
                                    <option value="blokir_polda" {{ old('document_type') == 'blokir_polda' ? 'selected' : '' }}>Surat Keterangan Blokir STNK Polda</option>
                                    <option value="lainnya" {{ old('document_type') == 'lainnya' ? 'selected' : '' }}>Dokumen Legalitas Lainnya</option>
                                </select>
                            </div>

                            <!-- Nomor Dokumen -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Nomor Surat / Seri Registrasi
                                </label>
                                <input type="text" name="document_number" value="{{ old('document_number') }}" placeholder="Contoh: 0829182/STNK/2026" class="w-full text-xs font-mono font-bold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                            </div>
                        </div>

                        <!-- Masa Berlaku -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Tanggal Terbit / Bayar -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Tanggal Terbit / Pembayaran
                                </label>
                                <input type="date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" class="w-full text-xs font-mono px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                            </div>

                            <!-- Tanggal Jatuh Tempo -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Tanggal Jatuh Tempo (Expiry)
                                </label>
                                <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" class="w-full text-xs font-mono font-bold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                                <span class="text-[11px] text-slate-400 block mt-1">Sistem akan otomatis memicu pengingat jika sisa waktu < 30 hari</span>
                            </div>
                        </div>

                        <!-- Catatan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Catatan / Keterangan Dokumen
                            </label>
                            <textarea name="notes" rows="3" placeholder="Tambahkan catatan nomor resi, instansi samsat penerbit, atau keterangan penting lainnya..." class="w-full text-xs p-3.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 leading-relaxed">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Biaya, Berkas Scan & Ringkasan Transaksi (5 Cols) -->
            <div class="lg:col-span-5 space-y-6">

                <!-- Card 2: Nilai Biaya & Sinkronisasi Kas -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-[#FF7A3D] text-white text-xs font-black flex items-center justify-center shadow-2xs">2</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900">Biaya Administrasi & Pajak</h2>
                                <p class="text-[11px] text-slate-500">Nominal yang dibayarkan ke instansi terkait</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-[#FF7A3D] uppercase tracking-wider bg-[#FF7A3D]/10 px-2 py-0.5 rounded">Buku Kas</span>
                    </div>

                    <div class="p-6 space-y-4">
                        <!-- Input Biaya -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Biaya yang Dikeluarkan (Rp) <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <div class="flex rounded-xl shadow-2xs">
                                <span class="inline-flex items-center px-3.5 rounded-l-xl border border-r-0 border-slate-300 bg-slate-50 text-slate-600 text-xs font-black font-mono">
                                    Rp
                                </span>
                                <input type="number" name="cost" id="cost" value="{{ old('cost', 0) }}" placeholder="0" required class="w-full text-sm font-black font-mono px-3.5 py-2.5 rounded-r-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#FF7A3D] text-slate-900">
                            </div>
                            @error('cost') <span class="text-[#FF7A3D] text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                            <span class="text-[11px] text-slate-400 block mt-1">Jika diisi > Rp 0, otomatis dicatat pada buku kas operasional</span>
                        </div>

                        <!-- Banner Info Kas Terhubung -->
                        <div class="p-3.5 rounded-xl bg-gradient-to-r from-blue-50/70 to-indigo-50/70 border border-blue-100 flex items-start gap-3">
                            <div class="w-7 h-7 rounded-lg bg-[#015471] text-white flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                            </div>
                            <div class="text-[11px] text-slate-600 leading-relaxed">
                                <strong class="text-slate-900 font-bold block mb-0.5">Integrasi Pengeluaran Finansial</strong>
                                Pembayaran ini langsung terdata pada buku kas pengeluaran kategori <strong>Pajak STNK & Legalitas</strong> unit terkait.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Upload Berkas Scan (PDF/Foto) -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white text-xs font-black flex items-center justify-center shadow-2xs">3</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900">Lampiran Berkas Scan / Foto</h2>
                                <p class="text-[11px] text-slate-500">Scan dokumen STNK, BPKB, atau lembar PKB Samsat</p>
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
                                <span>Pilih atau seret berkas scan di sini</span>
                                <input type="file" name="file_path" id="file_path" accept=".pdf,.jpg,.jpeg,.png" onchange="showDocFileName(this)" class="sr-only">
                            </label>
                            <p class="text-[11px] text-slate-400 mt-1">Mendukung format PDF, JPG, PNG (Maks 10MB)</p>
                            <div id="file_path_preview" class="hidden mt-3 p-2 bg-white rounded-lg border border-slate-200 text-xs text-slate-700 font-bold items-center justify-center gap-2">
                                <i data-lucide="paperclip" class="w-3.5 h-3.5 text-[#015471]"></i>
                                <span id="file_path_text" class="truncate max-w-[200px]"></span>
                            </div>
                        </div>
                        @error('file_path') <span class="text-[#FF7A3D] text-[11px] block font-semibold">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Bottom Quick Action Card -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-2">
                    <button type="submit" class="w-full py-3 px-4 rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i> Simpan Dokumen Legalitas
                    </button>
                    <a href="{{ route('documents.index') }}" class="w-full py-2.5 px-4 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition flex items-center justify-center gap-1.5">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i> Batal & Kembali
                    </a>
                </div>

            </div>

        </div>
    </form>
</div>

<script>
function showDocFileName(input) {
    const preview = document.getElementById('file_path_preview');
    const text = document.getElementById('file_path_text');
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
