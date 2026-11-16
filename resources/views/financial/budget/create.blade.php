@extends('layouts.app')

@section('title', 'Input Plafon Pos Anggaran Operasional')
@section('page_title', 'Perencanaan & Plafon Anggaran Operasional')

@section('content')
<div class="w-full space-y-6 pb-12">

    <!-- Top Header Bar -->
    <div class="bg-white rounded-3xl border-2 border-slate-100 shadow-sm p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#015471] to-[#013547] text-white flex items-center justify-center shadow-md shrink-0">
                    <i data-lucide="piggy-bank" class="w-7 h-7 text-[#FF7A3D]"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-xl font-black text-slate-900 tracking-tight">Input Plafon Pos Anggaran Operasional</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#015471]/10 text-[#015471] border border-[#015471]/20">
                            Pagu Kontrak 36 Bulan
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Tetapkan alokasi pagu acuan untuk pemeliharaan, ban, accu, sparepart, asuransi, atau operasional armada
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('financial.index', ['tab' => 'overview']) }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl border-2 border-slate-200 text-slate-700 bg-white hover:bg-slate-50 transition shadow-2xs">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-slate-400"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Main Form Card -->
    <form action="{{ route('financial.budget.store') }}" method="POST" id="budgetForm" class="space-y-6">
        @csrf

        <!-- Vendor Context Badge -->
        @if($currentVendor)
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#015471] text-white flex items-center justify-center font-black text-xs">
                        {{ strtoupper(substr($currentVendor->name, 0, 2)) }}
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Vendor Kontrak Aktif</span>
                        <span class="text-xs font-black text-slate-900">{{ $currentVendor->name }} ({{ $currentVendor->code }})</span>
                        <span class="text-[11px] text-slate-500 block">Klien: {{ $currentVendor->client_name ?: 'Internal Koperasi Swadharma' }}</span>
                    </div>
                </div>
                <input type="hidden" name="vendor_id" value="{{ $currentVendor->id }}">
                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-[11px] font-bold rounded-full border border-emerald-200">
                    Terhubung
                </span>
            </div>
        @elseif($vendors->count() > 0)
            <div class="bg-white rounded-2xl border-2 border-slate-100 p-5 shadow-sm space-y-2">
                <label class="block text-xs font-bold text-slate-900">
                    Alokasikan ke Vendor Kontrak <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <select name="vendor_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold focus:ring-2 focus:ring-[#015471] focus:outline-hidden">
                    <option value="">-- Semua Vendor / Umum --</option>
                    @foreach($vendors as $v)
                        <option value="{{ $v->id }}">{{ $v->name }} ({{ $v->code }}) - Klien: {{ $v->client_name }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <!-- Card Section 1: Kategori & Pos Anggaran -->
        <div class="bg-white rounded-3xl border-2 border-slate-100 shadow-sm p-6 space-y-5">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-[#FF7A3D]/10 text-[#FF7A3D] flex items-center justify-center font-bold">
                    <i data-lucide="layers" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-900">1. Kategori & Identitas Pos Anggaran</h3>
                    <p class="text-[11px] text-slate-500">Pilih kategori pos atau gunakan preset kalkulasi memo</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5">
                        Kategori Pos Anggaran <span class="text-[#FF7A3D]">*</span>
                    </label>
                    <div class="relative">
                        <select name="category" id="categorySelect" required onchange="applyPreset(this.value)"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-[#015471] focus:border-transparent focus:outline-hidden transition appearance-none bg-white">
                            @foreach($categoryPresets as $key => $preset)
                                <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>
                                    {{ $preset['label'] }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <p id="categoryDescription" class="text-[11px] text-slate-500 mt-1.5 italic">
                        {{ $categoryPresets['service_berkala']['desc'] }}
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5">
                        Nama / Label Pos Anggaran <span class="text-[#FF7A3D]">*</span>
                    </label>
                    <input type="text" name="label" id="labelInput" value="{{ old('label', $categoryPresets['service_berkala']['label']) }}" 
                           placeholder="Contoh: Service Berkala & Pelumas - CRF 150 & Supra X" required 
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-[#015471] focus:outline-hidden">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1.5">
                            Target Kendaraan / Armada
                        </label>
                        <input type="text" name="vehicle_group" id="targetInput" value="{{ old('vehicle_group', $categoryPresets['service_berkala']['target']) }}" 
                               placeholder="Contoh: CRF 150 & Supra X / Semua Unit" 
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-[#015471] focus:outline-hidden">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-1.5">
                            Jumlah Unit Kontrak <span class="text-[#FF7A3D]">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" name="qty_units" id="qtyInput" value="{{ old('qty_units', 70) }}" min="1" required oninput="recalculate()"
                                   class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-[#015471] focus:outline-hidden">
                            <span class="absolute right-3.5 top-2.5 text-xs font-bold text-slate-400">Unit</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Section 2: Simulasi Tarif & Plafon 36 Bulan -->
        <div class="bg-white rounded-3xl border-2 border-slate-100 shadow-sm p-6 space-y-5">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-[#015471]/10 text-[#015471] flex items-center justify-center font-bold">
                    <i data-lucide="calculator" class="w-4 h-4 text-[#FF7A3D]"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-900">2. Kalkulasi Tarif Satuan & Total Pagu 36 Bulan</h3>
                    <p class="text-[11px] text-slate-500">Kalkulator otomatis berdasarkan formula memo tender proyek</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5">
                        Tarif / Unit / Bulan (Rp) <span class="text-[#FF7A3D]">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-xs font-bold text-slate-400">Rp</span>
                        <input type="number" name="unit_rate_monthly" id="monthlyRateInput" 
                               value="{{ old('unit_rate_monthly', 83333) }}" min="0" required oninput="onMonthlyRateChange()"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-[#015471] focus:outline-hidden">
                    </div>
                    <span class="text-[10px] text-slate-400 block mt-1">Acuan bulanan per kendaraan</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5">
                        Tarif / Unit / Tahun (Rp) <span class="text-slate-400 font-normal">(Auto x 12)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-xs font-bold text-slate-400">Rp</span>
                        <input type="number" name="unit_rate_yearly" id="yearlyRateInput" 
                               value="{{ old('unit_rate_yearly', 1000000) }}" min="0" oninput="onYearlyRateChange()"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 focus:ring-2 focus:ring-[#015471] focus:outline-hidden">
                    </div>
                    <span class="text-[10px] text-slate-400 block mt-1">Dihitung otomatis: Bulanan x 12</span>
                </div>
            </div>

            <!-- Total 36 Months Banner Calculator -->
            <div class="p-5 rounded-2xl bg-gradient-to-br from-[#015471] to-[#013547] text-white space-y-3 shadow-md">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-[#FF7A3D]">Total Plafon Anggaran 36 Bulan (Rp) *</span>
                        <h4 class="text-2xl font-black tracking-tight font-mono" id="budgetDisplayFormatted">
                            Rp 210.000.000
                        </h4>
                    </div>
                    <div class="text-left sm:text-right">
                        <span class="text-[10px] text-slate-300 block">Formula Perhitungan:</span>
                        <span class="text-xs font-bold text-white block font-mono" id="formulaDetail">
                            70 Unit x Rp 83.333 x 36 Bulan
                        </span>
                    </div>
                </div>

                <div class="pt-3 border-t border-white/10 flex items-center justify-between gap-4">
                    <span class="text-[11px] text-slate-200">
                        Total angka riil di database:
                    </span>
                    <div class="w-48">
                        <input type="number" name="total_budget_36m" id="totalBudgetInput" 
                               value="{{ old('total_budget_36m', 210000000) }}" required 
                               class="w-full px-3 py-1.5 rounded-lg bg-white/10 border border-white/20 text-white font-mono font-bold text-xs text-right focus:bg-white focus:text-black focus:outline-hidden transition">
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Section 3: Catatan & Dasar Tender -->
        <div class="bg-white rounded-3xl border-2 border-slate-100 shadow-sm p-6 space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-900">3. Catatan & Referensi Dokumen Tender</h3>
                    <p class="text-[11px] text-slate-500">Nomor memo internal atau acuan kontrak tender</p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-800 mb-1.5">
                    Catatan / Dasar Memo Anggaran
                </label>
                <textarea name="notes" rows="3" placeholder="Contoh: Dokumen Acuan Tender Sewa Kendaraan 70 Unit (36 Bulan) Memo Internal 2026 - 2028 Koperasi Swadharma..."
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-[#015471] focus:outline-hidden">{{ old('notes') }}</textarea>
            </div>
        </div>

        <!-- Bottom Action Buttons -->
        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('financial.index', ['tab' => 'overview']) }}" class="px-5 py-3 rounded-xl border-2 border-slate-200 text-slate-700 hover:bg-slate-100 font-bold text-xs transition">
                Batal & Kembali
            </a>
            <button type="submit" class="inline-flex items-center gap-2 px-7 py-3 rounded-xl bg-[#015471] hover:bg-[#013e54] text-white font-black text-xs shadow-md shadow-[#015471]/20 transition transform active:scale-98">
                <i data-lucide="check-circle-2" class="w-4 h-4 text-[#FF7A3D]"></i> Simpan Plafon Pos Anggaran
            </button>
        </div>
    </form>
</div>

<!-- Dynamic Script for Preset & Calculations -->
<script>
    const presets = @json($categoryPresets);

    function applyPreset(categoryKey) {
        if (!presets[categoryKey]) return;
        const p = presets[categoryKey];

        document.getElementById('labelInput').value = p.label;
        document.getElementById('targetInput').value = p.target;
        document.getElementById('qtyInput').value = p.qty;
        document.getElementById('monthlyRateInput').value = p.monthly_rate;
        document.getElementById('categoryDescription').innerText = p.desc;

        onMonthlyRateChange();
    }

    function onMonthlyRateChange() {
        const monthly = parseFloat(document.getElementById('monthlyRateInput').value) || 0;
        document.getElementById('yearlyRateInput').value = Math.round(monthly * 12);
        recalculate();
    }

    function onYearlyRateChange() {
        const yearly = parseFloat(document.getElementById('yearlyRateInput').value) || 0;
        document.getElementById('monthlyRateInput').value = Math.round(yearly / 12);
        recalculate();
    }

    function recalculate() {
        const qty = parseFloat(document.getElementById('qtyInput').value) || 0;
        const monthly = parseFloat(document.getElementById('monthlyRateInput').value) || 0;
        const total = Math.round(qty * monthly * 36);

        document.getElementById('totalBudgetInput').value = total;
        document.getElementById('budgetDisplayFormatted').innerText = 'Rp ' + total.toLocaleString('id-ID');
        document.getElementById('formulaDetail').innerText = `${qty} Unit x Rp ${monthly.toLocaleString('id-ID')} x 36 Bulan`;
    }

    document.addEventListener('DOMContentLoaded', function() {
        recalculate();
    });
</script>
@endsection
