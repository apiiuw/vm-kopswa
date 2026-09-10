@extends('layouts.app')

@section('title', 'Tindak Lanjut Tiket Pengajuan Klien')
@section('page_title', 'Detail Permohonan & Tanggapan Admin')

@section('content')
@php
    $files = $clientRequest->proof_files ?? [];
    
    // Separate vehicle angle photos, complaint photos, and general documents
    $vehicleAnglePhotos = [];
    $complaintPhotos = [];
    $generalDocs = [];

    foreach ($files as $f) {
        $name = $f['name'] ?? '';
        if (str_contains($name, 'Foto Tampak Depan') || str_contains($name, 'Tampak Depan')) {
            $vehicleAnglePhotos['front'] = $f;
        } elseif (str_contains($name, 'Foto Tampak Samping Kanan') || str_contains($name, 'Samping Kanan')) {
            $vehicleAnglePhotos['right'] = $f;
        } elseif (str_contains($name, 'Foto Tampak Samping Kiri') || str_contains($name, 'Samping Kiri')) {
            $vehicleAnglePhotos['left'] = $f;
        } elseif (str_contains($name, 'Foto Tampak Belakang') || str_contains($name, 'Tampak Belakang')) {
            $vehicleAnglePhotos['behind'] = $f;
        } elseif (str_starts_with($name, 'Foto Keluhan #') || isset($f['complaint_index'])) {
            $complaintPhotos[] = $f;
        } else {
            // General supporting documents or additional photos (from proof_files)
            $generalDocs[] = $f;
        }
    }

    // Extract metadata blocks and complaints from description (handling both \r\n and \n)
    $normalizedDesc = str_replace("\r\n", "\n", $clientRequest->description ?? '');
    $descLines = explode("\n", $normalizedDesc);

    $structuredData = [
        'pic_name' => '',
        'pic_phone' => '',
        'operational_status' => '',
        'unit_position' => '',
        'physical_notes' => '',
        'workshop_phone' => '',
        'mechanic_name' => '',
        'mechanic_phone' => '',
    ];

    $complaintBlocks = [];
    $cleanDescLines = [];
    $currentComplaint = null;

    foreach ($descLines as $line) {
        $trimmed = trim($line);
        if (empty($trimmed)) continue;

        if (str_starts_with($trimmed, '[PIC Pengaju:')) {
            $content = trim($trimmed, '[]');
            if (preg_match('/PIC Pengaju:\s*([^-]+?)(?:\s*-\s*Telp:\s*(.*))?$/i', $content, $pm)) {
                $structuredData['pic_name'] = trim($pm[1] ?? '');
                $structuredData['pic_phone'] = trim($pm[2] ?? '');
            }
        } elseif (str_starts_with($trimmed, '[Kondisi Operasional:') || str_contains($trimmed, 'Kondisi Operasional:')) {
            $content = trim($trimmed, '[]');
            $parts = explode('|', $content);
            foreach ($parts as $part) {
                $partTrim = trim($part);
                if (str_starts_with($partTrim, 'Kondisi Operasional:')) {
                    $structuredData['operational_status'] = trim(substr($partTrim, 20));
                } elseif (str_starts_with($partTrim, 'Posisi Unit:')) {
                    $structuredData['unit_position'] = trim(substr($partTrim, 12));
                } elseif (str_starts_with($partTrim, 'Catatan Fisik:')) {
                    $structuredData['physical_notes'] = trim(substr($partTrim, 14));
                }
            }
        } elseif (str_starts_with($trimmed, '[Telp Bengkel:') || str_contains($trimmed, 'Telp Bengkel:') || str_contains($trimmed, 'Mekanik:')) {
            $content = trim($trimmed, '[]');
            $parts = explode('|', $content);
            foreach ($parts as $part) {
                $partTrim = trim($part);
                if (str_starts_with($partTrim, 'Telp Bengkel:')) {
                    $structuredData['workshop_phone'] = trim(substr($partTrim, 13));
                } elseif (str_starts_with($partTrim, 'Mekanik:')) {
                    $mContent = trim(substr($partTrim, 8));
                    if (preg_match('/^(.*?)\s*\(Telp:\s*(.*?)\)$/i', $mContent, $mm)) {
                        $structuredData['mechanic_name'] = trim($mm[1]);
                        $structuredData['mechanic_phone'] = trim($mm[2]);
                    } else {
                        $structuredData['mechanic_name'] = $mContent;
                    }
                }
            }
        } elseif (preg_match('/^\[Keluhan #(\d+): (.*?)\]$/', $trimmed, $m)) {
            if ($currentComplaint) {
                $complaintBlocks[] = $currentComplaint;
            }
            $currentComplaint = [
                'num' => $m[1],
                'category' => $m[2],
                'title' => '',
                'cost' => '',
                'desc' => '',
            ];
        } elseif ($currentComplaint && str_starts_with($trimmed, 'Judul: ')) {
            $titleLine = substr($trimmed, 7);
            if (preg_match('/^(.*?) \(Estimasi: (.*?)\)$/', $titleLine, $cm)) {
                $currentComplaint['title'] = $cm[1];
                $currentComplaint['cost'] = $cm[2];
            } else {
                $currentComplaint['title'] = $titleLine;
            }
        } elseif ($currentComplaint && str_starts_with($trimmed, 'Uraian: ')) {
            $currentComplaint['desc'] = substr($trimmed, 8);
        } elseif ($currentComplaint) {
            $currentComplaint['desc'] .= (!empty($currentComplaint['desc']) ? "\n" : '') . $trimmed;
        } else {
            $cleanDescLines[] = $trimmed;
        }
    }

    if ($currentComplaint) {
        $complaintBlocks[] = $currentComplaint;
    }

    // Fallback if complaintBlocks is empty for perbaikan_servis
    if (empty($complaintBlocks) && $clientRequest->request_type === 'perbaikan_servis') {
        $complaintBlocks[] = [
            'num' => '1',
            'category' => 'Pengajuan Perbaikan / Servis',
            'title' => $clientRequest->title,
            'cost' => $clientRequest->estimated_cost > 0 ? 'Rp ' . number_format($clientRequest->estimated_cost, 0, ',', '.') : '',
            'desc' => implode("\n", $cleanDescLines) ?: $clientRequest->description,
        ];
    }

    $remainingDescription = implode("\n", $cleanDescLines);
@endphp

<div class="w-full space-y-6">

    <!-- Top Navigation & Header Bar -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.client-requests.index') }}" class="w-10 h-10 rounded-2xl border border-slate-200 hover:border-[#015471] bg-slate-50 hover:bg-white text-slate-600 hover:text-[#015471] flex items-center justify-center transition shadow-2xs">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <h1 class="text-lg font-black text-slate-900">Tiket Permohonan #REQ-{{ str_pad($clientRequest->id, 4, '0', STR_PAD_LEFT) }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#015471]/10 text-[#015471]">
                        {{ $clientRequest->vendor ? $clientRequest->vendor->name : 'Klien Mitra' }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-black border {{ $clientRequest->status_badge['bg'] }}">
                        <span class="w-2 h-2 rounded-full {{ $clientRequest->status_badge['dot'] }}"></span>
                        {{ $clientRequest->status_badge['label'] }}
                    </span>
                </div>
                <p class="text-xs text-slate-500">
                    Diajukan oleh <strong class="text-slate-800">{{ $clientRequest->user ? $clientRequest->user->name : '-' }}</strong> &bull; {{ $clientRequest->created_at->isoFormat('dddd, D MMMM Y - HH:mm') }} WIB
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Tombol Cetak PDF -->
            <a href="{{ route('exports.clientRequestPdf', $clientRequest) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 shadow-2xs transition">
                <i data-lucide="file-text" class="w-3.5 h-3.5 text-rose-600"></i>
                <span>Cetak PDF</span>
            </a>

            <!-- Tombol Cetak Word -->
            <a href="{{ route('exports.clientRequestDoc', $clientRequest) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 shadow-2xs transition">
                <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5 text-blue-600"></i>
                <span>Cetak Word</span>
            </a>
        </div>
    </div>

    <!-- Master Layout Split: Left (Steps Details 1-4) & Right (Admin Actions & Vehicle Info) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- LEFT COLUMN: Rincian Lengkap Sesuai Tahapan (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">

            <!-- TAHAP 1: Kategori & Identitas Armada Motor -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-xl bg-[#015471] text-white text-xs font-black flex items-center justify-center shadow-2xs">1</span>
                        <div>
                            <h2 class="text-xs font-black uppercase tracking-wider text-slate-900">Tahap 1: Kategori & Armada Kendaraan</h2>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-[#015471] bg-[#015471]/10 px-2.5 py-1 rounded-md">Identitas Armada</span>
                </div>

                <div class="p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-[#015471]/5 via-slate-50 to-[#FF7A3D]/5 border border-[#015471]/15 gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-[#015471] text-white flex items-center justify-center shrink-0 shadow-2xs">
                                <i data-lucide="{{ ($clientRequest->vehicle && $clientRequest->vehicle->type === 'mobil') ? 'car' : 'bike' }}" class="w-6 h-6 text-[#FF7A3D]"></i>
                            </div>
                            <div>
                                <span class="text-[11px] text-slate-400 font-semibold block">Armada Unit:</span>
                                <h3 class="text-base font-black text-slate-900 font-mono tracking-tight">
                                    {{ $clientRequest->vehicle ? $clientRequest->vehicle->plate_number : '-' }}
                                </h3>
                                <p class="text-xs font-bold text-slate-700">
                                    @php
                                        $vBrand = $clientRequest->vehicle ? $clientRequest->vehicle->brand : '';
                                        $vModel = $clientRequest->vehicle ? $clientRequest->vehicle->model_name : '';
                                        $displayModel = ($vBrand && str_starts_with(strtolower($vModel), strtolower($vBrand))) ? $vModel : trim($vBrand . ' ' . $vModel);
                                    @endphp
                                    {{ $displayModel ?: '-' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-col sm:items-end gap-1.5 text-xs">
                            <div>
                                <span class="text-slate-400 block text-[10px] font-bold uppercase sm:text-right">Kategori Pengajuan:</span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-black shadow-2xs {{ 
                                    $clientRequest->request_type === 'perbaikan_servis' ? 'bg-[#015471] text-white' : 
                                    ($clientRequest->request_type === 'unit_pengganti' ? 'bg-amber-500 text-white' : 'bg-rose-600 text-white')
                                }}">
                                    <i data-lucide="{{ $clientRequest->request_type === 'perbaikan_servis' ? 'wrench' : ($clientRequest->request_type === 'unit_pengganti' ? 'bike' : 'shield-alert') }}" class="w-3.5 h-3.5"></i>
                                    {{ $clientRequest->request_type_label }}
                                </span>
                            </div>

                            <div class="text-left sm:text-right text-[11px] text-slate-500 pt-0.5">
                                <span>Lokasi: <strong>{{ $clientRequest->vehicle && $clientRequest->vehicle->location ? $clientRequest->vehicle->location->name : '-' }}</strong></span>
                                <span class="mx-1">&bull;</span>
                                <span class="font-mono font-bold text-[#015471]">Status: {{ strtoupper($clientRequest->vehicle ? $clientRequest->vehicle->status : '-') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAHAP 2: Rincian Kerusakan / Permohonan (Khusus Sesuai Kategori) -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-xl bg-[#015471] text-white text-xs font-black flex items-center justify-center shadow-2xs">2</span>
                        <div>
                            <h2 class="text-xs font-black uppercase tracking-wider text-slate-900">
                                @if($clientRequest->request_type === 'perbaikan_servis')
                                    Tahap 2: Daftar Rincian Keluhan & Foto Kerusakan
                                @elseif($clientRequest->request_type === 'unit_pengganti')
                                    Tahap 2: Rincian Kebutuhan Unit Pengganti
                                @else
                                    Tahap 2: Kronologi Kejadian & Laporan Kehilangan (TLO)
                                @endif
                            </h2>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-[#FF7A3D] bg-[#FF7A3D]/10 px-2.5 py-1 rounded-md">
                        {{ count($complaintBlocks) > 0 ? count($complaintBlocks) . ' Keluhan' : 'Detail Pengajuan' }}
                    </span>
                </div>

                <div class="p-6 space-y-5">
                    <!-- Judul Utama Pengajuan -->
                    <div>
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Judul Permohonan:</span>
                        <h3 class="text-base font-black text-slate-900 leading-snug">{{ $clientRequest->title }}</h3>
                    </div>

                    @if($clientRequest->request_type === 'perbaikan_servis')
                        <!-- TAMPILAN KHUSUS PERBAIKAN: KARTU RINCIAN KELUHAN & FOTO PER ITEM -->
                        @if(count($complaintBlocks) > 0)
                            <div class="space-y-4">
                                @foreach($complaintBlocks as $idx => $complaint)
                                    @php
                                        // Match photos specifically tagged with this complaint index or label
                                        $thisComplaintPhotos = array_filter($complaintPhotos, function($p) use ($idx, $complaint) {
                                             $pName = $p['name'] ?? '';
                                             return (isset($p['complaint_index']) && $p['complaint_index'] === $idx) || 
                                                    str_contains($pName, "Foto Keluhan #{$complaint['num']}");
                                        });

                                        // Fallback if only 1 complaint and photos exist in complaintPhotos without specific tags
                                        if (empty($thisComplaintPhotos) && count($complaintBlocks) === 1 && count($complaintPhotos) > 0) {
                                            $thisComplaintPhotos = $complaintPhotos;
                                        }
                                    @endphp
                                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-50/80 border-2 border-slate-200/90 space-y-4 hover:border-[#015471]/40 transition shadow-2xs">
                                        <div class="flex flex-wrap items-center justify-between border-b border-slate-200/80 pb-3 gap-2">
                                            <div class="flex items-center gap-2.5">
                                                <span class="w-7 h-7 rounded-xl bg-[#015471] text-white text-xs font-black flex items-center justify-center shadow-2xs">
                                                    {{ $complaint['num'] ?? ($idx + 1) }}
                                                </span>
                                                <div>
                                                    <span class="text-xs font-black text-slate-900 block">{{ $complaint['title'] ?: 'Keluhan #' . ($idx + 1) }}</span>
                                                    <span class="text-[10px] font-bold text-[#015471] bg-[#015471]/10 px-2 py-0.5 rounded">
                                                        {{ $complaint['category'] ?? 'Keluhan Kerusakan' }}
                                                    </span>
                                                </div>
                                            </div>
                                            @if(!empty($complaint['cost']))
                                                <span class="text-xs font-black font-mono text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200 shadow-2xs">
                                                    Estimasi: {{ $complaint['cost'] }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="space-y-2">
                                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Uraian & Gejala Kerusakan:</span>
                                            <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-line bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
                                                {{ $complaint['desc'] }}
                                            </div>
                                        </div>

                                        <!-- Foto Kerusakan Khusus Item Keluhan Ini -->
                                        <div class="pt-2 border-t border-dashed border-slate-200 space-y-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs font-black text-slate-800 flex items-center gap-1.5">
                                                    <i data-lucide="camera" class="w-4 h-4 text-[#FF7A3D]"></i>
                                                    Foto Bukti Kerusakan (Keluhan #{{ $complaint['num'] ?? ($idx + 1) }}):
                                                </span>
                                                <span class="text-[10px] font-bold text-slate-500">
                                                    {{ count($thisComplaintPhotos) > 0 ? count($thisComplaintPhotos) . ' Foto Terlampir' : 'Tidak Ada Foto' }}
                                                </span>
                                            </div>

                                            @if(count($thisComplaintPhotos) > 0)
                                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 pt-1">
                                                    @foreach($thisComplaintPhotos as $cp)
                                                        <a href="{{ asset('storage/' . $cp['path']) }}" target="_blank" class="group relative rounded-2xl overflow-hidden border border-slate-200 hover:border-[#015471] transition bg-white block shadow-2xs">
                                                            <img src="{{ asset('storage/' . $cp['path']) }}" alt="{{ $cp['name'] }}" class="w-full h-28 object-cover group-hover:scale-105 transition duration-200">
                                                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition flex items-end p-2.5">
                                                                <span class="text-[10px] text-white font-bold truncate">Perbesar Foto &rarr;</span>
                                                            </div>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="p-3.5 rounded-xl bg-slate-100/70 border border-dashed border-slate-300 text-center text-xs text-slate-400 italic">
                                                    Tidak ada foto kerusakan khusus yang diunggah untuk kartu keluhan ini.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-700 leading-relaxed whitespace-pre-line">
                                {{ $remainingDescription ?: $clientRequest->description }}
                            </div>
                        @endif

                    @elseif($clientRequest->request_type === 'unit_pengganti')
                        <!-- TAMPILAN KHUSUS UNIT PENGGANTI -->
                        <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200 space-y-3 text-xs text-amber-950">
                            <div class="flex items-center gap-2 font-bold text-amber-900">
                                <i data-lucide="info" class="w-4 h-4 text-amber-600"></i>
                                <span>Rincian Permohonan Unit Pengganti Sementara:</span>
                            </div>
                            <div class="bg-white p-3.5 rounded-xl border border-amber-200/80 leading-relaxed whitespace-pre-line text-slate-800">
                                {{ $remainingDescription ?: $clientRequest->description }}
                            </div>
                        </div>

                    @else
                        <!-- TAMPILAN KHUSUS LAPORAN KEHILANGAN (TLO) -->
                        <div class="p-4 rounded-2xl bg-rose-50/70 border border-rose-200 space-y-3 text-xs text-rose-950">
                            <div class="flex items-center gap-2 font-bold text-rose-900">
                                <i data-lucide="shield-alert" class="w-4 h-4 text-rose-600"></i>
                                <span>Kronologi Lengkap Insiden Kehilangan (TLO):</span>
                            </div>
                            <div class="bg-white p-3.5 rounded-xl border border-rose-200/80 leading-relaxed whitespace-pre-line text-slate-800">
                                {{ $remainingDescription ?: $clientRequest->description }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- TAHAP 3: Informasi Lapangan, PIC & Bengkel -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-xl bg-[#015471] text-white text-xs font-black flex items-center justify-center shadow-2xs">3</span>
                        <div>
                            <h2 class="text-xs font-black uppercase tracking-wider text-slate-900">Tahap 3: Informasi Unit Motor, PIC & Bengkel</h2>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-[#015471] bg-[#015471]/10 px-2.5 py-1 rounded-md">Data Lapangan</span>
                </div>

                <div class="p-6 space-y-5 text-xs">
                    
                    <!-- Sub-Card 1: Kondisi Motor & Odometer Terkini -->
                    <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200 space-y-3.5 shadow-2xs">
                        <div class="flex items-center justify-between border-b border-slate-200/70 pb-2.5">
                            <div class="flex items-center gap-2 text-[#015471]">
                                <i data-lucide="gauge" class="w-4 h-4 text-[#FF7A3D]"></i>
                                <span class="font-black text-xs">Kondisi Motor & Odometer Terkini</span>
                            </div>
                            <span class="text-[10px] font-mono font-bold text-slate-500 bg-white px-2 py-0.5 rounded border border-slate-200">
                                Speedometer Fisik
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <!-- Speedometer KM -->
                            <div class="p-3 bg-white rounded-xl border border-slate-200/80">
                                <span class="text-slate-400 block text-[10px] font-bold uppercase">Speedometer Terkini:</span>
                                <div class="flex items-baseline gap-1.5 mt-0.5">
                                    <span class="text-lg font-black font-mono text-[#015471]">
                                        {{ $clientRequest->odometer_km ? number_format($clientRequest->odometer_km, 0, ',', '.') : '-' }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-400 font-mono">KM</span>
                                </div>
                            </div>

                            <!-- Status Kelayakan -->
                            <div class="p-3 bg-white rounded-xl border border-slate-200/80">
                                <span class="text-slate-400 block text-[10px] font-bold uppercase">Status Operasional:</span>
                                <span class="font-bold text-slate-800 block mt-1">
                                    {{ $structuredData['operational_status'] ?: 'Masih Bisa Jalan Normal' }}
                                </span>
                            </div>

                            <!-- Posisi / Lokasi Terkini -->
                            <div class="p-3 bg-white rounded-xl border border-slate-200/80">
                                <span class="text-slate-400 block text-[10px] font-bold uppercase">Posisi / Lokasi Unit:</span>
                                <span class="font-bold text-slate-800 block mt-1">
                                    {{ $structuredData['unit_position'] ?: ($clientRequest->vehicle && $clientRequest->vehicle->location ? $clientRequest->vehicle->location->name : 'Lokasi Operasional') }}
                                </span>
                            </div>
                        </div>

                        @if(!empty($structuredData['physical_notes']))
                            <div class="p-3 rounded-xl bg-amber-50/60 border border-amber-200/80 text-[11px] text-amber-900 flex items-start gap-2">
                                <i data-lucide="alert-circle" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"></i>
                                <div>
                                    <span class="font-bold">Catatan Fisik Tambahan:</span>
                                    <span>{{ $structuredData['physical_notes'] }}</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Sub-Card 2: Pengemudi Terdaftar & PIC Pengaju Lapangan -->
                    <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200 space-y-3.5 shadow-2xs">
                        <div class="flex items-center justify-between border-b border-slate-200/70 pb-2.5">
                            <div class="flex items-center gap-2 text-[#015471]">
                                <i data-lucide="users" class="w-4 h-4 text-[#FF7A3D]"></i>
                                <span class="font-black text-xs">Data Kontak Pengemudi & PIC Lapangan</span>
                            </div>
                            <span class="text-[10px] font-bold text-slate-500 bg-white px-2 py-0.5 rounded border border-slate-200">Kontak Resmi</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <!-- Pengemudi Terdaftar -->
                            <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400 text-[10px] font-bold uppercase">Pengemudi / Pemegang Unit:</span>
                                    <span class="text-[9px] font-bold text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">Tercatat</span>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 text-sm leading-snug">
                                        {{ $clientRequest->driver_name ?: '-' }}
                                    </div>
                                    @if(!empty($clientRequest->driver_phone))
                                        <div class="text-xs text-slate-500 font-mono flex items-center gap-1.5 mt-1">
                                            <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i>
                                            <span>{{ $clientRequest->driver_phone }}</span>
                                        </div>
                                    @else
                                        <div class="text-[11px] text-slate-400 italic mt-0.5">Tidak ada no. telepon</div>
                                    @endif
                                </div>
                            </div>

                            <!-- PIC Pengaju Lapangan -->
                            <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400 text-[10px] font-bold uppercase">PIC Penanggung Jawab:</span>
                                    <span class="text-[9px] font-bold text-[#015471] bg-[#015471]/10 px-1.5 py-0.5 rounded">Pengaju</span>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 text-sm leading-snug">
                                        {{ $structuredData['pic_name'] ?: ($clientRequest->user ? $clientRequest->user->name : '-') }}
                                    </div>
                                    @if(!empty($structuredData['pic_phone']))
                                        <div class="text-xs text-slate-500 font-mono flex items-center gap-1.5 mt-1">
                                            <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i>
                                            <span>{{ $structuredData['pic_phone'] }}</span>
                                        </div>
                                    @else
                                        <div class="text-[11px] text-slate-400 italic mt-0.5">Tidak ada no. telepon</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-Card 3: Data Bengkel Rujukan, Mekanik & Total Estimasi Biaya -->
                    @if($clientRequest->request_type === 'perbaikan_servis' || $clientRequest->workshop_name || $clientRequest->estimated_cost > 0)
                        <div class="p-5 rounded-2xl border-2 border-[#015471]/15 bg-gradient-to-r from-slate-50 to-[#015471]/5 space-y-3.5 shadow-2xs">
                            <div class="flex items-center justify-between border-b border-[#015471]/10 pb-2.5">
                                <div class="flex items-center gap-2 text-[#015471]">
                                    <i data-lucide="wrench" class="w-4 h-4 text-[#FF7A3D]"></i>
                                    <span class="font-black text-xs">Rujukan Bengkel, Mekanik & Estimasi Biaya</span>
                                </div>
                                <span class="text-[10px] font-bold text-slate-500 bg-white px-2 py-0.5 rounded border border-slate-200">Layanan Servis</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5">
                                <!-- Data Bengkel & Mekanik (7 Cols) -->
                                <div class="sm:col-span-7 p-3.5 bg-white rounded-xl border border-slate-200 space-y-3">
                                    <!-- Bengkel -->
                                    <div>
                                        <span class="text-slate-400 block text-[10px] font-bold uppercase mb-0.5">Nama Bengkel / AHASS:</span>
                                        <div class="font-bold text-slate-900 text-sm leading-snug">
                                            {{ $clientRequest->workshop_name ?: 'Bengkel Rekanan SUP' }}
                                        </div>
                                        @if(!empty($structuredData['workshop_phone']))
                                            <div class="text-xs text-slate-500 font-mono flex items-center gap-1.5 mt-1">
                                                <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i>
                                                <span>{{ $structuredData['workshop_phone'] }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Mekanik -->
                                    @if(!empty($structuredData['mechanic_name']))
                                        <div class="pt-2.5 border-t border-slate-100">
                                            <span class="text-slate-400 block text-[10px] font-bold uppercase mb-0.5">Mekanik / Teknisi:</span>
                                            <div class="font-bold text-slate-900 text-sm leading-snug">
                                                {{ $structuredData['mechanic_name'] }}
                                            </div>
                                            @if(!empty($structuredData['mechanic_phone']))
                                                <div class="text-xs text-slate-500 font-mono flex items-center gap-1.5 mt-1">
                                                    <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i>
                                                    <span>{{ $structuredData['mechanic_phone'] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <!-- Total Estimasi Biaya (5 Cols) -->
                                <div class="sm:col-span-5 p-4 bg-white rounded-xl border-2 border-emerald-200/80 flex flex-col justify-center items-center text-center shadow-2xs">
                                    <span class="text-slate-400 block text-[10px] font-bold uppercase mb-1">Total Akumulasi Biaya:</span>
                                    <span class="text-lg font-black font-mono text-emerald-700">
                                        Rp {{ number_format($clientRequest->estimated_cost ?: 0, 0, ',', '.') }}
                                    </span>
                                    <span class="text-[9px] text-slate-400 mt-1">Akumulasi seluruh keluhan</span>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            <!-- TAHAP 4: Foto Fisik 4 Sisi Kendaraan & Dokumen Lampiran -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-xl bg-[#015471] text-white text-xs font-black flex items-center justify-center shadow-2xs">4</span>
                        <div>
                            <h2 class="text-xs font-black uppercase tracking-wider text-slate-900">Tahap 4: Foto Fisik 4 Sudut & Dokumen Lampiran</h2>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-[#015471] bg-[#015471]/10 px-2.5 py-1 rounded-md">Verifikasi Fisik</span>
                </div>

                <div class="p-6 space-y-6">
                    <!-- 4 Sudut Foto Fisik Kendaraan -->
                    <div class="space-y-2.5">
                        <span class="text-xs font-black text-slate-800 block">Foto Fisik 4 Sisi Unit Kendaraan:</span>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <!-- 1. Tampak Depan -->
                            <div class="p-2 rounded-2xl border border-slate-200 bg-slate-50 flex flex-col justify-between">
                                <span class="text-[10px] font-black text-slate-700 block mb-1.5 text-center">1. Tampak Depan</span>
                                @if(isset($vehicleAnglePhotos['front']))
                                    <a href="{{ asset('storage/' . $vehicleAnglePhotos['front']['path']) }}" target="_blank" class="block rounded-xl overflow-hidden border border-slate-200 group relative">
                                        <img src="{{ asset('storage/' . $vehicleAnglePhotos['front']['path']) }}" class="w-full h-24 object-cover group-hover:scale-105 transition duration-200" alt="Depan">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-[10px] font-bold">Perbesar</div>
                                    </a>
                                @else
                                    <div class="w-full h-24 rounded-xl bg-slate-100 border border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-400 text-center p-2">
                                        <i data-lucide="image-off" class="w-5 h-5 mb-1"></i>
                                        <span class="text-[9px]">Belum Diunggah</span>
                                    </div>
                                @endif
                            </div>

                            <!-- 2. Samping Kanan -->
                            <div class="p-2 rounded-2xl border border-slate-200 bg-slate-50 flex flex-col justify-between">
                                <span class="text-[10px] font-black text-slate-700 block mb-1.5 text-center">2. Samping Kanan</span>
                                @if(isset($vehicleAnglePhotos['right']))
                                    <a href="{{ asset('storage/' . $vehicleAnglePhotos['right']['path']) }}" target="_blank" class="block rounded-xl overflow-hidden border border-slate-200 group relative">
                                        <img src="{{ asset('storage/' . $vehicleAnglePhotos['right']['path']) }}" class="w-full h-24 object-cover group-hover:scale-105 transition duration-200" alt="Kanan">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-[10px] font-bold">Perbesar</div>
                                    </a>
                                @else
                                    <div class="w-full h-24 rounded-xl bg-slate-100 border border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-400 text-center p-2">
                                        <i data-lucide="image-off" class="w-5 h-5 mb-1"></i>
                                        <span class="text-[9px]">Belum Diunggah</span>
                                    </div>
                                @endif
                            </div>

                            <!-- 3. Samping Kiri -->
                            <div class="p-2 rounded-2xl border border-slate-200 bg-slate-50 flex flex-col justify-between">
                                <span class="text-[10px] font-black text-slate-700 block mb-1.5 text-center">3. Samping Kiri</span>
                                @if(isset($vehicleAnglePhotos['left']))
                                    <a href="{{ asset('storage/' . $vehicleAnglePhotos['left']['path']) }}" target="_blank" class="block rounded-xl overflow-hidden border border-slate-200 group relative">
                                        <img src="{{ asset('storage/' . $vehicleAnglePhotos['left']['path']) }}" class="w-full h-24 object-cover group-hover:scale-105 transition duration-200" alt="Kiri">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-[10px] font-bold">Perbesar</div>
                                    </a>
                                @else
                                    <div class="w-full h-24 rounded-xl bg-slate-100 border border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-400 text-center p-2">
                                        <i data-lucide="image-off" class="w-5 h-5 mb-1"></i>
                                        <span class="text-[9px]">Belum Diunggah</span>
                                    </div>
                                @endif
                            </div>

                            <!-- 4. Tampak Belakang -->
                            <div class="p-2 rounded-2xl border border-slate-200 bg-slate-50 flex flex-col justify-between">
                                <span class="text-[10px] font-black text-slate-700 block mb-1.5 text-center">4. Tampak Belakang</span>
                                @if(isset($vehicleAnglePhotos['behind']))
                                    <a href="{{ asset('storage/' . $vehicleAnglePhotos['behind']['path']) }}" target="_blank" class="block rounded-xl overflow-hidden border border-slate-200 group relative">
                                        <img src="{{ asset('storage/' . $vehicleAnglePhotos['behind']['path']) }}" class="w-full h-24 object-cover group-hover:scale-105 transition duration-200" alt="Belakang">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-[10px] font-bold">Perbesar</div>
                                    </a>
                                @else
                                    <div class="w-full h-24 rounded-xl bg-slate-100 border border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-400 text-center p-2">
                                        <i data-lucide="image-off" class="w-5 h-5 mb-1"></i>
                                        <span class="text-[9px]">Belum Diunggah</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Dokumen & Foto Pendukung Lainnya (PDF/Foto/LP Polisi/SPK) -->
                    @if(count($generalDocs) > 0)
                        <div class="space-y-3 pt-4 border-t border-slate-100">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-black text-slate-800 flex items-center gap-1.5">
                                    <i data-lucide="paperclip" class="w-4 h-4 text-[#FF7A3D]"></i>
                                    Dokumen & Foto Bukti Lampiran Lainnya:
                                </span>
                                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">
                                    {{ count($generalDocs) }} Berkas
                                </span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach($generalDocs as $doc)
                                    @php
                                        $docName = $doc['name'] ?? 'Berkas Lampiran';
                                        $docPath = $doc['path'] ?? '';
                                        $ext = strtolower(pathinfo($docName, PATHINFO_EXTENSION));
                                        $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
                                    @endphp
                                    @if($isImg)
                                        <a href="{{ asset('storage/' . $docPath) }}" target="_blank" class="group relative rounded-2xl overflow-hidden border border-slate-200 hover:border-[#015471] bg-white transition shadow-2xs block">
                                            <img src="{{ asset('storage/' . $docPath) }}" alt="{{ $docName }}" class="w-full h-32 object-cover group-hover:scale-105 transition duration-200">
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition flex items-end p-2.5">
                                                <span class="text-[10px] text-white font-bold truncate">{{ $docName }} &rarr;</span>
                                            </div>
                                            <div class="p-2 bg-white border-t border-slate-100">
                                                <span class="text-[11px] font-bold text-slate-800 truncate block">{{ $docName }}</span>
                                                <span class="text-[9px] text-[#015471] font-semibold flex items-center gap-1">
                                                    <i data-lucide="image" class="w-3 h-3"></i> Foto Dokumen Lampiran
                                                </span>
                                            </div>
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/' . $docPath) }}" target="_blank" class="p-3 rounded-2xl border border-slate-200 hover:border-[#015471] bg-slate-50 hover:bg-white transition flex items-center gap-3 group shadow-2xs">
                                            <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 border border-red-200 flex items-center justify-center shrink-0">
                                                <i data-lucide="file-text" class="w-5 h-5"></i>
                                            </div>
                                            <div class="overflow-hidden flex-1 text-xs">
                                                <span class="font-bold text-slate-900 group-hover:text-[#015471] block truncate">{{ $docName }}</span>
                                                <span class="text-[10px] text-slate-400 font-semibold block mt-0.5">Format .{{ strtoupper($ext) }} &bull; Klik buka file &rarr;</span>
                                            </div>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: Respon Admin PT SUP & Ringkasan Status (5 Cols) -->
        <div class="lg:col-span-5 space-y-6 sticky top-6">

            <!-- Card 1: Form Respon & Tindak Lanjut Admin -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-[#FF7A3D] text-white text-xs font-black flex items-center justify-center shadow-2xs">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                        </span>
                        <div>
                            <h2 class="text-sm font-black text-slate-900">Tindak Lanjut & Respon Admin</h2>
                            <p class="text-[11px] text-slate-500">Perbarui status tiket & kirim tanggapan ke klien</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.client-requests.updateStatus', $clientRequest) }}" method="POST" class="p-6 space-y-4 text-xs">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">Status Penanganan Tiket <span class="text-[#FF7A3D]">*</span></label>
                        <select name="status" required class="w-full text-xs font-semibold px-4 py-3 rounded-2xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer shadow-2xs">
                            <option value="menunggu_review" {{ old('status', $clientRequest->status) === 'menunggu_review' ? 'selected' : '' }}>Menunggu Review</option>
                            <option value="diproses" {{ old('status', $clientRequest->status) === 'diproses' ? 'selected' : '' }}>Sedang Diproses (Koordinasi Bengkel / Tim)</option>
                            <option value="unit_dikirim" {{ old('status', $clientRequest->status) === 'unit_dikirim' ? 'selected' : '' }}>Unit Pengganti Dikirim ke Lokasi</option>
                            <option value="selesai" {{ old('status', $clientRequest->status) === 'selesai' ? 'selected' : '' }}>Selesai / Disetujui</option>
                            <option value="ditolak" {{ old('status', $clientRequest->status) === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">Catatan / Tanggapan untuk Klien</label>
                        <textarea name="admin_response" rows="4" placeholder="Tuliskan konfirmasi mekanik, nomor resi pengiriman unit pengganti, atau estimasi selesai servis..." class="w-full text-xs p-4 rounded-2xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 leading-relaxed shadow-2xs">{{ old('admin_response', $clientRequest->admin_response) }}</textarea>
                    </div>

                    <!-- Auto Sync Option to Maintenance Module -->
                    @if($clientRequest->request_type === 'perbaikan_servis' || $clientRequest->request_type === 'unit_pengganti')
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                            <label class="flex items-start gap-2.5 cursor-pointer">
                                <input type="checkbox" name="sync_to_maintenance" value="1" checked class="mt-0.5 rounded text-[#015471] focus:ring-[#015471]">
                                <div>
                                    <span class="font-bold text-slate-900 block">Otomatis Sinkron ke Buku Servis</span>
                                    <span class="text-[11px] text-slate-500">Jika status diset Selesai, catat otomatis ke riwayat Servis & Pemeliharaan dan pulihkan status armada jadi Aktif.</span>
                                </div>
                            </label>
                        </div>
                    @elseif($clientRequest->request_type === 'lapor_kehilangan')
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 space-y-2">
                            <label class="flex items-start gap-2.5 cursor-pointer">
                                <input type="checkbox" name="sync_to_insurance" value="1" checked class="mt-0.5 rounded text-rose-600 focus:ring-rose-500">
                                <div>
                                    <span class="font-bold text-rose-900 block">Otomatis Buat Klaim Asuransi TLO</span>
                                    <span class="text-[11px] text-rose-600">Catat otomatis ke daftar Klaim Asuransi Kehilangan PT SUP.</span>
                                </div>
                            </label>
                        </div>
                    @endif

                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 text-xs font-black rounded-2xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-md transition">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Simpan Status & Kirim Respon</span>
                    </button>
                </form>
            </div>

            <!-- Card 2: Status Log & Informasi Waktu -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xs p-6 space-y-3.5 text-xs">
                <div class="flex items-center gap-2 text-[#015471]">
                    <i data-lucide="clock" class="w-4 h-4 text-[#FF7A3D]"></i>
                    <h3 class="font-bold uppercase tracking-wider text-xs">Informasi & Waktu Pengajuan</h3>
                </div>

                <div class="space-y-2.5">
                    <div class="flex justify-between border-b border-slate-100 pb-2">
                        <span class="text-slate-400">Waktu Masuk Tiket:</span>
                        <span class="font-bold text-slate-900">{{ $clientRequest->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-100 pb-2">
                        <span class="text-slate-400">Terakhir Diperbarui:</span>
                        <span class="font-bold text-slate-900">{{ $clientRequest->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($clientRequest->resolved_at)
                        <div class="flex justify-between border-b border-slate-100 pb-2">
                            <span class="text-slate-400">Waktu Selesai:</span>
                            <span class="font-bold text-emerald-600">{{ $clientRequest->resolved_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-slate-400">Mitra Klien:</span>
                        <span class="font-bold text-slate-900">{{ $clientRequest->vendor ? $clientRequest->vendor->name : '-' }}</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

