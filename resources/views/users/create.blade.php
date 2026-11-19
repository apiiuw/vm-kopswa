@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru')
@section('page_title', 'Tambah Pengguna Baru')

@section('content')
<div class="w-full space-y-6 pb-12">

    <!-- Top Header Bar -->
    <div class="bg-white rounded-3xl border-2 border-slate-100 shadow-sm p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#015471] to-[#013547] text-white flex items-center justify-center shadow-md shrink-0">
                    <i data-lucide="user-plus" class="w-7 h-7 text-[#FF7A3D]"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-xl font-black text-slate-900 tracking-tight">Formulir Pendaftaran Pengguna Baru</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#FF7A3D]/10 text-[#FF7A3D] border border-[#FF7A3D]/20">
                            Akses & Autentikasi
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Pemberian hak akses login sistem untuk staf internal operasional Koperasi Swadharma maupun akun perwakilan Klien Mitra
                    </p>
                </div>
            </div>

            <!-- Back Link -->
            <div class="flex items-center gap-2.5">
                <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 transition shadow-2xs">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar Pengguna
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-3 shadow-2xs">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-500 shrink-0 mt-0.5"></i>
            <div>
                <strong class="font-bold block mb-1">Gagal menyimpan data pengguna:</strong>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('users.store') }}" method="POST" id="userForm" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left & Middle: Form Inputs (2 Cols) -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Card 1: Kategori Hak Akses -->
                <div class="bg-white rounded-3xl border-2 border-slate-100 shadow-sm p-6 space-y-5">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <div class="w-9 h-9 rounded-xl bg-[#015471]/10 text-[#015471] flex items-center justify-center font-bold">
                            1
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Peran & Kategori Hak Akses</h2>
                            <p class="text-[11px] text-slate-500">Tentukan apakah pengguna adalah staf internal Koperasi Swadharma atau perwakilan klien mitra</p>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-2.5 text-xs">Pilih Tipe Akun Pengguna <span class="text-[#FF7A3D]">*</span></label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="flex items-start gap-3.5 p-4 rounded-2xl border-2 border-slate-200 cursor-pointer hover:border-[#015471]/50 hover:bg-slate-50 transition has-[:checked]:border-[#015471] has-[:checked]:bg-[#015471]/5">
                                <input type="radio" name="category" value="internal" id="category_internal" onchange="toggleCategoryVendor()" {{ old('category', $defaultCategory ?? 'internal') === 'internal' ? 'checked' : '' }} class="mt-1 text-[#015471] focus:ring-[#015471]">
                                <div>
                                    <span class="font-bold text-slate-900 text-sm block">Internal Koperasi Swadharma</span>
                                    <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                                        Hak akses penuh mengelola operasional armada, input servis, keuangan, STNK, asuransi, dan approval tiket klien.
                                    </p>
                                </div>
                            </label>

                            <label class="flex items-start gap-3.5 p-4 rounded-2xl border-2 border-slate-200 cursor-pointer hover:border-[#FF7A3D]/50 hover:bg-slate-50 transition has-[:checked]:border-[#FF7A3D] has-[:checked]:bg-[#FF7A3D]/5">
                                <input type="radio" name="category" value="klien" id="category_klien" onchange="toggleCategoryVendor()" {{ old('category', $defaultCategory ?? '') === 'klien' ? 'checked' : '' }} class="mt-1 text-[#FF7A3D] focus:ring-[#FF7A3D]">
                                <div>
                                    <span class="font-bold text-slate-900 text-sm block">Klien Mitra (External)</span>
                                    <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                                        Hak akses Portal Klien: memantau unit sewa aktif, pengajuan tiket servis/perbaikan, dan pelaporan unit hilang/rusak.
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Dropdown Pilihan Klien (Muncul jika kategori Klien dipilih) -->
                    <div id="vendor_select_wrapper" class="p-4 rounded-2xl bg-amber-50/50 border border-amber-200/80 space-y-2 transition-all duration-300 {{ old('category', $defaultCategory ?? 'internal') === 'klien' ? '' : 'hidden' }}">
                        <label class="block font-bold text-slate-800 text-xs">
                            Hubungkan ke Perusahaan Klien (Master Klien) <span class="text-[#FF7A3D]">*</span>
                        </label>
                        <select name="client_id" id="client_id" class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#FF7A3D] bg-white text-slate-900 cursor-pointer">
                            <option value="">-- Pilih Perusahaan Klien --</option>
                            @foreach($clients ?? [] as $cl)
                                <option value="{{ $cl->id }}" {{ old('client_id') == $cl->id ? 'selected' : '' }}>
                                    {{ $cl->name }} (Kode: {{ $cl->code }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-500">Akun klien otomatis dapat mengakses seluruh kontrak & unit milik perusahaan klien ini.</p>
                    </div>
                </div>

                <!-- Card 2: Informasi Profil & Kredensial Login -->
                <div class="bg-white rounded-3xl border-2 border-slate-100 shadow-sm p-6 space-y-5">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <div class="w-9 h-9 rounded-xl bg-[#015471]/10 text-[#015471] flex items-center justify-center font-bold">
                            2
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Profil & Kredensial Pengguna</h2>
                            <p class="text-[11px] text-slate-500">Nama lengkap, alamat email resmi untuk login, dan kata sandi</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div class="md:col-span-2">
                            <label class="block font-bold text-slate-700 mb-1.5">
                                Nama Lengkap Pengguna <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="user" class="w-4 h-4"></i>
                                </span>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                                    placeholder="Contoh: Budi Santoso (Admin Operasional / PIC Lapangan)"
                                    class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#015471]">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">
                                Alamat Email Login <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="mail" class="w-4 h-4"></i>
                                </span>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required 
                                    placeholder="nama@swadharma.co.id / klien@pertamina.com"
                                    class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#015471]">
                            </div>
                            <span class="text-[11px] text-slate-400 mt-1 block">Email harus unik dan belum terdaftar di sistem</span>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">
                                Kata Sandi (Password) <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="lock" class="w-4 h-4"></i>
                                </span>
                                <input type="password" name="password" id="password" required minlength="6"
                                    placeholder="Minimal 6 karakter"
                                    class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#015471]">
                            </div>
                            <span class="text-[11px] text-slate-400 mt-1 block">Gunakan kombinasi huruf & angka demi keamanan</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Panduan & Konfirmasi (1 Col) -->
            <div class="space-y-6">

                <div class="bg-gradient-to-br from-[#015471] to-[#013547] text-white rounded-3xl shadow-xl p-6 space-y-6 static lg:sticky lg:top-6">
                    <div class="flex items-center justify-between border-b border-white/10 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-[#FF7A3D]">
                                <i data-lucide="shield-check" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm tracking-tight text-white">Panduan Hak Akses</h3>
                                <p class="text-[11px] text-white/70">Hak Otentikasi Pengguna</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#FF7A3D] text-white">
                            Security
                        </span>
                    </div>

                    <div class="space-y-3.5 text-xs text-white/80 leading-relaxed">
                        <div class="p-3.5 rounded-2xl bg-white/10 border border-white/15 space-y-1.5">
                            <span class="font-bold text-white flex items-center gap-2">
                                <i data-lucide="users" class="w-4 h-4 text-[#FF7A3D]"></i> Akun Internal Koperasi Swadharma
                            </span>
                            <p class="text-[11px] text-white/70">
                                Pengguna internal dapat mengakses seluruh modul: dashboard komparasi, data armada, servis, pengeluaran kas, STNK, dan manajemen proyek.
                            </p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-white/10 border border-white/15 space-y-1.5">
                            <span class="font-bold text-white flex items-center gap-2">
                                <i data-lucide="briefcase" class="w-4 h-4 text-emerald-400"></i> Akun Klien Mitra
                            </span>
                            <p class="text-[11px] text-white/70">
                                Pengguna klien login langsung ke antarmuka Portal Klien (tanpa akses ke keuangan internal) untuk mengawasi armada dan membuat tiket servis/klaim.
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 border-t border-white/15 space-y-2.5">
                        <button type="submit" class="w-full py-3 px-4 rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white font-bold text-xs shadow-lg flex items-center justify-center gap-2 transition cursor-pointer">
                            <i data-lucide="user-check" class="w-4 h-4"></i> Daftarkan & Simpan Pengguna
                        </button>
                        <a href="{{ route('users.index') }}" class="w-full py-2.5 px-4 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs flex items-center justify-center gap-2 transition text-center">
                            Batal
                        </a>
                    </div>
                </div>

            </div>

        </div>

    </form>

</div>

<script>
    function toggleCategoryVendor() {
        const isClient = document.getElementById('category_klien').checked;
        const vendorSelectWrapper = document.getElementById('vendor_select_wrapper');
        const vendorSelect = document.getElementById('client_id');

        if (isClient) {
            vendorSelectWrapper.classList.remove('hidden');
            vendorSelect.setAttribute('required', 'required');
        } else {
            vendorSelectWrapper.classList.add('hidden');
            vendorSelect.removeAttribute('required');
            vendorSelect.value = '';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        toggleCategoryVendor();
    });
</script>
@endsection
