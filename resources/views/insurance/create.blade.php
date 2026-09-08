@extends('layouts.app')

@section('title', 'Catat Kasus / Klaim Asuransi')
@section('page_title', 'Formulir Laporan Klaim & Kehilangan')

@section('content')
<div class="w-full space-y-6">

    <!-- Form Tag Wrapping Entire Page -->
    <form action="{{ route('insurance.store') }}" method="POST" enctype="multipart/form-data" id="insuranceClaimForm">
        @csrf

        <!-- Top Header Action Bar -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#015471] to-[#023b4f] text-white flex items-center justify-center shadow-sm shrink-0">
                        <i data-lucide="shield-alert" class="w-6 h-6 text-[#FF7A3D]"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h1 class="text-xl font-black text-slate-900 tracking-tight">Formulir Laporan Insiden & Klaim Asuransi</h1>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#FF7A3D]/10 text-[#FF7A3D] border border-[#FF7A3D]/20">
                                Asuransi & TLO
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Pencatatan insiden kehilangan (TLO), kerusakan armada, kelengkapan berkas kepolisian & pengajuan klaim</p>
                    </div>
                </div>

                <!-- Action CTA Buttons -->
                <div class="flex items-center gap-2.5 shrink-0">
                    <a href="{{ route('insurance.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 hover:text-slate-900 transition shadow-2xs">
                        <i data-lucide="arrow-left" class="w-4 h-4 text-slate-400"></i> Batal
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-sm transition">
                        <i data-lucide="save" class="w-4 h-4"></i> Simpan Laporan Klaim
                    </button>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-3 mb-6">
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

            <!-- LEFT COLUMN: Rincian Insiden, Kendaraan & Pengemudi (7 Cols) -->
            <div class="lg:col-span-7 space-y-6">

                <!-- Card 1: Identitas Unit & Klasifikasi Klaim -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-[#015471] text-white text-xs font-black flex items-center justify-center shadow-2xs">1</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900">Kendaraan & Klasifikasi Insiden</h2>
                                <p class="text-[11px] text-slate-500">Pilih armada yang mengalami kejadian dan tipe klaim asuransi</p>
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
                                    <option value="{{ $v->id }}" {{ old('vehicle_id') == $v->id ? 'selected' : '' }}>
                                        {{ $v->plate_number }} — {{ $v->brand ? $v->brand . ' ' : '' }}{{ $v->model_name }} ({{ $v->location ? $v->location->name : 'Non-Lokasi' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('vehicle_id') <span class="text-[#FF7A3D] text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Jenis Klaim / Insiden -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Kategori Kasus / Klaim <span class="text-[#FF7A3D]">*</span>
                                </label>
                                <select name="claim_type" id="claim_type" required class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer">
                                    <option value="tlo_kehilangan" {{ old('claim_type') == 'tlo_kehilangan' ? 'selected' : '' }}>TLO (Kehilangan Total Unit)</option>
                                    <option value="partial_loss" {{ old('claim_type') == 'partial_loss' ? 'selected' : '' }}>Partial Loss (Kerusakan Sebagian)</option>
                                    <option value="kerusakan_berat" {{ old('claim_type') == 'kerusakan_berat' ? 'selected' : '' }}>Kerusakan Berat (>75%)</option>
                                </select>
                            </div>

                            <!-- Tanggal Kejadian -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Tanggal Kejadian Insiden <span class="text-[#FF7A3D]">*</span>
                                </label>
                                <input type="date" name="incident_date" value="{{ old('incident_date', date('Y-m-d')) }}" required class="w-full text-xs font-mono font-bold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                            </div>
                        </div>

                        <!-- Info TLO Callout -->
                        <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start gap-2.5">
                            <i data-lucide="info" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"></i>
                            <div>
                                <span class="font-bold block">Pemberitahuan Status Unit:</span>
                                Jika memilih tipe <span class="font-bold text-amber-950">TLO (Kehilangan Total Unit)</span>, status armada kendaraan akan otomatis diperbarui menjadi <span class="font-bold underline">Hilang / Lost</span> pada sistem.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Pengemudi & Kronologi Insiden -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-[#015471] text-white text-xs font-black flex items-center justify-center shadow-2xs">2</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900">Pengemudi & Kronologi Kejadian</h2>
                                <p class="text-[11px] text-slate-500">Identitas asisten/pengendara saat kejadian dan kronologis Berita Acara</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Nama Pengemudi -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Nama Pemakai / Pengemudi
                                </label>
                                <input type="text" name="driver_name" value="{{ old('driver_name') }}" placeholder="Contoh: Budi Santoso (Asisten Afdeling III)" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                            </div>

                            <!-- Nomor SIM Pengemudi -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Nomor SIM C Pengemudi
                                </label>
                                <input type="text" name="driver_sim_number" value="{{ old('driver_sim_number') }}" placeholder="Contoh: SIM-C-19829102830" class="w-full text-xs font-mono px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                            </div>
                        </div>

                        <!-- Kronologi Kejadian -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Kronologis Kejadian (BAP)
                            </label>
                            <textarea name="incident_chronology" rows="4" placeholder="Ceritakan kronologis kejadian lengkap (waktu, lokasi TKP, saksi, dan kondisi kejadian) untuk lampiran Berita Acara..." class="w-full text-xs p-3.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 leading-relaxed">{{ old('incident_chronology') }}</textarea>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Legalitas Kepolisian, Nilai Klaim & Dokumen (5 Cols) -->
            <div class="lg:col-span-5 space-y-6">

                <!-- Card 3: Nilai Klaim & Status Penanganan -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-[#FF7A3D] text-white text-xs font-black flex items-center justify-center shadow-2xs">3</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900">Nilai Klaim & Status</h2>
                                <p class="text-[11px] text-slate-500">Estimasi pertanggungan dan alur proses klaim</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        <!-- Nilai Pengajuan Klaim -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Estimasi Nilai Klaim (Rp) <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-500">Rp</span>
                                <input type="number" name="claim_amount" id="claim_amount" value="{{ old('claim_amount', 37800000) }}" required min="0" step="1000" class="w-full text-xs font-mono font-bold pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#FF7A3D] text-slate-900">
                            </div>
                            <span class="text-[11px] text-slate-500 mt-1 block">Nilai pertanggungan unit standar TLO (e.g. CRF 150)</span>
                        </div>

                        <!-- Status Penanganan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Status Penanganan Klaim <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <select name="status" required class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer">
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft Berita Acara</option>
                                <option value="diajukan" {{ old('status') == 'diajukan' ? 'selected' : '' }}>Diajukan ke Asuransi</option>
                                <option value="diverifikasi" {{ old('status', 'diverifikasi') == 'diverifikasi' ? 'selected' : '' }}>Verifikasi Dokumen</option>
                                <option value="disetujui" {{ old('status') == 'disetujui' ? 'selected' : '' }}>Disetujui Asuransi</option>
                                <option value="cair" {{ old('status') == 'cair' ? 'selected' : '' }}>Cair / Selesai</option>
                                <option value="ditolak" {{ old('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Legalitas Kepolisian & Polda -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-[#015471] text-white text-xs font-black flex items-center justify-center shadow-2xs">4</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900">Legalitas & Kepolisian</h2>
                                <p class="text-[11px] text-slate-500">Nomor Laporan Polisi & SKET Blokir Polda</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        <!-- Nomor Laporan Polisi -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Nomor Laporan Polisi (LP Polsek/Polres)
                            </label>
                            <input type="text" name="police_report_number" value="{{ old('police_report_number') }}" placeholder="Contoh: LP/B/142/XII/2025/SPKT/..." class="w-full text-xs font-mono font-bold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                        </div>

                        <!-- Nomor Blokir STNK Polda -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Nomor Surat Blokir STNK Polda
                            </label>
                            <input type="text" name="polda_blocking_number" value="{{ old('polda_blocking_number') }}" placeholder="Contoh: SKET-BLOKIR/08/II/2026/DITLANTAS" class="w-full text-xs font-mono font-bold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                        </div>
                    </div>
                </div>

                <!-- Card 5: Unggah Berkas Bukti & Dokumen -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-[#015471] text-white text-xs font-black flex items-center justify-center shadow-2xs">5</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900">Berkas Lampiran Klaim</h2>
                                <p class="text-[11px] text-slate-500">LP Polisi, Foto Kerusakan, BAP, STNK (PDF/Gambar)</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                File Lampiran Dokumen
                            </label>
                            <input type="file" name="claim_file" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#015471]/10 file:text-[#015471] hover:file:bg-[#015471]/20 px-3 py-2 rounded-xl border border-slate-300 text-slate-600 bg-slate-50 cursor-pointer">
                            <span class="text-[11px] text-slate-400 mt-1 block">Format: PDF, JPG, PNG (Maks. 4MB)</span>
                        </div>

                        <!-- Syarat Berkas Checklist Info -->
                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-[11px] text-slate-600 space-y-1.5">
                            <div class="font-bold text-slate-800 flex items-center gap-1.5">
                                <i data-lucide="clipboard-check" class="w-3.5 h-3.5 text-[#015471]"></i> Dokumen yang Wajib Disiapkan:
                            </div>
                            <ul class="list-disc list-inside space-y-0.5 text-slate-500">
                                <li>Laporan Polisi Asli (LP) dari Polsek/Polres</li>
                                <li>Surat Keterangan Blokir STNK Polda</li>
                                <li>Fotokopi KTP & SIM C Pengemudi</li>
                                <li>Kunci Kontak Asli & Duplikat (Untuk TLO)</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!-- Sticky / Bottom Submission Bar -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <i data-lucide="shield-check" class="w-4 h-4 text-[#015471]"></i>
                Data laporan klaim akan tersinkronisasi dengan status unit armada & riwayat asuransi.
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                <a href="{{ route('insurance.index') }}" class="w-full sm:w-auto text-center px-4 py-2.5 text-xs font-bold rounded-xl border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-sm transition">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Laporan Klaim
                </button>
            </div>
        </div>

    </form>

</div>
@endsection
