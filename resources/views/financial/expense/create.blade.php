@extends('layouts.app')

@section('title', 'Catat Transaksi Pengeluaran Kas Baru')
@section('page_title', 'Catat Transaksi Pengeluaran Kas Baru')

@section('content')
<div class="w-full space-y-6 pb-12">

    <!-- Top Header Bar -->
    <div class="bg-white rounded-3xl border-2 border-slate-100 shadow-sm p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#015471] to-[#013547] text-white flex items-center justify-center shadow-md shrink-0">
                    <i data-lucide="wallet" class="w-7 h-7 text-[#FF7A3D]"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-xl font-black text-slate-900 tracking-tight">Formulir Catat Pengeluaran Kas Operasional</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#015471]/10 text-[#015471] border border-[#015471]/20">
                            Buku Kas & Beban Armada
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Pencatatan realisasi beban servis rutin, ban, aki, suku cadang, asuransi, pajak, unit motor pengganti, atau operasional lapangan
                    </p>
                </div>
            </div>

            <!-- Back Link -->
            <div class="flex items-center gap-2.5">
                <a href="{{ route('financial.index', ['tab' => 'expenses']) }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 transition shadow-2xs">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Buku Kas
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

    <form action="{{ route('financial.expense.store') }}" method="POST" enctype="multipart/form-data" id="expenseForm" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left & Middle: Form Inputs (2 Cols) -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Card 1: Kategori & Transaksi Dasar -->
                <div class="bg-white rounded-3xl border-2 border-slate-100 shadow-sm p-6 space-y-5">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <div class="w-9 h-9 rounded-xl bg-[#015471]/10 text-[#015471] flex items-center justify-center font-bold">
                            1
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Klasifikasi Beban & Waktu Transaksi</h2>
                            <p class="text-[11px] text-slate-500">Pilih pos pengeluaran anggaran dan tanggal realisasi pembayaran kas</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">
                                Tanggal Transaksi Kas <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="calendar" class="w-4 h-4"></i>
                                </span>
                                <input type="date" name="transaction_date" id="transaction_date" 
                                    value="{{ old('transaction_date', date('Y-m-d')) }}" 
                                    required 
                                    class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-300 font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#015471]">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">
                                Kategori Pos Anggaran <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <select name="category" id="category" required onchange="updateCategoryInfo()"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#015471]">
                                @foreach($categories as $key => $cat)
                                    <option value="{{ $key }}" data-desc="{{ $cat['desc'] }}" {{ old('category') === $key ? 'selected' : '' }}>
                                        {{ $cat['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Banner Deskripsi Pos Kategori -->
                        <div class="md:col-span-2 p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-start gap-3">
                            <i data-lucide="info" class="w-4 h-4 text-[#015471] shrink-0 mt-0.5"></i>
                            <div class="text-xs">
                                <span class="font-bold text-slate-800" id="cat_label_display">Service Berkala & Pelumas</span>
                                <p class="text-[11px] text-slate-500 mt-0.5" id="cat_desc_display">Ganti oli mesin, tune-up berkala, filter udara, dan servis rutin berkala.</p>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block font-bold text-slate-700 mb-1.5">
                                Terkait Unit Kendaraan Tertentu (Opsional)
                            </label>
                            <select name="vehicle_id" id="vehicle_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#015471]">
                                <option value="">-- Biaya Umum / Overhead / Seluruh Armada (Non-Spesifik) --</option>
                                @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}" {{ old('vehicle_id') == $v->id ? 'selected' : '' }}>
                                        {{ $v->plate_number }} — {{ $v->model_name }} (Lokasi: {{ $v->location ? $v->location->name : 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-[11px] text-slate-400 mt-1 block">Pilih nomor polisi jika pengeluaran dibebankan ke satu unit spesifik (misal: ganti ban motor tertentu)</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Rincian Finansial & Penerima -->
                <div class="bg-white rounded-3xl border-2 border-slate-100 shadow-sm p-6 space-y-5">
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                        <div class="w-9 h-9 rounded-xl bg-[#015471]/10 text-[#015471] flex items-center justify-center font-bold">
                            2
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">Uraian Transaksi, Nominal & Dokumen Bukti</h2>
                            <p class="text-[11px] text-slate-500">Judul pengeluaran, nominal rupiah, nomor bukti dan vendor rekanan</p>
                        </div>
                    </div>

                    <div class="space-y-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">
                                Judul / Uraian Pengeluaran Kas <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" 
                                placeholder="Contoh: Penggantian Ban Luar Belakang IRC & Servis Berkala 20.000 KM" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#015471]">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1.5">
                                    Nominal Pengeluaran Kas (Rp) <span class="text-[#FF7A3D]">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-xs font-bold text-slate-400">Rp</span>
                                    <input type="number" name="amount" id="amount" value="{{ old('amount') }}" 
                                        placeholder="0" min="0" required oninput="updateLiveAmount()"
                                        class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 font-mono font-bold text-base text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#015471]">
                                </div>
                                <span class="text-[11px] text-slate-400 mt-1 block">Total nominal bruto yang dibayarkan kasir</span>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1.5">
                                    Penerima Kas / Bengkel Rekanan / Vendor
                                </label>
                                <input type="text" name="recipient_vendor" id="recipient_vendor" value="{{ old('recipient_vendor') }}" 
                                    placeholder="Contoh: AHASS Bintang Motor / Toko Ban Sentosa"
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#015471]">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1.5">
                                    Nomor Memo Pengajuan Internal
                                </label>
                                <input type="text" name="memo_number" id="memo_number" value="{{ old('memo_number') }}" 
                                    placeholder="SUP/LPG/MEMO/..." 
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#015471]">
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1.5">
                                    Nomor Journal Entry (Buku Besar Akuntansi)
                                </label>
                                <input type="text" name="journal_entry_number" id="journal_entry_number" value="{{ old('journal_entry_number') }}" 
                                    placeholder="JE-2026-..." 
                                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#015471]">
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Keterangan / Rincian Pekerjaan Tambahan</label>
                            <textarea name="description" rows="3" placeholder="Rincian suku cadang yang diganti, nomor part, atau catatan faktur bengkel..."
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#015471]">{{ old('description') }}</textarea>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5">Upload Bukti Fisik / Kwitansi / Nota Kas (PDF, JPG, PNG)</label>
                            <input type="file" name="proof_file" accept=".pdf,.jpg,.jpeg,.png"
                                class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#015471] file:text-white hover:file:bg-[#013547] cursor-pointer">
                            <span class="text-[11px] text-slate-400 mt-1 block">Maksimal ukuran file 4MB. Format yang didukung: PDF, JPEG, PNG.</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Summary & Confirmation Card (1 Col) -->
            <div class="space-y-6">

                <div class="bg-gradient-to-br from-[#015471] to-[#013547] text-white rounded-3xl shadow-xl p-6 space-y-6 sticky top-6">
                    <div class="flex items-center justify-between border-b border-white/10 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-[#FF7A3D]">
                                <i data-lucide="receipt" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm tracking-tight text-white">Ringkasan Pengeluaran</h3>
                                <p class="text-[11px] text-white/70">Realisasi Anggaran Operasional</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#FF7A3D] text-white">
                            Realtime
                        </span>
                    </div>

                    <!-- Live Details -->
                    <div class="space-y-4 text-xs">
                        <div class="p-4 rounded-2xl bg-white/10 border border-white/15 space-y-1">
                            <span class="text-xs text-white/70 block">Total Beban Kas:</span>
                            <div class="font-mono font-black text-2xl text-[#FF7A3D]" id="summary_amount_display">
                                Rp 0
                            </div>
                        </div>

                        <div class="space-y-2.5 text-xs text-white/80">
                            <div class="flex justify-between items-start gap-2">
                                <span class="text-white/60">Pos Kategori:</span>
                                <span class="font-bold text-right text-white" id="summary_cat_display">Service Berkala & Pelumas</span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-white/60">Tanggal Kas:</span>
                                <span class="font-bold text-white" id="summary_date_display">{{ date('d/m/Y') }}</span>
                            </div>

                            <div class="flex justify-between items-start gap-2">
                                <span class="text-white/60">Kendaraan:</span>
                                <span class="font-bold text-right text-white" id="summary_vehicle_display">Biaya Umum</span>
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-200 text-[11px] flex items-center gap-2">
                            <i data-lucide="check-circle-2" class="w-4 h-4 shrink-0 text-emerald-400"></i>
                            <span>Otomatis memotong pagu anggaran terkait dan tercatat di Buku Besar Kas.</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 border-t border-white/15 space-y-2.5">
                        <button type="submit" class="w-full py-3 px-4 rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white font-bold text-xs shadow-lg flex items-center justify-center gap-2 transition cursor-pointer">
                            <i data-lucide="check" class="w-4 h-4"></i> Catat & Simpan Pengeluaran
                        </button>
                        <a href="{{ route('financial.index', ['tab' => 'expenses']) }}" class="w-full py-2.5 px-4 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs flex items-center justify-center gap-2 transition text-center">
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

    function updateLiveAmount() {
        const val = parseFloat(document.getElementById('amount').value) || 0;
        document.getElementById('summary_amount_display').textContent = formatRupiah(val);
    }

    function updateCategoryInfo() {
        const sel = document.getElementById('category');
        const opt = sel.options[sel.selectedIndex];
        const label = opt.text;
        const desc = opt.getAttribute('data-desc');

        document.getElementById('cat_label_display').textContent = label;
        document.getElementById('cat_desc_display').textContent = desc || '';
        document.getElementById('summary_cat_display').textContent = label;
    }

    function updateVehicleDisplay() {
        const sel = document.getElementById('vehicle_id');
        const text = sel.options[sel.selectedIndex].text;
        document.getElementById('summary_vehicle_display').textContent = sel.value ? text.split('—')[0].trim() : 'Biaya Umum';
    }

    function updateDateDisplay() {
        const d = document.getElementById('transaction_date').value;
        if (d) {
            const parts = d.split('-');
            if (parts.length === 3) {
                document.getElementById('summary_date_display').textContent = parts[2] + '/' + parts[1] + '/' + parts[0];
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateCategoryInfo();
        updateLiveAmount();
        updateVehicleDisplay();
        updateDateDisplay();

        document.getElementById('vehicle_id').addEventListener('change', updateVehicleDisplay);
        document.getElementById('transaction_date').addEventListener('change', updateDateDisplay);
    });
</script>
@endsection
