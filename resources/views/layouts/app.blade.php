<!DOCTYPE html>
<html lang="id" class="h-full bg-white text-black">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Monitoring Kendaraan') | Koperasi Swadharma</title>
    
    <!-- Favicon / Icon -->
    <link rel="icon" type="image/png" href="{{ asset('img/icon/kopswa-icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/icon/kopswa-icon.png') }}">
    
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

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div id="sidebarBackdrop" onclick="toggleSidebar(false)" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-30 hidden lg:hidden transition-opacity duration-300"></div>

    <!-- Sidebar (Full Solid Blue Theme #015471) - Responsive Offcanvas Drawer on Mobile, Fixed Static on Desktop -->
    <aside id="mainSidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-[#015471] text-white flex flex-col shrink-0 border-r border-[#01435b] transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 lg:static lg:z-30">
        <!-- Logo Area (Height 64px / h-16, perfectly aligned with topbar header) -->
        <div class="h-16 flex items-center justify-between px-5 border-b border-[#01435b] bg-[#015471]">
            <a href="{{ route('dashboard') }}" class="flex flex-col justify-center min-w-0 py-1 group">
                <img src="{{ asset('img/logo/logo-kopswa.png') }}" alt="KOPSWA" class="h-7 w-auto max-w-[165px] object-contain object-left group-hover:opacity-95 transition-opacity">
                <span class="text-[9px] font-extrabold text-[#FF7A3D] uppercase tracking-widest block mt-1 leading-none pl-0.5">Vehicle Monitoring</span>
            </a>
            <!-- Mobile Close Button (Hidden on Desktop) -->
            <button type="button" onclick="toggleSidebar(false)" class="lg:hidden p-1.5 rounded-xl text-slate-300 hover:text-white hover:bg-[#01435b] transition" title="Tutup Menu">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Multi-Contract Selector Badge in Sidebar (with Searchable Dropdown) -->
        <div class="px-3.5 py-3 mx-3 my-2.5 rounded-xl bg-[#014760] border border-[#026c91] text-xs relative">
            <form action="{{ route('vendors.switch') }}" method="POST" id="vendorSwitchForm" class="space-y-1.5">
                @csrf
                <input type="hidden" name="vendor_id" id="sidebarVendorIdInput" value="{{ $activeVendorId ?? 'all' }}">
                <div class="flex items-center justify-between text-slate-200">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-300 flex items-center gap-1.5 whitespace-nowrap">
                        <i data-lucide="file-text" class="w-3.5 h-3.5 text-[#FF7A3D] shrink-0"></i>
                        <span>Filter Kontrak Sewa</span>
                    </span>
                    @if($currentVendor)
                        <span class="px-1.5 py-0.5 rounded text-[9px] bg-[#FF7A3D] text-white font-bold whitespace-nowrap">{{ $currentVendor->code }}</span>
                    @else
                        <span class="px-1.5 py-0.5 rounded text-[9px] bg-slate-700 text-slate-300 font-bold whitespace-nowrap">Semua</span>
                    @endif
                </div>

                <!-- Trigger Button -->
                <button type="button" onclick="toggleContractDropdown()" class="w-full bg-[#003447] border border-[#026c91] hover:border-[#FF7A3D] text-white text-xs rounded-lg px-2.5 py-2 flex items-center justify-between gap-2 text-left transition">
                    <span class="truncate font-semibold">
                        @if($currentVendor)
                            {{ $currentVendor->contract_number ?: $currentVendor->code }} — {{ $currentVendor->name }}
                        @else
                            -- Semua Kontrak (Gabungan) --
                        @endif
                    </span>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-300 shrink-0"></i>
                </button>

                <!-- Searchable Dropdown Panel -->
                <div id="contractDropdownMenu" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-white text-slate-900 rounded-xl shadow-2xl border border-slate-200 z-50 overflow-hidden">
                    <div class="p-2 border-b border-slate-100 bg-slate-50">
                        <div class="relative">
                            <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5"></i>
                            <input type="text" id="contractSearchInput" onkeyup="filterSidebarContracts()" placeholder="Cari no. kontrak / klien..." class="w-full pl-8 pr-2.5 py-1.5 text-xs rounded-lg border border-slate-300 focus:outline-none focus:border-[#015471] text-slate-900 bg-white">
                        </div>
                    </div>
                    <div class="max-h-56 overflow-y-auto divide-y divide-slate-100" id="contractOptionsList">
                        <button type="button" onclick="selectSidebarContract('all')" class="contract-option w-full text-left px-3 py-2 text-xs hover:bg-slate-50 transition {{ empty($activeVendorId) ? 'bg-[#015471]/10 font-bold text-[#015471]' : 'text-slate-700' }}" data-search="semua kontrak gabungan">
                            -- Semua Kontrak (Gabungan) --
                        </button>
                        @foreach($globalVendors as $v)
                            <button type="button" onclick="selectSidebarContract('{{ $v->id }}')" class="contract-option w-full text-left px-3 py-2 text-xs hover:bg-slate-50 transition {{ $activeVendorId == $v->id ? 'bg-[#015471]/10 font-bold text-[#015471]' : 'text-slate-800' }}" data-search="{{ strtolower(($v->contract_number ?? '') . ' ' . $v->code . ' ' . $v->name) }}">
                                <div class="font-mono font-bold text-[11px] text-[#015471] truncate">{{ $v->contract_number ?: $v->code }}</div>
                                <div class="text-[10px] text-slate-500 truncate">{{ $v->name }}</div>
                            </button>
                        @endforeach
                    </div>
                </div>

                @if($currentVendor && $currentVendor->contract_number)
                    <p class="text-[10px] text-slate-300 truncate pt-0.5" title="Klien: {{ $currentVendor->name }}">
                        Klien: <span class="text-white font-semibold">{{ $currentVendor->name }}</span>
                    </p>
                @endif
            </form>
        </div>

        <script>
            function toggleContractDropdown() {
                const menu = document.getElementById('contractDropdownMenu');
                menu.classList.toggle('hidden');
                if (!menu.classList.contains('hidden')) {
                    setTimeout(() => document.getElementById('contractSearchInput').focus(), 50);
                }
            }
            function filterSidebarContracts() {
                const q = document.getElementById('contractSearchInput').value.toLowerCase();
                document.querySelectorAll('#contractOptionsList .contract-option').forEach(btn => {
                    const hay = btn.dataset.search || '';
                    btn.style.display = hay.includes(q) ? '' : 'none';
                });
            }
            function selectSidebarContract(val) {
                document.getElementById('sidebarVendorIdInput').value = val;
                document.getElementById('vendorSwitchForm').submit();
            }
            document.addEventListener('click', function(e) {
                const form = document.getElementById('vendorSwitchForm');
                const menu = document.getElementById('contractDropdownMenu');
                if (form && menu && !form.contains(e.target)) {
                    menu.classList.add('hidden');
                }
            });
        </script>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto px-3 py-2 space-y-1 text-sm font-medium sidebar-scroll">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors whitespace-nowrap {{ request()->routeIs('dashboard') ? 'bg-[#FF7A3D] text-white font-bold shadow-sm' : 'text-slate-100 hover:bg-[#01435b] hover:text-white' }}">
                <i data-lucide="layout-dashboard" class="w-4 h-4 shrink-0"></i>
                <span class="whitespace-nowrap">Dashboard</span>
            </a>

            <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-300 whitespace-nowrap">Klien & Kontrak Sewa</div>

            <a href="{{ route('clients.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors whitespace-nowrap {{ request()->routeIs('clients.*') ? 'bg-[#FF7A3D] text-white font-bold shadow-sm' : 'text-slate-100 hover:bg-[#01435b] hover:text-white' }}">
                <i data-lucide="building-2" class="w-4 h-4 shrink-0"></i>
                <span class="whitespace-nowrap">Master Klien</span>
            </a>

            <a href="{{ route('vendors.index') }}" class="flex items-center justify-between gap-3 px-3.5 py-2.5 rounded-xl transition-colors whitespace-nowrap {{ request()->routeIs('vendors.*') ? 'bg-[#FF7A3D] text-white font-bold shadow-sm' : 'text-slate-100 hover:bg-[#01435b] hover:text-white' }}">
                <div class="flex items-center gap-3 min-w-0">
                    <i data-lucide="file-text" class="w-4 h-4 shrink-0"></i>
                    <span class="whitespace-nowrap">Kontrak Sewa</span>
                </div>
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full shrink-0 {{ request()->routeIs('vendors.*') ? 'bg-white text-[#FF7A3D]' : 'bg-[#013e54] text-white' }}">{{ $globalVendors->count() }}</span>
            </a>

            <a href="{{ route('bast.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors whitespace-nowrap {{ request()->routeIs('bast.*') ? 'bg-[#FF7A3D] text-white font-bold shadow-sm' : 'text-slate-100 hover:bg-[#01435b] hover:text-white' }}">
                <i data-lucide="file-signature" class="w-4 h-4 shrink-0"></i>
                <span class="whitespace-nowrap">BAST & Serah Terima</span>
            </a>

            <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-300 whitespace-nowrap">Layanan & Permohonan Klien</div>

            <a href="{{ route('admin.client-requests.index') }}" class="flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('admin.client-requests.*') ? 'bg-[#FF7A3D] text-white font-bold shadow-sm' : 'text-slate-100 hover:bg-[#01435b] hover:text-white' }}">
                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                    <i data-lucide="inbox" class="w-4 h-4 shrink-0"></i>
                    <span class="truncate text-[13px]">Tiket & Pengajuan</span>
                </div>
                @if(!empty($globalPendingRequestsCount) && $globalPendingRequestsCount > 0)
                    <span class="w-5 h-5 flex items-center justify-center text-[10px] font-black rounded-full shrink-0 {{ request()->routeIs('admin.client-requests.*') ? 'bg-white text-[#FF7A3D]' : 'bg-[#FF7A3D] text-white' }} animate-pulse shadow-xs ml-1">
                        {{ $globalPendingRequestsCount }}
                    </span>
                @endif
            </a>

            <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-300 whitespace-nowrap">Aset & Operasional</div>

            <a href="{{ route('vehicles.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors whitespace-nowrap {{ request()->routeIs('vehicles.*') ? 'bg-[#FF7A3D] text-white font-bold shadow-sm' : 'text-slate-100 hover:bg-[#01435b] hover:text-white' }}">
                <i data-lucide="car" class="w-4 h-4 shrink-0"></i>
                <span class="whitespace-nowrap">Data Aset & Unit</span>
            </a>

            <a href="{{ route('maintenance.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors whitespace-nowrap {{ request()->routeIs('maintenance.*') ? 'bg-[#FF7A3D] text-white font-bold shadow-sm' : 'text-slate-100 hover:bg-[#01435b] hover:text-white' }}">
                <i data-lucide="wrench" class="w-4 h-4 shrink-0"></i>
                <span class="whitespace-nowrap">Servis & Pengganti</span>
            </a>

            <a href="{{ route('documents.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors whitespace-nowrap {{ request()->routeIs('documents.*') ? 'bg-[#FF7A3D] text-white font-bold shadow-sm' : 'text-slate-100 hover:bg-[#01435b] hover:text-white' }}">
                <i data-lucide="file-check" class="w-4 h-4 shrink-0"></i>
                <span class="whitespace-nowrap">STNK & Pajak</span>
            </a>

            <a href="{{ route('insurance.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors {{ request()->routeIs('insurance.*') ? 'bg-[#FF7A3D] text-white font-bold shadow-sm' : 'text-slate-100 hover:bg-[#01435b] hover:text-white' }}">
                <i data-lucide="shield-alert" class="w-4 h-4 shrink-0"></i>
                <span class="whitespace-nowrap">Polis Asuransi & Klaim</span>
            </a>

            <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-300 whitespace-nowrap">Keuangan & Pajak</div>

            <a href="{{ route('financial.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors whitespace-nowrap {{ request()->routeIs('financial.*') ? 'bg-[#FF7A3D] text-white font-bold shadow-sm' : 'text-slate-100 hover:bg-[#01435b] hover:text-white' }}">
                <i data-lucide="wallet" class="w-4 h-4 shrink-0"></i>
                <span class="whitespace-nowrap">Keuangan & Coretax</span>
            </a>

            <div class="pt-3 pb-1 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-300 whitespace-nowrap">Pengaturan</div>

            <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors whitespace-nowrap {{ request()->routeIs('users.*') ? 'bg-[#FF7A3D] text-white font-bold shadow-sm' : 'text-slate-100 hover:bg-[#01435b] hover:text-white' }}">
                <i data-lucide="users" class="w-4 h-4 shrink-0"></i>
                <span class="whitespace-nowrap">Pengguna & Akses</span>
            </a>

            <a href="{{ route('integrations.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-colors whitespace-nowrap {{ request()->routeIs('integrations.*') ? 'bg-[#FF7A3D] text-white font-bold shadow-sm' : 'text-slate-100 hover:bg-[#01435b] hover:text-white' }}">
                <i data-lucide="plug-zap" class="w-4 h-4 shrink-0"></i>
                <span class="whitespace-nowrap">Integrasi API</span>
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
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-3.5 sm:px-6 z-20 shrink-0 gap-2">
            <!-- Left: Mobile Menu Toggle & Page Title & Current Vendor Badge -->
            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                <!-- Mobile Hamburger Button (Hidden on lg / desktop) -->
                <button type="button" onclick="toggleSidebar(true)" class="lg:hidden p-2 rounded-xl text-slate-700 hover:bg-slate-100 hover:text-[#015471] transition shrink-0" title="Buka Navigasi">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>

                <h2 class="text-sm sm:text-base font-extrabold text-black tracking-tight leading-none truncate">@yield('page_title', 'Dashboard Monitoring')</h2>
                @if($currentVendor)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#015471]/10 text-[#015471] text-xs font-bold border border-[#015471]/20 shrink-0 hidden sm:inline-flex">
                        <i data-lucide="building-2" class="w-3.5 h-3.5 text-[#FF7A3D]"></i>
                        <span>{{ $currentVendor->name }}</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold border border-slate-200 shrink-0 hidden sm:inline-flex">
                        <i data-lucide="layers" class="w-3.5 h-3.5 text-[#FF7A3D]"></i>
                        <span>Semua Klien</span>
                    </span>
                @endif
            </div>

            <!-- Right: Action Buttons & Context Info -->
            <div class="flex items-center gap-2 sm:gap-3 shrink-0"></div>
        </header>

        <!-- Page Content Scroll Area -->
        <main class="flex-1 overflow-y-auto bg-white p-3.5 sm:p-6">
            @if(isset($currentVendor) && $currentVendor && !request()->routeIs('vendors.*'))
                <div class="mb-4 px-4 py-2.5 rounded-xl bg-amber-50 border border-amber-200 flex flex-wrap items-center justify-between gap-2 text-xs">
                    <div class="flex items-center gap-2 text-amber-900">
                        <i data-lucide="filter" class="w-4 h-4 text-[#FF7A3D] shrink-0"></i>
                        <span>
                            Sedang memfilter data khusus kontrak: <strong>{{ $currentVendor->name }}</strong>
                            @if($currentVendor->contract_number)
                                (<span class="font-mono">{{ $currentVendor->contract_number }}</span>)
                            @endif
                        </span>
                    </div>
                    <form action="{{ route('vendors.switch') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="vendor_id" value="all">
                        <button type="submit" class="px-3 py-1 rounded-lg bg-white border border-amber-300 text-amber-900 font-bold hover:bg-amber-100 transition text-[11px]">
                            Tampilkan Semua Kontrak
                        </button>
                    </form>
                </div>
            @endif
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

        // Responsive Offcanvas Mobile Sidebar Toggle
        function toggleSidebar(open) {
            const sidebar = document.getElementById('mainSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (!sidebar || !backdrop) return;

            if (open) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }

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
