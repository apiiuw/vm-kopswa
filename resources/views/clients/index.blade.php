@extends('layouts.app')

@section('title', 'Master Data Klien')
@section('header_title', 'Master Data Klien')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2.5">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-2.5">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-black text-slate-900">Master Data Klien / Perusahaan Mitra</h2>
            <p class="text-xs text-slate-500 mt-0.5">Data perusahaan penyewa, NPWP/NITKU (Coretax), dan PIC utama. Satu klien dapat memiliki banyak kontrak sewa.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <form action="{{ route('clients.index') }}" method="GET" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / NPWP..." class="text-xs px-3 py-2 rounded-xl border border-slate-300 text-slate-900">
                <button type="submit" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-bold text-slate-700">Cari</button>
            </form>
            <a href="{{ route('exports.clientsPdf') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold rounded-xl bg-white hover:bg-slate-50 text-slate-800 border-2 border-slate-200 shadow-2xs transition">
                <i data-lucide="file-text" class="w-4 h-4 text-[#FF7A3D]"></i> Ekspor PDF
            </a>
            <a href="{{ route('exports.clientsExcel') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold rounded-xl bg-white hover:bg-slate-50 text-slate-800 border-2 border-slate-200 shadow-2xs transition">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-[#015471]"></i> Ekspor Excel
            </a>
            <button type="button" onclick="openClientModal()" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-xs transition">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Klien Baru
            </button>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase text-[10px] font-extrabold">
                    <tr>
                        <th class="px-5 py-3.5">Kode</th>
                        <th class="px-5 py-3.5">Nama Perusahaan Klien</th>
                        <th class="px-5 py-3.5">NPWP / NITKU</th>
                        <th class="px-5 py-3.5">PIC & Kontak</th>
                        <th class="px-5 py-3.5 text-center">Jumlah Kontrak</th>
                        <th class="px-5 py-3.5 text-center">Akun User</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($clients as $c)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-3.5 font-mono font-bold text-[#015471]">{{ $c->code }}</td>
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900">{{ $c->name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $c->address ?: '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5 font-mono text-[11px]">
                                <div>NPWP: {{ $c->npwp ?: '-' }}</div>
                                @if($c->nitku)<div class="text-slate-400">NITKU: {{ $c->nitku }}</div>@endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-slate-800">{{ $c->pic_name ?: '-' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $c->phone }} {{ $c->email ? '• '.$c->email : '' }}</div>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="px-2.5 py-1 rounded-full bg-[#015471]/10 text-[#015471] font-bold">{{ $c->contracts_count }} Kontrak</span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 font-bold">{{ $c->users_count }} User</span>
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                                <button type="button" onclick='openEditClientModal(@json($c))' class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-[11px]">Edit</button>
                                <form action="{{ route('clients.destroy', $c) }}" method="POST" class="inline" onsubmit="return confirm('Hapus klien ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-slate-400">Belum ada data klien.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($clients->hasPages())
            <div class="p-4 border-t border-slate-100">{{ $clients->links() }}</div>
        @endif
    </div>
</div>

<!-- Modal Client -->
<div id="clientModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
            <h3 id="clientModalTitle" class="font-bold text-slate-900 text-sm">Tambah Master Klien Baru</h3>
            <button type="button" onclick="document.getElementById('clientModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="clientForm" action="{{ route('clients.store') }}" method="POST" class="space-y-3.5 text-xs">
            @csrf
            <div id="clientMethodField"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Perusahaan Klien *</label>
                    <input type="text" name="name" id="cl_name" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-slate-900">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kode Klien *</label>
                    <input type="text" name="code" id="cl_code" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 font-mono uppercase text-slate-900">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">NPWP (Coretax)</label>
                    <input type="text" name="npwp" id="cl_npwp" placeholder="16 digit NPWP" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 font-mono text-slate-900">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">NITKU</label>
                    <input type="text" name="nitku" id="cl_nitku" placeholder="22 digit NITKU" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 font-mono text-slate-900">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">PIC Utama</label>
                    <input type="text" name="pic_name" id="cl_pic" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-slate-900">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">No. Telepon</label>
                    <input type="text" name="phone" id="cl_phone" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-slate-900">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" id="cl_email" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-slate-900">
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Alamat Perusahaan</label>
                <textarea name="address" id="cl_address" rows="2" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-slate-900"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('clientModal').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 font-bold text-slate-700">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-[#FF7A3D] text-white font-bold">Simpan Klien</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openClientModal() {
        document.getElementById('clientModalTitle').innerText = 'Tambah Master Klien Baru';
        document.getElementById('clientForm').action = "{{ route('clients.store') }}";
        document.getElementById('clientMethodField').innerHTML = '';
        ['cl_name','cl_code','cl_npwp','cl_nitku','cl_pic','cl_phone','cl_email','cl_address'].forEach(id => document.getElementById(id).value = '');
        document.getElementById('clientModal').classList.remove('hidden');
    }
    function openEditClientModal(c) {
        document.getElementById('clientModalTitle').innerText = 'Edit Klien: ' + c.name;
        document.getElementById('clientForm').action = "/clients/" + c.id;
        document.getElementById('clientMethodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('cl_name').value = c.name || '';
        document.getElementById('cl_code').value = c.code || '';
        document.getElementById('cl_npwp').value = c.npwp || '';
        document.getElementById('cl_nitku').value = c.nitku || '';
        document.getElementById('cl_pic').value = c.pic_name || '';
        document.getElementById('cl_phone').value = c.phone || '';
        document.getElementById('cl_email').value = c.email || '';
        document.getElementById('cl_address').value = c.address || '';
        document.getElementById('clientModal').classList.remove('hidden');
    }
</script>
@endsection
