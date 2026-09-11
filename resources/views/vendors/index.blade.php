@extends('layouts.app')

@section('title', 'Manajemen Klien & Kontrak Proyek')
@section('page_title', 'Daftar Klien Proyek Sewa')

@section('content')
<div class="space-y-6 w-full bg-white text-black">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-black tracking-tight">Manajemen Klien & Kontrak Sewa</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola seluruh klien penyewa armada motor, nomor kontrak tender, dan alokasi unit sewa</p>
        </div>
        <div>
            <button onclick="openCreateVendorModal()" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-sm transition">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Klien / Kontrak Baru
            </button>
        </div>
    </div>

    <!-- Active Client/Contract Banner Indicator -->
    <div class="bg-gradient-to-r from-[#015471]/5 via-[#015471]/[0.02] to-transparent rounded-2xl p-4 sm:p-5 border border-[#015471]/15 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl bg-[#015471] text-white flex items-center justify-center shrink-0 shadow-xs">
                <i data-lucide="building-2" class="w-5 h-5 text-[#FF7A3D]"></i>
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-semibold text-slate-500">Filter Klien Aktif:</span>
                    @if($currentVendor)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-[#015471] text-white text-xs font-bold shadow-2xs">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#FF7A3D]"></span>
                            {{ $currentVendor->name }} ({{ $currentVendor->code }})
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-200/80 text-slate-800 text-xs font-bold">
                            <i data-lucide="layers" class="w-3.5 h-3.5 text-[#FF7A3D]"></i>
                            Semua Klien
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    @if($currentVendor)
                        Menampilkan monitoring armada sewa khusus klien <strong>{{ $currentVendor->name }}</strong> (No. Kontrak: {{ $currentVendor->contract_number ?: '-' }}).
                    @else
                        Menampilkan seluruh unit motor sewaan PT SUP yang tersebar di semua klien mitra.
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            @if($currentVendor)
                <form action="{{ route('vendors.switch') }}" method="POST">
                    @csrf
                    <input type="hidden" name="vendor_id" value="all">
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-300 hover:border-[#015471] text-xs font-bold text-slate-700 hover:text-[#015471] bg-white hover:bg-[#015471]/5 transition shadow-2xs">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 text-[#FF7A3D]"></i>
                        <span>Reset ke Semua Klien</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Clients / Contracts Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="overflow-x-auto overflow-y-hidden scrollbar-thin">
            <table class="w-full text-left text-xs whitespace-normal min-w-[1450px]">
                <thead class="bg-[#f4f8fa] border-b border-slate-200 text-[#015471] font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-6 py-4 w-[280px]">Klien Penyewa & Kode</th>
                        <th class="px-6 py-4 w-[220px]">Nomor Kontrak</th>
                        <th class="px-6 py-4 w-[340px]">Nama Proyek Tender</th>
                        <th class="px-6 py-4 w-[240px]">Periode Kontrak Sewa</th>
                        <th class="px-6 py-4 w-[240px]">PIC / Kontak Klien</th>
                        <th class="px-6 py-4 text-center w-[130px]">Armada Disewa</th>
                        <th class="px-6 py-4 text-center w-[120px]">Status</th>
                        <th class="px-6 py-4 text-right w-[160px] sticky right-0 bg-[#f4f8fa] shadow-[-6px_0_10px_-4px_rgba(0,0,0,0.05)]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-black">
                    @forelse($vendors as $v)
                        <tr class="hover:bg-slate-50/70 transition-colors {{ $activeVendorId == $v->id ? 'bg-[#015471]/5' : '' }}">
                            <!-- Vendor & Code -->
                            <td class="px-6 py-4 align-top">
                                <div class="flex items-start gap-3">
                                    <span class="px-2.5 py-1 rounded-lg bg-[#015471] text-white font-mono text-[11px] font-extrabold tracking-wider shrink-0 mt-0.5 shadow-2xs">
                                        {{ $v->code }}
                                    </span>
                                    <div class="min-w-0">
                                        <span class="font-extrabold text-black text-xs block leading-snug">{{ $v->name }}</span>
                                        <span class="text-[11px] text-slate-500 block truncate mt-0.5">{{ $v->email ?: '-' }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Nomor Kontrak -->
                            <td class="px-6 py-4 align-top">
                                @if($v->contract_number)
                                    <span class="inline-block font-mono text-[11px] font-semibold text-[#015471] bg-[#015471]/10 px-3 py-1.5 rounded-lg border border-[#015471]/15 whitespace-nowrap">
                                        {{ $v->contract_number }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <!-- Klien & Proyek -->
                            <td class="px-6 py-4 align-top">
                                <div class="space-y-1">
                                    <div class="font-bold text-black text-xs leading-snug">
                                        {{ $v->client_name ?: '-' }}
                                    </div>
                                    @if($v->project_name)
                                        <div class="text-[11px] text-slate-500 leading-relaxed">
                                            {{ $v->project_name }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Periode Kontrak -->
                            <td class="px-6 py-4 align-top whitespace-nowrap">
                                @if($v->contract_start_date && $v->contract_end_date)
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-700 text-[11px] font-medium">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-[#015471]"></i>
                                        <span>{{ $v->contract_start_date->format('d/m/Y') }} – {{ $v->contract_end_date->format('d/m/Y') }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <!-- PIC / Kontak -->
                            <td class="px-6 py-4 align-top">
                                <div class="space-y-1">
                                    <div class="font-bold text-black text-xs leading-snug">
                                        {{ $v->pic_name ?: '-' }}
                                    </div>
                                    @if($v->phone)
                                        <div class="text-[11px] text-slate-600 font-mono flex items-center gap-1.5">
                                            <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                                            <span>{{ $v->phone }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Armada -->
                            <td class="px-6 py-4 text-center align-top whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-[#015471]/10 text-[#015471] font-black text-xs">
                                    <i data-lucide="truck" class="w-3.5 h-3.5 text-[#FF7A3D]"></i>
                                    {{ $v->vehicles_count }} Unit
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 text-center align-top whitespace-nowrap">
                                @if($v->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi (Sticky Right agar tetap terlihat saat di-scroll) -->
                            <td class="px-6 py-4 text-right align-top whitespace-nowrap sticky right-0 bg-white group-hover:bg-slate-50/70 shadow-[-6px_0_10px_-4px_rgba(0,0,0,0.05)] {{ $activeVendorId == $v->id ? 'bg-[#f7fafc]' : '' }}">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Switch Active Filter -->
                                    <form action="{{ route('vendors.switch') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="vendor_id" value="{{ $v->id }}">
                                        @if($activeVendorId == $v->id)
                                            <span class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold rounded-xl bg-[#015471] text-white shadow-2xs cursor-default">
                                                <i data-lucide="check" class="w-3.5 h-3.5 text-[#FF7A3D]"></i>
                                                <span class="text-[11px]">Aktif</span>
                                            </span>
                                        @else
                                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold rounded-xl border border-slate-200 hover:border-[#015471] text-[#015471] hover:bg-[#015471] hover:text-white transition shadow-2xs" title="Pilih filter vendor ini">
                                                <i data-lucide="check-circle" class="w-3.5 h-3.5 text-[#FF7A3D]"></i>
                                                <span class="text-[11px]">Pilih</span>
                                            </button>
                                        @endif
                                    </form>

                                    <!-- Manage Locations Button -->
                                    <button onclick="openManageLocationsModal({{ json_encode($v) }})" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-bold rounded-xl border border-emerald-200 hover:border-emerald-500 text-emerald-700 bg-emerald-50/60 hover:bg-emerald-100/80 transition shadow-2xs" title="Kelola Wilayah Kebun / Pos Operasional">
                                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-600"></i>
                                        <span class="text-[11px]">{{ $v->locations_count }} Kebun</span>
                                    </button>

                                    <!-- Edit Button -->
                                    <button onclick="openEditVendorModal({{ json_encode($v) }})" class="p-1.5 text-slate-500 hover:text-[#015471] rounded-xl hover:bg-slate-100 transition" title="Edit Vendor">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <form action="{{ route('vendors.destroy', $v) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus vendor ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-xl hover:bg-rose-50 transition" title="Hapus Vendor">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-400">
                                <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-2 opacity-40"></i>
                                <span class="text-sm font-semibold block">Belum ada data vendor terdaftar</span>
                                <span class="text-xs text-slate-400 mt-1 block">Silakan klik tombol "Tambah Vendor Baru" di atas untuk menambahkan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vendors->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $vendors->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal Create / Edit Client / Contract -->
<div id="vendorModal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-xl w-full p-6 border border-slate-200 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#015471]/10 text-[#015471] flex items-center justify-center font-bold">
                    <i data-lucide="building-2" class="w-4 h-4 text-[#FF7A3D]"></i>
                </div>
                <h3 id="modalTitle" class="text-base font-extrabold text-black">Tambah Klien / Kontrak Sewa</h3>
            </div>
            <button onclick="closeVendorModal()" class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-black transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="vendorForm" action="{{ route('vendors.store') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div id="methodField"></div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-800 mb-1">Nama Klien / Perusahaan Penyewa <span class="text-[#FF7A3D]">*</span></label>
                    <input type="text" name="name" id="vendorName" required placeholder="PT Perkebunan Nusantara I Regional 7" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#015471] focus:ring-2 focus:ring-[#015471]/20 outline-none text-black transition">
                </div>
                <div>
                    <label class="block font-bold text-slate-800 mb-1">Kode Klien <span class="text-[#FF7A3D]">*</span></label>
                    <input type="text" name="code" id="vendorCode" required placeholder="PTPN-7 / AGRO" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#015471] focus:ring-2 focus:ring-[#015471]/20 outline-none text-black uppercase font-mono transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-800 mb-1">Nomor Kontrak Sewa</label>
                    <input type="text" name="contract_number" id="vendorContractNumber" placeholder="TNP/TT/KTR/0075/2025" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#015471] focus:ring-2 focus:ring-[#015471]/20 outline-none text-black font-mono transition">
                </div>
                <div>
                    <label class="block font-bold text-slate-800 mb-1">Penyedia Armada (Vendor)</label>
                    <input type="text" name="client_name" id="vendorClientName" value="PT Swadharma Utama Prima (Penyedia)" placeholder="PT Swadharma Utama Prima" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#015471] focus:ring-2 focus:ring-[#015471]/20 outline-none text-black transition">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-800 mb-1">Nama Proyek / Tender Sewa</label>
                <input type="text" name="project_name" id="vendorProjectName" placeholder="Pengadaan Sewa 76 Unit Sepeda Motor Operasional..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#015471] focus:ring-2 focus:ring-[#015471]/20 outline-none text-black transition">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-800 mb-1">Nama PIC Klien / Pengawas Lapangan</label>
                    <input type="text" name="pic_name" id="vendorPicName" placeholder="Nama Koordinator / Pengawas Klien" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#015471] focus:ring-2 focus:ring-[#015471]/20 outline-none text-black transition">
                </div>
                <div>
                    <label class="block font-bold text-slate-800 mb-1">No. Telepon / HP PIC</label>
                    <input type="text" name="phone" id="vendorPhone" placeholder="0812-..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#015471] focus:ring-2 focus:ring-[#015471]/20 outline-none text-black transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-800 mb-1">Tanggal Mulai Kontrak</label>
                    <input type="date" name="contract_start_date" id="vendorStartDate" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#015471] focus:ring-2 focus:ring-[#015471]/20 outline-none text-black transition">
                </div>
                <div>
                    <label class="block font-bold text-slate-800 mb-1">Tanggal Selesai Kontrak</label>
                    <input type="date" name="contract_end_date" id="vendorEndDate" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#015471] focus:ring-2 focus:ring-[#015471]/20 outline-none text-black transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-800 mb-1">Email Klien</label>
                    <input type="email" name="email" id="vendorEmail" placeholder="operasional@klien.co.id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#015471] focus:ring-2 focus:ring-[#015471]/20 outline-none text-black transition">
                </div>
                <div>
                    <label class="block font-bold text-slate-800 mb-1">Status Kontrak</label>
                    <select name="status" id="vendorStatus" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#015471] focus:ring-2 focus:ring-[#015471]/20 outline-none text-black bg-white transition">
                        <option value="active">Aktif</option>
                        <option value="inactive">Selesai / Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-800 mb-1">Catatan Tambahan Kontrak</label>
                <textarea name="notes" id="vendorNotes" rows="2" placeholder="Detail spesifikasi tender, ketentuan servis, atau catatan unit..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#015471] focus:ring-2 focus:ring-[#015471]/20 outline-none text-black transition"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100">
                <button type="button" onclick="closeVendorModal()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 font-bold transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white font-bold transition shadow-xs">
                    Simpan Data Klien & Kontrak
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateVendorModal() {
        document.getElementById('modalTitle').innerText = 'Tambah Klien / Kontrak Sewa Baru';
        document.getElementById('vendorForm').action = "{{ route('vendors.store') }}";
        document.getElementById('methodField').innerHTML = '';
        
        document.getElementById('vendorName').value = '';
        document.getElementById('vendorCode').value = '';
        document.getElementById('vendorContractNumber').value = '';
        document.getElementById('vendorClientName').value = 'PT Swadharma Utama Prima (Penyedia)';
        document.getElementById('vendorProjectName').value = '';
        document.getElementById('vendorPicName').value = '';
        document.getElementById('vendorPhone').value = '';
        document.getElementById('vendorEmail').value = '';
        document.getElementById('vendorStartDate').value = '';
        document.getElementById('vendorEndDate').value = '';
        document.getElementById('vendorStatus').value = 'active';
        document.getElementById('vendorNotes').value = '';

        document.getElementById('vendorModal').classList.remove('hidden');
    }

    function openEditVendorModal(v) {
        document.getElementById('modalTitle').innerText = 'Edit Klien & Kontrak: ' + v.name;
        document.getElementById('vendorForm').action = "/vendors/" + v.id;
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        document.getElementById('vendorName').value = v.name || '';
        document.getElementById('vendorCode').value = v.code || '';
        document.getElementById('vendorContractNumber').value = v.contract_number || '';
        document.getElementById('vendorClientName').value = v.client_name || 'PT Swadharma Utama Prima (Penyedia)';
        document.getElementById('vendorProjectName').value = v.project_name || '';
        document.getElementById('vendorPicName').value = v.pic_name || '';
        document.getElementById('vendorPhone').value = v.phone || '';
        document.getElementById('vendorEmail').value = v.email || '';
        document.getElementById('vendorStartDate').value = v.contract_start_date ? v.contract_start_date.substring(0, 10) : '';
        document.getElementById('vendorEndDate').value = v.contract_end_date ? v.contract_end_date.substring(0, 10) : '';
        document.getElementById('vendorStatus').value = v.status || 'active';
        document.getElementById('vendorNotes').value = v.notes || '';

        document.getElementById('vendorModal').classList.remove('hidden');
    }

    function closeVendorModal() {
        document.getElementById('vendorModal').classList.add('hidden');
    }

    // Kelola Wilayah / Kebun per Klien Modal
    let currentVendorData = null;

    function openManageLocationsModal(vendor) {
        currentVendorData = vendor;
        document.getElementById('manageLocVendorName').innerText = vendor.name + ' (' + vendor.code + ')';
        document.getElementById('newLocVendorId').value = vendor.id;
        document.getElementById('newLocName').value = '';
        document.getElementById('newLocCode').value = '';
        document.getElementById('newLocManager').value = '';

        renderLocationsList(vendor.locations || []);
        document.getElementById('manageLocationsModal').classList.remove('hidden');
        lucide.createIcons();
    }

    function closeManageLocationsModal() {
        document.getElementById('manageLocationsModal').classList.add('hidden');
    }

    function renderLocationsList(locations) {
        const container = document.getElementById('locationsListContainer');
        if (!locations || locations.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8 text-slate-400">
                    <i data-lucide="map-pin-off" class="w-8 h-8 mx-auto mb-1.5 opacity-40"></i>
                    <p class="text-xs font-semibold">Belum ada wilayah kebun untuk klien ini</p>
                    <p class="text-[10px] text-slate-400">Gunakan form di bawah untuk menambahkan kebun baru</p>
                </div>
            `;
            lucide.createIcons();
            return;
        }

        let html = '<div class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden">';
        locations.forEach(loc => {
            html += `
                <div class="p-3 flex items-center justify-between hover:bg-slate-50 transition bg-white">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-slate-900">${loc.name}</span>
                            ${loc.code ? `<span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-600">${loc.code}</span>` : ''}
                        </div>
                        <div class="flex items-center gap-3 text-[11px] text-slate-500 mt-0.5">
                            <span>PIC: ${loc.manager_name || '-'}</span>
                            <span>•</span>
                            <span class="text-[#015471] font-semibold">${loc.vehicles_count ?? 0} Unit Terdaftar</span>
                        </div>
                    </div>
                    <div>
                        <form action="/locations/${loc.id}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kebun ini?')" class="inline">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition" title="Hapus Kebun">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
        lucide.createIcons();
    }
</script>

<!-- Modal Kelola Kebun / Lokasi Klien -->
<div id="manageLocationsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-slate-100 transition-all flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <i data-lucide="map-pin" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Kelola Wilayah Kebun / Pos</h3>
                    <p class="text-[11px] text-slate-500 font-semibold" id="manageLocVendorName">Klien</p>
                </div>
            </div>
            <button type="button" onclick="closeManageLocationsModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Daftar Kebun Eksisting -->
        <div class="py-4 overflow-y-auto flex-1 space-y-3">
            <h4 class="text-xs font-bold text-slate-700 flex items-center justify-between">
                <span>Daftar Wilayah Kebun Terdaftar</span>
            </h4>
            <div id="locationsListContainer">
                <!-- Injected via JS -->
            </div>
        </div>

        <!-- Form Tambah Kebun Baru -->
        <div class="pt-4 border-t border-slate-100 bg-slate-50/60 -mx-6 -mb-6 p-6 rounded-b-2xl">
            <h4 class="text-xs font-bold text-slate-800 mb-2 flex items-center gap-1.5">
                <i data-lucide="plus-circle" class="w-3.5 h-3.5 text-[#015471]"></i>
                <span>Tambah Kebun Baru untuk Klien Ini</span>
            </h4>
            <form action="{{ route('locations.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="vendor_id" id="newLocVendorId">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <div class="sm:col-span-2">
                        <input type="text" name="name" id="newLocName" required placeholder="Nama Kebun (misal: Kebun Bergen)" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white">
                    </div>
                    <div>
                        <input type="text" name="code" id="newLocCode" placeholder="Kode (BERGEN)" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white uppercase">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="text" name="manager_name" id="newLocManager" placeholder="Nama Manajer / Pengawas Kebun (Opsional)" class="flex-1 text-xs px-3 py-2 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white">
                    <button type="submit" class="px-4 py-2 text-xs font-bold rounded-xl bg-[#015471] hover:bg-[#023e54] text-white transition flex items-center gap-1.5 shrink-0">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Tambah</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
