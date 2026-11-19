@extends('layouts.app')

@section('title', 'Integrasi API Accounting & Coretax')
@section('header_title', 'Integrasi API Accounting & Coretax')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2.5">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('generated_api_token'))
        <div class="p-5 rounded-2xl bg-amber-50 border-2 border-amber-300 text-amber-950 text-xs space-y-2">
            <strong class="font-black text-sm block">Simpan API Token Baru Anda Sekarang (Hanya Ditampilkan 1x):</strong>
            <code class="block p-3 rounded-xl bg-white border border-amber-200 font-mono text-xs select-all break-all">{{ session('generated_api_token') }}</code>
            <p class="text-[11px] text-amber-800">Gunakan header <code class="font-mono">Authorization: Bearer &lt;TOKEN&gt;</code> atau <code class="font-mono">X-API-KEY: &lt;TOKEN&gt;</code> dari sistem Accounting Anda.</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Create & Manage API Tokens -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
            <h2 class="text-sm font-black text-slate-900">Manajemen API Key (Koneksi Accounting)</h2>
            <form action="{{ route('integrations.tokens.store') }}" method="POST" class="flex gap-2">
                @csrf
                <input type="text" name="name" required placeholder="Nama Integrasi (mis. Jurnal Accounting Prod)" class="flex-1 text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-900">
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-[#015471] hover:bg-[#01435b] text-white text-xs font-bold shrink-0">Generate Token</button>
            </form>

            <div class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden text-xs">
                @forelse($tokens as $t)
                    <div class="p-3.5 flex items-center justify-between gap-3">
                        <div>
                            <div class="font-bold text-slate-900">{{ $t->name }}</div>
                            <div class="text-[11px] text-slate-500 font-mono">{{ $t->plain_prefix }} &bull; Terakhir dipakai: {{ $t->last_used_at ? $t->last_used_at->diffForHumans() : 'Belum pernah' }}</div>
                        </div>
                        <form action="{{ route('integrations.tokens.destroy', $t) }}" method="POST" onsubmit="return confirm('Cabut token ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 font-bold text-[11px] hover:bg-rose-100">Cabut</button>
                        </form>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400">Belum ada API Token dibuat.</div>
                @endforelse
            </div>
        </div>

        <!-- Endpoint Reference -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-3 text-xs">
            <h2 class="text-sm font-black text-slate-900">Daftar Endpoint REST API Accounting</h2>
            <p class="text-slate-500">Seluruh endpoint berikut mengembalikan JSON siap sinkronisasi ke jurnal sistem Accounting:</p>
            <div class="space-y-2 font-mono text-[11px]">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold mr-2">GET</span>
                    <span>{{ url('/api/v1/accounting/summary') }}</span>
                    <p class="font-sans text-slate-500 mt-1">Ringkasan total nilai perolehan, nilai buku, pendapatan, pengeluaran, & faktur pajak.</p>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold mr-2">GET</span>
                    <span>{{ url('/api/v1/accounting/incomes') }}</span>
                    <p class="font-sans text-slate-500 mt-1">Data tagihan sewa per nomor kontrak, termin (1/3/6 bln), status lunas, & pendapatan lain-lain.</p>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold mr-2">GET</span>
                    <span>{{ url('/api/v1/accounting/expenses') }}</span>
                    <p class="font-sans text-slate-500 mt-1">Data pengeluaran operasional (servis, pajak STNK, kendaraan pengganti, premi).</p>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold mr-2">GET</span>
                    <span>{{ url('/api/v1/accounting/depreciation') }}</span>
                    <p class="font-sans text-slate-500 mt-1">Data nilai faktur, nilai perolehan, penyusutan bulanan, akumulasi penyusutan, sisa nilai buku, & penjualan unit.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
