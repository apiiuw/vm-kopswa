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
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('exports.vendorsPdf') }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold rounded-xl bg-white hover:bg-slate-50 text-slate-800 border-2 border-slate-200 shadow-2xs transition">
                <i data-lucide="file-text" class="w-4 h-4 text-[#FF7A3D]"></i> Ekspor PDF
            </a>
            <a href="{{ route('exports.vendorsExcel') }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold rounded-xl bg-white hover:bg-slate-50 text-slate-800 border-2 border-slate-200 shadow-2xs transition">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-[#015471]"></i> Ekspor Excel
            </a>
            <a href="{{ route('exports.persewaanExcel') }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition" title="Download 2 Sheet: Data Kontrak & Data Kdr Kontrak">
                <i data-lucide="download" class="w-4 h-4"></i> Excel qryPersewaan
            </a>
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
                        Menampilkan seluruh unit motor sewaan Koperasi Swadharma yang tersebar di semua klien mitra.
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
            <table class="w-full text-left text-xs whitespace-normal min-w-[1600px]">
                <thead class="bg-[#f4f8fa] border-b border-slate-200 text-[#015471] font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-6 py-4 min-w-[280px]">Klien Penyewa & Kode</th>
                        <th class="px-6 py-4 min-w-[220px]">Nomor Kontrak</th>
                        <th class="px-6 py-4 min-w-[360px]">Nama Proyek Tender</th>
                        <th class="px-6 py-4 min-w-[260px]">Periode Kontrak Sewa</th>
                        <th class="px-6 py-4 text-right min-w-[210px]">Nilai Kontrak</th>
                        <th class="px-6 py-4 text-center min-w-[160px]">Jenis Pembayaran</th>
                        <th class="px-6 py-4 min-w-[260px]">PIC / Kontak Klien</th>
                        <th class="px-6 py-4 text-center min-w-[140px]">Armada Disewa</th>
                        <th class="px-6 py-4 text-center min-w-[120px]">Status</th>
                        <th class="px-6 py-4 text-right min-w-[180px] sticky right-0 bg-[#f4f8fa] shadow-[-6px_0_10px_-4px_rgba(0,0,0,0.05)]">Aksi</th>
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
                                <div class="text-[10px] text-slate-500 mt-1 space-x-2">
                                    @if($v->contract_date)
                                        <span>Tgl: <strong class="text-slate-700">{{ $v->contract_date->format('d/m/Y') }}</strong></span>
                                    @endif
                                    @if($v->duration_months)
                                        <span>Tenor: <strong class="text-slate-700">{{ $v->duration_months }} Bln</strong></span>
                                    @endif
                                    <span>• {{ $v->billing_cycle_label }}</span>
                                </div>
                            </td>

                            <!-- Nilai Kontrak -->
                            <td class="px-6 py-4 text-right align-top whitespace-nowrap font-mono">
                                @if((float) $v->contract_value > 0)
                                    <div class="font-bold text-slate-900 text-xs">
                                        Rp {{ number_format($v->contract_value, 0, ',', '.') }}
                                    </div>
                                    @if((float) $v->main_monthly_value > 0 || (float) $v->additional_contract_value > 0)
                                        <div class="text-[10px] text-slate-500 mt-0.5">
                                            Utama: Rp {{ number_format($v->main_monthly_value, 0, ',', '.') }}/bln
                                            @if((float) $v->additional_contract_value > 0)
                                                <br><span class="text-[#FF7A3D]">+Tambahan: Rp {{ number_format($v->additional_contract_value, 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <!-- Jenis Pembayaran -->
                            <td class="px-6 py-4 text-center align-top whitespace-nowrap">
                                @php
                                    $payBadge = match($v->payment_method) {
                                        'qris' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'va' => 'bg-sky-50 text-sky-700 border-sky-200',
                                        'transfer' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'giro' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'cash' => 'bg-slate-100 text-slate-700 border-slate-200',
                                        default => null,
                                    };
                                @endphp
                                @if($payBadge)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold border {{ $payBadge }}">
                                        {{ $v->payment_method_label }}
                                    </span>
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
                                        <div class="text-[11px] text-slate-600 font-mono flex items-center gap-1.5 whitespace-nowrap">
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

                                    <!-- Edit Button -->
                                    <button onclick="openEditVendorModal({{ json_encode($v) }})" class="p-1.5 text-slate-500 hover:text-[#015471] rounded-xl hover:bg-slate-100 transition" title="Edit Vendor">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <form action="{{ route('vendors.destroy', $v) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kontrak ini? Seluruh kendaraan terkait akan menjadi Standby di Pool.');" class="inline">
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
                            <td colspan="10" class="text-center py-12 text-slate-400">
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

            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                <div class="flex items-center justify-between">
                    <label class="block font-bold text-slate-800">Pilih Perusahaan Klien <span class="text-[#FF7A3D]">*</span></label>
                    <a href="{{ route('clients.index') }}" class="text-[11px] font-bold text-[#015471] hover:underline">+ Kelola Master Klien</a>
                </div>
                <select name="client_id" id="vendorClientId" required onchange="syncClientInfoToContract()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-black font-semibold">
                    <option value="">-- Pilih Klien --</option>
                    @foreach($clients ?? [] as $cl)
                        <option value="{{ $cl->id }}" data-name="{{ $cl->name }}" data-npwp="{{ $cl->npwp }}" data-pic="{{ $cl->pic_name }}" data-phone="{{ $cl->phone }}" data-email="{{ $cl->email }}">
                            {{ $cl->name }} ({{ $cl->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-800 mb-1">Alias / Nama Tampilan Kontrak</label>
                    <input type="text" name="name" id="vendorName" placeholder="Otomatis mengikuti nama klien" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#015471] focus:ring-2 focus:ring-[#015471]/20 outline-none text-black transition">
                </div>
                <div>
                    <label class="block font-bold text-slate-800 mb-1">Kode Kontrak <span class="text-[#FF7A3D]">*</span></label>
                    <input type="text" name="code" id="vendorCode" required placeholder="Contoh: KTR-2026-01" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#015471] focus:ring-2 focus:ring-[#015471]/20 outline-none text-black uppercase font-mono transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-800 mb-1">Nomor Kontrak Sewa</label>
                    <input type="text" name="contract_number" id="vendorContractNumber" placeholder="TNP/TT/KTR/0075/2025" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#015471] focus:ring-2 focus:ring-[#015471]/20 outline-none text-black font-mono transition">
                </div>
                <div>
                    <label class="block font-bold text-slate-800 mb-1">Penyedia Armada (Vendor)</label>
                    <input type="text" name="client_name" id="vendorClientName" value="Koperasi Swadharma (Penyedia)" placeholder="Koperasi Swadharma" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#015471] focus:ring-2 focus:ring-[#015471]/20 outline-none text-black transition">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-800 mb-1">Nama Proyek / Tender Sewa</label>
                <input type="text" name="project_name" id="vendorProjectName" placeholder="Contoh: Pengadaan Sewa Unit Operasional..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#015471] focus:ring-2 focus:ring-[#015471]/20 outline-none text-black transition">
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

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-800 mb-1">Tanggal Kontrak</label>
                    <input type="date" name="contract_date" id="vendorContractDate" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#015471] focus:ring-2 focus:ring-[#015471]/20 outline-none text-black transition">
                </div>
                <div>
                    <label class="block font-bold text-slate-800 mb-1">Tanggal Mulai (PrdAwal)</label>
                    <input type="date" name="contract_start_date" id="vendorStartDate" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#015471] focus:ring-2 focus:ring-[#015471]/20 outline-none text-black transition">
                </div>
                <div>
                    <label class="block font-bold text-slate-800 mb-1">Tanggal Selesai (PrdAkhir)</label>
                    <input type="date" name="contract_end_date" id="vendorEndDate" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#015471] focus:ring-2 focus:ring-[#015471]/20 outline-none text-black transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-800 mb-1">Jangka Waktu (Bulan)</label>
                    <input type="number" name="duration_months" id="vendorDurationMonths" min="1" placeholder="36" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#015471] focus:ring-2 focus:ring-[#015471]/20 outline-none text-black transition">
                </div>
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

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-800 mb-1">Siklus Penagihan / Cara Bayar</label>
                    <select name="billing_cycle" id="vendorBillingCycle" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#015471] focus:ring-2 focus:ring-[#015471]/20 outline-none text-black bg-white transition">
                        <option value="bulanan">1 Bulan (Bulanan)</option>
                        <option value="triwulanan">3 Bulan (Triwulan)</option>
                        <option value="semesteran">6 Bulan (Semester)</option>
                        <option value="tahunan">12 Bulan (Tahunan)</option>
                        <option value="2_tahunan">24 Bulan (2 Tahunan)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-800 mb-1">Jenis Pembayaran</label>
                    <select name="payment_method" id="vendorPaymentMethod" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#015471] focus:ring-2 focus:ring-[#015471]/20 outline-none text-black bg-white transition">
                        <option value="">-- Pilih Jenis Pembayaran --</option>
                        <option value="qris">QRIS</option>
                        <option value="va">Virtual Account (VA)</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="giro">Giro / Cek</option>
                        <option value="cash">Tunai (Cash)</option>
                    </select>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                <div class="text-[11px] font-extrabold text-[#015471] uppercase tracking-wider">Rincian Nilai Kontrak Sewa (Rp)</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nilai Kontrak Utama</label>
                        <input type="number" step="0.01" min="0" name="main_contract_value" id="vendorMainContractValue" oninput="recalcVendorContractTotal()" value="0" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-black font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nilai Bulanan Utama</label>
                        <input type="number" step="0.01" min="0" name="main_monthly_value" id="vendorMainMonthlyValue" value="0" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-black font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nilai Kontrak Tambahan</label>
                        <input type="number" step="0.01" min="0" name="additional_contract_value" id="vendorAdditionalContractValue" oninput="recalcVendorContractTotal()" value="0" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-black font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nilai Bulanan Tambahan</label>
                        <input type="number" step="0.01" min="0" name="additional_monthly_value" id="vendorAdditionalMonthlyValue" value="0" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-black font-mono">
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-800 mb-1">Nilai Kontrak Total (Utama + Tambahan)</label>
                    <input type="number" step="0.01" min="0" name="contract_value" id="vendorContractValue" value="0" class="w-full px-3.5 py-2 rounded-xl border border-[#015471]/30 bg-white text-[#015471] font-bold font-mono">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-800 mb-1">NPWP Klien (Untuk Faktur Pajak Coretax)</label>
                <input type="text" name="npwp" id="vendorNpwp" placeholder="01.234.567.8-901.000" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#015471] focus:ring-2 focus:ring-[#015471]/20 outline-none text-black font-mono transition">
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
        document.getElementById('modalTitle').innerText = 'Tambah Kontrak Sewa Baru';
        document.getElementById('vendorForm').action = "{{ route('vendors.store') }}";
        document.getElementById('methodField').innerHTML = '';
        
        document.getElementById('vendorClientId').value = '';
        document.getElementById('vendorName').value = '';
        document.getElementById('vendorCode').value = '';
        document.getElementById('vendorContractNumber').value = '';
        document.getElementById('vendorClientName').value = 'Koperasi Swadharma (Penyedia)';
        document.getElementById('vendorProjectName').value = '';
        document.getElementById('vendorPicName').value = '';
        document.getElementById('vendorPhone').value = '';
        document.getElementById('vendorEmail').value = '';
        document.getElementById('vendorContractDate').value = '';
        document.getElementById('vendorStartDate').value = '';
        document.getElementById('vendorEndDate').value = '';
        document.getElementById('vendorDurationMonths').value = '';
        document.getElementById('vendorStatus').value = 'active';
        document.getElementById('vendorBillingCycle').value = 'bulanan';
        document.getElementById('vendorPaymentMethod').value = '';
        document.getElementById('vendorMainContractValue').value = 0;
        document.getElementById('vendorMainMonthlyValue').value = 0;
        document.getElementById('vendorAdditionalContractValue').value = 0;
        document.getElementById('vendorAdditionalMonthlyValue').value = 0;
        document.getElementById('vendorContractValue').value = 0;
        document.getElementById('vendorNpwp').value = '';
        document.getElementById('vendorNotes').value = '';

        document.getElementById('vendorModal').classList.remove('hidden');
    }

    function syncClientInfoToContract() {
        const sel = document.getElementById('vendorClientId');
        const opt = sel.options[sel.selectedIndex];
        if (!opt || !opt.value) return;
        document.getElementById('vendorName').value = opt.dataset.name || '';
        document.getElementById('vendorNpwp').value = opt.dataset.npwp || '';
        document.getElementById('vendorPicName').value = opt.dataset.pic || '';
        document.getElementById('vendorPhone').value = opt.dataset.phone || '';
        document.getElementById('vendorEmail').value = opt.dataset.email || '';
    }

    function openEditVendorModal(v) {
        document.getElementById('modalTitle').innerText = 'Edit Kontrak: ' + (v.contract_number || v.name);
        document.getElementById('vendorForm').action = "/vendors/" + v.id;
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        document.getElementById('vendorClientId').value = v.client_id || '';
        document.getElementById('vendorName').value = v.name || '';
        document.getElementById('vendorCode').value = v.code || '';
        document.getElementById('vendorContractNumber').value = v.contract_number || '';
        document.getElementById('vendorClientName').value = v.client_name || 'Koperasi Swadharma (Penyedia)';
        document.getElementById('vendorProjectName').value = v.project_name || '';
        document.getElementById('vendorPicName').value = v.pic_name || '';
        document.getElementById('vendorPhone').value = v.phone || '';
        document.getElementById('vendorEmail').value = v.email || '';
        document.getElementById('vendorContractDate').value = v.contract_date ? v.contract_date.substring(0, 10) : '';
        document.getElementById('vendorStartDate').value = v.contract_start_date ? v.contract_start_date.substring(0, 10) : '';
        document.getElementById('vendorEndDate').value = v.contract_end_date ? v.contract_end_date.substring(0, 10) : '';
        document.getElementById('vendorDurationMonths').value = v.duration_months || '';
        document.getElementById('vendorStatus').value = v.status || 'active';
        document.getElementById('vendorBillingCycle').value = v.billing_cycle || 'bulanan';
        document.getElementById('vendorPaymentMethod').value = v.payment_method || '';
        document.getElementById('vendorMainContractValue').value = v.main_contract_value || 0;
        document.getElementById('vendorMainMonthlyValue').value = v.main_monthly_value || 0;
        document.getElementById('vendorAdditionalContractValue').value = v.additional_contract_value || 0;
        document.getElementById('vendorAdditionalMonthlyValue').value = v.additional_monthly_value || 0;
        document.getElementById('vendorContractValue').value = v.contract_value || 0;
        document.getElementById('vendorNpwp').value = v.npwp || '';
        document.getElementById('vendorNotes').value = v.notes || '';

        document.getElementById('vendorModal').classList.remove('hidden');
    }

    function recalcVendorContractTotal() {
        const main = parseFloat(document.getElementById('vendorMainContractValue').value) || 0;
        const add = parseFloat(document.getElementById('vendorAdditionalContractValue').value) || 0;
        document.getElementById('vendorContractValue').value = main + add;
    }

    function closeVendorModal() {
        document.getElementById('vendorModal').classList.add('hidden');
    }
</script>
@endsection
