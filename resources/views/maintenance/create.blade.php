@extends('layouts.app')

@section('title', 'Formulir Catat Pemeliharaan Armada')
@section('page_title', 'Catat Pemeliharaan & Operasional Armada')

@section('content')
<div class="w-full space-y-6">

    <form action="{{ route('maintenance.store') }}" method="POST" enctype="multipart/form-data" id="maintenanceWizardForm">
        @csrf
        <input type="hidden" name="existing_proof_files" id="existing_proof_files" value="">

        <!-- Top Header Action & Stepper Card -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-5 sm:p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#015471] to-[#023b4f] text-white flex items-center justify-center shadow-sm shrink-0">
                        <i data-lucide="wrench" class="w-6 h-6 text-[#FF7A3D]"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h1 class="text-xl font-black text-slate-900 tracking-tight">Formulir Catat Pemeliharaan Armada</h1>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-[#FF7A3D]/10 text-[#FF7A3D] border border-[#FF7A3D]/20">
                                4 Tahap Lengkap
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Catat riwayat servis berkala, multi-keluhan penggantian suku cadang, foto bukti fisik, mekanik & sinkronisasi kas otomatis</p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 shrink-0">
                    <a href="{{ route('maintenance.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold rounded-xl border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 transition shadow-2xs">
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
                        <span class="text-[11px] sm:text-xs font-black text-slate-900 mt-2 step-title">Kendaraan & Kategori</span>
                        <span class="hidden sm:inline text-[10px] text-slate-400 font-medium">Pilih armada & tipe</span>
                    </div>

                    <!-- Step 2 Indicator -->
                    <div class="step-indicator flex flex-col items-center text-center group cursor-pointer" onclick="goToStep(2)" id="stepTab2">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-slate-100 text-slate-400 font-black text-xs sm:text-sm flex items-center justify-center transition-all duration-200 step-icon">
                            <span>2</span>
                        </div>
                        <span class="text-[11px] sm:text-xs font-bold text-slate-500 mt-2 step-title">Rincian & Keluhan</span>
                        <span class="hidden sm:inline text-[10px] text-slate-400 font-medium" id="step2Subtext">Multi-Sub Kategori</span>
                    </div>

                    <!-- Step 3 Indicator -->
                    <div class="step-indicator flex flex-col items-center text-center group cursor-pointer" onclick="goToStep(3)" id="stepTab3">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-slate-100 text-slate-400 font-black text-xs sm:text-sm flex items-center justify-center transition-all duration-200 step-icon">
                            <span>3</span>
                        </div>
                        <span class="text-[11px] sm:text-xs font-bold text-slate-500 mt-2 step-title">Bengkel & Lapangan</span>
                        <span class="hidden sm:inline text-[10px] text-slate-400 font-medium">PIC, Mekanik & KM</span>
                    </div>

                    <!-- Step 4 Indicator -->
                    <div class="step-indicator flex flex-col items-center text-center group cursor-pointer" onclick="goToStep(4)" id="stepTab4">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-slate-100 text-slate-400 font-black text-xs sm:text-sm flex items-center justify-center transition-all duration-200 step-icon">
                            <span>4</span>
                        </div>
                        <span class="text-[11px] sm:text-xs font-bold text-slate-500 mt-2 step-title">Foto & Konfirmasi</span>
                        <span class="hidden sm:inline text-[10px] text-slate-400 font-medium">Nota, 4 Sudut & Kas</span>
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

        <!-- Card Referensi / Merujuk Tiket Pengajuan Klien (Opsional & Autofill Otomatis) -->
        <div class="bg-white rounded-3xl border-2 border-[#015471]/20 shadow-xs overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-[#015471]/5 via-white to-[#FF7A3D]/5 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-[#015471] text-white flex items-center justify-center text-xs shadow-2xs">
                        <i data-lucide="link-2" class="w-4 h-4 text-[#FF7A3D]"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-900">Merujuk Tiket Permohonan Klien (Opsional)</h2>
                        <p class="text-[11px] text-slate-500">Pilih tiket pengajuan masuk agar form otomatis terisi seluruh detailnya (unit, keluhan, bengkel, pic, biaya, km)</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-[#015471] bg-[#015471]/10 px-2.5 py-1 rounded-full border border-[#015471]/20">Autofill Otomatis</span>
            </div>

            <div class="p-6">
                <div class="relative">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Pilih Tiket Permohonan Klien Terkait:
                    </label>
                    <select id="client_request_selector" onchange="applyClientRequestAutofill(this.value)" class="w-full text-xs font-semibold px-4 py-3 rounded-2xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer shadow-2xs">
                        <option value="">-- Tidak Merujuk Tiket (Input Manual Baru) --</option>
                        @foreach($clientRequests as $cr)
                            @php
                                $crType = $cr->request_type_label;
                                $crPlate = $cr->vehicle ? $cr->vehicle->plate_number : 'Non-Unit';
                                $crCost = $cr->estimated_cost > 0 ? 'Rp ' . number_format($cr->estimated_cost, 0, ',', '.') : 'Rp 0';
                            @endphp
                            <option value="{{ $cr->id }}" {{ old('client_request_id', $selectedRequestId) == $cr->id ? 'selected' : '' }}>
                                #REQ-{{ str_pad($cr->id, 4, '0', STR_PAD_LEFT) }} &bull; {{ $crPlate }} — {{ $cr->title }} ({{ $crType }} - {{ $crCost }}) &bull; [{{ $cr->status_badge['label'] }}]
                            </option>
                        @endforeach
                    </select>
                </div>
                <div id="autofill_badge_alert" class="hidden mt-3 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-center justify-between">
                    <span class="flex items-center gap-2 font-bold">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                        Data tiket berhasil diimpor ke dalam formulir pemeliharaan.
                    </span>
                    <button type="button" onclick="clearClientRequestAutofill()" class="text-[11px] text-emerald-700 hover:text-emerald-900 underline font-semibold">
                        Batalkan Referensi
                    </button>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- STEP CONTAINER 1: Kendaraan & Kategori Operasional -->
        <!-- ========================================================================= -->
        <div class="step-content space-y-6" id="stepContent1">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-[#015471] text-white text-xs font-black flex items-center justify-center shadow-2xs">1</span>
                        <div>
                            <h2 class="text-sm font-black text-slate-900">Tahap 1: Unit Kendaraan & Kategori Pekerjaan</h2>
                            <p class="text-[11px] text-slate-500">Pilih armada target, tanggal pengerjaan & klasifikasi transaksi</p>
                        </div>
                    </div>
                    <span class="text-[11px] font-bold text-[#015471] bg-[#015471]/10 px-2.5 py-1 rounded-full">Langkah 1 dari 4</span>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <!-- Searchable Dropdown Unit Kendaraan (2 Cols) -->
                        <div class="lg:col-span-2 relative" id="searchableVehicleWrapper">
                            <label class="block text-xs font-bold text-slate-700 mb-2">
                                Pilih Unit Armada Kendaraan <span class="text-[#FF7A3D]">*</span>
                            </label>                            <!-- Hidden real input for form submission -->
                            <input type="hidden" name="vehicle_id" id="vehicle_id" value="{{ old('vehicle_id', $selectedVehicleId) }}">

                            @php
                                $preselectedVehicle = $vehicles->firstWhere('id', old('vehicle_id', $selectedVehicleId));
                                $initialLabel = $preselectedVehicle 
                                    ? $preselectedVehicle->plate_number . ' — ' . $preselectedVehicle->model_name . ' (' . ($preselectedVehicle->location ? $preselectedVehicle->location->name : 'Kebun') . ')'
                                    : '-- Pilih Unit Armada Motor --';
                            @endphp

                            <!-- Dropdown Trigger Button -->
                            <button type="button" id="vehicleDropdownTrigger" onclick="toggleVehicleDropdown()" class="w-full text-left text-xs font-semibold px-4 py-3 rounded-2xl border border-slate-300 bg-white hover:bg-slate-50/50 focus:outline-none focus:ring-2 focus:ring-[#015471] focus:border-[#015471] text-slate-900 flex items-center justify-between shadow-2xs transition">
                                <span id="selectedVehicleLabel" class="truncate font-bold text-slate-800">
                                    {{ $initialLabel }}
                                </span>
                                <div class="flex items-center gap-2 pl-2 shrink-0">
                                    <span id="clearVehicleSelection" onclick="event.stopPropagation(); resetVehicleSelection();" class="{{ $preselectedVehicle ? '' : 'hidden' }} text-[11px] text-slate-400 hover:text-rose-500 font-bold px-1 py-0.5 rounded transition">
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
                                            $isSelected = old('vehicle_id', $selectedVehicleId) == $v->id;
                                            $holderName = $v->user_holder ?: 'Pengemudi Lapangan';
                                            $holderPhone = $v->driver_phone ?: '0812-3456-7890';
                                        @endphp
                                        <div class="vehicle-option-item p-3 rounded-xl hover:bg-[#015471]/5 cursor-pointer transition flex items-center justify-between gap-3 {{ $isSelected ? 'bg-[#015471]/10 text-[#015471]' : 'text-slate-800' }}"
                                             data-id="{{ $v->id }}"
                                             data-plate="{{ $v->plate_number }}"
                                             data-model="{{ $v->brand ? $v->brand . ' ' : '' }}{{ $v->model_name }}"
                                             data-location="{{ $v->location ? $v->location->name : 'Lokasi Kebun' }}"
                                             data-holder="{{ $holderName }}"
                                             data-holderphone="{{ $holderPhone }}"
                                             data-label="{{ $fullLabel }}"
                                             data-keywords="{{ $searchKeywords }}"
                                             onclick="selectVehicle('{{ $v->id }}', '{{ addslashes($fullLabel) }}', '{{ addslashes($holderName) }}', '{{ addslashes($holderPhone) }}')">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div class="w-8 h-8 rounded-lg bg-[#015471]/10 text-[#015471] flex items-center justify-center shrink-0">
                                                    <i data-lucide="{{ ($v->type === 'mobil') ? 'car' : 'bike' }}" class="w-4 h-4 text-[#FF7A3D]"></i>
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
                            @error('vehicle_id') <span class="text-[#FF7A3D] text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                        </div>

                        <!-- Tanggal Pekerjaan / Nota -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">
                                Tanggal Pengerjaan / Nota <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <input type="date" name="date" id="maintenance_date" value="{{ old('date', date('Y-m-d')) }}" class="w-full text-xs font-mono font-bold px-4 py-3 rounded-2xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 shadow-2xs bg-white">
                            @error('date') <span class="text-[#FF7A3D] text-[11px] block mt-1 font-semibold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Radio Buttons Jenis Kategori Pekerjaan Pokok -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">
                            Pilih Kategori Transaksi Pemeliharaan <span class="text-[#FF7A3D]">*</span>
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Option 1: Perbaikan / Service -->
                            <label class="relative flex flex-col p-4 rounded-2xl border-2 border-slate-200 cursor-pointer hover:border-[#015471] transition group has-[:checked]:border-[#015471] has-[:checked]:bg-[#015471]/5 shadow-2xs">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="w-10 h-10 rounded-xl bg-[#015471]/10 text-[#015471] flex items-center justify-center group-hover:scale-105 transition-transform">
                                        <i data-lucide="wrench" class="w-5 h-5 text-[#FF7A3D]"></i>
                                    </div>
                                    <input type="radio" name="type" value="service_berkala" {{ old('type', $defaultType) === 'service_berkala' ? 'checked' : '' }} onchange="onTypeChanged()" class="mt-1 text-[#015471] focus:ring-[#015471] w-4 h-4">
                                </div>
                                <span class="font-black text-xs text-slate-900 block mb-1">Perbaikan / Servis & Sparepart</span>
                                <span class="text-[11px] text-slate-500 leading-snug">Servis berkala AHASS, penggantian ban, aki drop, atau sparepart multi-keluhan.</span>
                            </label>

                            <!-- Option 2: Biaya Sewa Unit Pengganti -->
                            <label class="relative flex flex-col p-4 rounded-2xl border-2 border-slate-200 cursor-pointer hover:border-[#FF7A3D] transition group has-[:checked]:border-[#FF7A3D] has-[:checked]:bg-[#FF7A3D]/5 shadow-2xs">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="w-10 h-10 rounded-xl bg-[#FF7A3D]/10 text-[#FF7A3D] flex items-center justify-center group-hover:scale-105 transition-transform">
                                        <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                                    </div>
                                    <input type="radio" name="type" value="motor_pengganti" {{ old('type', $defaultType) === 'motor_pengganti' ? 'checked' : '' }} onchange="onTypeChanged()" class="mt-1 text-[#FF7A3D] focus:ring-[#FF7A3D] w-4 h-4">
                                </div>
                                <span class="font-black text-xs text-slate-900 block mb-1">Biaya Sewa Unit Pengganti</span>
                                <span class="text-[11px] text-slate-500 leading-snug">Alokasi biaya sewa / drop unit motor cadangan sementara saat unit utama menginap.</span>
                            </label>

                            <!-- Option 3: Lainnya / Biaya Operasional Khusus -->
                            <label class="relative flex flex-col p-4 rounded-2xl border-2 border-slate-200 cursor-pointer hover:border-indigo-500 transition group has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/50 shadow-2xs">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-105 transition-transform">
                                        <i data-lucide="layers" class="w-5 h-5"></i>
                                    </div>
                                    <input type="radio" name="type" value="lainnya" {{ old('type', $defaultType) === 'lainnya' ? 'checked' : '' }} onchange="onTypeChanged()" class="mt-1 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                </div>
                                <span class="font-black text-xs text-slate-900 block mb-1">Pengeluaran Operasional Lainnya</span>
                                <span class="text-[11px] text-slate-500 leading-snug">Pencatatan pengeluaran teknis armada lainnya di luar servis berkala.</span>
                            </label>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- STEP CONTAINER 2: Rincian & Multi-Keluhan Suku Cadang (Sub Kategori) -->
        <!-- ========================================================================= -->
        <div class="step-content space-y-6 hidden" id="stepContent2">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-[#015471] text-white text-xs font-black flex items-center justify-center shadow-2xs">2</span>
                        <div>
                            <h2 class="text-sm font-black text-slate-900" id="step2Title">Tahap 2: Rincian & Daftar Sub Kategori Keluhan</h2>
                            <p class="text-[11px] text-slate-500" id="step2Subtitle">Bisa mencatat lebih dari satu jenis suku cadang / keluhan sekaligus pada unit ini</p>
                        </div>
                    </div>
                    <span class="text-[11px] font-bold text-[#015471] bg-[#015471]/10 px-2.5 py-1 rounded-full">Langkah 2 dari 4</span>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    
                    <!-- VARIANT A: MULTI-KELUHAN REPEATER UNTUK PERBAIKAN & SERVIS -->
                    <div id="dynamicFormRepair" class="space-y-6">
                        <div class="p-4 rounded-2xl bg-[#015471]/5 border border-[#015471]/20 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-[#015471]/10 text-[#015471] flex items-center justify-center shrink-0">
                                <i data-lucide="wrench" class="w-5 h-5 text-[#FF7A3D]"></i>
                            </div>
                            <div class="text-xs">
                                <span class="font-bold text-[#015471] block">Daftar Rincian Pekerjaan & Penggantian Sparepart</span>
                                <span class="text-slate-600">Anda dapat menambahkan lebih dari satu sub kategori keluhan (contoh: Servis Rutin + Ganti Ban Luar + Ganti Aki) beserta foto bukti pergantian masing-masing.</span>
                            </div>
                        </div>

                        <!-- Container List Keluhan Repeater -->
                        <div id="complaintsListContainer" class="space-y-5">
                            <!-- Complaint Item 1 (Default) -->
                            <div class="complaint-card p-5 sm:p-6 rounded-2xl border-2 border-slate-200 bg-white space-y-4 relative shadow-2xs" data-index="0">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                    <div class="flex items-center gap-2.5">
                                        <span class="complaint-badge w-6 h-6 rounded-lg bg-[#015471] text-white text-[11px] font-black flex items-center justify-center">1</span>
                                        <span class="font-black text-xs text-slate-900 complaint-label">Pekerjaan / Suku Cadang #1</span>
                                    </div>
                                    <button type="button" onclick="removeComplaintItem(this)" class="btn-remove-complaint hidden text-xs font-bold text-rose-500 hover:text-rose-700 hover:bg-rose-50 px-2 py-1 rounded-lg transition flex items-center gap-1">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                    </button>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                            Sub Kategori Pekerjaan / Pergantian <span class="text-[#FF7A3D]">*</span>
                                        </label>
                                        <select name="complaints[0][category]" onchange="onComplaintCategoryChange(this)" class="complaint-category w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer">
                                            <option value="service_berkala">Service Berkala & Ganti Pelumas / Oli</option>
                                            <option value="ganti_ban">Penggantian Ban (Depan / Belakang / Set)</option>
                                            <option value="ganti_accu">Penggantian Accu / Aki Kering</option>
                                            <option value="sparepart">Penggantian Sparepart & Fast Moving (Kampas/Rantai/Gir)</option>
                                        </select>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                                Nama Komponen / Pekerjaan <span class="text-[#FF7A3D]">*</span>
                                            </label>
                                            <input type="text" name="complaints[0][title]" placeholder="Contoh: Paket Servis AHASS & Oli MPX" class="complaint-title w-full text-xs font-bold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                                Biaya Riil untuk Item Ini (Rp) <span class="text-[#FF7A3D]">*</span>
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
                                            Deskripsi Teknis Pengerjaan / Rincian Kerusakan <span class="text-[#FF7A3D]">*</span>
                                        </label>
                                        <textarea name="complaints[0][description]" rows="3" placeholder="Jelaskan kondisi kerusakan suku cadang lama, nomor seri suku cadang baru, atau catatan teknisi..." class="complaint-desc w-full text-xs p-3.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 leading-relaxed"></textarea>
                                    </div>

                                    <!-- Upload Foto Bukti Khusus Sub Kategori Ini -->
                                    <div class="pt-2 border-t border-dashed border-slate-200">
                                        <div class="flex items-center justify-between mb-2">
                                            <label class="block text-xs font-bold text-slate-700">
                                                Unggah Foto Bukti Fisik Pergantian untuk Item Ini
                                            </label>
                                            <span class="text-[11px] text-slate-400">Format: JPG, PNG, PDF</span>
                                        </div>

                                        <!-- Hidden real input -->
                                        <input type="file" name="complaint_photos_0[]" multiple accept=".pdf,.jpg,.jpeg,.png" onchange="handleComplaintPhotosChange(this)" class="complaint-photo-input hidden">

                                        <div class="flex flex-wrap items-center gap-3">
                                            <!-- Custom Tombol Tambah Foto -->
                                            <button type="button" onclick="triggerComplaintPhotoUpload(this)" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border-2 border-dashed border-[#015471]/40 hover:border-[#015471] bg-[#015471]/5 hover:bg-[#015471]/10 text-[#015471] font-bold text-xs transition shadow-2xs">
                                                <i data-lucide="camera" class="w-4 h-4 text-[#FF7A3D]"></i>
                                                <span>+ Tambah Foto Bukti Fisik / Sparepart</span>
                                            </button>
                                            <span class="photo-count-badge text-xs font-semibold text-slate-500">Belum ada foto dipilih</span>
                                        </div>

                                        <!-- Visual Image Preview Grid -->
                                        <div class="complaint-photo-preview mt-3 hidden grid grid-cols-2 sm:grid-cols-4 gap-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Tambah Keluhan Lainnya -->
                        <div class="flex justify-center pt-2">
                            <button type="button" onclick="addComplaintItem()" class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold rounded-2xl border-2 border-dashed border-[#015471]/40 text-[#015471] hover:bg-[#015471]/5 hover:border-[#015471] transition w-full sm:w-auto justify-center shadow-2xs">
                                <i data-lucide="plus-circle" class="w-4 h-4 text-[#FF7A3D]"></i>
                                <span>+ Tambah Sub Kategori / Suku Cadang Lainnya</span>
                            </button>
                        </div>
                    </div>

                    <!-- VARIANT B: JIKA PILIH MOTOR PENGGANTI / LAINNYA -->
                    <div id="dynamicFormOther" class="space-y-5 hidden">
                        <div class="p-4 rounded-2xl bg-[#FF7A3D]/10 border border-[#FF7A3D]/25 flex items-center gap-3">
                            <i data-lucide="refresh-cw" class="w-5 h-5 text-[#FF7A3D] shrink-0"></i>
                            <div class="text-xs">
                                <span class="font-bold text-slate-900 block">Rincian Sewa Motor Pengganti / Biaya Khusus</span>
                                <span class="text-slate-600">Catat detail unit motor pengganti sementara yang dialokasikan ke pengguna lapangan.</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Judul Transaksi / Alokasi <span class="text-[#FF7A3D]">*</span></label>
                                <input type="text" name="other_title" id="other_title" placeholder="Contoh: Biaya Sewa Motor Pengganti Honda Revo" class="w-full text-xs font-bold px-4 py-3 rounded-2xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 shadow-2xs">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nominal Biaya Sewa (Rp) <span class="text-[#FF7A3D]">*</span></label>
                                <div class="relative flex rounded-2xl shadow-2xs">
                                    <span class="inline-flex items-center px-4 rounded-l-2xl border border-r-0 border-slate-300 bg-slate-100 text-[#015471] text-xs font-black font-mono">
                                        Rp
                                    </span>
                                    <input type="text" inputmode="numeric" id="other_cost_display" placeholder="0" oninput="formatOtherCostInput(this)" class="w-full text-xs font-mono font-bold px-4 py-3 rounded-r-2xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 bg-white">
                                    <input type="hidden" name="other_cost" id="other_cost" value="0">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Uraian Lengkap / Catatan Alokasi <span class="text-[#FF7A3D]">*</span>
                            </label>
                            <textarea name="other_description" id="other_description" rows="4" placeholder="Jelaskan durasi sewa, nomor plat motor pengganti, dan tujuan operasional..." class="w-full text-xs p-4 rounded-2xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 leading-relaxed shadow-2xs"></textarea>
                        </div>
                    </div>

                    <!-- Hidden Fields to harmonize form submission -->
                    <input type="hidden" name="item_name" id="formItemName">
                    <input type="hidden" name="description" id="formDescription">
                    <input type="hidden" name="qty" id="formQty" value="1">
                    <input type="hidden" name="cost" id="cost" value="{{ old('cost', 0) }}">

                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- STEP CONTAINER 3: Data Lapangan, Pengemudi, PIC, Bengkel & Mekanik -->
        <!-- ========================================================================= -->
        <div class="step-content space-y-6 hidden" id="stepContent3">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-[#015471] text-white text-xs font-black flex items-center justify-center shadow-2xs">3</span>
                        <div>
                            <h2 class="text-sm font-black text-slate-900">Tahap 3: Informasi Lapangan, Bengkel & Personil</h2>
                            <p class="text-[11px] text-slate-500">Lengkapi data Odometer KM, Pengemudi, PIC, Bengkel Rujukan & Teknisi Mekanik</p>
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
                            <!-- Posisi Odometer Terkini (KM) -->
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
                                <span class="text-[10px] text-slate-400 block">Status fisik unit menentukan kondisi operasional armada.</span>
                            </div>
                        </div>

                        <!-- Info Posisi Unit Lapangan & Kondisi Fisik Tambahan -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Posisi / Lokasi Keberadaan Motor Terkini
                                </label>
                                <input type="text" name="vehicle_current_location" id="vehicle_current_location" value="{{ old('vehicle_current_location') }}" placeholder="Contoh: Bengkel AHASS / Mess Kebun Afdeling II" class="w-full text-xs px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 bg-white shadow-2xs">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Nomor Memo / SPK / Referensi Servis
                                </label>
                                <input type="text" name="memo_number" id="memo_number" value="{{ old('memo_number') }}" placeholder="Contoh: MEMO/SUP/WS/2026/089 atau #REQ-0001" class="w-full text-xs font-mono font-bold px-4 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 bg-white shadow-2xs">
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
                                    <span class="text-[11px] text-slate-500">Pemegang kendaraan dan penanggung jawab operasional di lokasi</span>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold text-[#015471] bg-[#015471]/10 px-2.5 py-1 rounded-full border border-[#015471]/20">Kontak Personil</span>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <!-- Data Pengemudi Lapangan -->
                            <div class="p-4 rounded-2xl bg-white border border-slate-200/90 space-y-3 shadow-2xs">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <div class="flex items-center gap-2 text-slate-700">
                                        <i data-lucide="user-check" class="w-4 h-4 text-[#015471]"></i>
                                        <span class="text-xs font-bold text-slate-800">Pengemudi / Pemegang Unit</span>
                                    </div>
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">
                                        Data Unit
                                    </span>
                                </div>

                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1">
                                            Nama Pengemudi Lapangan
                                        </label>
                                        <input type="text" name="driver_name" id="driver_name" value="{{ old('driver_name') }}" placeholder="Nama driver pemegang motor" class="w-full text-xs font-bold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 shadow-2xs bg-white">
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-600 mb-1">
                                            No. HP / WhatsApp Pengemudi
                                        </label>
                                        <input type="text" name="driver_phone" id="driver_phone" value="{{ old('driver_phone') }}" placeholder="0812-xxxx-xxxx" class="w-full text-xs font-bold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 shadow-2xs bg-white">
                                    </div>
                                </div>
                            </div>

                            <!-- Data PIC Pengaju / Penanggung Jawab -->
                            <div class="p-4 rounded-2xl bg-white border-2 border-[#015471]/20 space-y-3 shadow-2xs">
                                <div class="flex items-center justify-between border-b border-[#015471]/10 pb-2">
                                    <div class="flex items-center gap-2 text-[#015471]">
                                        <i data-lucide="contact" class="w-4 h-4 text-[#FF7A3D]"></i>
                                        <span class="text-xs font-black">Data PIC Penanggung Jawab Lapangan</span>
                                    </div>
                                    <span class="text-[10px] font-bold text-[#FF7A3D] bg-[#FF7A3D]/10 px-2 py-0.5 rounded-md">Lengkap</span>
                                </div>

                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">
                                            Nama PIC Lapangan
                                        </label>
                                        <input type="text" name="pic_name" id="pic_name" value="{{ old('pic_name') }}" placeholder="Contoh: Bambang Irawan / Asisten Kebun" oninput="updateSummary()" class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 shadow-2xs bg-white">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">
                                            No. HP / WhatsApp PIC
                                        </label>
                                        <input type="text" name="pic_phone" id="pic_phone" value="{{ old('pic_phone') }}" placeholder="Contoh: 081234567890" oninput="updateSummary()" class="w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 shadow-2xs bg-white">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KELOMPOK 3: Bengkel Rujukan & Teknisi Mekanik -->
                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-50/90 border border-slate-200/90 space-y-4 shadow-2xs">
                        <div class="flex items-center justify-between border-b border-slate-200/80 pb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-[#015471] text-white flex items-center justify-center text-xs shadow-2xs">
                                    <i data-lucide="wrench" class="w-4 h-4 text-[#FF7A3D]"></i>
                                </div>
                                <div>
                                    <span class="text-xs font-black text-slate-900 block">3. Data Bengkel Rujukan & Teknisi Mekanik</span>
                                    <span class="text-[11px] text-slate-500">Pencatatan toko / AHASS rekanan dan kontak mekanik yang menangani</span>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold text-[#015471] bg-[#015471]/10 px-2.5 py-1 rounded-full border border-[#015471]/20">Bengkel Rekanan</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Bengkel Info -->
                            <div class="p-4 rounded-2xl bg-white border border-slate-200/90 space-y-3 shadow-2xs">
                                <span class="text-xs font-bold text-slate-800 block border-b border-slate-100 pb-2">Informasi Bengkel / Toko</span>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Nama Bengkel / AHASS</label>
                                    <input type="text" name="workshop_name" id="workshop_name" value="{{ old('workshop_name') }}" placeholder="Contoh: AHASS Astra Sentral Motor" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 shadow-2xs bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">No. Telp / WA Bengkel</label>
                                    <input type="text" name="workshop_phone" id="workshop_phone" value="{{ old('workshop_phone') }}" placeholder="Contoh: 0812-3456-7890" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 shadow-2xs bg-white">
                                </div>
                            </div>

                            <!-- Mekanik Info -->
                            <div class="p-4 rounded-2xl bg-white border border-slate-200/90 space-y-3 shadow-2xs">
                                <span class="text-xs font-bold text-slate-800 block border-b border-slate-100 pb-2">Informasi Teknisi / Mekanik</span>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Nama Mekanik / Teknisi</label>
                                    <input type="text" name="mechanic_name" id="mechanic_name" value="{{ old('mechanic_name') }}" placeholder="Contoh: Mas Joko / Pak Hendra" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 shadow-2xs bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1">No. Telp / WA Mekanik</label>
                                    <input type="text" name="mechanic_phone" id="mechanic_phone" value="{{ old('mechanic_phone') }}" placeholder="Contoh: 0821-9876-5432" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 shadow-2xs bg-white">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- STEP CONTAINER 4: Unggah Foto Bukti & Ringkasan Transaksi Kas -->
        <!-- ========================================================================= -->
        <div class="step-content space-y-6 hidden" id="stepContent4">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-xl bg-[#015471] text-white text-xs font-black flex items-center justify-center shadow-2xs">4</span>
                        <div>
                            <h2 class="text-sm font-black text-slate-900">Tahap 4: Lampiran Bukti Fisik, Nota Kas & Ringkasan</h2>
                            <p class="text-[11px] text-slate-500">Unggah foto fisik kendaraan 4 sudut, kuitansi nota & tinjau rekap pengeluaran kas</p>
                        </div>
                    </div>
                    <span class="text-[11px] font-bold text-[#015471] bg-[#015471]/10 px-2.5 py-1 rounded-full">Langkah 4 dari 4</span>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    
                    <!-- 4 Foto Fisik Sudut Kendaraan -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <div>
                                <label class="block text-xs font-black text-slate-900">
                                    Foto Fisik 4 Sisi Unit Kendaraan <span class="text-slate-400 font-normal">(Opsional / Disarankan)</span>
                                </label>
                                <p class="text-[11px] text-slate-500">Unggah foto fisik unit untuk identifikasi visual dan verifikasi armada di lapangan</p>
                            </div>
                            <span class="text-[10px] font-bold text-[#015471] bg-[#015471]/10 px-2.5 py-1 rounded-full">4 Sudut</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- 1. Tampak Depan -->
                            <div class="rounded-2xl border-2 border-dashed border-slate-300 hover:border-[#015471] bg-slate-50/50 hover:bg-slate-50 transition p-3.5 flex flex-col justify-between group relative overflow-hidden">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[11px] font-black text-slate-800">1. Tampak Depan</span>
                                        <span class="text-[9px] font-bold text-[#FF7A3D] bg-[#FF7A3D]/10 px-1.5 py-0.5 rounded">Nopol Jelas</span>
                                    </div>
                                    <p class="text-[10px] text-slate-400 mb-3">Foto bagian depan dengan plat nomor terbaca</p>
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
                                    <p class="text-[10px] text-slate-400 mb-3">Foto menyeluruh dari sisi samping kanan</p>
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
                                    <p class="text-[10px] text-slate-400 mb-3">Foto menyeluruh dari sisi samping kiri</p>
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

                            <!-- 4. Tampak Belakang -->
                            <div class="rounded-2xl border-2 border-dashed border-slate-300 hover:border-[#015471] bg-slate-50/50 hover:bg-slate-50 transition p-3.5 flex flex-col justify-between group relative overflow-hidden">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-[11px] font-black text-slate-800">4. Tampak Belakang</span>
                                        <span class="text-[9px] font-bold text-[#FF7A3D] bg-[#FF7A3D]/10 px-1.5 py-0.5 rounded">Nopol Jelas</span>
                                    </div>
                                    <p class="text-[10px] text-slate-400 mb-3">Foto bagian belakang dengan plat nomor terbaca</p>
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

                    <!-- Upload Kuitansi / Bukti Pembayaran Nota Kas -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 pt-2">
                        <!-- Box Kuitansi Kas Riil -->
                        <div class="p-5 rounded-2xl bg-white border-2 border-[#015471]/20 space-y-3 shadow-2xs">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                <span class="text-xs font-black text-slate-900 flex items-center gap-2">
                                    <i data-lucide="receipt" class="w-4 h-4 text-[#FF7A3D]"></i>
                                    Lampiran Kuitansi / Faktur Nota Kas
                                </span>
                                <span class="text-[10px] font-bold text-[#015471] bg-[#015471]/10 px-2 py-0.5 rounded">Nota Kas</span>
                            </div>

                            <label for="receipt_file" class="relative border-2 border-dashed border-slate-300 hover:border-[#015471] rounded-2xl p-5 text-center transition bg-slate-50/50 hover:bg-slate-50 cursor-pointer block group select-none">
                                <div class="w-10 h-10 rounded-xl bg-white text-[#015471] group-hover:bg-[#015471] group-hover:text-white shadow-2xs mx-auto flex items-center justify-center mb-2 transition">
                                    <i data-lucide="paperclip" class="w-5 h-5"></i>
                                </div>
                                <span class="block text-xs font-bold text-slate-800 group-hover:text-[#015471] transition">
                                    Pilih file struk / nota kuitansi
                                </span>
                                <p class="text-[10px] text-slate-400 mt-0.5">PDF, JPG, PNG (Maks 5MB)</p>
                                <input type="file" name="receipt_file" id="receipt_file" accept=".pdf,.jpg,.jpeg,.png" onchange="showReceiptFileName(this)" class="hidden">
                            </label>
                            <div id="receipt_file_preview" class="hidden p-2.5 bg-emerald-50 rounded-xl border border-emerald-200 text-xs text-emerald-800 font-bold items-center justify-center gap-2 shadow-2xs">
                                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                                <span id="receipt_file_text" class="truncate max-w-[240px]"></span>
                            </div>
                        </div>

                        <!-- Box Berkas Lampiran Tambahan Lainnya -->
                        <div class="p-5 rounded-2xl bg-white border border-slate-200 space-y-3 shadow-2xs">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                <span class="text-xs font-bold text-slate-800 flex items-center gap-2">
                                    <i data-lucide="file-plus" class="w-4 h-4 text-slate-500"></i>
                                    Dokumen / Lampiran Tambahan Lainnya
                                </span>
                                <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">Opsional</span>
                            </div>

                            <label for="proof_files" class="relative border-2 border-dashed border-slate-300 hover:border-[#015471] rounded-2xl p-5 text-center transition bg-slate-50/50 hover:bg-slate-50 cursor-pointer block group select-none">
                                <div class="w-10 h-10 rounded-xl bg-white text-slate-600 group-hover:bg-[#015471] group-hover:text-white shadow-2xs mx-auto flex items-center justify-center mb-2 transition">
                                    <i data-lucide="files" class="w-5 h-5"></i>
                                </div>
                                <span class="block text-xs font-bold text-slate-800 group-hover:text-[#015471] transition">
                                    Pilih dokumen pendukung lainnya
                                </span>
                                <p class="text-[10px] text-slate-400 mt-0.5">PDF, SPK bengkel, dll</p>
                                <input type="file" name="proof_files[]" id="proof_files" multiple accept=".pdf,.jpg,.jpeg,.png" onchange="previewSelectedFiles(this)" class="hidden">
                            </label>
                            <div id="file_list_preview" class="hidden grid grid-cols-1 gap-1.5 text-left text-xs"></div>
                        </div>
                    </div>

                    <!-- Ringkasan Konfirmasi Transaksi -->
                    <div class="p-5 rounded-2xl bg-slate-50/90 border border-slate-200 space-y-3">
                        <div class="flex items-center gap-2 text-[#015471]">
                            <i data-lucide="clipboard-check" class="w-4 h-4 text-[#FF7A3D]"></i>
                            <h3 class="text-xs font-black uppercase tracking-wider">Ringkasan Pencatatan Pemeliharaan</h3>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div class="p-3 bg-white rounded-xl border border-slate-200">
                                <span class="text-[11px] text-slate-400 block">Armada Kendaraan:</span>
                                <span class="font-black text-slate-900 font-mono mt-0.5 block" id="summaryVehicle">-</span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-slate-200">
                                <span class="text-[11px] text-slate-400 block">Jenis Kategori Pekerjaan:</span>
                                <span class="font-bold text-[#015471] mt-0.5 block" id="summaryType">-</span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-slate-200">
                                <span class="text-[11px] text-slate-400 block">Rincian Item / Keluhan:</span>
                                <span class="font-bold text-slate-900 mt-0.5 block" id="summaryTitle">-</span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-slate-200">
                                <span class="text-[11px] text-slate-400 block">Pengemudi & PIC Lapangan:</span>
                                <span class="font-bold text-slate-900 mt-0.5 block" id="summaryDriver">-</span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-slate-200">
                                <span class="text-[11px] text-slate-400 block">Odometer KM & Bengkel Rekanan:</span>
                                <span class="font-black text-[#015471] mt-0.5 block" id="summaryOdometer">-</span>
                            </div>
                            <div class="p-3 bg-white rounded-xl border border-slate-200">
                                <span class="text-[11px] text-slate-400 block">Kondisi Operasional Motor:</span>
                                <span class="font-bold text-slate-900 mt-0.5 block" id="summaryCondition">-</span>
                            </div>
                            <div class="p-3.5 bg-gradient-to-r from-[#015471]/10 to-[#FF7A3D]/10 rounded-xl border-2 border-[#015471]/20 sm:col-span-2 flex items-center justify-between">
                                <div>
                                    <span class="text-[11px] font-bold text-slate-600 block">Total Biaya Riil (Otomatis ke Kas Pengeluaran):</span>
                                    <span class="text-[10px] text-slate-500">Tersinkronisasi otomatis dari akumulasi sub kategori</span>
                                </div>
                                <span class="font-black text-[#FF7A3D] font-mono text-base" id="summaryCost">Rp 0</span>
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
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan & Masukkan ke Kas</span>
                </button>
            </div>
        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>
    const clientRequestsData = @json($clientRequestsData);

    let currentStep = 1;
    const totalSteps = 4;

    function getSelectedType() {
        const checked = document.querySelector('input[name="type"]:checked');
        return checked ? checked.value : 'service_berkala';
    }

    function onTypeChanged() {
        const type = getSelectedType();
        const repairForm = document.getElementById('dynamicFormRepair');
        const otherForm = document.getElementById('dynamicFormOther');

        const step2Title = document.getElementById('step2Title');
        const step2Subtitle = document.getElementById('step2Subtitle');

        if (type === 'service_berkala' || type === 'ganti_ban' || type === 'ganti_accu' || type === 'sparepart') {
            repairForm.classList.remove('hidden');
            otherForm.classList.add('hidden');
            step2Title.innerText = 'Tahap 2: Rincian & Multi-Sub Kategori Suku Cadang';
            step2Subtitle.innerText = 'Bisa mencatat lebih dari satu jenis suku cadang / keluhan servis sekaligus beserta foto bukti fisik';
        } else {
            repairForm.classList.add('hidden');
            otherForm.classList.remove('hidden');
            step2Title.innerText = 'Tahap 2: Rincian Biaya Sewa / Alokasi Khusus';
            step2Subtitle.innerText = 'Tuliskan rincian biaya dan durasi alokasi motor pengganti sementara';
        }

        calculateTotalCost();
        updateSummary();
    }

    // MULTI-COMPLAINT REPEATER FUNCTIONS
    let complaintCount = 1;

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

        calculateTotalCost();
    }

    function formatOtherCostInput(displayInput) {
        const rawValue = displayInput.value.replace(/[^0-9]/g, '');
        const hiddenInput = document.getElementById('other_cost');
        
        if (!rawValue) {
            displayInput.value = '';
            if (hiddenInput) hiddenInput.value = '0';
        } else {
            displayInput.value = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            if (hiddenInput) hiddenInput.value = rawValue;
        }

        calculateTotalCost();
    }

    function calculateTotalCost() {
        const type = getSelectedType();
        let total = 0;

        if (type === 'service_berkala' || type === 'ganti_ban' || type === 'ganti_accu' || type === 'sparepart') {
            const costInputs = document.querySelectorAll('.complaint-cost');
            costInputs.forEach(input => {
                const val = parseFloat(input.value) || 0;
                total += val;
            });
        } else {
            const otherCostVal = parseFloat(document.getElementById('other_cost')?.value) || 0;
            total = otherCostVal;
        }

        const costHidden = document.getElementById('cost');
        const summaryCostElem = document.getElementById('summaryCost');

        if (costHidden) costHidden.value = total;
        if (summaryCostElem) {
            summaryCostElem.innerText = `Rp ${Number(total).toLocaleString('id-ID')}`;
        }
    }

    function addComplaintItem(initialData = null) {
        const container = document.getElementById('complaintsListContainer');
        const newIndex = complaintCount++;
        
        const card = document.createElement('div');
        card.className = 'complaint-card p-5 sm:p-6 rounded-2xl border-2 border-slate-200 bg-white space-y-4 relative shadow-2xs transition-all duration-200';
        card.setAttribute('data-index', newIndex);
        
        const catVal = initialData ? (initialData.category || 'service_berkala') : 'service_berkala';
        const titleVal = initialData ? (initialData.title || '') : '';
        const costVal = initialData ? (initialData.cost || 0) : 0;
        const descVal = initialData ? (initialData.desc || '') : '';
        const costFormatted = costVal > 0 ? formatRupiahNumber(costVal) : '';

        card.innerHTML = `
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <span class="complaint-badge w-6 h-6 rounded-lg bg-[#015471] text-white text-[11px] font-black flex items-center justify-center"></span>
                    <span class="font-black text-xs text-slate-900 complaint-label">Pekerjaan / Suku Cadang #${newIndex + 1}</span>
                </div>
                <button type="button" onclick="removeComplaintItem(this)" class="btn-remove-complaint text-xs font-bold text-rose-500 hover:text-rose-700 hover:bg-rose-50 px-2 py-1 rounded-lg transition flex items-center gap-1">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Sub Kategori Pekerjaan / Pergantian <span class="text-[#FF7A3D]">*</span>
                    </label>
                    <select name="complaints[${newIndex}][category]" onchange="onComplaintCategoryChange(this)" class="complaint-category w-full text-xs font-semibold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] bg-white text-slate-900 cursor-pointer">
                        <option value="service_berkala" ${catVal === 'service_berkala' ? 'selected' : ''}>Service Berkala & Ganti Pelumas / Oli</option>
                        <option value="ganti_ban" ${catVal === 'ganti_ban' ? 'selected' : ''}>Penggantian Ban (Depan / Belakang / Set)</option>
                        <option value="ganti_accu" ${catVal === 'ganti_accu' ? 'selected' : ''}>Penggantian Accu / Aki Kering</option>
                        <option value="sparepart" ${catVal === 'sparepart' ? 'selected' : ''}>Penggantian Sparepart & Fast Moving (Kampas/Rantai/Gir)</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nama Komponen / Pekerjaan <span class="text-[#FF7A3D]">*</span>
                        </label>
                        <input type="text" name="complaints[${newIndex}][title]" value="${titleVal.replace(/"/g, '&quot;')}" placeholder="Contoh: Penggantian Ban Luar Belakang" class="complaint-title w-full text-xs font-bold px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Biaya Riil untuk Item Ini (Rp) <span class="text-[#FF7A3D]">*</span>
                        </label>
                        <div class="relative flex rounded-xl shadow-2xs">
                            <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0 border-slate-300 bg-slate-100 text-slate-700 text-xs font-mono font-black">
                                Rp
                            </span>
                            <input type="text" inputmode="numeric" value="${costFormatted}" placeholder="0" oninput="formatRupiahInput(this)" class="complaint-cost-display w-full text-xs font-mono font-bold px-3.5 py-2.5 rounded-r-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 bg-white">
                            <input type="hidden" name="complaints[${newIndex}][cost]" value="${costVal}" class="complaint-cost">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Deskripsi Teknis Pengerjaan / Rincian Kerusakan <span class="text-[#FF7A3D]">*</span>
                    </label>
                    <textarea name="complaints[${newIndex}][description]" rows="3" placeholder="Jelaskan kondisi kerusakan suku cadang lama, nomor seri suku cadang baru, atau catatan teknisi..." class="complaint-desc w-full text-xs p-3.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#015471] text-slate-900 leading-relaxed">${descVal}</textarea>
                </div>

                <!-- Upload Foto Bukti Khusus Sub Kategori Ini -->
                <div class="pt-2 border-t border-dashed border-slate-200">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-slate-700">
                            Unggah Foto Bukti Fisik Pergantian untuk Item Ini
                        </label>
                        <span class="text-[11px] text-slate-400">Format: JPG, PNG, PDF</span>
                    </div>

                    <!-- Hidden real input -->
                    <input type="file" name="complaint_photos_${newIndex}[]" multiple accept=".pdf,.jpg,.jpeg,.png" onchange="handleComplaintPhotosChange(this)" class="complaint-photo-input hidden">

                    <div class="flex flex-wrap items-center gap-3">
                        <!-- Custom Tombol Tambah Foto -->
                        <button type="button" onclick="triggerComplaintPhotoUpload(this)" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border-2 border-dashed border-[#015471]/40 hover:border-[#015471] bg-[#015471]/5 hover:bg-[#015471]/10 text-[#015471] font-bold text-xs transition shadow-2xs">
                            <i data-lucide="camera" class="w-4 h-4 text-[#FF7A3D]"></i>
                            <span>+ Tambah Foto Bukti Fisik / Sparepart</span>
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
        calculateTotalCost();

        // Render any initial imported photos for this complaint
        if (initialData && initialData.photos && initialData.photos.length > 0) {
            complaintInitialPhotosMap.set(String(newIndex), initialData.photos);
            renderComplaintPhotosPreview(card);
        }

        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function removeComplaintItem(button) {
        const card = button.closest('.complaint-card');
        if (card) {
            const idx = card.getAttribute('data-index');
            complaintFilesMap.delete(idx);
            complaintInitialPhotosMap.delete(idx);
            card.remove();
            renumberComplaints();
            calculateTotalCost();
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
            if (label) label.innerText = `Pekerjaan / Suku Cadang #${idx + 1}`;

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

        if (cat === 'service_berkala') {
            titleInput.placeholder = 'Contoh: Paket Servis AHASS & Oli MPX';
            descInput.placeholder = 'Jelaskan riwayat pemakaian, kilometer servis terakhir, dan kondisi tarikan mesin...';
        } else if (cat === 'ganti_ban') {
            titleInput.placeholder = 'Contoh: Penggantian Ban Luar Belakang & Depan';
            descInput.placeholder = 'Jelaskan kondisi ban lama (gundul/retak/robek) dan merk/ukuran ban baru yang dipasang...';
        } else if (cat === 'ganti_accu') {
            titleInput.placeholder = 'Contoh: Penggantian Accu / Aki GS GTZ5S';
            descInput.placeholder = 'Jelaskan kondisi aki lama (drop/soak) dan merk aki baru yang dipasang...';
        } else if (cat === 'sparepart') {
            titleInput.placeholder = 'Contoh: Penggantian Kampas Rem, Gir Set & Rantai';
            descInput.placeholder = 'Sebutkan rincian sparepart yang diganti beserta alasan penggantian...';
        }
    }

    const complaintFilesMap = new Map();
    const complaintInitialPhotosMap = new Map();

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

    function removeInitialComplaintPhoto(btn, photoPath) {
        const card = btn.closest('.complaint-card');
        const cardIdx = card.getAttribute('data-index');
        
        let initialPhotos = complaintInitialPhotosMap.get(cardIdx) || [];
        initialPhotos = initialPhotos.filter(p => p.path !== photoPath);
        complaintInitialPhotosMap.set(cardIdx, initialPhotos);

        // Also remove from existing_proof_files hidden input
        const existingInput = document.getElementById('existing_proof_files');
        if (existingInput && existingInput.value) {
            try {
                let files = JSON.parse(existingInput.value);
                files = files.filter(f => f.path !== photoPath);
                existingInput.value = JSON.stringify(files);
            } catch(e) {}
        }

        renderComplaintPhotosPreview(card);
    }

    function renderComplaintPhotosPreview(card) {
        const previewContainer = card.querySelector('.complaint-photo-preview');
        const countBadge = card.querySelector('.photo-count-badge');
        const dt = getCardDataTransfer(card);
        const files = dt.files;
        const cardIdx = card.getAttribute('data-index');
        const initialPhotos = complaintInitialPhotosMap.get(cardIdx) || [];

        previewContainer.innerHTML = '';

        const totalCount = (files ? files.length : 0) + initialPhotos.length;

        if (totalCount > 0) {
            countBadge.innerText = `${totalCount} foto siap dilampirkan`;
            countBadge.className = 'photo-count-badge text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200';
            previewContainer.classList.remove('hidden');

            // 1. Render Initial / Imported Photos
            initialPhotos.forEach((photo) => {
                const item = document.createElement('div');
                item.className = 'relative group rounded-2xl border border-[#015471]/30 bg-slate-50 p-2 overflow-hidden shadow-2xs hover:border-[#015471] transition';

                if (photo.is_image) {
                    item.innerHTML = `
                        <div class="w-full h-24 rounded-xl overflow-hidden bg-slate-200 relative mb-1.5 border border-slate-100">
                            <img src="${photo.url}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" alt="${photo.name}">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-1.5">
                                <span class="text-[9px] text-white truncate font-medium">Tiket Klien</span>
                            </div>
                            <button type="button" onclick="removeInitialComplaintPhoto(this, '${photo.path}')" title="Hapus foto ini" class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center shadow-md transition z-10">
                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                        <div class="flex items-center justify-between gap-1">
                            <span class="block truncate text-[10px] font-bold text-slate-800" title="${photo.name}">${photo.name}</span>
                            <span class="text-[9px] font-extrabold text-[#015471] bg-[#015471]/10 px-1 py-0.2 rounded shrink-0">Tiket</span>
                        </div>
                    `;
                } else {
                    item.innerHTML = `
                        <div class="w-full h-24 rounded-xl bg-slate-100 border border-slate-200 flex flex-col items-center justify-center p-2 text-center mb-1.5 relative">
                            <i data-lucide="file-text" class="w-7 h-7 text-[#015471] mb-1"></i>
                            <span class="text-[9px] font-bold text-slate-500 uppercase">${photo.name.split('.').pop()}</span>
                            <button type="button" onclick="removeInitialComplaintPhoto(this, '${photo.path}')" title="Hapus berkas ini" class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center shadow-md transition z-10">
                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                        <div class="flex items-center justify-between gap-1">
                            <span class="block truncate text-[10px] font-bold text-slate-800" title="${photo.name}">${photo.name}</span>
                            <span class="text-[9px] font-extrabold text-[#015471] bg-[#015471]/10 px-1 py-0.2 rounded shrink-0">Tiket</span>
                        </div>
                    `;
                }
                previewContainer.appendChild(item);
            });

            // 2. Render Newly Selected Local Files
            if (files && files.length > 0) {
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
                                    <button type="button" onclick="removeSingleComplaintPhoto(this, ${fileIdx})" title="Hapus foto ini" class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center shadow-md transition z-10">
                                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                                <span class="block truncate text-[10px] font-bold text-slate-700">${file.name}</span>
                            `;
                            if (typeof lucide !== 'undefined') lucide.createIcons();
                        };
                        reader.readAsDataURL(file);
                    } else {
                        item.innerHTML = `
                            <div class="w-full h-24 rounded-xl bg-slate-100 border border-slate-200 flex flex-col items-center justify-center p-2 text-center mb-1.5 relative">
                                <i data-lucide="file-text" class="w-7 h-7 text-[#015471] mb-1"></i>
                                <span class="text-[9px] font-bold text-slate-500 uppercase">${file.name.split('.').pop()}</span>
                                <button type="button" onclick="removeSingleComplaintPhoto(this, ${fileIdx})" title="Hapus berkas ini" class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center shadow-md transition z-10">
                                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                            <span class="block truncate text-[10px] font-bold text-slate-700">${file.name}</span>
                        `;
                    }

                    previewContainer.appendChild(item);
                });
            }

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
                        title: 'Unit Kendaraan Belum Dipilih',
                        text: 'Silakan pilih unit armada kendaraan yang dilakukan pemeliharaan pada Tahap 1.',
                        confirmButtonColor: '#015471',
                        confirmButtonText: 'Pilih Armada'
                    });
                }
                return false;
            }

            const maintenanceDate = document.getElementById('maintenance_date');
            if (maintenanceDate && !maintenanceDate.value) {
                if (showSwal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tanggal Pengerjaan Belum Diisi',
                        text: 'Silakan tentukan tanggal pengerjaan atau nota transaksi pemeliharaan.',
                        confirmButtonColor: '#015471',
                        confirmButtonText: 'Isi Tanggal'
                    });
                    maintenanceDate.focus();
                }
                return false;
            }
        } else if (step === 2) {
            const type = getSelectedType();

            if (type === 'service_berkala' || type === 'ganti_ban' || type === 'ganti_accu' || type === 'sparepart') {
                const complaintCards = document.querySelectorAll('.complaint-card');
                if (complaintCards.length === 0) {
                    if (showSwal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Rincian Suku Cadang Masih Kosong',
                            text: 'Silakan tambahkan minimal 1 rincian pekerjaan atau suku cadang pada Tahap 2.',
                            confirmButtonColor: '#015471',
                            confirmButtonText: '+ Tambah Rincian'
                        });
                    }
                    return false;
                }

                let allValid = true;
                let firstInvalidInput = null;
                let invalidReason = '';
                let summaryTitles = [];
                let summaryDescs = [];
                let totalCost = 0;

                complaintCards.forEach((card, idx) => {
                    const titleInput = card.querySelector('.complaint-title');
                    const descInput = card.querySelector('.complaint-desc');
                    const costDisplayInput = card.querySelector('.complaint-cost-display');
                    const costInput = card.querySelector('.complaint-cost');
                    
                    const title = titleInput ? titleInput.value.trim() : '';
                    const desc = descInput ? descInput.value.trim() : '';
                    const costVal = parseFloat(costInput ? costInput.value : 0) || 0;
                    totalCost += costVal;
                    const costFormatted = costVal > 0 ? ` (Biaya: Rp ${Number(costVal).toLocaleString('id-ID')})` : '';

                    const categorySelect = card.querySelector('.complaint-category');
                    const categoryText = categorySelect.options[categorySelect.selectedIndex]?.text || '';

                    if (!title) {
                        allValid = false;
                        if (!firstInvalidInput) {
                            firstInvalidInput = titleInput;
                            invalidReason = `Nama komponen/pekerjaan pada Item #${idx + 1} belum diisi.`;
                        }
                    } else if (!desc) {
                        allValid = false;
                        if (!firstInvalidInput) {
                            firstInvalidInput = descInput;
                            invalidReason = `Deskripsi teknis/kondisi kerusakan pada Item #${idx + 1} belum diisi.`;
                        }
                    } else {
                        summaryTitles.push(title);
                        summaryDescs.push(`[Item #${idx + 1}: ${categoryText}]\nJudul: ${title}${costFormatted}\nUraian: ${desc}`);
                    }
                });

                calculateTotalCost();

                if (!allValid) {
                    if (showSwal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Lengkapi Data Rincian Suku Cadang',
                            text: invalidReason || 'Mohon lengkapi nama suku cadang dan deskripsi pengerjaan pada setiap kartu.',
                            confirmButtonColor: '#015471',
                            confirmButtonText: 'Lengkapi Sekarang'
                        });
                        if (firstInvalidInput) firstInvalidInput.focus();
                    }
                    return false;
                }

                document.getElementById('formItemName').value = summaryTitles.join(', ');
                document.getElementById('formDescription').value = summaryDescs.join("\n\n");

            } else {
                const otherTitle = document.getElementById('other_title');
                const otherDesc = document.getElementById('other_description');

                if (!otherTitle || !otherTitle.value.trim()) {
                    if (showSwal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Judul Transaksi Belum Diisi',
                            text: 'Mohon isi judul transaksi / sewa motor pengganti pada Tahap 2.',
                            confirmButtonColor: '#015471',
                            confirmButtonText: 'Isi Judul'
                        });
                        if (otherTitle) otherTitle.focus();
                    }
                    return false;
                }

                if (!otherDesc || !otherDesc.value.trim()) {
                    if (showSwal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Uraian Transaksi Belum Diisi',
                            text: 'Mohon isi uraian lengkap dan catatan operasional pada Tahap 2.',
                            confirmButtonColor: '#015471',
                            confirmButtonText: 'Isi Uraian'
                        });
                        if (otherDesc) otherDesc.focus();
                    }
                    return false;
                }

                document.getElementById('formItemName').value = otherTitle.value.trim();
                document.getElementById('formDescription').value = otherDesc.value.trim();
            }
        } else if (step === 3) {
            const odoInput = document.getElementById('odometer_km');
            const odoValue = odoInput ? odoInput.value.trim() : '';

            if (!odoValue) {
                if (showSwal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Kilometer Odometer Wajib Diisi',
                        text: 'Mohon masukkan angka kilometer terakhir pada speedometer motor pada Tahap 3.',
                        confirmButtonColor: '#015471',
                        confirmButtonText: 'Isi Odometer'
                    });
                    if (odoInput) odoInput.focus();
                }
                return false;
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

    function selectVehicle(id, label, holder, holderPhone) {
        document.getElementById('vehicle_id').value = id;
        document.getElementById('selectedVehicleLabel').innerHTML = `<span class="text-slate-900 font-extrabold">${label}</span>`;
        document.getElementById('clearVehicleSelection').classList.remove('hidden');
        selectedVehicleFullText = label;

        if (holder) {
            document.getElementById('driver_name').value = holder;
        }
        if (holderPhone) {
            document.getElementById('driver_phone').value = holderPhone;
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

    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('searchableVehicleWrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            closeVehicleDropdown();
        }
    });

    // Auto-fill from Client Request with Clean Multi-Card & Photo Previews
    function applyClientRequestAutofill(requestId) {
        if (!requestId) {
            const badge = document.getElementById('autofill_badge_alert');
            if (badge) badge.classList.add('hidden');
            return;
        }

        const cr = typeof clientRequestsData !== 'undefined' ? clientRequestsData[requestId] : null;
        if (!cr) {
            console.warn('No client request data found for ID:', requestId);
            return;
        }

        try {
            // Set existing proof files in hidden field for backend processing
            const existingInput = document.getElementById('existing_proof_files');
            if (existingInput) {
                existingInput.value = JSON.stringify(cr.all_proof_files || []);
            }
        } catch (e) {
            console.error('Error setting existing proof files:', e);
        }

        try {
            // 1. Select Vehicle
            if (cr.vehicle_id) {
                const vehicleOption = document.querySelector(`.vehicle-option-item[data-id="${cr.vehicle_id}"]`);
                if (vehicleOption) {
                    const fullLabel = vehicleOption.getAttribute('data-label') || cr.vehicle_label;
                    const holderName = cr.driver_name || vehicleOption.getAttribute('data-holder') || '';
                    const holderPhone = cr.driver_phone || vehicleOption.getAttribute('data-holderphone') || '';
                    selectVehicle(cr.vehicle_id, fullLabel, holderName, holderPhone);
                } else {
                    selectVehicle(cr.vehicle_id, cr.vehicle_label || 'Armada Terpilih', cr.driver_name || '', cr.driver_phone || '');
                }
            }
        } catch (err) {
            console.error('Error autofilling vehicle:', err);
        }

        try {
            // 2. Select Main Category & Populate Cards with Photos
            if (cr.request_type === 'unit_pengganti') {
                const rad = document.querySelector('input[name="type"][value="motor_pengganti"]');
                if (rad) { 
                    rad.checked = true; 
                    onTypeChanged(); 
                }
                
                const otherTitleElem = document.getElementById('other_title');
                if (otherTitleElem) otherTitleElem.value = cr.title || '';

                const otherDescElem = document.getElementById('other_description');
                if (otherDescElem) otherDescElem.value = cr.clean_other_desc || cr.title || '';

                const otherCostDisp = document.getElementById('other_cost_display');
                if (otherCostDisp) otherCostDisp.value = cr.cost > 0 ? formatRupiahNumber(cr.cost) : '0';

                const otherCostVal = document.getElementById('other_cost');
                if (otherCostVal) otherCostVal.value = cr.cost || 0;
            } else {
                const rad = document.querySelector('input[name="type"][value="service_berkala"]');
                if (rad) { 
                    rad.checked = true; 
                    onTypeChanged(); 
                }

                const container = document.getElementById('complaintsListContainer');
                if (container) {
                    container.innerHTML = '';
                    complaintCount = 0;
                    complaintFilesMap.clear();
                    complaintInitialPhotosMap.clear();

                    if (cr.complaints && cr.complaints.length > 0) {
                        cr.complaints.forEach((comp, idx) => {
                            const compPhotos = (cr.complaint_photos_map && cr.complaint_photos_map[idx]) ? cr.complaint_photos_map[idx] : [];
                            addComplaintItem({
                                category: comp.category || 'service_berkala',
                                title: comp.title || '',
                                cost: comp.cost || 0,
                                desc: comp.desc || '',
                                photos: compPhotos,
                            });
                        });
                    } else {
                        // Fallback: 1 single complaint card with all complaint photos if any
                        const compPhotos = (cr.complaint_photos_map && cr.complaint_photos_map[0]) ? cr.complaint_photos_map[0] : [];
                        addComplaintItem({
                            category: 'service_berkala',
                            title: cr.title || '',
                            cost: cr.cost || 0,
                            desc: cr.clean_other_desc || cr.title || '',
                            photos: compPhotos,
                        });
                    }
                }
            }
        } catch (err) {
            console.error('Error autofilling complaint cards:', err);
        }

        try {
            // 3. Technical & Lapangan Info (Tahap 3)
            if (cr.odometer_km) {
                const odoElem = document.getElementById('odometer_km');
                if (odoElem) odoElem.value = cr.odometer_km;
            }

            if (cr.operational_status) {
                const statusSelect = document.getElementById('vehicle_operational_status');
                if (statusSelect) {
                    const targetStatus = String(cr.operational_status).toLowerCase().trim();
                    let matched = false;
                    for (let i = 0; i < statusSelect.options.length; i++) {
                        const opt = statusSelect.options[i];
                        const optVal = opt.value.toLowerCase();
                        const optText = opt.text.toLowerCase();
                        if (optVal === targetStatus || optText === targetStatus || optText.includes(targetStatus) || targetStatus.includes(optVal)) {
                            statusSelect.selectedIndex = i;
                            matched = true;
                            break;
                        }
                    }
                    if (!matched) {
                        if (targetStatus.includes('jalan') || targetStatus.includes('normal')) statusSelect.value = 'masih_bisa_jalan';
                        else if (targetStatus.includes('mogok')) statusSelect.value = 'mogok_total';
                        else if (targetStatus.includes('aman') || targetStatus.includes('bahaya')) statusSelect.value = 'rusak_tidak_aman';
                        else if (targetStatus.includes('bengkel')) statusSelect.value = 'sudah_di_bengkel';
                        else if (targetStatus.includes('hilang') || targetStatus.includes('tlo')) statusSelect.value = 'unit_hilang';
                    }
                }
            }

            if (cr.unit_position) {
                const posElem = document.getElementById('vehicle_current_location');
                if (posElem) posElem.value = cr.unit_position;
            }

            if (cr.memo) {
                const memoElem = document.getElementById('memo_number');
                if (memoElem) memoElem.value = cr.memo;
            }
        } catch (err) {
            console.error('Error autofilling technical info:', err);
        }

        try {
            // 4. Personil (Driver & PIC)
            if (cr.driver_name) {
                const driverElem = document.getElementById('driver_name');
                if (driverElem) driverElem.value = cr.driver_name;
            }
            if (cr.driver_phone) {
                const driverPhoneElem = document.getElementById('driver_phone');
                if (driverPhoneElem) driverPhoneElem.value = cr.driver_phone;
            }
            if (cr.pic_name) {
                const picElem = document.getElementById('pic_name');
                if (picElem) picElem.value = cr.pic_name;
            }
            if (cr.pic_phone) {
                const picPhoneElem = document.getElementById('pic_phone');
                if (picPhoneElem) picPhoneElem.value = cr.pic_phone;
            }
        } catch (err) {
            console.error('Error autofilling personil info:', err);
        }

        try {
            // 5. Bengkel & Mekanik
            if (cr.workshop_name) {
                const wsNameElem = document.getElementById('workshop_name');
                if (wsNameElem) wsNameElem.value = cr.workshop_name;
            }
            if (cr.workshop_phone) {
                const wsPhoneElem = document.getElementById('workshop_phone');
                if (wsPhoneElem) wsPhoneElem.value = cr.workshop_phone;
            }
            if (cr.mechanic_name) {
                const mechNameElem = document.getElementById('mechanic_name');
                if (mechNameElem) mechNameElem.value = cr.mechanic_name;
            }
            if (cr.mechanic_phone) {
                const mechPhoneElem = document.getElementById('mechanic_phone');
                if (mechPhoneElem) mechPhoneElem.value = cr.mechanic_phone;
            }
        } catch (err) {
            console.error('Error autofilling workshop info:', err);
        }

        try {
            // 6. Autofill Tahap 4: 4 Sudut Foto Fisik & Dokumen Tambahan
            const angleKeys = ['photo_front', 'photo_right', 'photo_left', 'photo_behind'];
            angleKeys.forEach(fieldKey => {
                const previewBox = document.getElementById(`preview_box_${fieldKey}`);
                const imgElem = document.getElementById(`img_preview_${fieldKey}`);
                const labelText = document.getElementById(`upload_label_${fieldKey}`);

                if (cr.vehicle_photos && cr.vehicle_photos[fieldKey]) {
                    const photo = cr.vehicle_photos[fieldKey];
                    if (imgElem) imgElem.src = photo.url;
                    if (previewBox) previewBox.classList.remove('hidden');
                    if (labelText) labelText.innerText = 'Foto Terimpor (Klik ganti)';
                } else {
                    if (imgElem) imgElem.src = '';
                    if (previewBox) previewBox.classList.add('hidden');
                    if (labelText) {
                        if (fieldKey === 'photo_front') labelText.innerText = 'Pilih Foto Depan';
                        else if (fieldKey === 'photo_right') labelText.innerText = 'Pilih Foto Kanan';
                        else if (fieldKey === 'photo_left') labelText.innerText = 'Pilih Foto Kiri';
                        else if (fieldKey === 'photo_behind') labelText.innerText = 'Pilih Foto Belakang';
                    }
                }
            });

            // Populate General Proof Documents preview
            const fileListContainer = document.getElementById('file_list_preview');
            if (fileListContainer) {
                fileListContainer.innerHTML = '';
                if (cr.general_proofs && cr.general_proofs.length > 0) {
                    fileListContainer.classList.remove('hidden');
                    cr.general_proofs.forEach(file => {
                        const item = document.createElement('div');
                        item.className = 'p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-2.5 shadow-2xs';
                        item.innerHTML = `
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-[#015471]/10 text-[#015471] flex items-center justify-center shrink-0">
                                    <i data-lucide="${file.is_image ? 'image' : 'file-text'}" class="w-3.5 h-3.5"></i>
                                </div>
                                <span class="truncate font-bold text-slate-800 text-xs">${file.name}</span>
                            </div>
                            <span class="text-[10px] font-extrabold text-[#015471] bg-[#015471]/10 px-2 py-0.5 rounded shrink-0">Tiket Klien</span>
                        `;
                        fileListContainer.appendChild(item);
                    });
                } else {
                    fileListContainer.classList.add('hidden');
                }
            }
        } catch (err) {
            console.error('Error autofilling photo previews:', err);
        }

        calculateTotalCost();
        updateSummary();

        const badge = document.getElementById('autofill_badge_alert');
        if (badge) badge.classList.remove('hidden');

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    function clearClientRequestAutofill() {
        document.getElementById('client_request_selector').value = '';
        document.getElementById('autofill_badge_alert').classList.add('hidden');

        // Clear existing files hidden input
        const existingInput = document.getElementById('existing_proof_files');
        if (existingInput) existingInput.value = '';

        // Reset vehicle angles
        const angleKeys = ['photo_front', 'photo_right', 'photo_left', 'photo_behind'];
        angleKeys.forEach(fieldKey => {
            removeSingleVehiclePhoto(fieldKey);
        });

        // Reset general proofs list
        const fileListContainer = document.getElementById('file_list_preview');
        if (fileListContainer) {
            fileListContainer.innerHTML = '';
            fileListContainer.classList.add('hidden');
        }
    }

    function updateSummary() {
        document.getElementById('summaryVehicle').innerText = selectedVehicleFullText || '-';

        const type = getSelectedType();
        let typeText = 'Perbaikan / Servis & Sparepart';
        if (type === 'motor_pengganti') {
            typeText = 'Biaya Sewa Motor Pengganti';
        } else if (type === 'lainnya') {
            typeText = 'Pengeluaran Operasional Lainnya';
        }
        document.getElementById('summaryType').innerText = typeText;

        const title = document.getElementById('formItemName')?.value.trim() || '-';
        document.getElementById('summaryTitle').innerText = title;

        const driver = document.getElementById('driver_name')?.value.trim() || '-';
        const pic = document.getElementById('pic_name')?.value.trim();
        const picPhone = document.getElementById('pic_phone')?.value.trim();
        
        let driverSummary = driver;
        if (pic) {
            driverSummary += ` | PIC: ${pic}` + (picPhone ? ` (${picPhone})` : '');
        }
        document.getElementById('summaryDriver').innerText = driverSummary;

        const odo = document.getElementById('odometer_km')?.value.trim();
        const workshop = document.getElementById('workshop_name')?.value.trim();
        let odoWorkshopText = odo ? `${Number(odo).toLocaleString('id-ID')} KM` : '-';
        if (workshop) {
            odoWorkshopText += ` &bull; Bengkel: ${workshop}`;
        }
        document.getElementById('summaryOdometer').innerHTML = odoWorkshopText;

        const statusSelect = document.getElementById('vehicle_operational_status');
        if (statusSelect) {
            const statusText = statusSelect.options[statusSelect.selectedIndex]?.text || '-';
            document.getElementById('summaryCondition').innerText = statusText;
        }

        calculateTotalCost();
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

    function showReceiptFileName(input) {
        const preview = document.getElementById('receipt_file_preview');
        const text = document.getElementById('receipt_file_text');
        if (input.files && input.files[0]) {
            text.innerText = input.files[0].name;
            preview.classList.remove('hidden');
            preview.classList.add('flex');
        } else {
            preview.classList.add('hidden');
            preview.classList.remove('flex');
        }
    }

    function previewSelectedFiles(input) {
        const container = document.getElementById('file_list_preview');
        container.innerHTML = '';
        if (input.files && input.files.length > 0) {
            container.classList.remove('hidden');
            Array.from(input.files).forEach(file => {
                const item = document.createElement('div');
                item.className = 'p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center gap-2.5 shadow-2xs';
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

        const form = document.getElementById('maintenanceWizardForm');
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

                Swal.fire({
                    title: 'Simpan Data Pemeliharaan?',
                    html: `
                        <div class="text-left text-xs space-y-2 p-2 bg-slate-50 rounded-xl border border-slate-200 mt-2">
                            <div><strong class="text-slate-500">Armada Kendaraan:</strong> <span class="font-bold text-slate-900">${vehicleLabel}</span></div>
                            <div><strong class="text-slate-500">Total Biaya Riil:</strong> <span class="font-black text-[#FF7A3D] font-mono">${totalCostText}</span></div>
                            <p class="text-[11px] text-slate-500 pt-1 border-t border-slate-200">Data pemeliharaan akan disimpan dan otomatis dicatat ke dalam buku kas pengeluaran operasional.</p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#FF7A3D',
                    cancelButtonColor: '#64748B',
                    confirmButtonText: '<i data-lucide="check" class="w-4 h-4 inline-block mr-1"></i> Ya, Simpan Sekarang',
                    cancelButtonText: 'Periksa Kembali',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit the form
                        form.submit();
                    }
                });
            });
        }

        const initialVehicleId = document.getElementById('vehicle_id').value;
        if (initialVehicleId) {
            const preselectedItem = document.querySelector(`.vehicle-option-item[data-id="${initialVehicleId}"]`);
            if (preselectedItem) {
                const label = preselectedItem.getAttribute('data-label');
                const holder = preselectedItem.getAttribute('data-holder');
                const holderPhone = preselectedItem.getAttribute('data-holderphone');
                selectVehicle(initialVehicleId, label, holder, holderPhone);
            }
        }

        const selector = document.getElementById('client_request_selector');
        if (selector && selector.value) {
            applyClientRequestAutofill(selector.value);
        }
    });
</script>
@endpush
