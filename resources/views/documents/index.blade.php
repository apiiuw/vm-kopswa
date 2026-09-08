@extends('layouts.app')

@section('title', 'Dokumen & Pajak Kendaraan')
@section('page_title', 'Modul Legalitas, STNK & Pajak Kendaraan')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto bg-white text-black">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-black tracking-tight">Dokumen & Pajak STNK Armada</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pantau masa berlaku STNK tahunan, ganti plat 5 tahunan, BPKB, dan lakukan pencatatan bayar pajak langsung ke buku kas</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('documents.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-sm transition">
                <i data-lucide="plus" class="w-4 h-4"></i> Catat Dokumen Baru
            </a>
        </div>
    </div>

    <!-- Alert Filter Banner -->
    @if($expiringCount > 0)
        <div class="p-4 rounded-2xl bg-[#FF7A3D]/10 border-2 border-[#FF7A3D]/40 text-black flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#FF7A3D] text-white flex items-center justify-center shrink-0 font-bold shadow-sm">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-black">Perhatian: Ada {{ $expiringCount }} Dokumen / Pajak Jatuh Tempo Dalam 30 Hari!</h4>
                    <p class="text-[11px] text-slate-600">Segera lakukan pengurusan perpanjangan STNK dan pembayaran PKB ke Samsat. Klik tombol Bayar Pajak untuk pelunasan.</p>
                </div>
            </div>
            <a href="{{ route('documents.index', ['filter' => 'expiring_soon']) }}" class="inline-flex items-center justify-center px-3.5 py-1.5 text-xs font-bold bg-[#FF7A3D] text-white rounded-xl hover:bg-[#e56b32] transition shrink-0">
                Filter Unit Jatuh Tempo
            </a>
        </div>
    @endif

    <!-- Table Card Container -->
    <div class="bg-white rounded-2xl border-2 border-slate-100 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-white flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <a href="{{ route('documents.index') }}" class="px-3 py-1.5 text-xs font-bold rounded-lg {{ !request('filter') ? 'bg-[#015471] text-white' : 'bg-white text-black border-2 border-slate-200' }}">
                    Semua Dokumen
                </a>
                <a href="{{ route('documents.index', ['filter' => 'expiring_soon']) }}" class="px-3 py-1.5 text-xs font-bold rounded-lg {{ request('filter') == 'expiring_soon' ? 'bg-[#FF7A3D] text-white' : 'bg-white text-black border-2 border-slate-200' }}">
                    Jatuh Tempo < 30 Hari ({{ $expiringCount }})
                </a>
                <a href="{{ route('documents.index', ['filter' => 'expired']) }}" class="px-3 py-1.5 text-xs font-bold rounded-lg {{ request('filter') == 'expired' ? 'bg-rose-600 text-white' : 'bg-white text-black border-2 border-slate-200' }}">
                    Lewat Jatuh Tempo
                </a>
            </div>

            <!-- Filter By Document Type & Per Page -->
            <form action="{{ route('documents.index') }}" method="GET" class="flex items-center gap-3">
                @if(request('filter')) <input type="hidden" name="filter" value="{{ request('filter') }}"> @endif
                
                <select name="type" onchange="this.form.submit()" class="text-xs font-bold px-3 py-1.5 rounded-xl border border-slate-300 focus:outline-[#015471] bg-white text-black cursor-pointer">
                    <option value="">Semua Jenis Dokumen</option>
                    <option value="stnk" {{ request('type') == 'stnk' ? 'selected' : '' }}>STNK / Pajak Tahunan</option>
                    <option value="pajak_tahunan" {{ request('type') == 'pajak_tahunan' ? 'selected' : '' }}>PKB & SWDKLLJ Tahunan</option>
                    <option value="plat_5_tahunan" {{ request('type') == 'plat_5_tahunan' ? 'selected' : '' }}>Pajak & Ganti Plat 5 Tahunan</option>
                    <option value="bpkb" {{ request('type') == 'bpkb' ? 'selected' : '' }}>BPKB</option>
                    <option value="asuransi" {{ request('type') == 'asuransi' ? 'selected' : '' }}>Polis Asuransi</option>
                    <option value="blokir_polda" {{ request('type') == 'blokir_polda' ? 'selected' : '' }}>Surat Blokir Polda</option>
                </select>

                <div class="flex items-center gap-1.5">
                    <label for="per_page" class="text-xs font-bold text-slate-600">Tampilkan:</label>
                    <select name="per_page" onchange="this.form.submit()" class="text-xs font-bold px-2.5 py-1.5 rounded-xl border border-slate-300 focus:outline-[#015471] bg-white text-black cursor-pointer shadow-2xs">
                        <option value="10" {{ ($perPage ?? 15) == 10 ? 'selected' : '' }}>10 Baris</option>
                        <option value="15" {{ ($perPage ?? 15) == 15 ? 'selected' : '' }}>15 Baris</option>
                        <option value="25" {{ ($perPage ?? 15) == 25 ? 'selected' : '' }}>25 Baris</option>
                        <option value="50" {{ ($perPage ?? 15) == 50 ? 'selected' : '' }}>50 Baris</option>
                        <option value="100" {{ ($perPage ?? 15) == 100 ? 'selected' : '' }}>100 Baris</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs whitespace-nowrap">
                <thead class="bg-[#e6f0f3] border-b border-slate-200 text-[#015471] font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">Plat Nomor & Unit</th>
                        <th class="px-5 py-3.5">Jenis Dokumen</th>
                        <th class="px-5 py-3.5">Nomor Registrasi / Seri</th>
                        <th class="px-5 py-3.5">Tgl Pembayaran Terakhir</th>
                        <th class="px-5 py-3.5">Jatuh Tempo (Expiry)</th>
                        <th class="px-5 py-3.5">Biaya Terakhir</th>
                        <th class="px-5 py-3.5 text-right">Aksi & Bayar Pajak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-black">
                    @forelse($documents as $doc)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3.5 font-bold text-black">
                                <a href="{{ route('vehicles.show', $doc->vehicle) }}" class="inline-flex items-center gap-1.5 font-mono text-[#015471] hover:text-[#FF7A3D] font-black">
                                    {{ $doc->vehicle ? $doc->vehicle->plate_number : '-' }}
                                </a>
                                <span class="block text-[10px] text-slate-500 font-normal">
                                    {{ $doc->vehicle ? $doc->vehicle->model_name : '' }} • {{ $doc->vehicle && $doc->vehicle->location ? $doc->vehicle->location->name : 'Non-Lokasi' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 font-bold text-black">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-[#015471]/10 text-[#015471]">
                                    {{ $doc->type_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 font-mono text-slate-700">
                                {{ $doc->document_number ?: '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-600 font-mono">
                                {{ $doc->issue_date ? $doc->issue_date->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-5 py-3.5">
                                @if($doc->expiry_date)
                                    @php
                                        $diffDays = (int) round(now()->startOfDay()->diffInDays($doc->expiry_date->startOfDay(), false));
                                    @endphp
                                    <span class="inline-block px-2.5 py-1 rounded-md text-[10px] font-bold font-mono {{ $diffDays < 0 ? 'bg-rose-100 text-rose-700 border border-rose-200' : ($diffDays <= 30 ? 'bg-[#FF7A3D]/20 text-[#FF7A3D] border border-[#FF7A3D]/30' : 'bg-emerald-50 text-emerald-800 border border-emerald-200') }}">
                                        {{ $doc->expiry_date->format('d M Y') }}
                                        ({{ $diffDays < 0 ? 'Telat ' . abs($diffDays) . ' hari' : ($diffDays == 0 ? 'Hari Ini' : $diffDays . ' hari lagi') }})
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">Tidak diset</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 font-bold text-black font-mono">
                                Rp {{ number_format($doc->cost, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @php
                                        $isTaxDoc = in_array($doc->document_type, ['stnk', 'pajak_tahunan', 'plat_5_tahunan']);
                                        $needsPayment = $doc->expiry_date && (int) round(now()->startOfDay()->diffInDays($doc->expiry_date->startOfDay(), false)) <= 30;
                                    @endphp

                                    @if($isTaxDoc && $needsPayment)
                                        <!-- Tombol Bayar Pajak Hanya Muncul jika Jatuh Tempo <= 30 hari atau Telat -->
                                        <button type="button" onclick="openPayTaxModal({{ $doc->id }}, '{{ $doc->type_label }}', '{{ $doc->vehicle ? $doc->vehicle->plate_number : '' }}', '{{ $doc->document_number }}', '{{ $doc->expiry_date ? $doc->expiry_date->format('Y-m-d') : '' }}', '{{ $doc->cost }}')" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-2xs transition">
                                            <i data-lucide="credit-card" class="w-3.5 h-3.5"></i> Bayar Pajak
                                        </button>
                                    @elseif($isTaxDoc && !$needsPayment && $doc->expiry_date)
                                        <!-- Jika masa berlaku masih panjang / sudah dibayar -->
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[10px]">
                                            <i data-lucide="check-circle" class="w-3 h-3 text-emerald-600"></i> Lunas / Aktif
                                        </span>
                                    @endif

                                    @if($doc->file_path)
                                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="p-1.5 text-[#015471] hover:bg-[#015471]/10 rounded-lg transition" title="Lihat Lampiran">
                                            <i data-lucide="paperclip" class="w-4 h-4"></i>
                                        </a>
                                    @endif

                                    <form id="delete-doc-{{ $doc->id }}" action="{{ route('documents.destroy', $doc) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('delete-doc-{{ $doc->id }}', 'dokumen ini')" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-slate-100 transition" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-slate-400">
                                <i data-lucide="folder-x" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                                <p class="text-xs">Tidak ada data dokumen atau pajak yang ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($documents->hasPages())
            <div class="px-5 py-3 border-t border-slate-200 bg-white">
                {{ $documents->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal Dialog Set Bayar Pajak / Perpanjangan -->
<div id="payTaxModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-lg w-full overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                    <i data-lucide="credit-card" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-black text-slate-900" id="modalTaxTitle">Pelunasan & Perpanjangan Pajak</h3>
                    <p class="text-[11px] text-slate-500" id="modalTaxSubtitle">Unit Kendaraan</p>
                </div>
            </div>
            <button type="button" onclick="closePayTaxModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="payTaxForm" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Biaya Pajak (Rp) <span class="text-[#FF7A3D]">*</span></label>
                    <div class="flex rounded-xl shadow-2xs">
                        <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-slate-300 bg-slate-50 text-slate-600 text-xs font-bold font-mono">
                            Rp
                        </span>
                        <input type="number" name="cost" id="modalTaxCost" required placeholder="Contoh: 350000" class="w-full text-xs font-mono font-bold px-3 py-2 rounded-r-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Registrasi / NTPN</label>
                    <input type="text" name="document_number" id="modalTaxDocNumber" placeholder="Contoh: 092819/PKB/2026" class="w-full text-xs font-mono px-3 py-2 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Pembayaran <span class="text-[#FF7A3D]">*</span></label>
                    <input type="date" name="paid_date" id="modalTaxPaidDate" value="{{ date('Y-m-d') }}" required class="w-full text-xs font-mono px-3 py-2 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jatuh Tempo Berikutnya <span class="text-[#FF7A3D]">*</span></label>
                    <input type="date" name="next_expiry_date" id="modalTaxNextExpiry" required class="w-full text-xs font-mono font-bold px-3 py-2 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Unggah Struk / Bukti Bayar Pajak (PDF/Foto)</label>
                <input type="file" name="receipt_file" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs px-3 py-2 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" id="modalTaxNotes" rows="2" placeholder="Catatan loket samsat atau rincian denda jika ada..." class="w-full text-xs p-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900"></textarea>
            </div>

            <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-100 text-[11px] text-emerald-800">
                <i data-lucide="check-circle" class="w-3.5 h-3.5 inline mr-1 text-emerald-600"></i>
                Nominal pembayaran pajak ini akan <strong>otomatis tercatat di Buku Kas Pengeluaran</strong> dan tanggal jatuh tempo unit diperbarui.
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closePayTaxModal()" class="px-4 py-2 text-xs font-bold rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 text-xs font-bold rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition">
                    Simpan Pelunasan Pajak
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openPayTaxModal(id, label, plate, docNumber, currentExpiry, cost) {
    const modal = document.getElementById('payTaxModal');
    const form = document.getElementById('payTaxForm');
    form.action = `/documents/${id}/pay-tax`;
    
    document.getElementById('modalTaxTitle').innerText = `Bayar & Perpanjang ${label}`;
    document.getElementById('modalTaxSubtitle').innerText = `Unit Armada: ${plate}`;
    document.getElementById('modalTaxCost').value = cost > 0 ? cost : '';
    document.getElementById('modalTaxDocNumber').value = docNumber || '';
    
    // Set default next expiry date (+1 year if yearly, or +5 years if 5 year)
    let nextDate = new Date();
    if (currentExpiry) {
        nextDate = new Date(currentExpiry);
    }
    nextDate.setFullYear(nextDate.getFullYear() + 1);
    const yyyy = nextDate.getFullYear();
    const mm = String(nextDate.getMonth() + 1).padStart(2, '0');
    const dd = String(nextDate.getDate()).padStart(2, '0');
    document.getElementById('modalTaxNextExpiry').value = `${yyyy}-${mm}-${dd}`;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    if (window.lucide) { lucide.createIcons(); }
}

function closePayTaxModal() {
    const modal = document.getElementById('payTaxModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endpush
@endsection
