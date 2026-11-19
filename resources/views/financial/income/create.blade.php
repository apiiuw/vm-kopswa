@extends('layouts.app')

@section('title', 'Buat Invoice Penagihan Sewa Bulanan')
@section('page_title', 'Buat Invoice Penagihan Sewa Bulanan')

@section('content')
<div class="w-full space-y-6 pb-12">

    <!-- Top Header Bar -->
    <div class="bg-white rounded-3xl border-2 border-slate-100 shadow-sm p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#015471] to-[#013547] text-white flex items-center justify-center shadow-md shrink-0">
                    <i data-lucide="file-text" class="w-7 h-7 text-[#FF7A3D]"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-xl font-black text-slate-900 tracking-tight">Formulir Invoice Penagihan Sewa Bulanan</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#FF7A3D]/10 text-[#FF7A3D] border border-[#FF7A3D]/20">
                            Pendapatan Sewa (Incomes)
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Penerbitan faktur tagihan sewa armada berkala ke instansi klien mitra beserta kalkulasi otomatis DPP, PPN 11%, dan PPh 23 (2%)
                    </p>
                </div>
            </div>

            <!-- Back & Action Links -->
            <div class="flex items-center gap-2.5">
                <a href="{{ route('financial.index', ['tab' => 'incomes']) }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 transition shadow-2xs">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Rekap Tagihan
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-3 shadow-2xs">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-500 shrink-0 mt-0.5"></i>
            <div>
                <strong class="font-bold block mb-1">Terdapat kesalahan pengisian formulir:</strong>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('financial.income.store') }}" method="POST" id="incomeForm" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left & Middle: Form Inputs (2 Cols) -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Card 1: Identitas Faktur & Periode Kontrak -->
                <div class="bg-white rounded-3xl border-2 border-slate-100 shadow-sm p-6 space-y-5">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <div class="w-9 h-9 rounded-xl bg-[#015471]/10 text-[#015471] flex items-center justify-center font-bold">
                            1
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Identitas Tagihan & Klien Mitra</h2>
                            <p class="text-[11px] text-slate-500">Nomor invoice resmi, tanggal terbit, dan kontrak rekanan</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">
                                Nomor Invoice / Faktur <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="hash" class="w-4 h-4"></i>
                                </span>
                                <input type="text" name="invoice_number" id="invoice_number" 
                                    value="{{ old('invoice_number', 'INV/RENT/' . date('Ym') . '/0' . rand(10, 99)) }}" 
                                    required 
                                    class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-300 font-mono font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#015471]">
                            </div>
                            <span class="text-[11px] text-slate-400 mt-1 block">Nomor faktur unik penagihan ke bagian keuangan mitra</span>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">
                                Tanggal Penerbitan Invoice <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="calendar" class="w-4 h-4"></i>
                                </span>
                                <input type="date" name="invoice_date" id="invoice_date" 
                                    value="{{ old('invoice_date', date('Y-m-d')) }}" 
                                    required 
                                    class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-300 font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#015471]">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">
                                Periode Tagihan Sewa (Bulan & Tahun) <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <input type="text" name="period_month_year" id="period_month_year" 
                                value="{{ old('period_month_year', date('F Y')) }}" 
                                placeholder="Contoh: September 2026 atau Triwulan III 2026" 
                                required 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#015471]">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">
                                Nama Klien / Instansi Rekanan <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <input type="text" name="client_name" id="client_name" 
                                value="{{ old('client_name', $currentVendor ? ($currentVendor->client?->name ?: $currentVendor->name) : '') }}" 
                                placeholder="Nama perusahaan klien / instansi..."
                                required 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#015471]">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block font-bold text-slate-700 mb-1.5">
                                Hubungkan dengan Entitas Kontrak Sewa (Otomatis Isi Nominal & Siklus)
                            </label>
                            <select name="vendor_id" id="vendor_id" onchange="syncContractToInvoice()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#015471]">
                                <option value="">-- Hubungkan dengan Kontrak Aktif / Otomatis --</option>
                                @foreach($vendors as $v)
                                    @php
                                        $monthlyContract = (float) $v->main_monthly_value + (float) $v->additional_monthly_value;
                                    @endphp
                                    <option value="{{ $v->id }}"
                                        data-client="{{ $v->client?->name ?: $v->name }}"
                                        data-contract="{{ $v->contract_number }}"
                                        data-cycle="{{ $v->billing_cycle ?: 'bulanan' }}"
                                        data-monthly="{{ $monthlyContract }}"
                                        {{ (old('vendor_id', $currentVendor?->id) == $v->id) ? 'selected' : '' }}>
                                        {{ $v->contract_number ?: $v->code }} — {{ $v->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Nomor Kontrak</label>
                            <input type="text" name="contract_number" id="contract_number" value="{{ old('contract_number', $currentVendor?->contract_number) }}" placeholder="Nomor kontrak sewa..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#015471]">
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Siklus Penagihan</label>
                            <select name="billing_cycle" id="billing_cycle" onchange="syncContractToInvoice(true)" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-semibold text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-[#015471]">
                                <option value="bulanan" {{ old('billing_cycle', $currentVendor?->billing_cycle) === 'bulanan' ? 'selected' : '' }}>1 Bulan (Bulanan)</option>
                                <option value="triwulanan" {{ old('billing_cycle', $currentVendor?->billing_cycle) === 'triwulanan' ? 'selected' : '' }}>3 Bulan (Triwulan)</option>
                                <option value="semesteran" {{ old('billing_cycle', $currentVendor?->billing_cycle) === 'semesteran' ? 'selected' : '' }}>6 Bulan (Semester)</option>
                                <option value="tahunan" {{ old('billing_cycle', $currentVendor?->billing_cycle) === 'tahunan' ? 'selected' : '' }}>12 Bulan (Tahunan)</option>
                                <option value="2_tahunan" {{ old('billing_cycle', $currentVendor?->billing_cycle) === '2_tahunan' ? 'selected' : '' }}>24 Bulan (2 Tahunan)</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block font-bold text-slate-700 mb-1.5">
                                Nominal DPP Kontrak / Custom (Rp) <span class="text-[11px] font-normal text-slate-400">(Isi jika kontrak menggunakan tarif mobil/fotokopi/dispenser atau tarif kontrak langsung; biarkan 0 untuk pakai hitungan CRF/Supra di bawah)</span>
                            </label>
                            <input type="number" name="custom_gross_amount" id="custom_gross_amount" oninput="recalculateInvoice()" value="{{ old('custom_gross_amount', 0) }}" min="0" class="w-full px-3.5 py-2.5 rounded-xl border border-[#015471]/30 bg-[#015471]/5 font-mono font-bold text-[#015471] focus:outline-none focus:ring-2 focus:ring-[#015471]">
                        </div>
                    </div>
                </div>

                <!-- Card 2: Rincian Unit Armada & Tarif Bulanan -->
                <div class="bg-white rounded-3xl border-2 border-slate-100 shadow-sm p-6 space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-[#015471]/10 text-[#015471] flex items-center justify-center font-bold">
                                2
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">Rincian Unit Sewa & Tarif Sewa per Unit</h2>
                                <p class="text-[11px] text-slate-500">Perhitungan DPP sewa berdasarkan jumlah unit kontrak aktif</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                            Auto Calculated
                        </span>
                    </div>

                    <!-- Ringkasan Master Unit Otomatis -->
                    <div class="p-3.5 rounded-2xl bg-[#015471]/5 border border-[#015471]/15 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-[#015471] text-white flex items-center justify-center font-bold text-[11px]">
                                <i data-lucide="database" class="w-3.5 h-3.5"></i>
                            </div>
                            <div>
                                <span class="font-bold text-slate-800">Total Armada Terdata: <strong class="text-[#015471]">{{ $totalUnits }} Unit</strong></span>
                                <p class="text-[10px] text-slate-500">Nilai di bawah otomatis terisi sesuai master data armada aktif di sistem</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 font-mono font-bold text-[11px]">
                            <span class="px-2 py-0.5 rounded bg-white text-[#015471] border border-slate-200">{{ $crfCount }} CRF</span>
                            <span class="px-2 py-0.5 rounded bg-white text-[#FF7A3D] border border-slate-200">{{ $supraCount }} Supra</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                        <!-- Unit Type 1: CRF 150 L -->
                        <div class="p-4 rounded-2xl bg-slate-50/90 border border-slate-200/80 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-[#015471]/10 text-[#015471] flex items-center justify-center">
                                        <i data-lucide="bike" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-xs">Honda CRF 150 L</h3>
                                        <span class="text-[10px] text-slate-500">Kategori Trail On/Off Road</span>
                                    </div>
                                </div>
                                <span class="text-[11px] font-bold text-[#015471] bg-[#015471]/10 px-2.5 py-1 rounded-lg">
                                    Rp {{ number_format($crfRate, 0, ',', '.') }} / unit
                                </span>
                            </div>

                            <div class="p-3 bg-white rounded-xl border border-slate-200/80 flex items-center justify-between">
                                <div>
                                    <span class="text-[11px] text-slate-500 block">Unit Terpilih di Checklist:</span>
                                    <div class="flex items-baseline gap-1.5 mt-0.5">
                                        <span class="text-2xl font-black font-mono text-[#015471]" id="display_crf_count">{{ $crfCount }}</span>
                                        <span class="text-xs font-bold text-slate-400">/ {{ $crfCount }} Unit</span>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-1 rounded-lg border border-emerald-200">
                                    Auto Calculated
                                </span>
                            </div>

                            <!-- Hidden input untuk submit form -->
                            <input type="hidden" name="crf_count" id="crf_count" value="{{ old('crf_count', $crfCount) }}">

                            <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-xs font-bold">
                                <span class="text-slate-500">Subtotal DPP CRF:</span>
                                <span class="text-[#015471] font-mono text-sm" id="label_crf_subtotal">Rp 0</span>
                            </div>
                        </div>

                        <!-- Unit Type 2: Supra X 125 -->
                        <div class="p-4 rounded-2xl bg-slate-50/90 border border-slate-200/80 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-[#FF7A3D]/10 text-[#FF7A3D] flex items-center justify-center">
                                        <i data-lucide="bike" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-xs">Honda Supra X 125</h3>
                                        <span class="text-[10px] text-slate-500">Kategori Bebek Operasional</span>
                                    </div>
                                </div>
                                <span class="text-[11px] font-bold text-[#FF7A3D] bg-[#FF7A3D]/10 px-2.5 py-1 rounded-lg">
                                    Rp {{ number_format($supraRate, 0, ',', '.') }} / unit
                                </span>
                            </div>

                            <div class="p-3 bg-white rounded-xl border border-slate-200/80 flex items-center justify-between">
                                <div>
                                    <span class="text-[11px] text-slate-500 block">Unit Terpilih di Checklist:</span>
                                    <div class="flex items-baseline gap-1.5 mt-0.5">
                                        <span class="text-2xl font-black font-mono text-[#FF7A3D]" id="display_supra_count">{{ $supraCount }}</span>
                                        <span class="text-xs font-bold text-slate-400">/ {{ $supraCount }} Unit</span>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-1 rounded-lg border border-emerald-200">
                                    Auto Calculated
                                </span>
                            </div>

                            <!-- Hidden input untuk submit form -->
                            <input type="hidden" name="supra_count" id="supra_count" value="{{ old('supra_count', $supraCount) }}">

                            <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-xs font-bold">
                                <span class="text-slate-500">Subtotal DPP Supra:</span>
                                <span class="text-[#FF7A3D] font-mono text-sm" id="label_supra_subtotal">Rp 0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Interactive Checklist Unit Armada -->
                    <div class="border border-slate-200 rounded-2xl overflow-hidden bg-white shadow-2xs">
                        <div class="p-4 bg-slate-50/90 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-[#015471] text-white flex items-center justify-center">
                                    <i data-lucide="list-checks" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-900 text-xs">Pilih Unit Armada yang Ditagihkan</h3>
                                    <p class="text-[10px] text-slate-500">Hilangkan centang jika unit tertentu sedang tidak aktif / dipotong sewanya</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="checkAllVehicles(true)" class="px-2.5 py-1 text-[10px] font-bold rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 transition">
                                    Pilih Semua (76)
                                </button>
                                <button type="button" onclick="checkAllVehicles(false)" class="px-2.5 py-1 text-[10px] font-bold rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 transition">
                                    Batalkan Semua
                                </button>
                            </div>
                        </div>

                        <!-- Search Box for Fleet Filter -->
                        <div class="p-3 border-b border-slate-100 bg-white">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="search" class="w-3.5 h-3.5"></i>
                                </span>
                                <input type="text" id="vehicleSearchInput" onkeyup="filterVehicleChecklist()" placeholder="Cari nomor plat, pemakai, atau lokasi kebun..." 
                                    class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#015471]">
                            </div>
                        </div>

                        <!-- Scrollable Vehicle Checklist Grid -->
                        <div class="p-3 max-h-72 overflow-y-auto divide-y divide-slate-100" id="vehicleChecklistContainer">
                            @foreach($vehicles as $v)
                                @php
                                    $isCrf = (stripos($v->vehicle_type, 'crf') !== false || stripos($v->model_name, 'crf') !== false);
                                    $vType = $isCrf ? 'crf' : 'supra';
                                    $rate = $v->rental_price_per_month ?: ($isCrf ? $crfRate : $supraRate);
                                @endphp
                                <label class="vehicle-item flex items-center justify-between p-2 rounded-xl hover:bg-slate-50/80 cursor-pointer transition text-xs" 
                                       data-search="{{ strtolower($v->plate_number . ' ' . $v->user_holder . ' ' . ($v->location ? $v->location->name : '')) }}">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" name="selected_vehicles[]" value="{{ $v->id }}" 
                                            class="vehicle-checkbox rounded text-[#015471] focus:ring-[#015471] w-4 h-4 cursor-pointer" 
                                            data-type="{{ $vType }}" 
                                            data-rate="{{ $rate }}" 
                                            checked 
                                            onchange="syncChecklistToCounts()">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono font-bold text-slate-900">{{ $v->plate_number }}</span>
                                                <span class="text-[10px] px-1.5 py-0.5 rounded font-bold {{ $isCrf ? 'bg-[#015471]/10 text-[#015471]' : 'bg-[#FF7A3D]/10 text-[#FF7A3D]' }}">
                                                    {{ $isCrf ? 'CRF 150 L' : 'Supra X 125' }}
                                                </span>
                                            </div>
                                            <p class="text-[10px] text-slate-500 mt-0.5">
                                                Pemakai: <strong class="text-slate-700">{{ $v->user_holder ?: 'Belum ditentukan' }}</strong> • {{ $v->location ? $v->location->name : 'Pusat' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-mono font-bold text-slate-800 text-[11px]">Rp {{ number_format($rate, 0, ',', '.') }}</span>
                                        <span class="block text-[9px] text-emerald-600 font-semibold">Aktif Sewa</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div class="px-4 py-2.5 bg-slate-50/80 border-t border-slate-200 flex items-center justify-between text-[11px] text-slate-600">
                            <span>Unit Ditagihkan: <strong id="checklist_selected_count" class="text-[#015471] font-bold">{{ $totalUnits }}</strong> dari {{ $totalUnits }} Unit</span>
                            <span class="text-emerald-700 font-bold flex items-center gap-1">
                                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Perhitungan Realtime
                            </span>
                        </div>
                    </div>

                    <!-- Dokumen Pendukung & BAST -->
                    <div class="pt-3 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">
                                Nomor BAST (Berita Acara Serah Terima)
                            </label>
                            <input type="text" name="bast_number" id="bast_number" value="{{ old('bast_number') }}" 
                                placeholder="Contoh: BAST/OPS/KOPSWA/2026/..." 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#015471]">
                            <span class="text-[11px] text-slate-400 mt-1 block">Rujukan nomor surat serah terima fisik unit</span>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">
                                Status Penagihan Awal <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <select name="status" id="status" onchange="togglePaymentDate()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#015471]">
                                <option value="tertagih" {{ old('status') === 'tertagih' ? 'selected' : '' }}>Tertagih (Faktur Terkirim / Belum Bayar)</option>
                                <option value="lunas" {{ old('status') === 'lunas' ? 'selected' : '' }}>Lunas (Telah Diterima Transfer Kas)</option>
                                <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (Konsep Internal)</option>
                            </select>
                        </div>

                        <div id="payment_date_wrapper" class="md:col-span-2 {{ old('status') === 'lunas' ? '' : 'hidden' }}">
                            <label class="block font-bold text-slate-700 mb-1.5">
                                Tanggal Pelunasan / Transfer Masuk <span class="text-emerald-600">*</span>
                            </label>
                            <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-emerald-300 bg-emerald-50/40 font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <span class="text-[11px] text-emerald-700 mt-1 block">Wajib diisi bila status telah lunas sebagai dasar pembukuan kas bank</span>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block font-bold text-slate-700 mb-1.5">Catatan Tambahan & Keterangan</label>
                            <textarea name="notes" rows="2" placeholder="Catatan internal pengiriman invoice, termin pembayaran, atau no referensi bank..."
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#015471]">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Live Financial Calculation Summary (1 Col) -->
            <div class="space-y-6">

                <!-- Summary Breakdown Card -->
                <div class="bg-gradient-to-br from-[#015471] to-[#013547] text-white rounded-3xl shadow-xl p-6 space-y-6 static lg:sticky lg:top-6">
                    <div class="flex items-center justify-between border-b border-white/10 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-[#FF7A3D]">
                                <i data-lucide="calculator" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm tracking-tight text-white">Rincian Finansial Faktur</h3>
                                <p class="text-[11px] text-white/70">Kalkulasi DPP, PPN & PPh 23</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#FF7A3D] text-white">
                            Live
                        </span>
                    </div>

                    <!-- Breakdown List -->
                    <div class="space-y-3.5 text-xs">
                        <div class="flex justify-between items-center text-white/80">
                            <span>Subtotal CRF 150:</span>
                            <span class="font-mono font-bold text-white text-sm" id="calc_crf_display">Rp 0</span>
                        </div>

                        <div class="flex justify-between items-center text-white/80">
                            <span>Subtotal Supra X:</span>
                            <span class="font-mono font-bold text-white text-sm" id="calc_supra_display">Rp 0</span>
                        </div>

                        <div class="pt-3 border-t border-white/10 flex justify-between items-center">
                            <span class="font-bold text-white">Dasar Pengenaan Pajak (DPP):</span>
                            <span class="font-mono font-black text-white text-base" id="calc_dpp_display">Rp 0</span>
                        </div>

                        <div class="flex justify-between items-center text-emerald-300">
                            <div class="flex items-center gap-1.5">
                                <span>PPN Tagihan (11%):</span>
                                <i data-lucide="info" class="w-3.5 h-3.5 text-emerald-300 cursor-help" title="PPN 11% ditagihkan kepada klien"></i>
                            </div>
                            <span class="font-mono font-bold text-emerald-300 text-sm" id="calc_ppn_display">+ Rp 0</span>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-white/10 border border-white/15 space-y-1">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-white/90">Total Invoice (DPP + PPN):</span>
                                <span class="font-mono font-black text-[#FF7A3D] text-lg" id="calc_total_invoice_display">Rp 0</span>
                            </div>
                            <p class="text-[10px] text-white/60">Nilai fisik invoice yang ditagihkan ke klien mitra</p>
                        </div>

                        <div class="pt-2 border-t border-white/10 space-y-2">
                            <div class="flex justify-between items-center text-amber-300 text-xs">
                                <span>Estimasi Potongan PPh 23 (2%):</span>
                                <span class="font-mono font-bold text-amber-300" id="calc_pph_display">- Rp 0</span>
                            </div>

                            <div class="flex justify-between items-center text-emerald-300 font-bold text-xs">
                                <span>Penerimaan Kas Bersih (Netto):</span>
                                <span class="font-mono text-emerald-300 text-sm" id="calc_net_display">Rp 0</span>
                            </div>
                            <p class="text-[10px] text-white/50">Penerimaan bersih setelah dipotong withholding tax PPh 23 oleh bendahara klien</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 border-t border-white/15 space-y-2.5">
                        <button type="submit" class="w-full py-3 px-4 rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white font-bold text-xs shadow-lg flex items-center justify-center gap-2 transition cursor-pointer">
                            <i data-lucide="save" class="w-4 h-4"></i> Simpan & Terbitkan Invoice
                        </button>
                        <a href="{{ route('financial.index', ['tab' => 'incomes']) }}" class="w-full py-2.5 px-4 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs flex items-center justify-center gap-2 transition text-center">
                            Batal
                        </a>
                    </div>
                </div>

            </div>

        </div>

    </form>

</div>

<script>
    function formatRupiah(num) {
        return 'Rp ' + Math.round(num).toLocaleString('id-ID');
    }

    function recalculateInvoice() {
        const crfCount = parseInt(document.getElementById('crf_count').value) || 0;
        const supraCount = parseInt(document.getElementById('supra_count').value) || 0;

        const crfRate = {{ (float)$crfRate }};
        const supraRate = {{ (float)$supraRate }};

        const crfSubtotal = crfCount * crfRate;
        const supraSubtotal = supraCount * supraRate;
        const customGross = parseFloat(document.getElementById('custom_gross_amount')?.value || 0);
        const dpp = customGross > 0 ? customGross : (crfSubtotal + supraSubtotal);
        const ppn = dpp * 0.11;
        const totalInvoice = dpp + ppn;
        const pph23 = dpp * 0.02;
        const netIncome = dpp - pph23;

        document.getElementById('label_crf_subtotal').textContent = formatRupiah(crfSubtotal);
        document.getElementById('label_supra_subtotal').textContent = formatRupiah(supraSubtotal);

        document.getElementById('calc_crf_display').textContent = formatRupiah(crfSubtotal);
        document.getElementById('calc_supra_display').textContent = formatRupiah(supraSubtotal);
        document.getElementById('calc_dpp_display').textContent = formatRupiah(dpp);
        document.getElementById('calc_ppn_display').textContent = '+ ' + formatRupiah(ppn);
        document.getElementById('calc_total_invoice_display').textContent = formatRupiah(totalInvoice);
        document.getElementById('calc_pph_display').textContent = '- ' + formatRupiah(pph23);
        document.getElementById('calc_net_display').textContent = formatRupiah(netIncome);
    }

    function togglePaymentDate() {
        const status = document.getElementById('status').value;
        const wrapper = document.getElementById('payment_date_wrapper');
        if (status === 'lunas') {
            wrapper.classList.remove('hidden');
        } else {
            wrapper.classList.add('hidden');
        }
    }

    // Sinkronkan Checklist ke Input Angka & Total
    function syncChecklistToCounts() {
        const checkboxes = document.querySelectorAll('.vehicle-checkbox');
        let crf = 0;
        let supra = 0;
        let selectedTotal = 0;

        checkboxes.forEach(cb => {
            if (cb.checked) {
                selectedTotal++;
                if (cb.dataset.type === 'crf') {
                    crf++;
                } else {
                    supra++;
                }
            }
        });

        document.getElementById('crf_count').value = crf;
        document.getElementById('supra_count').value = supra;
        document.getElementById('display_crf_count').textContent = crf;
        document.getElementById('display_supra_count').textContent = supra;
        document.getElementById('checklist_selected_count').textContent = selectedTotal;

        recalculateInvoice();
    }

    function recalculateFromInputs() {
        recalculateInvoice();
    }

    function checkAllVehicles(state) {
        const checkboxes = document.querySelectorAll('.vehicle-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = state;
        });
        syncChecklistToCounts();
    }

    function filterVehicleChecklist() {
        const query = (document.getElementById('vehicleSearchInput').value || '').toLowerCase().trim();
        const items = document.querySelectorAll('.vehicle-item');

        items.forEach(item => {
            const searchData = item.getAttribute('data-search') || '';
            if (searchData.includes(query)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    }

    function syncContractToInvoice(cycleOnly = false) {
        const sel = document.getElementById('vendor_id');
        const opt = sel?.options[sel.selectedIndex];
        if (!opt || !opt.value) return;

        if (!cycleOnly) {
            if (opt.dataset.client) document.getElementById('client_name').value = opt.dataset.client;
            if (opt.dataset.contract) document.getElementById('contract_number').value = opt.dataset.contract;
            if (opt.dataset.cycle) document.getElementById('billing_cycle').value = opt.dataset.cycle;
        }

        const cycle = document.getElementById('billing_cycle')?.value || 'bulanan';
        const mult = { bulanan: 1, triwulanan: 3, semesteran: 6, tahunan: 12, '2_tahunan': 24 }[cycle] || 1;
        const monthly = parseFloat(opt.dataset.monthly || 0);
        if (monthly > 0) {
            document.getElementById('custom_gross_amount').value = Math.round(monthly * mult);
        }
        recalculateInvoice();
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('crf_count').addEventListener('input', recalculateFromInputs);
        document.getElementById('supra_count').addEventListener('input', recalculateFromInputs);
        syncChecklistToCounts();
        syncContractToInvoice();
        togglePaymentDate();
    });
</script>
@endsection
