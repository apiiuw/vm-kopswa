@extends('layouts.client')

@section('title', 'Formulir Pengajuan & Laporan Armada')

@section('content')
<div class="w-full space-y-6">

    <form action="{{ route('client.requests.store') }}" method="POST" enctype="multipart/form-data" id="clientRequestForm">
        @csrf

        <!-- Top Header Bar -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#015471] to-[#023b4f] text-white flex items-center justify-center shadow-sm shrink-0">
                        <i data-lucide="file-text" class="w-6 h-6 text-[#FF7A3D]"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h1 class="text-xl font-black text-slate-900 tracking-tight">Formulir Pengajuan & Laporan Klien</h1>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#FF7A3D]/10 text-[#FF7A3D] border border-[#FF7A3D]/20">
                                Layanan Klien
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Ajukan perbaikan/servis, klaim kehilangan (TLO), unit pengganti, atau input hasil & bukti perbaikan selesai</p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 shrink-0">
                    <a href="{{ route('client.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition shadow-2xs">
                        <i data-lucide="arrow-left" class="w-4 h-4 text-slate-400"></i> Batal
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-sm transition">
                        <i data-lucide="send" class="w-4 h-4"></i> Kirim Pengajuan
                    </button>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-3 mb-6 shadow-2xs">
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

        <!-- 2 Column Master Split Form -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- LEFT COLUMN (7 Cols): Armada & Rincian Pengajuan -->
            <div class="lg:col-span-7 space-y-6">

                <!-- Card 1: Unit Armada & Tipe Pengajuan -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-[#015471] text-white text-xs font-black flex items-center justify-center shadow-2xs">1</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900">Kendaraan & Jenis Permohonan</h2>
                                <p class="text-[11px] text-slate-500">Pilih unit motor dan tipe laporan yang diajukan</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-5">
                        <!-- Pilih Kendaraan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Unit Armada Kendaraan <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <select name="vehicle_id" id="vehicle_id" required class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer">
                                <option value="">-- Pilih Armada Motor --</option>
                                @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}" {{ (old('vehicle_id', $preselectedVehicleId) == $v->id) ? 'selected' : '' }}>
                                        {{ $v->plate_number }} — {{ $v->brand ? $v->brand . ' ' : '' }}{{ $v->model_name }} ({{ $v->location ? $v->location->name : 'Lokasi Kebun' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tipe Pengajuan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Jenis Pengajuan / Permohonan <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="flex items-start gap-2.5 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-[#015471] has-[:checked]:bg-[#015471]/5">
                                    <input type="radio" name="request_type" value="perbaikan_servis" {{ old('request_type', $preselectedType) === 'perbaikan_servis' ? 'checked' : '' }} class="mt-0.5 text-[#015471] focus:ring-[#015471]">
                                    <div>
                                        <span class="font-bold text-xs text-slate-900 block">Pengajuan Perbaikan / Servis</span>
                                        <span class="text-[10px] text-slate-500">Servis berkala, tune-up, ganti oli mesin</span>
                                    </div>
                                </label>

                                <label class="flex items-start gap-2.5 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-[#015471] has-[:checked]:bg-[#015471]/5">
                                    <input type="radio" name="request_type" value="ganti_ban_accu" {{ old('request_type', $preselectedType) === 'ganti_ban_accu' ? 'checked' : '' }} class="mt-0.5 text-[#015471] focus:ring-[#015471]">
                                    <div>
                                        <span class="font-bold text-xs text-slate-900 block">Ganti Ban & Accu</span>
                                        <span class="text-[10px] text-slate-500">Ban gundul/bocor atau aki motor drop</span>
                                    </div>
                                </label>

                                <label class="flex items-start gap-2.5 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-[#FF7A3D] has-[:checked]:bg-[#FF7A3D]/5">
                                    <input type="radio" name="request_type" value="unit_pengganti" {{ old('request_type', $preselectedType) === 'unit_pengganti' ? 'checked' : '' }} class="mt-0.5 text-[#FF7A3D] focus:ring-[#FF7A3D]">
                                    <div>
                                        <span class="font-bold text-xs text-slate-900 block">Unit Pengganti Sementara</span>
                                        <span class="text-[10px] text-slate-500">Motor backup jika perbaikan > 24 jam</span>
                                    </div>
                                </label>

                                <label class="flex items-start gap-2.5 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50">
                                    <input type="radio" name="request_type" value="lapor_kehilangan" {{ old('request_type', $preselectedType) === 'lapor_kehilangan' ? 'checked' : '' }} class="mt-0.5 text-rose-600 focus:ring-rose-500">
                                    <div>
                                        <span class="font-bold text-xs text-slate-900 block">Lapor Kehilangan (TLO)</span>
                                        <span class="text-[10px] text-slate-500">Insiden kehilangan motor & klaim asuransi</span>
                                    </div>
                                </label>

                                <label class="sm:col-span-2 flex items-start gap-2.5 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50">
                                    <input type="radio" name="request_type" value="input_hasil_perbaikan" {{ old('request_type', $preselectedType) === 'input_hasil_perbaikan' ? 'checked' : '' }} class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                                    <div>
                                        <span class="font-bold text-xs text-slate-900 block">Input Hasil & Bukti Perbaikan Selesai</span>
                                        <span class="text-[10px] text-slate-500">Upload nota kuitansi dan foto unit setelah selesai diservis di bengkel rekanan</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Judul Pengajuan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Judul Pengajuan / Ringkasan Kerusakan <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <input type="text" name="title" value="{{ old('title') }}" placeholder="Contoh: Penggantian Ban Luar Belakang & Kampas Rem CRF 150" required class="w-full text-xs font-bold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                        </div>

                        <!-- Deskripsi / Uraian -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Deskripsi Detail & Kronologis <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <textarea name="description" rows="4" placeholder="Jelaskan kondisi kendala mesin, keluhan pengendara, lokasi kejadian, atau catatan perbaikan yang dilakukan..." required class="w-full text-xs p-3.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 leading-relaxed">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN (5 Cols): Data Pengemudi, Bengkel & Upload Bukti -->
            <div class="lg:col-span-5 space-y-6">

                <!-- Card 2: Pengemudi, Kilometer & Bengkel -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-[#015471] text-white text-xs font-black flex items-center justify-center shadow-2xs">2</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900">Informasi Lapangan</h2>
                                <p class="text-[11px] text-slate-500">Pengemudi, Odometer KM & Bengkel</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Pengemudi / Pemakai</label>
                                <input type="text" name="driver_name" value="{{ old('driver_name') }}" placeholder="Asisten Lapangan" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">No. HP / WhatsApp Pengemudi</label>
                                <input type="text" name="driver_phone" value="{{ old('driver_phone') }}" placeholder="08xxxxxxxxxx" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Posisi Odometer (KM)</label>
                                <input type="number" name="odometer_km" value="{{ old('odometer_km') }}" placeholder="Contoh: 12450" class="w-full text-xs font-mono font-bold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Estimasi Biaya (Rp)</label>
                                <input type="number" name="estimated_cost" value="{{ old('estimated_cost', 0) }}" placeholder="0" class="w-full text-xs font-mono font-bold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Bengkel / AHASS (Jika Ada)</label>
                            <input type="text" name="workshop_name" value="{{ old('workshop_name') }}" placeholder="Contoh: AHASS Sentral Motor / Bengkel Rekanan" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                        </div>
                    </div>
                </div>

                <!-- Card 3: Upload Bukti Kerusakan / Nota / Foto -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-[#FF7A3D] text-white text-xs font-black flex items-center justify-center shadow-2xs">3</span>
                            <div>
                                <h2 class="text-sm font-black text-slate-900">Unggah Bukti & Foto</h2>
                                <p class="text-[11px] text-slate-500">Foto kerusakan, bukti nota kuitansi, atau LP Polisi</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Berkas Lampiran (Bisa lebih dari 1 file)</label>
                            <input type="file" name="proof_files[]" multiple accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#015471]/10 file:text-[#015471] hover:file:bg-[#015471]/20 px-3 py-2 rounded-xl border border-slate-300 text-slate-600 bg-slate-50 cursor-pointer">
                            <span class="text-[11px] text-slate-400 mt-1 block">Format: PDF, JPG, PNG (Maks. 5MB per file)</span>
                        </div>

                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-1">
                            <span class="font-bold text-slate-800 block">Panduan Lampiran:</span>
                            <ul class="list-disc list-inside text-[11px] text-slate-500 space-y-0.5">
                                <li><strong>Servis / Ban:</strong> Lampirkan foto kondisi sparepart lama & nota kuitansi bengkel.</li>
                                <li><strong>Kehilangan:</strong> Lampirkan surat Laporan Polisi (LP Polsek/Polres).</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!-- Sticky Bottom Bar -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                Pengajuan akan diproses dan status pengerjaan dapat dipantau langsung pada portal klien.
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                <a href="{{ route('client.dashboard') }}" class="w-full sm:w-auto text-center px-4 py-2.5 text-xs font-bold rounded-xl border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-sm transition">
                    <i data-lucide="send" class="w-4 h-4"></i> Kirim Pengajuan
                </button>
            </div>
        </div>

    </form>

</div>
@endsection
