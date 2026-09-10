@extends('layouts.client')

@section('title', 'Formulir Pengajuan & Laporan Armada')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <form action="{{ route('client.requests.store') }}" method="POST" enctype="multipart/form-data" id="clientWizardForm">
        @csrf

        <!-- Top Header Bar -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5 sm:p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#015471] to-[#023b4f] text-white flex items-center justify-center shadow-sm shrink-0">
                        <i data-lucide="file-text" class="w-6 h-6 text-[#FF7A3D]"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h1 class="text-xl font-black text-slate-900 tracking-tight">Formulir Pengajuan & Laporan Armada</h1>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#FF7A3D]/10 text-[#FF7A3D] border border-[#FF7A3D]/20">
                                4 Tahap Pengajuan
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Ajukan perbaikan berkala, penggantian ban/accu/sparepart, atau lapor kehilangan TLO</p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 shrink-0">
                    <a href="{{ route('client.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition shadow-2xs">
                        <i data-lucide="arrow-left" class="w-4 h-4 text-slate-400"></i> Batal
                    </a>
                </div>
            </div>

            <!-- Stepper Indicator Header (4 Steps) -->
            <div class="mt-8 pt-6 border-t border-slate-100">
                <div class="grid grid-cols-4 gap-2 sm:gap-4 relative">
                    <!-- Step 1 Indicator -->
                    <div class="step-indicator active flex flex-col items-center text-center group cursor-pointer" onclick="goToStep(1)" id="stepTab1">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-[#015471] text-white font-black text-xs sm:text-sm flex items-center justify-center shadow-md transition-all duration-200 step-icon ring-4 ring-[#015471]/10">
                            <span>1</span>
                        </div>
                        <span class="text-[11px] sm:text-xs font-black text-slate-900 mt-2 step-title">Kendaraan & Kendala</span>
                        <span class="hidden sm:inline text-[10px] text-slate-400 font-medium">Pilih armada & jenis</span>
                    </div>

                    <!-- Step 2 Indicator -->
                    <div class="step-indicator flex flex-col items-center text-center group cursor-pointer" onclick="goToStep(2)" id="stepTab2">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-slate-100 text-slate-400 font-black text-xs sm:text-sm flex items-center justify-center transition-all duration-200 step-icon">
                            <span>2</span>
                        </div>
                        <span class="text-[11px] sm:text-xs font-bold text-slate-500 mt-2 step-title">Rincian & Kategori</span>
                        <span class="hidden sm:inline text-[10px] text-slate-400 font-medium" id="step2Subtext">Formulir dinamis</span>
                    </div>

                    <!-- Step 3 Indicator -->
                    <div class="step-indicator flex flex-col items-center text-center group cursor-pointer" onclick="goToStep(3)" id="stepTab3">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-slate-100 text-slate-400 font-black text-xs sm:text-sm flex items-center justify-center transition-all duration-200 step-icon">
                            <span>3</span>
                        </div>
                        <span class="text-[11px] sm:text-xs font-bold text-slate-500 mt-2 step-title">Data Lapangan</span>
                        <span class="hidden sm:inline text-[10px] text-slate-400 font-medium">Pengemudi & KM</span>
                    </div>

                    <!-- Step 4 Indicator -->
                    <div class="step-indicator flex flex-col items-center text-center group cursor-pointer" onclick="goToStep(4)" id="stepTab4">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-slate-100 text-slate-400 font-black text-xs sm:text-sm flex items-center justify-center transition-all duration-200 step-icon">
                            <span>4</span>
                        </div>
                        <span class="text-[11px] sm:text-xs font-bold text-slate-500 mt-2 step-title">Foto & Konfirmasi</span>
                        <span class="hidden sm:inline text-[10px] text-slate-400 font-medium">Unggah bukti & kirim</span>
                    </div>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-3 mb-6 shadow-2xs">
                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-500 shrink-0 mt-0.5"></i>
                <div>
                    <strong class="font-bold block mb-1">Perhatian: Mohon periksa kesalahan input berikut:</strong>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- STEP CONTAINER 1: Kendaraan & Kendala Pokok -->
        <div class="step-content space-y-6" id="stepContent1">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-[#015471] text-white text-xs font-black flex items-center justify-center shadow-2xs">1</span>
                        <div>
                            <h2 class="text-sm font-black text-slate-900">Tahap 1: Unit Kendaraan & Jenis Kendala</h2>
                            <p class="text-[11px] text-slate-500">Pilih armada sewa dan jenis kendala yang dihadapi</p>
                        </div>
                    </div>
                    <span class="text-[11px] font-bold text-[#015471] bg-[#015471]/10 px-2.5 py-1 rounded-full">Langkah 1 dari 4</span>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    <!-- Searchable Dropdown Unit Kendaraan -->
                    <div class="relative" id="searchableVehicleWrapper">
                        <label class="block text-xs font-bold text-slate-700 mb-2">
                            Pilih Unit Kendaraan Motor <span class="text-[#FF7A3D]">*</span>
                        </label>

                        <!-- Hidden real input for form submission -->
                        <input type="hidden" name="vehicle_id" id="vehicle_id" value="{{ old('vehicle_id', $preselectedVehicleId) }}">

                        <!-- Dropdown Trigger Button -->
                        <button type="button" id="vehicleDropdownTrigger" onclick="toggleVehicleDropdown()" class="w-full text-left text-xs font-semibold px-4 py-3 rounded-2xl border border-slate-300 bg-white hover:bg-slate-50/50 focus:outline-none focus:ring-2 focus:ring-[#015471] focus:border-[#015471] text-slate-900 flex items-center justify-between shadow-2xs transition">
                            <span id="selectedVehicleLabel" class="truncate font-bold text-slate-800">
                                -- Pilih Unit Armada Motor --
                            </span>
                            <div class="flex items-center gap-2 pl-2 shrink-0">
                                <span id="clearVehicleSelection" onclick="event.stopPropagation(); resetVehicleSelection();" class="hidden text-[11px] text-slate-400 hover:text-rose-500 font-bold px-1 py-0.5 rounded transition">
                                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                </span>
                                <i data-lucide="chevron-down" id="vehicleDropdownChevron" class="w-4 h-4 text-slate-400 transition-transform duration-200"></i>
                            </div>
                        </button>

                        <!-- Floating Searchable Dropdown Panel -->
                        <div id="vehicleDropdownPanel" class="hidden absolute left-0 right-0 top-full mt-2 bg-white rounded-2xl border border-slate-200 shadow-xl z-50 overflow-hidden transition-all duration-200">
                            <!-- Search Bar Input Inside Dropdown -->
                            <div class="p-3 bg-slate-50/80 border-b border-slate-200 sticky top-0">
                                <div class="relative">
                                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                                    <input type="text" id="vehicleSearchInput" placeholder="Ketik Plat / Merk / Model / Lokasi..." autocomplete="off" onkeyup="filterVehicleList()" class="w-full text-xs font-semibold pl-9 pr-8 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 bg-white shadow-2xs">
                                    <button type="button" onclick="clearVehicleSearch()" id="btnClearSearch" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-slate-700 rounded-lg">
                                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-500 font-medium mt-2 px-1">
                                    <span>Tersedia: <strong id="vehicleCounter" class="text-slate-900 font-bold">{{ count($vehicles) }}</strong> unit</span>
                                    <span class="text-[10px] text-slate-400">Klik unit untuk memilih</span>
                                </div>
                            </div>

                            <!-- Options List Container -->
                            <div class="max-h-64 overflow-y-auto divide-y divide-slate-100 p-1.5 scrollbar-thin" id="vehicleOptionsContainer">
                                @forelse($vehicles as $v)
                                    @php
                                        $fullLabel = $v->plate_number . ' — ' . ($v->brand ? $v->brand . ' ' : '') . $v->model_name . ' (' . ($v->location ? $v->location->name : 'Lokasi Kebun') . ')';
                                        $searchKeywords = strtolower($v->plate_number . ' ' . $v->brand . ' ' . $v->model_name . ' ' . ($v->location ? $v->location->name : '') . ' ' . $v->user_holder);
                                        $isSelected = old('vehicle_id', $preselectedVehicleId) == $v->id;
                                        $holderName = $v->user_holder ?: 'Pengemudi Lapangan';
                                        $holderPhone = $v->driver_phone ?: ($v->location && $v->location->phone ? $v->location->phone : ($clientVendor && $clientVendor->phone ? $clientVendor->phone : '0812-3456-7890'));
                                        $defaultPicName = $clientVendor ? $clientVendor->pic_name : (Auth::user()->name ?: '');
                                        $defaultPicPhone = $clientVendor ? $clientVendor->phone : '';
                                    @endphp
                                    <div class="vehicle-option-item p-3 rounded-xl hover:bg-[#015471]/5 cursor-pointer transition flex items-center justify-between gap-3 {{ $isSelected ? 'bg-[#015471]/10 text-[#015471]' : 'text-slate-800' }}"
                                         data-id="{{ $v->id }}"
                                         data-plate="{{ $v->plate_number }}"
                                         data-model="{{ $v->brand ? $v->brand . ' ' : '' }}{{ $v->model_name }}"
                                         data-location="{{ $v->location ? $v->location->name : 'Lokasi Kebun' }}"
                                         data-holder="{{ $holderName }}"
                                         data-holderphone="{{ $holderPhone }}"
                                         data-pic="{{ $defaultPicName }}"
                                         data-picphone="{{ $defaultPicPhone }}"
                                         data-label="{{ $fullLabel }}"
                                         data-keywords="{{ $searchKeywords }}"
                                         onclick="selectVehicle('{{ $v->id }}', '{{ addslashes($fullLabel) }}', '{{ addslashes($holderName) }}', '{{ addslashes($holderPhone) }}', '{{ addslashes($defaultPicName) }}', '{{ addslashes($defaultPicPhone) }}')">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-8 h-8 rounded-lg bg-[#015471]/10 text-[#015471] flex items-center justify-center shrink-0">
                                                <i data-lucide="{{ $v->icon_name }}" class="w-4 h-4 text-[#FF7A3D]"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <span class="font-mono font-bold text-xs text-slate-900 block truncate">{{ $v->plate_number }}</span>
                                                <span class="text-[11px] text-slate-500 truncate block">{{ $v->brand ? $v->brand . ' ' : '' }}{{ $v->model_name }} &bull; {{ $v->location ? $v->location->name : 'Lokasi Kebun' }}</span>
                                            </div>
                                        </div>
                                        <div class="shrink-0 flex items-center">
                                            <span class="option-check {{ $isSelected ? 'inline-flex' : 'hidden' }} w-5 h-5 rounded-full bg-[#015471] text-white items-center justify-center">
                                                <i data-lucide="check" class="w-3 h-3"></i>
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-6 text-center text-slate-400 text-xs">
                                        Tidak ada data unit armada kendaraan yang tersedia.
                                    </div>
                                @endforelse
                                
                                <div id="vehicleNoResult" class="hidden p-6 text-center text-slate-400 text-xs">
                                    <i data-lucide="search-x" class="w-6 h-6 mx-auto text-slate-300 mb-1.5"></i>
                                    Unit kendaraan tidak ditemukan.
                                </div>
                            </div>
                        </div>

                        <span class="text-[11px] text-slate-400 mt-1.5 block">Hanya armada aktif dalam kontrak sewa perusahaan Anda yang ditampilkan.</span>
                    </div>

                    <!-- Radio Buttons Kendala Pokok (2 Pilihan Utama) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">
                            Pilih Kategori Kendala / Kebutuhan <span class="text-[#FF7A3D]">*</span>
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Option 1: Perbaikan / Service -->
                            <label class="relative flex flex-col p-4 rounded-2xl border-2 border-slate-200 cursor-pointer hover:border-[#015471] transition group has-[:checked]:border-[#015471] has-[:checked]:bg-[#015471]/5 shadow-2xs">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="w-10 h-10 rounded-xl bg-[#015471]/10 text-[#015471] flex items-center justify-center group-hover:scale-105 transition-transform">
                                        <i data-lucide="wrench" class="w-5 h-5 text-[#FF7A3D]"></i>
                                    </div>
                                    <input type="radio" name="request_type" value="perbaikan_servis" {{ old('request_type', $preselectedType) === 'perbaikan_servis' ? 'checked' : '' }} onchange="onTypeChanged()" class="mt-1 text-[#015471] focus:ring-[#015471] w-4 h-4">
                                </div>
                                <span class="font-black text-xs text-slate-900 block mb-1">Perbaikan / Service</span>
                                <span class="text-[11px] text-slate-500 leading-snug">Servis berkala, ganti ban gundul, aki drop, atau sparepart fast moving.</span>
                            </label>

                            <!-- Option 2: Lapor Kehilangan TLO -->
                            <label class="relative flex flex-col p-4 rounded-2xl border-2 border-slate-200 cursor-pointer hover:border-rose-500 transition group has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50/50 shadow-2xs">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                                        <i data-lucide="shield-alert" class="w-5 h-5"></i>
                                    </div>
                                    <input type="radio" name="request_type" value="lapor_kehilangan" {{ old('request_type', $preselectedType) === 'lapor_kehilangan' ? 'checked' : '' }} onchange="onTypeChanged()" class="mt-1 text-rose-600 focus:ring-rose-500 w-4 h-4">
                                </div>
                                <span class="font-black text-xs text-slate-900 block mb-1">Lapor Kehilangan (TLO)</span>
                                <span class="text-[11px] text-slate-500 leading-snug">Laporan insiden kehilangan armada motor untuk klaim asuransi & investigasi.</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP CONTAINER 2: Rincian Sesuai Pilihan Tahap 1 -->
        <div class="step-content space-y-6 hidden" id="stepContent2">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-[#015471] text-white text-xs font-black flex items-center justify-center shadow-2xs">2</span>
                        <div>
                            <h2 class="text-sm font-black text-slate-900" id="step2Title">Tahap 2: Rincian Pengajuan</h2>
                            <p class="text-[11px] text-slate-500" id="step2Subtitle">Sesuaikan detail permohonan sesuai kendala yang dipilih</p>
                        </div>
                    </div>
                    <span class="text-[11px] font-bold text-[#015471] bg-[#015471]/10 px-2.5 py-1 rounded-full">Langkah 2 dari 4</span>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    
                    <!-- VARIANT A: JIKA PILIH PERBAIKAN / SERVIS (Multi-Keluhan Repeater) -->
                    <div id="dynamicFormRepair" class="space-y-6">
                        <div class="p-4 rounded-2xl bg-[#015471]/5 border border-[#015471]/20 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-[#015471]/10 text-[#015471] flex items-center justify-center shrink-0">
                                <i data-lucide="wrench" class="w-5 h-5 text-[#FF7A3D]"></i>
                            </div>
                            <div class="text-xs">
                                <span class="font-bold text-[#015471] block">Daftar Keluhan & Kerusakan Armada</span>
                                <span class="text-slate-600">Anda dapat mengajukan lebih dari satu jenis keluhan perbaikan sekaligus pada unit motor ini.</span>
                            </div>
                        </div>

                        <!-- Container List Keluhan -->
                        <div id="complaintsListContainer" class="space-y-5">
                            <!-- Complaint Item 1 (Default) -->
                            <div class="complaint-card p-5 sm:p-6 rounded-2xl border-2 border-slate-200 bg-white space-y-4 relative shadow-2xs" data-index="0">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                    <div class="flex items-center gap-2.5">
                                        <span class="complaint-badge w-6 h-6 rounded-lg bg-[#015471] text-white text-[11px] font-black flex items-center justify-center">1</span>
                                        <span class="font-black text-xs text-slate-900 complaint-label">Keluhan / Perbaikan #1</span>
                                    </div>
                                    <button type="button" onclick="removeComplaintItem(this)" class="btn-remove-complaint hidden text-xs font-bold text-rose-500 hover:text-rose-700 hover:bg-rose-50 px-2 py-1 rounded-lg transition flex items-center gap-1">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                    </button>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                            Sub Kategori Pengajuan <span class="text-[#FF7A3D]">*</span>
                                        </label>
                                        <select name="complaints[0][category]" onchange="onComplaintCategoryChange(this)" class="complaint-category w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer">
                                            <option value="servis_berkala">Pengajuan Perbaikan / Servis Berkala</option>
                                            <option value="ganti_ban">Pengajuan Pergantian Ban (Depan / Belakang)</option>
                                            <option value="ganti_accu">Pengajuan Pergantian Accu / Aki</option>
                                            <option value="sparepart_fast_moving">Pengajuan Pergantian Sparepart Fast Moving</option>
                                        </select>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                                Judul Keluhan / Komponen Rusak <span class="text-[#FF7A3D]">*</span>
                                            </label>
                                            <input type="text" name="complaints[0][title]" placeholder="Contoh: Servis Rutin & Ganti Oli Mesin" class="complaint-title w-full text-xs font-bold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                                Estimasi Biaya untuk Keluhan Ini (Rp)
                                            </label>
                                            <div class="relative flex rounded-xl shadow-2xs">
                                                <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-slate-300 bg-slate-100 text-slate-700 text-xs font-mono font-black">
                                                    Rp
                                                </span>
                                                <input type="text" inputmode="numeric" placeholder="0" oninput="formatRupiahInput(this)" class="complaint-cost-display w-full text-xs font-mono font-bold px-3.5 py-2.5 rounded-r-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 bg-white">
                                                <input type="hidden" name="complaints[0][cost]" value="0" class="complaint-cost">
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                            Deskripsi Rincian Kerusakan & Gejala <span class="text-[#FF7A3D]">*</span>
                                        </label>
                                        <textarea name="complaints[0][description]" rows="3" placeholder="Jelaskan kondisi keluhan kendala, riwayat kilometer, atau rincian kerusakan..." class="complaint-desc w-full text-xs p-3.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 leading-relaxed"></textarea>
                                    </div>

                                    <!-- Upload Foto Khusus Keluhan Ini (Wajib) -->
                                    <div class="pt-2 border-t border-dashed border-slate-200">
                                        <div class="flex items-center justify-between mb-2">
                                            <label class="block text-xs font-bold text-slate-700">
                                                Unggah Foto Bukti Kerusakan untuk Keluhan Ini <span class="text-[#FF7A3D]">*</span>
                                            </label>
                                            <span class="text-[11px] text-slate-400">Format: JPG, PNG, PDF</span>
                                        </div>

                                        <!-- Hidden real input -->
                                        <input type="file" name="complaint_photos_0[]" multiple accept=".pdf,.jpg,.jpeg,.png" onchange="handleComplaintPhotosChange(this)" class="complaint-photo-input hidden">

                                        <div class="flex flex-wrap items-center gap-3">
                                            <!-- Custom Tombol Tambah Foto -->
                                            <button type="button" onclick="triggerComplaintPhotoUpload(this)" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 border-dashed border-[#015471]/40 hover:border-[#015471] bg-[#015471]/5 hover:bg-[#015471]/10 text-[#015471] font-bold text-xs transition shadow-2xs">
                                                <i data-lucide="camera" class="w-4 h-4 text-[#FF7A3D]"></i>
                                                <span>+ Tambah Foto / Bukti</span>
                                            </button>
                                            <span class="photo-count-badge text-xs font-semibold text-slate-500">Belum ada foto dipilih</span>
                                        </div>

                                        <!-- Visual Image Preview Grid -->
                                        <div class="complaint-photo-preview mt-3 hidden grid grid-cols-2 sm:grid-cols-4 gap-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-center pt-2">
                            <button type="button" onclick="addComplaintItem()" class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold rounded-2xl border-2 border-dashed border-[#015471]/40 text-[#015471] hover:bg-[#015471]/5 hover:border-[#015471] transition w-full sm:w-auto justify-center">
                                <i data-lucide="plus-circle" class="w-4 h-4 text-[#FF7A3D]"></i>
                                <span>+ Tambah Keluhan / Kerusakan Lainnya</span>
                            </button>
                        </div>
                    </div>

                    <!-- VARIANT B: JIKA PILIH UNIT PENGGANTI SEMENTARA -->
                    <div id="dynamicFormReplacement" class="space-y-5 hidden">
                        <div class="p-4 rounded-2xl bg-[#FF7A3D]/10 border border-[#FF7A3D]/25 flex items-center gap-3">
                            <i data-lucide="refresh-cw" class="w-5 h-5 text-[#FF7A3D] shrink-0"></i>
                            <div class="text-xs">
                                <span class="font-bold text-slate-900 block">Permohonan Drop Unit Motor Cadangan</span>
                                <span class="text-slate-600">Tim operasional PT SUP akan menyiapkan armada pengganti ke lokasi kebun/proyek Anda.</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Alasan Kebutuhan Unit Pengganti <span class="text-[#FF7A3D]">*</span></label>
                                <select name="replacement_reason" id="replacement_reason" class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900">
                                    <option value="servis_lebih_24_jam">Armada Masuk Bengkel Rekanan > 24 Jam</option>
                                    <option value="rusak_berat_lapangan">Kerusakan Mesin Berat di Jalur Lapangan</option>
                                    <option value="insiden_kehilangan">Unit Hilang / Dalam Proses Klaim</option>
                                    <option value="lainnya">Kebutuhan Operasional Mendesak Lainnya</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Titik Antar / Drop Point Kendaraan <span class="text-[#FF7A3D]">*</span></label>
                                <input type="text" name="drop_location" id="drop_location" placeholder="Contoh: Kantor Afdeling II / Mess Kebun" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Judul Permohonan Unit Pengganti <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <input type="text" name="replacement_title" id="replacement_title" placeholder="Contoh: Permohonan Unit Motor Pengganti Sementara untuk Operasional" class="w-full text-xs font-bold px-4 py-3 rounded-2xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 shadow-2xs">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Uraian Kebutuhan Lapangan & Estimasi Durasi Sewa Pengganti <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <textarea name="replacement_description" id="replacement_description" rows="4" placeholder="Jelaskan kronologis kendala di lapangan dan estimasi berapa hari membutuhkan motor pengganti..." class="w-full text-xs p-4 rounded-2xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 leading-relaxed shadow-2xs"></textarea>
                        </div>
                    </div>

                    <!-- VARIANT C: JIKA PILIH LAPOR KEHILANGAN (TLO) -->
                    <div id="dynamicFormLoss" class="space-y-5 hidden">
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 flex items-center gap-3 text-rose-800">
                            <i data-lucide="shield-alert" class="w-5 h-5 text-rose-600 shrink-0"></i>
                            <div class="text-xs">
                                <span class="font-bold block">Pelaporan Insiden Kehilangan Unit (TLO)</span>
                                <span>Laporan ini akan langsung diinvestigasi dan diteruskan ke pihak asuransi.</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Waktu Kejadian / Diketahui Hilang <span class="text-[#FF7A3D]">*</span></label>
                                <input type="datetime-local" name="incident_time" id="incident_time" class="w-full text-xs font-mono px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500 text-slate-900">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Lokasi Terakhir / TKP Kejadian <span class="text-[#FF7A3D]">*</span></label>
                                <input type="text" name="incident_location" id="incident_location" placeholder="Contoh: Parkiran Afdeling IV / Rumah Dinas" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500 text-slate-900">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Judul Laporan Kehilangan Unit <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <input type="text" name="loss_title" id="loss_title" placeholder="Contoh: Laporan Kehilangan Unit Motor Sewa CRF 150 di Area Kebun" class="w-full text-xs font-bold px-4 py-3 rounded-2xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500 text-slate-900 shadow-2xs">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Kronologi Lengkap Kejadian & Saksi di Lokasi <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <textarea name="loss_description" id="loss_description" rows="4" placeholder="Jelaskan kronologi kejadian secara runtut, nama saksi di lokasi, dan tindakan awal yang telah dilakukan..." class="w-full text-xs p-4 rounded-2xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500 text-slate-900 leading-relaxed shadow-2xs"></textarea>
                        </div>
                    </div>

                    <!-- Hidden Fields to harmonize form submission -->
                    <input type="hidden" name="title" id="formTitle">
                    <input type="hidden" name="description" id="formDescription">

                </div>
            </div>
        </div>

        <!-- STEP CONTAINER 3: Data Lapangan & Kondisi Unit Motor -->
        <div class="step-content space-y-6 hidden" id="stepContent3">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-[#015471] text-white text-xs font-black flex items-center justify-center shadow-2xs">3</span>
                        <div>
                            <h2 class="text-sm font-black text-slate-900">Tahap 3: Informasi Unit Motor & Lapangan</h2>
                            <p class="text-[11px] text-slate-500" id="step3Subtitle">Data teknis motor (KM terkini, kondisi operasional), pengemudi & PIC penanggung jawab</p>
                        </div>
                    </div>
                    <span class="text-[11px] font-bold text-[#015471] bg-[#015471]/10 px-2.5 py-1 rounded-full">Langkah 3 dari 4</span>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    
                    <!-- KELOMPOK 1: Kondisi Motor & Odometer Terkini -->
                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-50/90 border border-slate-200/90 space-y-4 shadow-2xs">
                        <div class="flex items-center justify-between border-b border-slate-200/80 pb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-[#015471] text-white flex items-center justify-center text-xs shadow-2xs">
                                    <i data-lucide="gauge" class="w-4 h-4 text-[#FF7A3D]"></i>
                                </div>
                                <div>
                                    <span class="text-xs font-black text-slate-900 block">1. Kondisi Motor & Odometer Terkini</span>
                                    <span class="text-[11px] text-slate-500">Angka kilometer terkini dan status operasional armada di lapangan</span>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold text-[#FF7A3D] bg-[#FF7A3D]/10 px-2.5 py-1 rounded-full border border-[#FF7A3D]/20">Wajib Diisi</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Posisi Odometer Terkini (KM) - WAJIB -->
                            <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-1.5">
                                <label class="block text-xs font-bold text-slate-800">
                                    Kilometer Odometer Terakhir (KM) <span class="text-[#FF7A3D]">*</span>
                                </label>
                                <div class="relative flex rounded-xl shadow-2xs overflow-hidden">
                                    <input type="number" name="odometer_km" id="odometer_km" value="{{ old('odometer_km') }}" placeholder="Contoh: 12450" min="0" oninput="updateSummary()" class="w-full text-sm font-mono font-bold px-4 py-2.5 border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 bg-white">
                                    <span class="inline-flex items-center px-4 border border-l-0 border-slate-300 bg-slate-100 text-[#015471] text-xs font-black font-mono">
                                        KM
                                    </span>
                                </div>
                                <span class="text-[10px] text-slate-400 block">Catat angka yang tertera di speedometer dashboard motor saat ini.</span>
                            </div>

                            <!-- Kondisi Operasional Motor Lapangan -->
                            <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs space-y-1.5">
                                <label class="block text-xs font-bold text-slate-800">
                                    Status Kelayakan Operasional Motor <span class="text-[#FF7A3D]">*</span>
                                </label>
                                <select name="vehicle_operational_status" id="vehicle_operational_status" onchange="updateSummary()" class="w-full text-xs font-bold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 bg-white shadow-2xs cursor-pointer">
                                    <option value="masih_bisa_jalan">Masih Bisa Berjalan Normal / Pelan</option>
                                    <option value="mogok_total">Mogok Total / Tidak Bisa Dihidupkan</option>
                                    <option value="rusak_tidak_aman">Bisa Hidup Tapi Berbahaya / Tidak Aman</option>
                                    <option value="sudah_di_bengkel">Unit Sudah Masuk / Menginap di Bengkel</option>
                                    <option value="unit_hilang">Unit Tidak di Tempat / Hilang (TLO)</option>
                                </select>
                                <span class="text-[10px] text-slate-400 block">Status fisik unit menentukan kecepatan & prioritas penanganan tim.</span>
                            </div>
                        </div>

                        <!-- Info Posisi Unit Lapangan & Kondisi Fisik Tambahan -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Posisi / Lokasi Keberadaan Motor Terkini
                                </label>
                                <input type="text" name="vehicle_current_location" id="vehicle_current_location" value="{{ old('vehicle_current_location') }}" placeholder="Contoh: Parkiran Afdeling III / Mess Staff / Bengkel Desa" class="w-full text-xs px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 bg-white shadow-2xs">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Kondisi Fisik / Catatan Tambahan Unit
                                </label>
                                <input type="text" name="vehicle_condition_notes" id="vehicle_condition_notes" value="{{ old('vehicle_condition_notes') }}" placeholder="Contoh: Body lecet, rem blong, lampu utama mati" class="w-full text-xs px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 bg-white shadow-2xs">
                            </div>
                        </div>
                    </div>

                    <!-- KELOMPOK 2: Kontak Pengemudi & PIC Lapangan -->
                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-50/90 border border-slate-200/90 space-y-4 shadow-2xs">
                        <div class="flex items-center justify-between border-b border-slate-200/80 pb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-[#015471] text-white flex items-center justify-center text-xs shadow-2xs">
                                    <i data-lucide="users" class="w-4 h-4 text-[#FF7A3D]"></i>
                                </div>
                                <div>
                                    <span class="text-xs font-black text-slate-900 block">2. Kontak Pengemudi & PIC Lapangan</span>
                                    <span class="text-[11px] text-slate-500">Penanggung jawab unit dan kontak konfirmasi operasional</span>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold text-[#015471] bg-[#015471]/10 px-2.5 py-1 rounded-full border border-[#015471]/20">Kontak Lapangan</span>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <!-- Data Pengemudi Lapangan (Otomatis Terisi & Readonly) -->
                            <div class="p-4 rounded-2xl bg-white border border-slate-200/90 space-y-3 shadow-2xs">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <div class="flex items-center gap-2 text-slate-700">
                                        <i data-lucide="user-check" class="w-4 h-4 text-[#015471]"></i>
                                        <span class="text-xs font-bold text-slate-800">Pengemudi / Pemegang Unit</span>
                                    </div>
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">
                                        <i data-lucide="lock" class="w-3 h-3 text-slate-400"></i> Terkunci
                                    </span>
                                </div>

                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1">
                                            Nama Pengemudi Terdaftar
                                        </label>
                                        <div class="relative">
                                            <input type="text" name="driver_name" id="driver_name" value="{{ old('driver_name') }}" readonly class="w-full text-xs font-bold px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-100 text-slate-800 cursor-not-allowed shadow-2xs focus:outline-none">
                                            <i data-lucide="lock" class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2"></i>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1">
                                            No. HP / WhatsApp Pengemudi
                                        </label>
                                        <div class="relative">
                                            <input type="text" name="driver_phone" id="driver_phone" value="{{ old('driver_phone') }}" readonly class="w-full text-xs font-bold px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-100 text-slate-800 cursor-not-allowed shadow-2xs focus:outline-none">
                                            <i data-lucide="lock" class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Input Data PIC Pengaju (Wajib Diisi / Editable) -->
                            <div class="p-4 rounded-2xl bg-white border-2 border-[#015471]/20 space-y-3 shadow-2xs">
                                <div class="flex items-center justify-between border-b border-[#015471]/10 pb-2">
                                    <div class="flex items-center gap-2 text-[#015471]">
                                        <i data-lucide="contact" class="w-4 h-4 text-[#FF7A3D]"></i>
                                        <span class="text-xs font-black">Data PIC Pengaju Lapangan</span>
                                    </div>
                                    <span class="text-[10px] font-bold text-[#FF7A3D] bg-[#FF7A3D]/10 px-2 py-0.5 rounded-md">Wajib Diisi</span>
                                </div>

                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">
                                            Nama PIC Pengaju <span class="text-[#FF7A3D]">*</span>
                                        </label>
                                        <input type="text" name="pic_name" id="pic_name" value="{{ old('pic_name', $clientVendor->pic_name ?? Auth::user()->name) }}" placeholder="Contoh: Bambang Irawan / Asisten Kebun" oninput="updateSummary()" class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 shadow-2xs bg-white">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">
                                            No. HP / WhatsApp PIC <span class="text-[#FF7A3D]">*</span>
                                        </label>
                                        <input type="text" name="pic_phone" id="pic_phone" value="{{ old('pic_phone', $clientVendor->phone ?? '') }}" placeholder="Contoh: 081234567890" oninput="updateSummary()" class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 shadow-2xs bg-white">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KELOMPOK 3: Bengkel Rujukan & Total Estimasi Biaya -->
                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-50/90 border border-slate-200/90 space-y-4 shadow-2xs" id="rowOdometerAndCost">
                        <div class="flex items-center justify-between border-b border-slate-200/80 pb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-[#015471] text-white flex items-center justify-center text-xs shadow-2xs">
                                    <i data-lucide="wrench" class="w-4 h-4 text-[#FF7A3D]"></i>
                                </div>
                                <div>
                                    <span class="text-xs font-black text-slate-900 block">3. Rujukan Bengkel, Mekanik & Total Estimasi Biaya</span>
                                    <span class="text-[11px] text-slate-500">Tempat penanganan servis, kontak mekanik, dan total biaya otomatis</span>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold text-[#015471] bg-[#015471]/10 px-2.5 py-1 rounded-full border border-[#015471]/20">
                                Layanan Perbaikan
                            </span>
                        </div>

                        <!-- Grid Bengkel & Estimasi Biaya -->
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                            <!-- Bagian Kiri: Info Bengkel & Mekanik (7 Kolom) -->
                            <div class="lg:col-span-7 p-4 bg-white rounded-2xl border border-slate-200/90 space-y-4 shadow-2xs">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <!-- Bengkel Rujukan (Wajib Diisi untuk Perbaikan / Servis) -->
                                    <div id="fieldWorkshop">
                                        <label class="block text-xs font-bold text-slate-800 mb-1">
                                            Nama Bengkel / AHASS <span class="text-[#FF7A3D]">*</span>
                                        </label>
                                        <input type="text" name="workshop_name" id="workshop_name" value="{{ old('workshop_name') }}" placeholder="Contoh: AHASS Sentral Motor" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 shadow-2xs bg-white">
                                    </div>

                                    <!-- No. Telepon Bengkel -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-800 mb-1">
                                            No. Telp / WA Bengkel
                                        </label>
                                        <input type="text" name="workshop_phone" id="workshop_phone" value="{{ old('workshop_phone') }}" placeholder="Contoh: 0812-3456-7890" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 shadow-2xs bg-white">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-2 border-t border-slate-100">
                                    <!-- Nama Mekanik -->
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">
                                            Nama Mekanik / Teknisi
                                        </label>
                                        <input type="text" name="mechanic_name" id="mechanic_name" value="{{ old('mechanic_name') }}" placeholder="Contoh: Mas Joko / Pak Hendra" class="w-full text-xs px-3.5 py-2 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 shadow-2xs bg-white">
                                    </div>

                                    <!-- No. Telepon Mekanik -->
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">
                                            No. Telp / WA Mekanik
                                        </label>
                                        <input type="text" name="mechanic_phone" id="mechanic_phone" value="{{ old('mechanic_phone') }}" placeholder="Contoh: 0821-9876-5432" class="w-full text-xs px-3.5 py-2 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 shadow-2xs bg-white">
                                    </div>
                                </div>
                            </div>

                            <!-- Bagian Kanan: Total Akumulasi Estimasi Biaya (5 Kolom) -->
                            <div id="colEstimatedCost" class="lg:col-span-5 p-5 bg-gradient-to-br from-[#015471]/5 via-white to-[#FF7A3D]/5 rounded-2xl border-2 border-[#015471]/20 flex flex-col justify-between shadow-2xs">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="block text-xs font-black text-slate-900">
                                            Total Estimasi Biaya
                                        </label>
                                        <span id="estimatedCostBadge" class="inline-flex items-center gap-1 text-[10px] font-bold text-[#015471] bg-[#015471]/10 px-2 py-0.5 rounded-full">
                                            <i data-lucide="calculator" class="w-3 h-3"></i> Otomatis dari Tahap 2
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 leading-relaxed">
                                        Jumlah seluruh estimasi biaya yang Anda cantumkan pada masing-masing kartu keluhan di Tahap 2.
                                    </p>
                                </div>

                                <div class="pt-4">
                                    <div class="relative flex items-center bg-white rounded-2xl border border-slate-300 shadow-inner px-4 py-3">
                                        <span class="text-sm font-black font-mono text-[#015471] mr-2">Rp</span>
                                        <input type="text" id="estimated_cost_display" value="0" readonly placeholder="0" class="w-full text-lg font-black font-mono text-[#FF7A3D] bg-transparent focus:outline-none cursor-not-allowed">
                                        <input type="hidden" name="estimated_cost" id="estimated_cost" value="{{ old('estimated_cost', 0) }}">
                                    </div>
                                    <span class="text-[10px] text-slate-400 mt-1.5 block text-right font-medium">
                                        *Tidak dapat diubah manual (otomatis tersinkronisasi)
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- STEP CONTAINER 4: Unggah Foto Bukti & Ringkasan Konfirmasi -->
        <div class="step-content space-y-6 hidden" id="stepContent4">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-[#015471] text-white text-xs font-black flex items-center justify-center shadow-2xs">4</span>
                        <div>
                            <h2 class="text-sm font-black text-slate-900">Tahap 4: Unggah Bukti & Konfirmasi Pengajuan</h2>
                            <p class="text-[11px] text-slate-500">Lampirkan foto fisik dan periksa ringkasan sebelum mengirim</p>
                        </div>
                    </div>
                    <span class="text-[11px] font-bold text-[#015471] bg-[#015471]/10 px-2.5 py-1 rounded-full">Langkah 4 dari 4</span>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    <!-- 4 Foto Standar Unit Kendaraan -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <div>
                                <label class="block text-xs font-black text-slate-900">
                                    Foto Fisik 4 Sisi Unit Kendaraan <span class="text-[#FF7A3D]">*</span>
                                </label>
                                <p class="text-[11px] text-slate-500">Unggah 4 sudut foto fisik unit untuk identifikasi visual dan verifikasi armada</p>
                            </div>
                            <span class="text-[10px] font-bold text-[#015471] bg-[#015471]/10 px-2.5 py-1 rounded-full">4 Sudut Wajib</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- 1. Tampak Depan (Nopol Terlihat) -->
                            <div class="rounded-2xl border-2 border-dashed border-slate-300 hover:border-[#015471] bg-slate-50/50 hover:bg-slate-50 transition p-3.5 flex flex-col justify-between group relative overflow-hidden">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[11px] font-black text-slate-800">1. Tampak Depan</span>
                                        <span class="text-[9px] font-bold text-[#FF7A3D] bg-[#FF7A3D]/10 px-1.5 py-0.5 rounded">Nopol Jelas</span>
                                    </div>
                                    <p class="text-[10px] text-slate-400 mb-3">Foto bagian depan dengan plat nomor terlihat jelas</p>
                                </div>

                                <div id="preview_box_photo_front" class="hidden mb-2 relative rounded-xl overflow-hidden border border-slate-200 bg-white">
                                    <img id="img_preview_photo_front" class="w-full h-28 object-cover" src="" alt="Tampak Depan">
                                    <button type="button" onclick="removeSingleVehiclePhoto('photo_front')" class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center shadow-md transition z-10">
                                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>

                                <label class="cursor-pointer block text-center py-3 px-3 rounded-xl bg-white border border-slate-200 group-hover:border-[#015471] group-hover:text-[#015471] text-slate-700 text-xs font-bold shadow-2xs transition select-none">
                                    <i data-lucide="camera" class="w-5 h-5 mx-auto mb-1 text-[#FF7A3D]"></i>
                                    <span id="upload_label_photo_front">Pilih Foto Depan</span>
                                    <input type="file" name="photo_front" id="photo_front" accept="image/*" onchange="previewVehiclePhoto(this, 'photo_front')" class="hidden">
                                </label>
                            </div>

                            <!-- 2. Tampak Samping Kanan -->
                            <div class="rounded-2xl border-2 border-dashed border-slate-300 hover:border-[#015471] bg-slate-50/50 hover:bg-slate-50 transition p-3.5 flex flex-col justify-between group relative overflow-hidden">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[11px] font-black text-slate-800">2. Samping Kanan</span>
                                        <span class="text-[9px] font-bold text-[#015471] bg-[#015471]/10 px-1.5 py-0.5 rounded">Body Kanan</span>
                                    </div>
                                    <p class="text-[10px] text-slate-400 mb-3">Foto menyeluruh dari sisi samping sebelah kanan</p>
                                </div>

                                <div id="preview_box_photo_right" class="hidden mb-2 relative rounded-xl overflow-hidden border border-slate-200 bg-white">
                                    <img id="img_preview_photo_right" class="w-full h-28 object-cover" src="" alt="Samping Kanan">
                                    <button type="button" onclick="removeSingleVehiclePhoto('photo_right')" class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center shadow-md transition z-10">
                                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>

                                <label class="cursor-pointer block text-center py-3 px-3 rounded-xl bg-white border border-slate-200 group-hover:border-[#015471] group-hover:text-[#015471] text-slate-700 text-xs font-bold shadow-2xs transition select-none">
                                    <i data-lucide="camera" class="w-5 h-5 mx-auto mb-1 text-[#FF7A3D]"></i>
                                    <span id="upload_label_photo_right">Pilih Foto Kanan</span>
                                    <input type="file" name="photo_right" id="photo_right" accept="image/*" onchange="previewVehiclePhoto(this, 'photo_right')" class="hidden">
                                </label>
                            </div>

                            <!-- 3. Tampak Samping Kiri -->
                            <div class="rounded-2xl border-2 border-dashed border-slate-300 hover:border-[#015471] bg-slate-50/50 hover:bg-slate-50 transition p-3.5 flex flex-col justify-between group relative overflow-hidden">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[11px] font-black text-slate-800">3. Samping Kiri</span>
                                        <span class="text-[9px] font-bold text-[#015471] bg-[#015471]/10 px-1.5 py-0.5 rounded">Body Kiri</span>
                                    </div>
                                    <p class="text-[10px] text-slate-400 mb-3">Foto menyeluruh dari sisi samping sebelah kiri</p>
                                </div>

                                <div id="preview_box_photo_left" class="hidden mb-2 relative rounded-xl overflow-hidden border border-slate-200 bg-white">
                                    <img id="img_preview_photo_left" class="w-full h-28 object-cover" src="" alt="Samping Kiri">
                                    <button type="button" onclick="removeSingleVehiclePhoto('photo_left')" class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center shadow-md transition z-10">
                                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>

                                <label class="cursor-pointer block text-center py-3 px-3 rounded-xl bg-white border border-slate-200 group-hover:border-[#015471] group-hover:text-[#015471] text-slate-700 text-xs font-bold shadow-2xs transition select-none">
                                    <i data-lucide="camera" class="w-5 h-5 mx-auto mb-1 text-[#FF7A3D]"></i>
                                    <span id="upload_label_photo_left">Pilih Foto Kiri</span>
                                    <input type="file" name="photo_left" id="photo_left" accept="image/*" onchange="previewVehiclePhoto(this, 'photo_left')" class="hidden">
                                </label>
                            </div>

                            <!-- 4. Tampak Belakang (Nopol Terlihat) -->
                            <div class="rounded-2xl border-2 border-dashed border-slate-300 hover:border-[#015471] bg-slate-50/50 hover:bg-slate-50 transition p-3.5 flex flex-col justify-between group relative overflow-hidden">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[11px] font-black text-slate-800">4. Tampak Belakang</span>
                                        <span class="text-[9px] font-bold text-[#FF7A3D] bg-[#FF7A3D]/10 px-1.5 py-0.5 rounded">Nopol Jelas</span>
                                    </div>
                                    <p class="text-[10px] text-slate-400 mb-3">Foto bagian belakang dengan plat nomor terlihat jelas</p>
                                </div>

                                <div id="preview_box_photo_behind" class="hidden mb-2 relative rounded-xl overflow-hidden border border-slate-200 bg-white">
                                    <img id="img_preview_photo_behind" class="w-full h-28 object-cover" src="" alt="Tampak Belakang">
                                    <button type="button" onclick="removeSingleVehiclePhoto('photo_behind')" class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center shadow-md transition z-10">
                                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>

                                <label class="cursor-pointer block text-center py-3 px-3 rounded-xl bg-white border border-slate-200 group-hover:border-[#015471] group-hover:text-[#015471] text-slate-700 text-xs font-bold shadow-2xs transition select-none">
                                    <i data-lucide="camera" class="w-5 h-5 mx-auto mb-1 text-[#FF7A3D]"></i>
                                    <span id="upload_label_photo_behind">Pilih Foto Belakang</span>
                                    <input type="file" name="photo_behind" id="photo_behind" accept="image/*" onchange="previewVehiclePhoto(this, 'photo_behind')" class="hidden">
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Dokumen & Berkas Pendukung Tambahan (Opsional) -->
                    <div class="pt-2">
                        <label class="block text-xs font-bold text-slate-700 mb-2">
                            Dokumen / Berkas Lampiran Tambahan <span class="text-slate-400 font-normal">(Opsional - PDF, SPK Bengkel, LP Polisi, dll)</span>
                        </label>
                        
                        <!-- Seluruh Kotak Menjadi Label Clickable -->
                        <label for="proof_files" class="relative border-2 border-dashed border-slate-300 hover:border-[#015471] rounded-2xl p-6 text-center transition bg-slate-50/50 hover:bg-slate-50 cursor-pointer block group select-none">
                            <div class="w-12 h-12 rounded-2xl bg-white text-[#015471] group-hover:bg-[#015471] group-hover:text-white shadow-2xs mx-auto flex items-center justify-center mb-3 transition">
                                <i data-lucide="paperclip" class="w-6 h-6"></i>
                            </div>
                            <span class="block text-xs font-bold text-slate-800 group-hover:text-[#015471] transition">
                                Pilih dokumen atau file pendukung lainnya
                            </span>
                            <p class="text-[11px] text-slate-400 mt-1">Mendukung format PDF, JPG, PNG (Maks. 5MB per file)</p>
                            <input type="file" name="proof_files[]" id="proof_files" multiple accept=".pdf,.jpg,.jpeg,.png" onchange="previewSelectedFiles(this)" class="hidden">
                        </label>

                        <div id="file_list_preview" class="hidden mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2 text-left text-xs"></div>

                        <!-- Panduan Dinamis Lampiran Berdasarkan Pilihan Tahap 1 -->
                        <div class="mt-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600" id="guideAttachment">
                            <div class="flex items-start gap-2.5">
                                <i data-lucide="info" class="w-4 h-4 text-[#015471] shrink-0 mt-0.5"></i>
                                <div>
                                    <strong class="text-slate-800 font-bold block mb-0.5">Panduan Lampiran:</strong>
                                    <p id="attachmentGuideText" class="text-[11px] text-slate-500 leading-relaxed">
                                        Pastikan 4 sudut foto kendaraan terunggah dengan jelas dan plat nomor terbaca untuk mempercepat verifikasi persetujuan oleh tim PT SUP.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Review Box -->
                    <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200 space-y-3">
                        <div class="flex items-center gap-2 text-[#015471]">
                            <i data-lucide="clipboard-check" class="w-4 h-4 text-[#FF7A3D]"></i>
                            <h3 class="text-xs font-black uppercase tracking-wider">Ringkasan Data Pengajuan</h3>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div class="p-3 bg-white rounded-xl border border-slate-200">
                                <span class="text-[11px] text-slate-400 block">Armada Kendaraan:</span>
                                <span class="font-black text-slate-900 font-mono mt-0.5 block" id="summaryVehicle">-</span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-slate-200">
                                <span class="text-[11px] text-slate-400 block">Jenis Permohonan:</span>
                                <span class="font-bold text-[#015471] mt-0.5 block" id="summaryType">-</span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-slate-200">
                                <span class="text-[11px] text-slate-400 block">Judul Pengajuan:</span>
                                <span class="font-bold text-slate-900 mt-0.5 block" id="summaryTitle">-</span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-slate-200">
                                <span class="text-[11px] text-slate-400 block">PIC / Pengemudi Lapangan:</span>
                                <span class="font-bold text-slate-900 mt-0.5 block" id="summaryDriver">-</span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-slate-200">
                                <span class="text-[11px] text-slate-400 block">Odometer Motor Terkini:</span>
                                <span class="font-black text-[#015471] font-mono mt-0.5 block" id="summaryOdometer">-</span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-slate-200">
                                <span class="text-[11px] text-slate-400 block">Kondisi Fisik / Operasional:</span>
                                <span class="font-bold text-slate-900 mt-0.5 block" id="summaryCondition">-</span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-slate-200 sm:col-span-2">
                                <span class="text-[11px] text-slate-400 block">Total Estimasi Biaya:</span>
                                <span class="font-black text-[#FF7A3D] font-mono text-sm mt-0.5 block" id="summaryCost">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky Bottom Nav Navigation Buttons -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-4 sm:p-5 flex items-center justify-between gap-4 mt-6">
            <button type="button" id="btnPrev" onclick="navigateStep(-1)" class="hidden inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold rounded-xl border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition shadow-2xs">
                <i data-lucide="chevron-left" class="w-4 h-4"></i> Sebelumnya
            </button>
            <div id="prevSpacer"></div>

            <div class="flex items-center gap-3">
                <button type="button" id="btnNext" onclick="navigateStep(1)" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 text-xs font-black rounded-xl bg-[#015471] hover:bg-[#013e54] text-white shadow-sm transition">
                    <span>Lanjutkan ke Tahap 2</span>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </button>

                <button type="submit" id="btnSubmit" class="hidden inline-flex items-center justify-center gap-2 px-7 py-2.5 text-xs font-black rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-md transition">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span>Kirim Pengajuan Sekarang</span>
                </button>
            </div>
        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>
    let currentStep = 1;
    const totalSteps = 4;

    function getSelectedType() {
        const checked = document.querySelector('input[name="request_type"]:checked');
        return checked ? checked.value : 'perbaikan_servis';
    }

    function onTypeChanged() {
        const type = getSelectedType();
        const repairForm = document.getElementById('dynamicFormRepair');
        const replacementForm = document.getElementById('dynamicFormReplacement');
        const lossForm = document.getElementById('dynamicFormLoss');

        const step2Title = document.getElementById('step2Title');
        const step2Subtitle = document.getElementById('step2Subtitle');
        const guideText = document.getElementById('attachmentGuideText');

        const rowOdometer = document.getElementById('rowOdometerAndCost');
        const fieldWorkshop = document.getElementById('fieldWorkshop');

        // Reset visibility
        repairForm.classList.add('hidden');
        replacementForm.classList.add('hidden');
        lossForm.classList.add('hidden');

        if (type === 'perbaikan_servis') {
            repairForm.classList.remove('hidden');
            step2Title.innerText = 'Tahap 2: Rincian & Daftar Keluhan Perbaikan';
            step2Subtitle.innerText = 'Tambahkan satu atau beberapa keluhan kerusakan motor beserta foto masing-masing';
            guideText.innerText = 'Lampirkan foto fisik bagian motor yang rusak pada setiap kartu keluhan di atas atau pada berkas pendukung di bawah.';
            
            if (rowOdometer) rowOdometer.classList.remove('hidden');
            if (fieldWorkshop) fieldWorkshop.classList.remove('hidden');
        } else if (type === 'unit_pengganti') {
            replacementForm.classList.remove('hidden');
            step2Title.innerText = 'Tahap 2: Rincian Kebutuhan Unit Pengganti';
            step2Subtitle.innerText = 'Tentukan alasan kebutuhan motor pengganti sementara dan titik antar unit';
            guideText.innerText = 'Lampirkan foto SPK servis bengkel atau foto kondisi motor di lapangan yang membutuhkan penggantian.';
            
            if (rowOdometer) rowOdometer.classList.remove('hidden');
            if (fieldWorkshop) fieldWorkshop.classList.add('hidden');
        } else if (type === 'lapor_kehilangan') {
            lossForm.classList.remove('hidden');
            step2Title.innerText = 'Tahap 2: Kronologi Laporan Kehilangan (TLO)';
            step2Subtitle.innerText = 'Lengkapi waktu kejadian, lokasi TKP, dan kronologi insiden kehilangan';
            guideText.innerText = 'Wajib melampirkan Surat Tanda Penerimaan Laporan (LP) dari Polsek/Polres setempat untuk klaim asuransi TLO.';
            
            if (rowOdometer) rowOdometer.classList.add('hidden');
            if (fieldWorkshop) fieldWorkshop.classList.add('hidden');
        }

        updateSummary();
    }

    // MULTI-COMPLAINT REPEATER FUNCTIONS
    let complaintCount = 1;

    // Helper to format string number with thousand separator (dots)
    function formatRupiahNumber(num) {
        if (!num && num !== 0) return '';
        const clean = String(num).replace(/[^0-9]/g, '');
        if (!clean) return '';
        return clean.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function formatRupiahInput(displayInput) {
        const rawValue = displayInput.value.replace(/[^0-9]/g, '');
        const hiddenInput = displayInput.parentElement.querySelector('.complaint-cost');
        
        if (!rawValue) {
            displayInput.value = '';
            if (hiddenInput) hiddenInput.value = '0';
        } else {
            displayInput.value = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            if (hiddenInput) hiddenInput.value = rawValue;
        }

        calculateTotalEstimatedCost();
    }

    function calculateTotalEstimatedCost() {
        const type = getSelectedType();
        const costInputs = document.querySelectorAll('.complaint-cost');
        let total = 0;

        costInputs.forEach(input => {
            const val = parseFloat(input.value) || 0;
            total += val;
        });

        const estCostInput = document.getElementById('estimated_cost');
        const estCostDisplay = document.getElementById('estimated_cost_display');
        const summaryCostElem = document.getElementById('summaryCost');

        if (type === 'perbaikan_servis') {
            if (estCostInput) {
                estCostInput.value = total;
            }
            if (estCostDisplay) {
                estCostDisplay.value = total > 0 ? formatRupiahNumber(total) : '0';
            }
            if (summaryCostElem) {
                summaryCostElem.innerText = `Rp ${Number(total).toLocaleString('id-ID')}`;
            }
        } else {
            if (estCostInput) {
                total = parseFloat(estCostInput.value) || 0;
            }
            if (estCostDisplay) {
                estCostDisplay.value = total > 0 ? formatRupiahNumber(total) : '0';
            }
            if (summaryCostElem) {
                summaryCostElem.innerText = `Rp ${Number(total).toLocaleString('id-ID')}`;
            }
        }
    }

    function addComplaintItem() {
        const container = document.getElementById('complaintsListContainer');
        const newIndex = complaintCount++;
        
        const card = document.createElement('div');
        card.className = 'complaint-card p-5 sm:p-6 rounded-2xl border-2 border-slate-200 bg-white space-y-4 relative shadow-2xs transition-all duration-200';
        card.setAttribute('data-index', newIndex);
        
        card.innerHTML = `
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <span class="complaint-badge w-6 h-6 rounded-lg bg-[#015471] text-white text-[11px] font-black flex items-center justify-center"></span>
                    <span class="font-black text-xs text-slate-900 complaint-label">Keluhan / Perbaikan #${newIndex + 1}</span>
                </div>
                <button type="button" onclick="removeComplaintItem(this)" class="btn-remove-complaint text-xs font-bold text-rose-500 hover:text-rose-700 hover:bg-rose-50 px-2 py-1 rounded-lg transition flex items-center gap-1">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Sub Kategori Pengajuan <span class="text-[#FF7A3D]">*</span>
                    </label>
                    <select name="complaints[${newIndex}][category]" onchange="onComplaintCategoryChange(this)" class="complaint-category w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer">
                        <option value="servis_berkala">Pengajuan Perbaikan / Servis Berkala</option>
                        <option value="ganti_ban">Pengajuan Pergantian Ban (Depan / Belakang)</option>
                        <option value="ganti_accu">Pengajuan Pergantian Accu / Aki</option>
                        <option value="sparepart_fast_moving">Pengajuan Pergantian Sparepart Fast Moving</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Judul Keluhan / Komponen Rusak <span class="text-[#FF7A3D]">*</span>
                        </label>
                        <input type="text" name="complaints[${newIndex}][title]" placeholder="Contoh: Penggantian Ban Luar Belakang" class="complaint-title w-full text-xs font-bold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Estimasi Biaya untuk Keluhan Ini (Rp)
                        </label>
                        <div class="relative flex rounded-xl shadow-2xs">
                            <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-slate-300 bg-slate-100 text-slate-700 text-xs font-mono font-black">
                                Rp
                            </span>
                            <input type="text" inputmode="numeric" placeholder="0" oninput="formatRupiahInput(this)" class="complaint-cost-display w-full text-xs font-mono font-bold px-3.5 py-2.5 rounded-r-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 bg-white">
                            <input type="hidden" name="complaints[${newIndex}][cost]" value="0" class="complaint-cost">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Deskripsi Rincian Kerusakan & Gejala <span class="text-[#FF7A3D]">*</span>
                    </label>
                    <textarea name="complaints[${newIndex}][description]" rows="3" placeholder="Jelaskan kondisi keluhan kendala, riwayat kilometer, atau rincian kerusakan..." class="complaint-desc w-full text-xs p-3.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 leading-relaxed"></textarea>
                </div>

                <!-- Upload Foto Khusus Keluhan Ini (Wajib) -->
                <div class="pt-2 border-t border-dashed border-slate-200">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-slate-700">
                            Unggah Foto Bukti Kerusakan untuk Keluhan Ini <span class="text-[#FF7A3D]">*</span>
                        </label>
                        <span class="text-[11px] text-slate-400">Format: JPG, PNG, PDF</span>
                    </div>

                    <!-- Hidden real input -->
                    <input type="file" name="complaint_photos_${newIndex}[]" multiple accept=".pdf,.jpg,.jpeg,.png" onchange="handleComplaintPhotosChange(this)" class="complaint-photo-input hidden">

                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Custom Tombol Tambah Foto -->
                        <button type="button" onclick="triggerComplaintPhotoUpload(this)" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 border-dashed border-[#015471]/40 hover:border-[#015471] bg-[#015471]/5 hover:bg-[#015471]/10 text-[#015471] font-bold text-xs transition shadow-2xs">
                            <i data-lucide="camera" class="w-4 h-4 text-[#FF7A3D]"></i>
                            <span>+ Tambah Foto / Bukti</span>
                        </button>
                        <span class="photo-count-badge text-xs font-semibold text-slate-500">Belum ada foto dipilih</span>
                    </div>

                    <!-- Visual Image Preview Grid -->
                    <div class="complaint-photo-preview mt-3 hidden grid grid-cols-2 sm:grid-cols-4 gap-3"></div>
                </div>
            </div>
        `;
        
        container.appendChild(card);
        renumberComplaints();
        calculateTotalEstimatedCost();
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function removeComplaintItem(button) {
        const card = button.closest('.complaint-card');
        if (card) {
            card.remove();
            renumberComplaints();
            calculateTotalEstimatedCost();
            updateSummary();
        }
    }

    function renumberComplaints() {
        const cards = document.querySelectorAll('.complaint-card');
        cards.forEach((card, idx) => {
            const badge = card.querySelector('.complaint-badge');
            const label = card.querySelector('.complaint-label');
            const btnRemove = card.querySelector('.btn-remove-complaint');

            if (badge) badge.innerText = idx + 1;
            if (label) label.innerText = `Keluhan / Perbaikan #${idx + 1}`;

            // Show remove button only if there are more than 1 complaints
            if (btnRemove) {
                if (cards.length > 1) {
                    btnRemove.classList.remove('hidden');
                } else {
                    btnRemove.classList.add('hidden');
                }
            }
        });
    }

    function onComplaintCategoryChange(selectElem) {
        const card = selectElem.closest('.complaint-card');
        const titleInput = card.querySelector('.complaint-title');
        const descInput = card.querySelector('.complaint-desc');
        const cat = selectElem.value;

        if (cat === 'servis_berkala') {
            titleInput.placeholder = 'Contoh: Servis Rutin & Ganti Oli Mesin';
            descInput.placeholder = 'Jelaskan riwayat pemakaian, kilometer servis terakhir, dan keluhan performa tarikan mesin...';
        } else if (cat === 'ganti_ban') {
            titleInput.placeholder = 'Contoh: Penggantian Ban Luar Belakang & Depan';
            descInput.placeholder = 'Jelaskan kondisi ban (gundul, robek, atau retak) dan kebutuhan tipe ban untuk medan kebun...';
        } else if (cat === 'ganti_accu') {
            titleInput.placeholder = 'Contoh: Penggantian Accu / Aki Kering 12V';
            descInput.placeholder = 'Jelaskan kendala starter elektrik, tegangan aki yang drop, atau kelistrikan motor...';
        } else if (cat === 'sparepart_fast_moving') {
            titleInput.placeholder = 'Contoh: Penggantian Kampas Rem, Rantai & Gir Set';
            descInput.placeholder = 'Sebutkan rincian sparepart yang aus dan perlu diganti segera untuk keselamatan operasional...';
        }
    }

    // Map to keep DataTransfer accumulator per card index so user can add multiple photos incrementally
    const complaintFilesMap = new Map();

    function getCardDataTransfer(card) {
        const idx = card.getAttribute('data-index');
        if (!complaintFilesMap.has(idx)) {
            complaintFilesMap.set(idx, new DataTransfer());
        }
        return complaintFilesMap.get(idx);
    }

    function triggerComplaintPhotoUpload(btn) {
        const card = btn.closest('.complaint-card');
        const input = card.querySelector('.complaint-photo-input');
        if (input) input.click();
    }

    function handleComplaintPhotosChange(input) {
        const card = input.closest('.complaint-card');
        const dt = getCardDataTransfer(card);

        if (input.files && input.files.length > 0) {
            Array.from(input.files).forEach(file => {
                // Avoid exact duplicate file names and sizes if already present
                let exists = false;
                for (let i = 0; i < dt.files.length; i++) {
                    if (dt.files[i].name === file.name && dt.files[i].size === file.size) {
                        exists = true;
                        break;
                    }
                }
                if (!exists) {
                    dt.items.add(file);
                }
            });
            // Update input files with combined list
            input.files = dt.files;
        }

        renderComplaintPhotosPreview(card);
    }

    function removeSingleComplaintPhoto(btn, fileIndex) {
        const card = btn.closest('.complaint-card');
        const dt = getCardDataTransfer(card);
        const input = card.querySelector('.complaint-photo-input');

        const newDt = new DataTransfer();
        for (let i = 0; i < dt.files.length; i++) {
            if (i !== fileIndex) {
                newDt.items.add(dt.files[i]);
            }
        }
        const cardIdx = card.getAttribute('data-index');
        complaintFilesMap.set(cardIdx, newDt);
        input.files = newDt.files;

        renderComplaintPhotosPreview(card);
    }

    function renderComplaintPhotosPreview(card) {
        const previewContainer = card.querySelector('.complaint-photo-preview');
        const countBadge = card.querySelector('.photo-count-badge');
        const dt = getCardDataTransfer(card);
        const files = dt.files;

        previewContainer.innerHTML = '';

        if (files && files.length > 0) {
            const count = files.length;
            countBadge.innerText = `${count} berkas foto dipilih`;
            countBadge.className = 'photo-count-badge text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200';
            previewContainer.classList.remove('hidden');

            Array.from(files).forEach((file, fileIdx) => {
                const item = document.createElement('div');
                item.className = 'relative group rounded-2xl border border-slate-200 bg-white p-2 overflow-hidden shadow-2xs hover:border-[#015471]/50 transition';

                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        item.innerHTML = `
                            <div class="w-full h-24 rounded-xl overflow-hidden bg-slate-100 relative mb-1.5 border border-slate-100">
                                <img src="${e.target.result}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" alt="${file.name}">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-1.5">
                                    <span class="text-[9px] text-white truncate font-medium">${(file.size / 1024).toFixed(0)} KB</span>
                                </div>
                                <button type="button" onclick="removeSingleComplaintPhoto(this, ${fileIdx})" title="Hapus foto ini" class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center shadow-md transition">
                                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                            <span class="block truncate text-[10px] font-bold text-slate-700">${file.name}</span>
                        `;
                        if (typeof lucide !== 'undefined') lucide.createIcons();
                    };
                    reader.readAsDataURL(file);
                } else {
                    // PDF or other documents
                    item.innerHTML = `
                        <div class="w-full h-24 rounded-xl bg-slate-100 border border-slate-200 flex flex-col items-center justify-center p-2 text-center mb-1.5 relative">
                            <i data-lucide="file-text" class="w-7 h-7 text-[#015471] mb-1"></i>
                            <span class="text-[9px] font-bold text-slate-500 uppercase">${file.name.split('.').pop()}</span>
                            <button type="button" onclick="removeSingleComplaintPhoto(this, ${fileIdx})" title="Hapus berkas ini" class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center shadow-md transition">
                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                        <span class="block truncate text-[10px] font-bold text-slate-700">${file.name}</span>
                    `;
                }

                previewContainer.appendChild(item);
            });

            if (typeof lucide !== 'undefined') lucide.createIcons();
        } else {
            countBadge.innerText = 'Belum ada foto dipilih';
            countBadge.className = 'photo-count-badge text-xs font-semibold text-slate-500';
            previewContainer.classList.add('hidden');
        }
    }

    function updateStepUI() {
        for (let i = 1; i <= totalSteps; i++) {
            const content = document.getElementById(`stepContent${i}`);
            const tab = document.getElementById(`stepTab${i}`);
            const icon = tab.querySelector('.step-icon');
            const title = tab.querySelector('.step-title');

            if (i === currentStep) {
                content.classList.remove('hidden');
                icon.className = 'w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-[#015471] text-white font-black text-xs sm:text-sm flex items-center justify-center shadow-md transition-all duration-200 step-icon ring-4 ring-[#015471]/10';
                title.className = 'text-[11px] sm:text-xs font-black text-slate-900 mt-2 step-title';
            } else if (i < currentStep) {
                content.classList.add('hidden');
                icon.className = 'w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-emerald-600 text-white font-black text-xs sm:text-sm flex items-center justify-center shadow-xs transition-all duration-200 step-icon';
                icon.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i>';
                title.className = 'text-[11px] sm:text-xs font-bold text-slate-700 mt-2 step-title';
            } else {
                content.classList.add('hidden');
                icon.className = 'w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-slate-100 text-slate-400 font-black text-xs sm:text-sm flex items-center justify-center transition-all duration-200 step-icon';
                icon.innerHTML = `<span>${i}</span>`;
                title.className = 'text-[11px] sm:text-xs font-bold text-slate-400 mt-2 step-title';
            }
        }

        // Handle buttons
        const btnPrev = document.getElementById('btnPrev');
        const prevSpacer = document.getElementById('prevSpacer');
        const btnNext = document.getElementById('btnNext');
        const btnSubmit = document.getElementById('btnSubmit');

        if (currentStep === 1) {
            if (btnPrev) {
                btnPrev.classList.add('hidden');
                btnPrev.style.display = 'none';
            }
            if (prevSpacer) prevSpacer.classList.remove('hidden');
        } else {
            if (btnPrev) {
                btnPrev.classList.remove('hidden');
                btnPrev.style.display = 'inline-flex';
            }
            if (prevSpacer) prevSpacer.classList.add('hidden');
        }

        if (currentStep === totalSteps) {
            if (btnNext) {
                btnNext.classList.add('hidden');
                btnNext.style.display = 'none';
            }
            if (btnSubmit) {
                btnSubmit.classList.remove('hidden');
                btnSubmit.style.display = 'inline-flex';
            }
            updateSummary();
        } else {
            if (btnNext) {
                btnNext.classList.remove('hidden');
                btnNext.style.display = 'inline-flex';
                btnNext.querySelector('span').innerText = `Lanjutkan ke Tahap ${currentStep + 1}`;
            }
            if (btnSubmit) {
                btnSubmit.classList.add('hidden');
                btnSubmit.style.display = 'none';
            }
        }

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function validateStep(step, showSwal = true) {
        if (step === 1) {
            const vehicle = document.getElementById('vehicle_id').value;
            if (!vehicle) {
                if (showSwal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Armada Motor',
                        text: 'Silakan pilih unit armada kendaraan yang mengalami kendala terlebih dahulu pada Tahap 1.',
                        confirmButtonColor: '#015471',
                        confirmButtonText: 'Pilih Motor'
                    }).then(() => {
                        const trigger = document.getElementById('vehicleDropdownTrigger');
                        if (trigger) trigger.focus();
                    });
                }
                return false;
            }

            const requestType = document.querySelector('input[name="request_type"]:checked');
            if (!requestType) {
                if (showSwal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih Kategori Kendala',
                        text: 'Silakan pilih salah satu opsi kendala (Perbaikan/Service, Unit Pengganti, atau Lapor Kehilangan) pada Tahap 1.',
                        confirmButtonColor: '#015471',
                        confirmButtonText: 'Pilih Kategori'
                    });
                }
                return false;
            }
        } else if (step === 2) {
            const type = getSelectedType();

            if (type === 'perbaikan_servis') {
                const complaintCards = document.querySelectorAll('.complaint-card');
                if (complaintCards.length === 0) {
                    if (showSwal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tambahkan Keluhan',
                            text: 'Silakan tambahkan minimal 1 rincian keluhan perbaikan pada Tahap 2.',
                            confirmButtonColor: '#015471',
                            confirmButtonText: '+ Tambah Keluhan'
                        });
                    }
                    return false;
                }

                let allValid = true;
                let firstInvalidInput = null;
                let invalidReason = '';
                let missingPhoto = false;
                let missingPhotoCard = null;
                let missingPhotoIdx = 0;
                let summaryTitles = [];
                let summaryDescs = [];
                let totalCost = 0;

                complaintCards.forEach((card, idx) => {
                    const titleInput = card.querySelector('.complaint-title');
                    const descInput = card.querySelector('.complaint-desc');
                    const title = titleInput ? titleInput.value.trim() : '';
                    const desc = descInput ? descInput.value.trim() : '';
                    const costInput = card.querySelector('.complaint-cost');
                    const costVal = parseFloat(costInput ? costInput.value : 0) || 0;
                    totalCost += costVal;
                    const costFormatted = costVal > 0 ? ` (Estimasi: Rp ${Number(costVal).toLocaleString('id-ID')})` : '';

                    const photoInput = card.querySelector('.complaint-photo-input');
                    const dt = getCardDataTransfer(card);
                    const hasPhotos = (dt && dt.files && dt.files.length > 0) || (photoInput && photoInput.files && photoInput.files.length > 0);

                    const categorySelect = card.querySelector('.complaint-category');
                    const categoryText = categorySelect.options[categorySelect.selectedIndex]?.text || '';

                    if (!title) {
                        allValid = false;
                        if (!firstInvalidInput) {
                            firstInvalidInput = titleInput;
                            invalidReason = `Judul keluhan / komponen rusak pada Keluhan #${idx + 1} belum diisi.`;
                        }
                    } else if (!desc) {
                        allValid = false;
                        if (!firstInvalidInput) {
                            firstInvalidInput = descInput;
                            invalidReason = `Deskripsi rincian kerusakan pada Keluhan #${idx + 1} belum diisi.`;
                        }
                    } else if (!hasPhotos && !missingPhoto) {
                        missingPhoto = true;
                        missingPhotoCard = card;
                        missingPhotoIdx = idx + 1;
                    } else {
                        summaryTitles.push(title);
                        summaryDescs.push(`[Keluhan #${idx + 1}: ${categoryText}]\nJudul: ${title}${costFormatted}\nUraian: ${desc}`);
                    }
                });

                calculateTotalEstimatedCost();

                if (!allValid) {
                    if (showSwal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Lengkapi Data Keluhan (Tahap 2)',
                            text: invalidReason || 'Mohon lengkapi judul dan deskripsi pada setiap kartu keluhan sebelum melanjutkan.',
                            confirmButtonColor: '#015471',
                            confirmButtonText: 'Lengkapi Sekarang'
                        }).then(() => {
                            if (firstInvalidInput) firstInvalidInput.focus();
                        });
                    }
                    return false;
                }

                if (missingPhoto) {
                    if (showSwal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Foto Kerusakan Wajib Diunggah (Tahap 2)',
                            text: `Mohon lampirkan minimal 1 foto bukti fisik kerusakan pada Keluhan #${missingPhotoIdx}.`,
                            confirmButtonColor: '#015471',
                            confirmButtonText: 'Unggah Foto'
                        }).then(() => {
                            if (missingPhotoCard) {
                                const photoBtn = missingPhotoCard.querySelector('button[onclick*="triggerComplaintPhotoUpload"]');
                                if (photoBtn) photoBtn.focus();
                            }
                        });
                    }
                    return false;
                }

                document.getElementById('formTitle').value = summaryTitles.join(', ');
                document.getElementById('formDescription').value = summaryDescs.join("\n\n");

            } else if (type === 'unit_pengganti') {
                const titleInput = document.getElementById('replacement_title');
                const descInput = document.getElementById('replacement_description');
                const dropLocInput = document.getElementById('drop_location');

                const title = titleInput ? titleInput.value.trim() : '';
                const desc = descInput ? descInput.value.trim() : '';
                const dropLoc = dropLocInput ? dropLocInput.value.trim() : '';

                if (!title) {
                    if (showSwal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Judul Permohonan Belum Diisi',
                            text: 'Mohon isi judul permohonan unit pengganti sementara pada Tahap 2.',
                            confirmButtonColor: '#015471',
                            confirmButtonText: 'Isi Judul'
                        }).then(() => {
                            if (titleInput) titleInput.focus();
                        });
                    }
                    return false;
                }
                if (!desc) {
                    if (showSwal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Uraian Kebutuhan Belum Diisi',
                            text: 'Mohon isi uraian kebutuhan unit pengganti pada Tahap 2.',
                            confirmButtonColor: '#015471',
                            confirmButtonText: 'Isi Uraian'
                        }).then(() => {
                            if (descInput) descInput.focus();
                        });
                    }
                    return false;
                }
                if (!dropLoc) {
                    if (showSwal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Titik Antar Diperlukan',
                            text: 'Mohon cantumkan titik antar / drop point armada pengganti pada Tahap 2.',
                            confirmButtonColor: '#015471',
                            confirmButtonText: 'Isi Titik Antar'
                        }).then(() => {
                            if (dropLocInput) dropLocInput.focus();
                        });
                    }
                    return false;
                }

                const reasonSelect = document.getElementById('replacement_reason');
                const reasonText = reasonSelect.options[reasonSelect.selectedIndex]?.text || '';
                document.getElementById('formTitle').value = title;
                document.getElementById('formDescription').value = `[Alasan: ${reasonText}]\n[Titik Drop: ${dropLoc}]\n${desc}`;

            } else if (type === 'lapor_kehilangan') {
                const titleInput = document.getElementById('loss_title');
                const descInput = document.getElementById('loss_description');
                const incidentTimeInput = document.getElementById('incident_time');
                const incidentLocInput = document.getElementById('incident_location');

                const title = titleInput ? titleInput.value.trim() : '';
                const desc = descInput ? descInput.value.trim() : '';
                const incidentTime = incidentTimeInput ? incidentTimeInput.value : '';
                const incidentLoc = incidentLocInput ? incidentLocInput.value.trim() : '';

                if (!title) {
                    if (showSwal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Judul Laporan Belum Diisi',
                            text: 'Mohon isi judul laporan kehilangan unit pada Tahap 2.',
                            confirmButtonColor: '#015471',
                            confirmButtonText: 'Isi Judul'
                        }).then(() => {
                            if (titleInput) titleInput.focus();
                        });
                    }
                    return false;
                }
                if (!desc) {
                    if (showSwal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Kronologi Belum Diisi',
                            text: 'Mohon isi kronologi lengkap kejadian kehilangan pada Tahap 2.',
                            confirmButtonColor: '#015471',
                            confirmButtonText: 'Isi Kronologi'
                        }).then(() => {
                            if (descInput) descInput.focus();
                        });
                    }
                    return false;
                }
                if (!incidentTime) {
                    if (showSwal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Waktu Kejadian Belum Diisi',
                            text: 'Mohon masukkan tanggal dan waktu insiden kehilangan pada Tahap 2.',
                            confirmButtonColor: '#015471',
                            confirmButtonText: 'Isi Waktu'
                        }).then(() => {
                            if (incidentTimeInput) incidentTimeInput.focus();
                        });
                    }
                    return false;
                }
                if (!incidentLoc) {
                    if (showSwal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Lokasi TKP Belum Diisi',
                            text: 'Mohon masukkan lokasi tempat kejadian perkara (TKP) kehilangan pada Tahap 2.',
                            confirmButtonColor: '#015471',
                            confirmButtonText: 'Isi Lokasi'
                        }).then(() => {
                            if (incidentLocInput) incidentLocInput.focus();
                        });
                    }
                    return false;
                }

                document.getElementById('formTitle').value = title;
                document.getElementById('formDescription').value = `[Waktu Kejadian: ${incidentTime}]\n[TKP Lokasi: ${incidentLoc}]\n${desc}`;
            }
        } else if (step === 3) {
            const odoInput = document.getElementById('odometer_km');
            const odoValue = odoInput ? odoInput.value.trim() : '';
            const type = getSelectedType();

            // Odometer is required for all requests (especially repairs & unit replacements)
            if (!odoValue) {
                if (showSwal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Kilometer Odometer Wajib Diisi (Tahap 3)',
                        text: 'Mohon masukkan angka kilometer terakhir pada speedometer motor Anda pada Tahap 3.',
                        confirmButtonColor: '#015471',
                        confirmButtonText: 'Isi Odometer'
                    }).then(() => {
                        if (odoInput) odoInput.focus();
                    });
                }
                return false;
            }

            if (isNaN(odoValue) || Number(odoValue) < 0) {
                if (showSwal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Angka Odometer Tidak Valid (Tahap 3)',
                        text: 'Silakan masukkan angka kilometer yang valid (bilangan positif).',
                        confirmButtonColor: '#015471',
                        confirmButtonText: 'Perbaiki Angka'
                    }).then(() => {
                        if (odoInput) odoInput.focus();
                    });
                }
                return false;
            }

            const picInput = document.getElementById('pic_name');
            const picPhoneInput = document.getElementById('pic_phone');
            const pic = picInput ? picInput.value.trim() : '';
            const picPhone = picPhoneInput ? picPhoneInput.value.trim() : '';

            if (!pic) {
                if (showSwal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Nama PIC Diperlukan (Tahap 3)',
                        text: 'Mohon isi nama PIC penanggung jawab lapangan pada Tahap 3.',
                        confirmButtonColor: '#015471',
                        confirmButtonText: 'Isi Nama PIC'
                    }).then(() => {
                        if (picInput) picInput.focus();
                    });
                }
                return false;
            }

            if (!picPhone) {
                if (showSwal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No. WhatsApp PIC Diperlukan (Tahap 3)',
                        text: 'Mohon isi nomor HP/WhatsApp PIC lapangan untuk konfirmasi operasional pada Tahap 3.',
                        confirmButtonColor: '#015471',
                        confirmButtonText: 'Isi No. HP PIC'
                    }).then(() => {
                        if (picPhoneInput) picPhoneInput.focus();
                    });
                }
                return false;
            }

            // Workshop name is required for perbaikan_servis
            if (type === 'perbaikan_servis') {
                const workshopInput = document.getElementById('workshop_name');
                const workshopName = workshopInput ? workshopInput.value.trim() : '';
                if (!workshopName) {
                    if (showSwal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Nama Bengkel / AHASS Wajib Diisi (Tahap 3)',
                            text: 'Mohon masukkan nama bengkel rujukan atau AHASS yang dituju untuk perbaikan unit pada Tahap 3.',
                            confirmButtonColor: '#015471',
                            confirmButtonText: 'Isi Nama Bengkel'
                        }).then(() => {
                            if (workshopInput) workshopInput.focus();
                        });
                    }
                    return false;
                }
            }
        }
        return true;
    }

    function validateAllStepsBeforeSubmit() {
        for (let s = 1; s <= totalSteps; s++) {
            if (!validateStep(s, false)) {
                goToStep(s);
                // Trigger full Swal with step context
                validateStep(s, true);
                return false;
            }
        }
        return true;
    }

    function navigateStep(delta) {
        if (delta > 0) {
            if (!validateStep(currentStep)) {
                return;
            }
        }
        currentStep = Math.min(Math.max(currentStep + delta, 1), totalSteps);
        updateStepUI();
    }

    function goToStep(step) {
        if (step === currentStep) return;

        if (step > currentStep) {
            for (let i = currentStep; i < step; i++) {
                if (!validateStep(i)) return;
            }
        }
        currentStep = step;
        updateStepUI();
    }

    // Searchable Vehicle Dropdown Functions
    let selectedVehicleFullText = '';

    function toggleVehicleDropdown() {
        const panel = document.getElementById('vehicleDropdownPanel');
        const chevron = document.getElementById('vehicleDropdownChevron');
        const isHidden = panel.classList.contains('hidden');

        if (isHidden) {
            panel.classList.remove('hidden');
            chevron.style.transform = 'rotate(180deg)';
            setTimeout(() => {
                const searchInput = document.getElementById('vehicleSearchInput');
                if (searchInput) searchInput.focus();
            }, 50);
        } else {
            closeVehicleDropdown();
        }
    }

    function closeVehicleDropdown() {
        const panel = document.getElementById('vehicleDropdownPanel');
        const chevron = document.getElementById('vehicleDropdownChevron');
        if (panel) panel.classList.add('hidden');
        if (chevron) chevron.style.transform = 'rotate(0deg)';
    }

    function filterVehicleList() {
        const query = document.getElementById('vehicleSearchInput').value.toLowerCase().trim();
        const items = document.querySelectorAll('.vehicle-option-item');
        const btnClear = document.getElementById('btnClearSearch');
        const counter = document.getElementById('vehicleCounter');
        const noResult = document.getElementById('vehicleNoResult');

        if (query) {
            btnClear.classList.remove('hidden');
        } else {
            btnClear.classList.add('hidden');
        }

        let visibleCount = 0;
        items.forEach(item => {
            const keywords = item.getAttribute('data-keywords') || '';
            if (keywords.includes(query)) {
                item.classList.remove('hidden');
                visibleCount++;
            } else {
                item.classList.add('hidden');
            }
        });

        if (counter) counter.innerText = visibleCount;
        if (noResult) {
            if (visibleCount === 0) {
                noResult.classList.remove('hidden');
            } else {
                noResult.classList.add('hidden');
            }
        }
    }

    function clearVehicleSearch() {
        const input = document.getElementById('vehicleSearchInput');
        if (input) {
            input.value = '';
            input.focus();
            filterVehicleList();
        }
    }

    function selectVehicle(id, label, holder, holderPhone, defaultPic, defaultPicPhone) {
        document.getElementById('vehicle_id').value = id;
        document.getElementById('selectedVehicleLabel').innerHTML = `<span class="text-slate-900 font-extrabold">${label}</span>`;
        document.getElementById('clearVehicleSelection').classList.remove('hidden');
        selectedVehicleFullText = label;

        // Auto-fill and lock driver name and phone
        if (holder) {
            document.getElementById('driver_name').value = holder;
        }
        if (holderPhone) {
            document.getElementById('driver_phone').value = holderPhone;
        }

        // Auto-fill default PIC if empty
        const picInput = document.getElementById('pic_name');
        const picPhoneInput = document.getElementById('pic_phone');
        if (picInput && !picInput.value.trim() && defaultPic) {
            picInput.value = defaultPic;
        }
        if (picPhoneInput && !picPhoneInput.value.trim() && defaultPicPhone) {
            picPhoneInput.value = defaultPicPhone;
        }

        // Update active checkmarks
        document.querySelectorAll('.vehicle-option-item').forEach(item => {
            const check = item.querySelector('.option-check');
            if (item.getAttribute('data-id') === String(id)) {
                item.classList.add('bg-[#015471]/10', 'text-[#015471]');
                if (check) check.classList.remove('hidden');
            } else {
                item.classList.remove('bg-[#015471]/10', 'text-[#015471]');
                if (check) check.classList.add('hidden');
            }
        });

        closeVehicleDropdown();
        updateSummary();
    }

    function resetVehicleSelection() {
        document.getElementById('vehicle_id').value = '';
        document.getElementById('selectedVehicleLabel').innerText = '-- Pilih Unit Armada Motor --';
        document.getElementById('clearVehicleSelection').classList.add('hidden');
        selectedVehicleFullText = '';

        document.getElementById('driver_name').value = '';
        document.getElementById('driver_phone').value = '';

        document.querySelectorAll('.vehicle-option-item').forEach(item => {
            item.classList.remove('bg-[#015471]/10', 'text-[#015471]');
            const check = item.querySelector('.option-check');
            if (check) check.classList.add('hidden');
        });

        updateSummary();
    }

    // Close dropdown on click outside
    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('searchableVehicleWrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            closeVehicleDropdown();
        }
    });

    function updateSummary() {
        document.getElementById('summaryVehicle').innerText = selectedVehicleFullText || '-';

        const type = getSelectedType();
        let typeText = 'Pengajuan Perbaikan / Servis';
        if (type === 'unit_pengganti') {
            typeText = 'Pengajuan Unit Pengganti Sementara';
        } else if (type === 'lapor_kehilangan') {
            typeText = 'Laporan Kehilangan Unit (TLO)';
        } else {
            const cat = document.getElementById('repair_category')?.value;
            if (cat === 'ganti_ban') typeText = 'Pengajuan Pergantian Ban';
            else if (cat === 'ganti_accu') typeText = 'Pengajuan Pergantian Accu';
            else if (cat === 'sparepart_fast_moving') typeText = 'Pengajuan Sparepart Fast Moving';
            else typeText = 'Pengajuan Perbaikan / Servis Berkala';
        }
        document.getElementById('summaryType').innerText = typeText;

        const title = document.getElementById('formTitle').value.trim() || '-';
        document.getElementById('summaryTitle').innerText = title;

        const driver = document.getElementById('driver_name').value.trim() || '-';
        const pic = document.getElementById('pic_name').value.trim();
        const picPhone = document.getElementById('pic_phone').value.trim();
        
        let driverSummary = driver;
        if (pic) {
            driverSummary += ` | PIC: ${pic}` + (picPhone ? ` (${picPhone})` : '');
        }
        document.getElementById('summaryDriver').innerText = driverSummary;

        // Odometer KM in summary
        const odo = document.getElementById('odometer_km')?.value.trim();
        const summaryOdometerElem = document.getElementById('summaryOdometer');
        if (summaryOdometerElem) {
            summaryOdometerElem.innerText = odo ? `${Number(odo).toLocaleString('id-ID')} KM` : '-';
        }

        // Vehicle Operational Status in summary
        const statusSelect = document.getElementById('vehicle_operational_status');
        const summaryConditionElem = document.getElementById('summaryCondition');
        if (statusSelect && summaryConditionElem) {
            const statusText = statusSelect.options[statusSelect.selectedIndex]?.text || '-';
            summaryConditionElem.innerText = statusText;
        }

        // Total Estimasi Biaya in summary
        const estCostInput = document.getElementById('estimated_cost');
        const summaryCostElem = document.getElementById('summaryCost');
        if (summaryCostElem) {
            const currentTotal = parseFloat(estCostInput ? estCostInput.value : 0) || 0;
            summaryCostElem.innerText = `Rp ${Number(currentTotal).toLocaleString('id-ID')}`;
        }
    }

    function previewVehiclePhoto(input, fieldKey) {
        const previewBox = document.getElementById(`preview_box_${fieldKey}`);
        const imgElem = document.getElementById(`img_preview_${fieldKey}`);
        const labelText = document.getElementById(`upload_label_${fieldKey}`);

        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                if (imgElem) imgElem.src = e.target.result;
                if (previewBox) previewBox.classList.remove('hidden');
                if (labelText) {
                    labelText.innerText = 'Ganti Foto';
                }
                if (typeof lucide !== 'undefined') lucide.createIcons();
            };
            reader.readAsDataURL(file);
        }
    }

    function removeSingleVehiclePhoto(fieldKey) {
        const input = document.getElementById(fieldKey);
        const previewBox = document.getElementById(`preview_box_${fieldKey}`);
        const imgElem = document.getElementById(`img_preview_${fieldKey}`);
        const labelText = document.getElementById(`upload_label_${fieldKey}`);

        if (input) input.value = '';
        if (imgElem) imgElem.src = '';
        if (previewBox) previewBox.classList.add('hidden');
        if (labelText) {
            if (fieldKey === 'photo_front') labelText.innerText = 'Pilih Foto Depan';
            else if (fieldKey === 'photo_right') labelText.innerText = 'Pilih Foto Kanan';
            else if (fieldKey === 'photo_left') labelText.innerText = 'Pilih Foto Kiri';
            else if (fieldKey === 'photo_behind') labelText.innerText = 'Pilih Foto Belakang';
        }
    }

    function previewSelectedFiles(input) {
        const container = document.getElementById('file_list_preview');
        container.innerHTML = '';
        if (input.files && input.files.length > 0) {
            container.classList.remove('hidden');
            Array.from(input.files).forEach(file => {
                const item = document.createElement('div');
                item.className = 'p-2.5 rounded-xl bg-white border border-slate-200 flex items-center gap-2.5 shadow-2xs';
                item.innerHTML = `
                    <div class="w-7 h-7 rounded-lg bg-[#015471]/10 text-[#015471] flex items-center justify-center shrink-0">
                        <i data-lucide="file-image" class="w-3.5 h-3.5"></i>
                    </div>
                    <span class="truncate font-bold text-slate-800 flex-1">${file.name}</span>
                    <span class="text-[10px] text-slate-400 font-mono">${(file.size / 1024 / 1024).toFixed(1)} MB</span>
                `;
                container.appendChild(item);
            });
            if (typeof lucide !== 'undefined') lucide.createIcons();
        } else {
            container.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        onTypeChanged();
        updateStepUI();

        const form = document.getElementById('clientWizardForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!validateAllStepsBeforeSubmit()) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }

                // Show confirmation popup before actual submission
                e.preventDefault();
                const totalCostText = document.getElementById('summaryCost')?.innerText || 'Rp 0';
                const vehicleLabel = document.getElementById('summaryVehicle')?.innerText || '-';
                const typeLabel = document.getElementById('summaryType')?.innerText || '-';

                Swal.fire({
                    title: 'Kirim Pengajuan Sekarang?',
                    html: `
                        <div class="text-left text-xs space-y-2 p-2 bg-slate-50 rounded-xl border border-slate-200 mt-2">
                            <div><strong class="text-slate-500">Armada Kendaraan:</strong> <span class="font-bold text-slate-900">${vehicleLabel}</span></div>
                            <div><strong class="text-slate-500">Jenis Pengajuan:</strong> <span class="font-bold text-[#015471]">${typeLabel}</span></div>
                            <div><strong class="text-slate-500">Total Estimasi Biaya:</strong> <span class="font-black text-[#FF7A3D] font-mono">${totalCostText}</span></div>
                            <p class="text-[11px] text-slate-500 pt-1 border-t border-slate-200">Pengajuan Anda akan diteruskan ke tim operasional PT SUP untuk ditinjau dan disetujui.</p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#FF7A3D',
                    cancelButtonColor: '#64748B',
                    confirmButtonText: '<i data-lucide="send" class="w-4 h-4 inline-block mr-1"></i> Ya, Kirim Pengajuan',
                    cancelButtonText: 'Periksa Kembali',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        }

        // Initial label setup if preselected
        const initialVehicleId = document.getElementById('vehicle_id').value;
        if (initialVehicleId) {
            const preselectedItem = document.querySelector(`.vehicle-option-item[data-id="${initialVehicleId}"]`);
            if (preselectedItem) {
                const label = preselectedItem.getAttribute('data-label');
                const holder = preselectedItem.getAttribute('data-holder');
                const holderPhone = preselectedItem.getAttribute('data-holderphone');
                const pic = preselectedItem.getAttribute('data-pic');
                const picPhone = preselectedItem.getAttribute('data-picphone');
                selectVehicle(initialVehicleId, label, holder, holderPhone, pic, picPhone);
            }
        }
    });
</script>
@endpush
