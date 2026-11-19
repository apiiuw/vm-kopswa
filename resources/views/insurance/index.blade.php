@extends('layouts.app')

@section('title', 'Asuransi & Klaim Kehilangan Kendaraan')
@section('page_title', 'Modul Asuransi & Laporan Klaim (TLO / Partial Loss)')

@section('content')
<div class="space-y-6 w-full bg-white text-black">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-black tracking-tight">Polis Asuransi & Klaim Kendaraan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Maintain data polis asuransi aktif (All Risk / TLO), masa berlaku polis, dan penanganan kasus klaim</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
            <a href="{{ route('exports.insurancePdf', ['tab' => $tab ?? 'policies']) }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold rounded-xl bg-white hover:bg-slate-50 text-slate-800 border-2 border-slate-200 shadow-2xs transition">
                <i data-lucide="file-text" class="w-4 h-4 text-[#FF7A3D]"></i> Ekspor PDF
            </a>
            <a href="{{ route('exports.insuranceExcel') }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold rounded-xl bg-white hover:bg-slate-50 text-slate-800 border-2 border-slate-200 shadow-2xs transition">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-[#015471]"></i> Ekspor Excel
            </a>
            <button type="button" onclick="document.getElementById('policyModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-xl bg-[#015471] hover:bg-[#01435b] text-white shadow-sm transition">
                <i data-lucide="shield-plus" class="w-4 h-4"></i> Tambah Polis Asuransi
            </button>
            <a href="{{ route('insurance.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-sm transition">
                <i data-lucide="plus" class="w-4 h-4"></i> Catat Kasus / Klaim Asuransi
            </a>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-2 text-xs font-bold">
        <a href="{{ route('insurance.index', ['tab' => 'policies']) }}" class="px-4 py-2 rounded-xl transition {{ ($tab ?? 'policies') === 'policies' ? 'bg-[#015471] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            Data Polis Asuransi ({{ $policyStats['total'] ?? 0 }})
        </a>
        <a href="{{ route('insurance.index', ['tab' => 'claims']) }}" class="px-4 py-2 rounded-xl transition {{ ($tab ?? 'policies') === 'claims' ? 'bg-[#FF7A3D] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            Klaim & Insiden ({{ $stats['total_claims'] }})
        </a>
    </div>

    @if(($tab ?? 'policies') === 'policies')
    <!-- Policy Stats & Table -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-sm">
            <span class="text-xs font-bold text-[#015471]">Total Polis Terdaftar</span>
            <span class="text-2xl font-black text-black block mt-1">{{ $policyStats['total'] ?? 0 }}</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-sm">
            <span class="text-xs font-bold text-emerald-700">Polis Aktif</span>
            <span class="text-2xl font-black text-emerald-700 block mt-1">{{ $policyStats['active'] ?? 0 }}</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-sm">
            <span class="text-xs font-bold text-rose-600">Segera Jatuh Tempo (&lt;45 Hari)</span>
            <span class="text-2xl font-black text-rose-600 block mt-1">{{ $policyStats['expiring_soon'] ?? 0 }}</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-sm">
            <span class="text-xs font-bold text-[#FF7A3D]">Total Nilai Premi</span>
            <span class="text-xl font-black text-[#FF7A3D] block mt-1 font-mono">Rp {{ number_format($policyStats['total_premium'] ?? 0, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="bg-white rounded-2xl border-2 border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase text-[10px] font-extrabold">
                    <tr>
                        <th class="px-5 py-3.5">No. Polis</th>
                        <th class="px-5 py-3.5">Perusahaan Asuransi</th>
                        <th class="px-5 py-3.5">Unit / Kontrak</th>
                        <th class="px-5 py-3.5">Pertanggungan</th>
                        <th class="px-5 py-3.5">Masa Berlaku</th>
                        <th class="px-5 py-3.5 text-right">Premi</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($policies ?? [] as $p)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-3.5 font-mono font-bold text-[#015471]">{{ $p->policy_number }}</td>
                            <td class="px-5 py-3.5 font-bold text-slate-900">{{ $p->insurance_company }}</td>
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-800">{{ $p->vehicle ? $p->vehicle->plate_number . ' — ' . $p->vehicle->model_name : 'Polis Kolektif / Fleet' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $p->vendor?->contract_number ?: $p->vendor?->name ?: '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5">{{ $p->coverage_label }}</td>
                            <td class="px-5 py-3.5 font-mono text-[11px]">{{ $p->start_date->format('d/m/Y') }} s.d. {{ $p->end_date->format('d/m/Y') }}</td>
                            <td class="px-5 py-3.5 text-right font-mono font-bold">Rp {{ number_format($p->premium_amount, 0, ',', '.') }}</td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $p->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                    {{ strtoupper($p->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <form action="{{ route('insurance.policies.destroy', $p) }}" method="POST" class="inline" onsubmit="return confirm('Hapus polis ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-slate-400">Belum ada data polis asuransi kendaraan. Klik "Tambah Polis Asuransi" untuk mencatat polis baru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @else

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-sm">
            <span class="text-xs font-bold text-[#015471]">Total Kasus / Klaim</span>
            <span class="text-2xl font-black text-black block mt-1">{{ $stats['total_claims'] }}</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-sm">
            <span class="text-xs font-bold text-[#FF7A3D]">Total Nilai Pengajuan Klaim</span>
            <span class="text-2xl font-black text-[#FF7A3D] block mt-1">Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-sm">
            <span class="text-xs font-bold text-[#015471]">Klaim Dicairkan</span>
            <span class="text-2xl font-black text-[#015471] block mt-1">Rp {{ number_format($stats['settled_amount'], 0, ',', '.') }}</span>
        </div>
        <div class="bg-white p-4 rounded-2xl border-2 border-slate-100 shadow-sm">
            <span class="text-xs font-bold text-amber-700">Sedang Diproses</span>
            <span class="text-2xl font-black text-amber-700 block mt-1">{{ $stats['pending_count'] }} Kasus</span>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border-2 border-slate-100 shadow-sm overflow-hidden space-y-0">
        
        <!-- Table Control Toolbar (Search Info & Per Page Dropdown) -->
        <div class="px-6 py-4 bg-white border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-[#015471]/10 text-[#015471] flex items-center justify-center font-bold text-xs">
                    <i data-lucide="shield-alert" class="w-4 h-4"></i>
                </span>
                <div>
                    <h3 class="text-xs font-black text-black">Daftar Berkas Klaim Asuransi & Insiden</h3>
                    <p class="text-[11px] text-slate-400">Total ditemukan <strong class="text-[#015471]">{{ $claims->total() }} kasus</strong> klaim</p>
                </div>
            </div>

            <!-- Per-Page Controls -->
            <div class="flex items-center gap-3">
                <form id="perPageForm" action="{{ route('insurance.index') }}" method="GET" class="flex items-center gap-2">
                    <label for="per_page_select" class="text-xs font-bold text-slate-600">Tampilkan:</label>
                    <select id="per_page_select" name="per_page" onchange="document.getElementById('perPageForm').submit()" class="text-xs font-bold px-3 py-1.5 rounded-xl border border-slate-300 focus:outline-[#015471] bg-white text-black cursor-pointer shadow-2xs">
                        <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10 Baris</option>
                        <option value="15" {{ ($perPage ?? 10) == 15 ? 'selected' : '' }}>15 Baris</option>
                        <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25 Baris</option>
                        <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50 Baris</option>
                        <option value="100" {{ ($perPage ?? 10) == 100 ? 'selected' : '' }}>100 Baris</option>
                    </select>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#e6f0f3] border-b border-slate-200 text-[#015471] font-bold uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-5 py-3.5">Tanggal Kejadian</th>
                        <th class="px-5 py-3.5">Plat Motor</th>
                        <th class="px-5 py-3.5">Jenis Klaim</th>
                        <th class="px-5 py-3.5">Surat Laporan Polisi & Blokir</th>
                        <th class="px-5 py-3.5">Pengemudi / Pemegang</th>
                        <th class="px-5 py-3.5">Nilai Klaim (Rp)</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-black">
                    @forelse($claims as $c)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3.5 font-medium text-slate-600 whitespace-nowrap">
                                {{ $c->incident_date->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3.5 font-bold text-black">
                                <a href="{{ route('vehicles.show', $c->vehicle) }}" class="hover:text-[#FF7A3D]">
                                    {{ $c->vehicle ? $c->vehicle->plate_number : '-' }}
                                </a>
                                <span class="block text-[10px] text-slate-500">{{ $c->vehicle && $c->vehicle->location ? $c->vehicle->location->name : '' }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-[#FF7A3D]/20 text-[#FF7A3D] uppercase">
                                    {{ str_replace('_', ' ', $c->claim_type) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-bold text-black block text-[11px]">{{ $c->police_report_number ?: 'LP: -' }}</span>
                                <span class="text-[10px] text-[#015471] font-mono font-bold">{{ $c->polda_blocking_number ?: 'Blokir: -' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-black">
                                {{ $c->driver_name ?: '-' }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-bold text-black block">Rp {{ number_format($c->claim_amount, 0, ',', '.') }}</span>
                                @if($c->settlement_amount > 0)
                                    <span class="text-[10px] text-[#015471] font-bold block">Cair: Rp {{ number_format($c->settlement_amount, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#e6f0f3] text-[#015471] uppercase">
                                    {{ $c->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                <form id="delete-claim-{{ $c->id }}" action="{{ route('insurance.destroy', $c) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete('delete-claim-{{ $c->id }}', 'catatan klaim ini')" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-slate-100 transition" title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-400">
                                Belum ada catatan klaim asuransi atau kehilangan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($claims->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-white">
                {{ $claims->links() }}
            </div>
        @else
            <div class="px-6 py-3 border-t border-slate-100 bg-white text-xs text-slate-500 text-right">
                Menampilkan seluruh <strong class="text-black">{{ $claims->total() }}</strong> berkas klaim
            </div>
        @endif
    </div>
    @endif

</div>

<!-- Modal Tambah Polis Asuransi -->
<div id="policyModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
            <h3 class="font-bold text-slate-900 text-sm">Tambah Data Polis Asuransi Kendaraan</h3>
            <button type="button" onclick="document.getElementById('policyModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form action="{{ route('insurance.policies.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Polis *</label>
                    <input type="text" name="policy_number" required placeholder="POL-ASW-2026-001" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 font-mono text-slate-900">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Perusahaan Asuransi *</label>
                    <input type="text" name="insurance_company" required placeholder="Asuransi Jasa Indonesia / Sinar Mas" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-slate-900">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Unit Kendaraan (Opsional jika Polis Fleet)</label>
                    <select name="vehicle_id" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900">
                        <option value="">-- Polis Kolektif / Semua Unit Kontrak --</option>
                        @foreach($vehicles as $veh)
                            <option value="{{ $veh->id }}">{{ $veh->plate_number }} — {{ $veh->model_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jenis Pertanggungan *</label>
                    <select name="coverage_type" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900">
                        <option value="all_risk">All Risk / Comprehensive</option>
                        <option value="tlo">TLO (Total Loss Only)</option>
                        <option value="kombinasi">Kombinasi</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal Mulai Polis *</label>
                    <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-slate-900">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal Jatuh Tempo Polis *</label>
                    <input type="date" name="end_date" value="{{ date('Y-m-d', strtotime('+1 year')) }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-slate-900">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Premi Polis (Rp) *</label>
                    <input type="number" name="premium_amount" value="0" min="0" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 font-mono text-slate-900">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nilai Pertanggungan (Rp)</label>
                    <input type="number" name="sum_insured" value="0" min="0" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 font-mono text-slate-900">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Status Polis</label>
                    <select name="status" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-900">
                        <option value="active">Active</option>
                        <option value="expired">Expired</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Upload File Polis (PDF/JPG)</label>
                <input type="file" name="policy_file" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-slate-700">
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('policyModal').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 font-bold text-slate-700">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-[#015471] text-white font-bold">Simpan Polis</button>
            </div>
        </form>
    </div>
</div>
@endsection
