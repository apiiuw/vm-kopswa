@extends('layouts.app')

@section('title', 'Manajemen Pengguna & Hak Akses')
@section('page_title', 'Akses & Pengguna Sistem')

@section('content')
<div class="w-full space-y-6">

    <!-- Top Header & Action Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#015471] to-[#023b4f] text-white flex items-center justify-center shadow-sm shrink-0">
                    <i data-lucide="users" class="w-6 h-6 text-[#FF7A3D]"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-xl font-black text-slate-900 tracking-tight">Manajemen Akun Pengguna & Hak Akses</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#015471]/10 text-[#015471] border border-[#015471]/20">
                            Internal & Klien Mitra
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola akun staf internal PT SUP dan akun perwakilan Klien Mitra dari daftar vendor/proyek</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2.5 shrink-0">
                <button onclick="openAddUserModal()" class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-sm transition">
                    <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah Pengguna Baru
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2.5 shadow-2xs">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-3 shadow-2xs">
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

    <!-- Quick Stats Cards (Full Width 3 Columns) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-500">Total Pengguna</span>
                <span class="text-2xl font-black text-slate-900 block mt-1">{{ $stats['total'] ?? $users->total() }}</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Akun terdaftar pada sistem</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-[#015471]/10 text-[#015471] flex items-center justify-center">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-500">Internal PT SUP</span>
                <span class="text-2xl font-black text-[#015471] block mt-1">{{ $stats['internal'] ?? 0 }}</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Hak akses penuh (Full Access)</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-[#015471]/10 text-[#015471] flex items-center justify-center">
                <i data-lucide="shield-check" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-slate-500">Akun Klien Mitra</span>
                <span class="text-2xl font-black text-[#FF7A3D] block mt-1">{{ $stats['klien'] ?? 0 }}</span>
                <span class="text-[11px] text-slate-400 mt-0.5 block">Terikat entitas Klien / Vendor</span>
            </div>
            <div class="w-11 h-11 rounded-xl bg-[#FF7A3D]/10 text-[#FF7A3D] flex items-center justify-center">
                <i data-lucide="building-2" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        
        <!-- Table Toolbar & Filters -->
        <div class="p-4 sm:p-5 bg-slate-50/70 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-[#015471] text-white flex items-center justify-center text-xs font-bold shadow-2xs">
                    <i data-lucide="list" class="w-4 h-4 text-[#FF7A3D]"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Daftar Pengguna Sistem</h3>
                    <p class="text-[11px] text-slate-500">Filter berdasarkan kategori Internal atau Klien Mitra</p>
                </div>
            </div>

            <!-- Filter Controls & Per-Page -->
            <form id="filterForm" action="{{ route('users.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <!-- Filter Kategori -->
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-semibold text-slate-600">Kategori:</span>
                    <select name="category" onchange="document.getElementById('filterForm').submit()" class="text-xs font-bold bg-white border border-slate-300 text-slate-800 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-[#015471] focus:outline-hidden cursor-pointer shadow-2xs">
                        <option value="">Semua Kategori</option>
                        <option value="internal" {{ request('category') === 'internal' ? 'selected' : '' }}>Internal PT SUP</option>
                        <option value="klien" {{ request('category') === 'klien' ? 'selected' : '' }}>Klien</option>
                    </select>
                </div>

                <!-- Filter Klien Vendor -->
                @if(request('category') === 'klien' || request('vendor_id'))
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs font-semibold text-slate-600">Instansi Klien:</span>
                        <select name="vendor_id" onchange="document.getElementById('filterForm').submit()" class="text-xs font-bold bg-white border border-slate-300 text-slate-800 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-[#015471] focus:outline-hidden cursor-pointer shadow-2xs">
                            <option value="">Semua Instansi Klien</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->id }}" {{ request('vendor_id') == $v->id ? 'selected' : '' }}>
                                    {{ $v->name }} {{ $v->client_name ? '(' . $v->client_name . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Per-Page Selector -->
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-semibold text-slate-600">Tampilkan:</span>
                    <select name="per_page" onchange="document.getElementById('filterForm').submit()" class="text-xs font-bold bg-white border border-slate-300 text-slate-800 rounded-xl px-3 py-1.5 focus:ring-2 focus:ring-[#015471] focus:outline-hidden cursor-pointer shadow-2xs">
                        <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10 per halaman</option>
                        <option value="15" {{ ($perPage ?? 10) == 15 ? 'selected' : '' }}>15 per halaman</option>
                        <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25 per halaman</option>
                        <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50 per halaman</option>
                        <option value="100" {{ ($perPage ?? 10) == 100 ? 'selected' : '' }}>100 per halaman</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Table Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#e6f0f3] border-b border-slate-200 text-[#015471] font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-6 py-4">Nama Pegawai / User</th>
                        <th class="px-6 py-4">Alamat Email</th>
                        <th class="px-6 py-4">Kategori Akun</th>
                        <th class="px-6 py-4">Instansi Klien (Vendor)</th>
                        <th class="px-6 py-4">Terdaftar Sejak</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-800">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Nama & Inisial -->
                            <td class="px-6 py-4 font-bold text-slate-900">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl {{ ($u->category ?? 'internal') === 'klien' ? 'bg-gradient-to-br from-[#FF7A3D] to-[#d95a20]' : 'bg-gradient-to-br from-[#015471] to-[#023b4f]' }} text-white flex items-center justify-center font-bold text-xs shadow-2xs">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="block text-slate-900 font-bold">{{ $u->name }}</span>
                                        @if($u->id === Auth::id())
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-[#FF7A3D]">
                                                <i data-lucide="check-circle-2" class="w-3 h-3"></i> Akun Anda Aktif
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="px-6 py-4 text-slate-700 font-medium">
                                <div class="flex items-center gap-1.5">
                                    <i data-lucide="mail" class="w-3.5 h-3.5 text-slate-400"></i>
                                    <span>{{ $u->email }}</span>
                                </div>
                            </td>

                            <!-- Kategori Role -->
                            <td class="px-6 py-4">
                                @if(($u->category ?? 'internal') === 'klien')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-[#FF7A3D]/10 text-[#FF7A3D] border border-[#FF7A3D]/25">
                                        <i data-lucide="briefcase" class="w-3.5 h-3.5 text-[#FF7A3D]"></i> Klien Mitra
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-[#e6f0f3] text-[#015471] border border-[#015471]/20">
                                        <i data-lucide="shield" class="w-3.5 h-3.5 text-[#015471]"></i> Internal PT SUP
                                    </span>
                                @endif
                            </td>

                            <!-- Instansi Klien -->
                            <td class="px-6 py-4">
                                @if(($u->category ?? 'internal') === 'klien' && $u->vendor)
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-900">{{ $u->vendor->name }}</span>
                                        @if($u->vendor->client_name)
                                            <span class="text-[11px] text-slate-500 font-medium">{{ $u->vendor->client_name }}</span>
                                        @endif
                                    </div>
                                @elseif(($u->category ?? 'internal') === 'klien')
                                    <span class="text-[11px] text-slate-400 italic">Belum dihubungkan ke Klien</span>
                                @else
                                    <span class="text-[11px] text-slate-400 font-medium">— PT Swadharma Utama Prima —</span>
                                @endif
                            </td>

                            <!-- Terdaftar -->
                            <td class="px-6 py-4 text-slate-500 font-medium">
                                {{ $u->created_at ? $u->created_at->format('d M Y, H:i') : '-' }}
                            </td>

                            <!-- Aksi -->
                            <td class="px-6 py-4 text-right">
                                @if($users->total() > 1 && $u->id !== Auth::id())
                                    <form id="delete-user-{{ $u->id }}" action="{{ route('users.destroy', $u) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('delete-user-{{ $u->id }}', 'pengguna {{ $u->name }}')" class="p-2 text-slate-400 hover:text-rose-600 rounded-xl hover:bg-rose-50 transition" title="Hapus User">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[11px] text-slate-400 font-semibold italic">Akun Utama</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-slate-400">
                                <i data-lucide="users" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                                Belum ada data pengguna yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-white">
                {{ $users->links() }}
            </div>
        @else
            <div class="px-6 py-3.5 border-t border-slate-200 bg-slate-50/50 text-xs text-slate-500 flex items-center justify-between">
                <span>Menampilkan seluruh <strong class="text-slate-900">{{ $users->total() }}</strong> pengguna</span>
                <span class="text-[11px] text-slate-400">Data terenkripsi dan aman</span>
            </div>
        @endif
    </div>

</div>

<!-- Modal Tambah User -->
<div id="addUserModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4 text-slate-900">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#015471]/10 text-[#015471] flex items-center justify-center">
                    <i data-lucide="user-plus" class="w-4 h-4 text-[#FF7A3D]"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-sm">Tambah Pengguna Baru</h3>
            </div>
            <button type="button" onclick="closeAddUserModal()" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg hover:bg-slate-100 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Nama Lengkap <span class="text-[#FF7A3D]">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama Karyawan / Staf Perwakilan" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Alamat Email <span class="text-[#FF7A3D]">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
            </div>

            <!-- Pilihan Kategori -->
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Kategori Pengguna <span class="text-[#FF7A3D]">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-[#015471] has-[:checked]:bg-[#015471]/5">
                        <input type="radio" name="category" value="internal" id="category_internal" onchange="toggleCategoryVendor()" {{ old('category', 'internal') === 'internal' ? 'checked' : '' }} class="text-[#015471] focus:ring-[#015471]">
                        <div>
                            <span class="font-bold text-slate-900 block">Internal PT SUP</span>
                            <span class="text-[10px] text-slate-500">Staf & Manajemen SUP</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-[#FF7A3D] has-[:checked]:bg-[#FF7A3D]/5">
                        <input type="radio" name="category" value="klien" id="category_klien" onchange="toggleCategoryVendor()" {{ old('category') === 'klien' ? 'checked' : '' }} class="text-[#FF7A3D] focus:ring-[#FF7A3D]">
                        <div>
                            <span class="font-bold text-slate-900 block">Klien Mitra</span>
                            <span class="text-[10px] text-slate-500">Perwakilan Perusahaan Klien</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Dropdown Pilihan Klien (Muncul jika kategori Klien dipilih) -->
            <div id="vendor_select_wrapper" class="space-y-1.5 transition-all duration-300 {{ old('category') === 'klien' ? '' : 'hidden' }}">
                <label class="block font-bold text-slate-700">Pilih Perusahaan / Instansi Klien <span class="text-[#FF7A3D]">*</span></label>
                <select name="vendor_id" id="vendor_id" class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#FF7A3D] bg-white text-slate-900 cursor-pointer">
                    <option value="">-- Pilih PT / Klien dari Daftar Klien (Vendors) --</option>
                    @foreach($vendors as $v)
                        <option value="{{ $v->id }}" {{ old('vendor_id') == $v->id ? 'selected' : '' }}>
                            {{ $v->name }} {{ $v->client_name ? '— ' . $v->client_name : '' }} ({{ $v->code }})
                        </option>
                    @endforeach
                </select>
                <span class="text-[11px] text-slate-500 block">Daftar klien bersumber dari halaman <a href="{{ route('vendors.index') }}" target="_blank" class="text-[#015471] font-bold underline">Manajemen Klien & Kontrak</a></span>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Kata Sandi (Minimal 6 karakter) <span class="text-[#FF7A3D]">*</span></label>
                <input type="password" name="password" placeholder="••••••••" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeAddUserModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold transition">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white font-bold shadow-sm transition">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddUserModal() {
        document.getElementById('addUserModal').classList.remove('hidden');
        toggleCategoryVendor();
    }

    function closeAddUserModal() {
        document.getElementById('addUserModal').classList.add('hidden');
    }

    function toggleCategoryVendor() {
        const isKlien = document.getElementById('category_klien').checked;
        const vendorWrapper = document.getElementById('vendor_select_wrapper');
        const vendorSelect = document.getElementById('vendor_id');

        if (isKlien) {
            vendorWrapper.classList.remove('hidden');
            vendorSelect.setAttribute('required', 'required');
        } else {
            vendorWrapper.classList.add('hidden');
            vendorSelect.removeAttribute('required');
            vendorSelect.value = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleCategoryVendor();
    });
</script>
@endsection
