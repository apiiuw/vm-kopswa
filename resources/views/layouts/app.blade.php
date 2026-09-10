<!DOCTYPE html>
<html lang="id" class="h-full bg-white text-black">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Monitoring Kendaraan') | PT Swadharma Utama Prima</title>
    
    <!-- Favicon / Icon -->
    <link rel="icon" type="image/png" href="{{ asset('img/icon/sup-icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/icon/sup-icon.png') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Scripts & Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Fallback CDN for sweetalert2 & chartjs for fast styling reliability -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
            --primary: #FF7A3D;
            --primary-hover: #e56b32;
            --secondary: #015471;
            --secondary-dark: #013e54;
            --secondary-light: #e6f0f3;
            --bg-color: #ffffff;
            --text-color: #000000;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
        }
        .btn-primary {
            background-color: #FF7A3D !important;
            color: #ffffff !important;
        }
        .btn-primary:hover {
            background-color: #e56b32 !important;
        }
        .btn-secondary {
            background-color: #015471 !important;
            color: #ffffff !important;
        }
        .btn-secondary:hover {
            background-color: #013e54 !important;
        }
        .border-secondary {
            border-color: #015471 !important;
        }
        .text-secondary {
            color: #015471 !important;
        }
        .text-primary-color {
            color: #FF7A3D !important;
        }

        /* Custom Floating Sleek Scrollbar for Sidebar */
        aside nav {
            scrollbar-width: thin !important;
            scrollbar-color: rgba(255, 122, 61, 0.4) transparent !important;
            padding-right: 4px;
        }
        aside nav::-webkit-scrollbar {
            width: 3px !important;
        }
        aside nav::-webkit-scrollbar-track {
            background: transparent !important;
            margin: 6px 0;
        }
        aside nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.25) !important;
            border-radius: 9999px !important;
        }
        aside nav:hover::-webkit-scrollbar-thumb {
            background: rgba(255, 122, 61, 0.7) !important;
        }
        aside nav::-webkit-scrollbar-thumb:hover {
            background: #FF7A3D !important;
        }

        /* Custom horizontal scrollbar */
        .scrollbar-thin::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 9999px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #015471;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full antialiased flex overflow-hidden bg-white text-black">

    <!-- Sidebar (Full Solid Blue Theme #015471) -->
    <aside class="w-64 bg-[#015471] text-white flex flex-col shrink-0 border-r border-[#01435b] z-30 transition-all duration-300">
        <!-- Logo Area (Height 64px / h-16, perfectly aligned with topbar header) -->
        <div class="h-16 flex items-center px-5 border-b border-[#01435b] bg-[#015471]">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 w-full">
                <div class="w-10 h-10 rounded-xl bg-white p-1.5 flex items-center justify-center shrink-0 shadow-xs">
                    <img src="{{ asset('img/icon/sup-icon.png') }}" alt="SUP Icon" class="w-full h-full object-contain">
                </div>
                <div class="overflow-hidden">
                    <h1 class="text-white font-extrabold text-base tracking-tight leading-none truncate">PT SUP</h1>
                    <span class="text-[10px] font-bold text-[#FF7A3D] uppercase tracking-wider block mt-0.5 truncate">Vehicle Monitoring</span>
                </div>
            </a>
        </div>

        <!-- Multi-Client Selector Badge in Sidebar -->
        <div class="px-3.5 py-3 mx-3 my-2.5 rounded-xl bg-[#014760] border border-[#026c91] text-xs">
            <form action="{{ route('vendors.switch') }}" method="POST" id="vendorSwitchForm" class="space-y-1.5">
                @csrf
                <div class="flex items-center justify-between text-slate-200">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-300 flex items-center gap-1.5 whitespace-nowrap">
                        <i data-lucide="building-2" class="w-3.5 h-3.5 text-[#FF7A3D] shrink-0"></i>
                        <span>Filter Klien Sewa</span>
                    </span>
                    @if($currentVendor)
                        <span class="px-1.5 py-0.5 rounded text-[9px] bg-[#FF7A3D] text-white font-bold whitespace-nowrap">{{ $currentVendor->code }}</span>
                    @else
                        <span class="px-1.5 py-0.5 rounded text-[9px] bg-slate-700 text-slate-300 font-bold whitespace-nowrap">Semua</span>
                    @endif
                </div>

                <select name="vendor_id" onchange="document.getElementById('vendorSwitchForm').submit()" class="w-full bg-[#003447] border border-[#026c91] text-white text-xs rounded-lg px-2.5 py-1.5 focus:outline-none focus:border-[#FF7A3D]">
                    <option value="all" {{ empty($activeVendorId) ? 'selected' : '' }}>-- Semua Klien (Gabungan) --</option>
                    @foreach($globalVendors as $v)
                        <option value="{{ $v->id }}" {{ $activeVendorId == $v->id ? 'selected' : '' }}>
                            {{ $v->name }} ({{ $v->code }})
                        </option>
                    @endforeach
                </select>

                @if($currentVendor && $currentVendor->contract_number)
                    <p class="text-[10px] text-slate-300 truncate pt-0.5" title="No. Kontrak: {{ $currentVendor->contract_number }}">
                        No: <span class="font-mono text-white">{{ $currentVendor->contract_number }}</span>
                    </p>
                @endif
            </form>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto px-3 py-2 space-y-1 text-sm font-medium sidebar-scroll">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors whitespace-nowrap {{ request()->routeIs('dashboard') ? 'bg-[#FF7A3D] text-white font-bold shadow-sm' : 'text-slate-100 hover:bg-[#01435b] hover:text-white' }}">
                <i data-lucide="layout-dashboard" class="w-4 h-4 shrink-0"></i>
                <span class="whitespace-nowrap">Dashboard</span>
            </a>

            <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-300 whitespace-nowrap">Klien & Kontrak Sewa</div>

            <a href="{{ route('vendors.index') }}" class="flex items-center justify-between gap-3 px-3.5 py-2.5 rounded-xl transition-colors whitespace-nowrap {{ request()->routeIs('vendors.*') ? 'bg-[#FF7A3D] text-white font-bold shadow-sm' : 'text-slate-100 hover:bg-[#01435b] hover:text-white' }}">
                <div class="flex items-center gap-3 min-w-0">
                    <i data-lucide="building-2" class="w-4 h-4 shrink-0"></i>
                    <span class="whitespace-nowrap">Klien Proyek Sewa</span>
                </div>
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full shrink-0 {{ request()->routeIs('vendors.*') ? 'bg-white text-[#FF7A3D]' : 'bg-[#013e54] text-white' }}">{{ $globalVendors->count() }}</span>
            </a>

            <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-300 whitespace-nowrap">Layanan & Permohonan Klien</div>

            <a href="{{ route('admin.client-requests.index') }}" class="flex items-center justify-between gap-3 px-3.5 py-2.5 rounded-xl transition-colors whitespace-nowrap {{ request()->routeIs('admin.client-requests.*') ? 'bg-[#FF7A3D] text-white font-bold shadow-sm' : 'text-slate-100 hover:bg-[#01435b] hover:text-white' }}">
                <div class="flex items-center gap-3 min-w-0">
                    <i data-lucide="inbox" class="w-4 h-4 shrink-0"></i>
                    <span class="whitespace-nowrap">Tiket & Pengajuan Klien</span>
                </div>
                @if(!empty($globalPendingRequestsCount) && $globalPendingRequestsCount > 0)
                    <span class="px-2 py-0.5 text-[10px] font-black rounded-full shrink-0 {{ request()->routeIs('admin.client-requests.*') ? 'bg-white text-[#FF7A3D]' : 'bg-[#FF7A3D] text-white' }} animate-pulse shadow-xs">
                        {{ $globalPendingRequestsCount }}
                    </span>
                @endif
            </a>

            <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-300 whitespace-nowrap">Armada & Operasional</div>

            <a href="{{ route('vehicles.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors whitespace-nowrap {{ request()->routeIs('vehicles.*') ? 'bg-[#FF7A3D] text-white font-bold shadow-sm' : 'text-slate-100 hover:bg-[#01435b] hover:text-white' }}">
                <i data-lucide="bike" class="w-4 h-4 shrink-0"></i>
                <span class="whitespace-nowrap">Data Armada</span>
            </a>

            <a href="{{ route('maintenance.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors whitespace-nowrap {{ request()->routeIs('maintenance.*') ? 'bg-[#FF7A3D] text-white font-bold shadow-sm' : 'text-slate-100 hover:bg-[#01435b] hover:text-white' }}">
                <i data-lucide="wrench" class="w-4 h-4 shrink-0"></i>
                <span class="whitespace-nowrap">Servis & Pemeliharaan</span>
            </a>

            <a href="{{ route('documents.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors whitespace-nowrap {{ request()->routeIs('documents.*') ? 'bg-[#FF7A3D] text-white font-bold shadow-sm' : 'text-slate-100 hover:bg-[#01435b] hover:text-white' }}">
                <i data-lucide="file-check" class="w-4 h-4 shrink-0"></i>
                <span class="whitespace-nowrap">STNK & Pajak</span>
            </a>

            <a href="{{ route('insurance.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('insurance.*') ? 'bg-[#FF7A3D] text-white font-bold shadow-sm' : 'text-slate-100 hover:bg-[#01435b] hover:text-white' }}">
                <i data-lucide="shield-alert" class="w-4 h-4 shrink-0"></i>
                <span class="whitespace-nowrap">Asuransi & Klaim</span>
            </a>

            <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-300 whitespace-nowrap">Keuangan & Anggaran</div>

            <a href="{{ route('financial.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors whitespace-nowrap {{ request()->routeIs('financial.*') ? 'bg-[#FF7A3D] text-white font-bold shadow-sm' : 'text-slate-100 hover:bg-[#01435b] hover:text-white' }}">
                <i data-lucide="wallet" class="w-4 h-4 shrink-0"></i>
                <span class="whitespace-nowrap">Pendapatan & Kas</span>
            </a>

            <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-300 whitespace-nowrap">Pengaturan</div>

            <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors whitespace-nowrap {{ request()->routeIs('users.*') ? 'bg-[#FF7A3D] text-white font-bold shadow-sm' : 'text-slate-100 hover:bg-[#01435b] hover:text-white' }}">
                <i data-lucide="users" class="w-4 h-4 shrink-0"></i>
                <span class="whitespace-nowrap">Pengguna Internal</span>
            </a>
        </nav>

        <!-- User Profile Bottom -->
        <div class="p-3 border-t border-[#01435b] bg-[#003c52]">
            <div class="flex items-center justify-between p-2 rounded-lg bg-[#015471]/80">
                <div class="flex items-center gap-2.5 overflow-hidden">
                    <div class="w-8 h-8 rounded-full bg-[#FF7A3D] text-white flex items-center justify-center font-bold text-xs shadow-xs shrink-0">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="truncate">
                        <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name ?? 'Internal User' }}</p>
                        <p class="text-[10px] text-slate-200 truncate">{{ Auth::user()->email ?? '' }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                    @csrf
                    <button type="button" onclick="confirmLogout()" class="p-1.5 text-slate-200 hover:text-[#FF7A3D] rounded-md hover:bg-[#01435b] transition shrink-0" title="Logout">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Container -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-white">
        <!-- Topbar (Perfect 64px height, vertically centered and aligned) -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 z-20 shrink-0">
            <!-- Left: Page Title & Current Vendor Badge -->
            <div class="flex items-center gap-3 min-w-0">
                <h2 class="text-base font-extrabold text-black tracking-tight leading-none truncate">@yield('page_title', 'Dashboard Monitoring')</h2>
                @if($currentVendor)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#015471]/10 text-[#015471] text-xs font-bold border border-[#015471]/20 shrink-0">
                        <i data-lucide="building-2" class="w-3.5 h-3.5 text-[#FF7A3D]"></i>
                        <span>{{ $currentVendor->name }}</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200 shrink-0">
                        <i data-lucide="layers" class="w-3.5 h-3.5 text-[#FF7A3D]"></i>
                        <span>Semua Klien</span>
                    </span>
                @endif
            </div>

            <!-- Right: Action Buttons & Context Info -->
            <div class="flex items-center gap-3 shrink-0">
                <!-- Quick Action Exports Dropdown -->
                <div class="relative group">
                    <button class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold rounded-xl bg-[#015471] hover:bg-[#013e54] text-white transition shadow-xs">
                        <i data-lucide="download" class="w-4 h-4 text-[#FF7A3D]"></i>
                        <span>Ekspor Laporan</span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 opacity-80"></i>
                    </button>
                    <div class="absolute right-0 mt-1.5 w-60 bg-white rounded-2xl shadow-xl border border-slate-200 py-1.5 hidden group-hover:block group-focus-within:block z-50">
                        <a href="{{ route('exports.budgetPdf') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-xs text-black hover:bg-[#e6f0f3] hover:text-[#015471] font-medium transition">
                            <i data-lucide="file-text" class="w-4 h-4 text-[#FF7A3D]"></i>
                            <span>PDF Rekap Anggaran</span>
                        </a>
                        <a href="{{ route('exports.vehiclesPdf') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-xs text-black hover:bg-[#e6f0f3] hover:text-[#015471] font-medium transition">
                            <i data-lucide="file-text" class="w-4 h-4 text-[#FF7A3D]"></i>
                            <span>PDF Daftar Armada</span>
                        </a>
                        <div class="border-t border-slate-100 my-1"></div>
                        <a href="{{ route('exports.financialExcel') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-xs text-black hover:bg-[#e6f0f3] hover:text-[#015471] font-medium transition">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4 text-[#015471]"></i>
                            <span>Excel Kas & Realisasi Anggaran</span>
                        </a>
                        <a href="{{ route('exports.vehiclesExcel') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-xs text-black hover:bg-[#e6f0f3] hover:text-[#015471] font-medium transition">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4 text-[#015471]"></i>
                            <span>Excel Master Data Kendaraan</span>
                        </a>
                    </div>
                </div>

                <div class="h-6 w-px bg-slate-200"></div>

                <!-- Vendor Context Tag: PT SUP Vendor Penyedia -->
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-extrabold text-black leading-tight">
                        {{ $currentVendor ? $currentVendor->name : 'PT SUP (Vendor Penyedia Sewa)' }}
                    </p>
                    <p class="text-[10px] text-[#015471] font-semibold leading-tight mt-0.5">
                        {{ $currentVendor ? ($currentVendor->project_name ?? 'Monitoring Armada Sewa') : 'Seluruh Unit Sewaan Klien' }}
                    </p>
                </div>
            </div>
        </header>

        <!-- Page Content Scroll Area -->
        <main class="flex-1 overflow-y-auto bg-white p-6">
            @yield('content')
        </main>
    </div>

    <!-- SweetAlert2 Trigger Notifications -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: {!! json_encode(html_entity_decode(session('success'), ENT_QUOTES, 'UTF-8')) !!},
                    confirmButtonColor: '#FF7A3D',
                    timer: 3000
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: {!! json_encode(html_entity_decode(session('error'), ENT_QUOTES, 'UTF-8')) !!},
                    confirmButtonColor: '#015471'
                });
            @endif

            @if(session('info'))
                Swal.fire({
                    icon: 'info',
                    title: 'Informasi',
                    text: {!! json_encode(html_entity_decode(session('info'), ENT_QUOTES, 'UTF-8')) !!},
                    confirmButtonColor: '#015471'
                });
            @endif
        });

        function confirmLogout() {
            Swal.fire({
                title: 'Konfirmasi Keluar',
                text: "Apakah Anda yakin ingin keluar dari sistem monitoring?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#FF7A3D',
                cancelButtonColor: '#015471',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logoutForm').submit();
                }
            });
        }

        function confirmDelete(formId, itemName = 'data ini') {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Apakah Anda yakin ingin menghapus ${itemName}? Tindakan ini tidak dapat dibatalkan!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FF7A3D',
                cancelButtonColor: '#015471',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
