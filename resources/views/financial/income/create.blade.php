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
                                value="{{ old('client_name', $currentVendor ? ($currentVendor->client_name ?: $currentVendor->name) : 'PT Pertamina EP Regional 2') }}" 
                                required 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#015471]">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block font-bold text-slate-700 mb-1.5">
                                Hubungkan dengan Entitas Kontrak / Vendor (Opsional)
                            </label>
                            <select name="vendor_id" id="vendor_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#015471]">
                                <option value="">-- Hubungkan dengan Kontrak Aktif / Otomatis --</option>
                                @foreach($vendors as $v)
                                    <option value="{{ $v->id }}" {{ (old('vendor_id', $currentVendor?->id) == $v->id) ? 'selected' : '' }}>
                                        {{ $v->name }} ({{ $v->code }}) - Klien: {{ $v->client_name ?: 'Tidak spesifik' }}
                                    </option>
                                @endforeach
                            </select>
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-xs">
                        <!-- Unit Type 1 -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="bike" class="w-4 h-4 text-[#015471]"></i>
                                    <h3 class="font-bold text-slate-900 text-xs">Unit Tipe 1 (CRF 150)</h3>
                                </div>
                                <span class="text-[10px] font-bold text-[#015471] bg-[#015471]/10 px-2 py-0.5 rounded-md">
                                    Rp 1.570.000 / unit
                                </span>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Jumlah Unit Aktif Disewa</label>
                                <div class="relative">
                                    <input type="number" name="crf_count" id="crf_count" value="{{ old('crf_count', 70) }}" min="0" required
                                        class="w-full px-3.5 py-2 rounded-xl border border-slate-300 font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#015471]">
                                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-bold text-slate-400">Unit</span>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-xs font-bold">
                                <span class="text-slate-500">Subtotal DPP CRF:</span>
                                <span class="text-slate-900 font-mono" id="label_crf_subtotal">Rp 109.900.000</span>
                            </div>
                        </div>

                        <!-- Unit Type 2 -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="bike" class="w-4 h-4 text-[#FF7A3D]"></i>
                                    <h3 class="font-bold text-slate-900 text-xs">Unit Tipe 2 (Supra X)</h3>
                                </div>
                                <span class="text-[10px] font-bold text-[#FF7A3D] bg-[#FF7A3D]/10 px-2 py-0.5 rounded-md">
                                    Rp 860.000 / unit
                                </span>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Jumlah Unit Aktif Disewa</label>
                                <div class="relative">
                                    <input type="number" name="supra_count" id="supra_count" value="{{ old('supra_count', 6) }}" min="0" required
                                        class="w-full px-3.5 py-2 rounded-xl border border-slate-300 font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#015471]">
                                    <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs font-bold text-slate-400">Unit</span>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-xs font-bold">
                                <span class="text-slate-500">Subtotal DPP Supra:</span>
                                <span class="text-slate-900 font-mono" id="label_supra_subtotal">Rp 5.160.000</span>
                            </div>
                        </div>
                    </div>

                    <!-- Dokumen Pendukung & BAST -->
                    <div class="pt-3 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">
                                Nomor BAST (Berita Acara Serah Terima)
                            </label>
                            <input type="text" name="bast_number" id="bast_number" value="{{ old('bast_number') }}" 
                                placeholder="Contoh: BAST/OPS/SUP/2026/..." 
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

        const crfRate = 1570000;
        const supraRate = 860000;

        const crfSubtotal = crfCount * crfRate;
        const supraSubtotal = supraCount * supraRate;
        const dpp = crfSubtotal + supraSubtotal;
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

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('crf_count').addEventListener('input', recalculateInvoice);
        document.getElementById('supra_count').addEventListener('input', recalculateInvoice);
        recalculateInvoice();
        togglePaymentDate();
    });
</script>
@endsection
