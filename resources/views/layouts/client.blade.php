<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 text-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Layanan Klien') | PT Swadharma Utama Prima</title>
    
    <!-- Favicon / Icon -->
    <link rel="icon" type="image/png" href="{{ asset('img/icon/sup-icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/icon/sup-icon.png') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Scripts & Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- SweetAlert2 & Lucide Icons -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="h-full antialiased bg-slate-50 text-slate-900 flex flex-col">

    <!-- Top Navigation Bar (No Sidebar Layout) -->
    <header class="bg-[#015471] text-white border-b border-[#013e54] sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                
                <!-- Left: Logo & Portal Title -->
                <div class="flex items-center gap-4">
                    <a href="{{ route('client.dashboard') }}" class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-white p-1.5 flex items-center justify-center shrink-0 shadow-xs">
                            <img src="{{ asset('img/icon/sup-icon.png') }}" alt="PT SUP" class="w-full h-full object-contain">
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h1 class="font-extrabold text-base sm:text-lg text-white tracking-tight leading-tight">Portal Klien Mitra</h1>
                                <span class="hidden sm:inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-[#FF7A3D] text-white uppercase tracking-wider">
                                    Client Area
                                </span>
                            </div>
                            <span class="text-xs text-cyan-200 font-medium block">
                                {{ Auth::user()->vendor ? Auth::user()->vendor->name : 'PT Swadharma Utama Prima' }}
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Center: Navigation Menus -->
                <nav class="hidden md:flex items-center space-x-1 font-semibold text-xs">
                    <a href="{{ route('client.dashboard') }}" class="px-3.5 py-2 rounded-xl transition flex items-center gap-2 {{ request()->routeIs('client.dashboard') ? 'bg-white/15 text-white font-bold' : 'text-cyan-100 hover:text-white hover:bg-white/10' }}">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 text-[#FF7A3D]"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('client.requests.index') }}" class="px-3.5 py-2 rounded-xl transition flex items-center gap-2 {{ request()->routeIs('client.requests.*') ? 'bg-white/15 text-white font-bold' : 'text-cyan-100 hover:text-white hover:bg-white/10' }}">
                        <i data-lucide="activity" class="w-4 h-4 text-[#FF7A3D]"></i>
                        <span>Status Pengajuan Realtime</span>
                    </a>
                    <a href="{{ route('client.vehicles.index') }}" class="px-3.5 py-2 rounded-xl transition flex items-center gap-2 {{ request()->routeIs('client.vehicles.*') ? 'bg-white/15 text-white font-bold' : 'text-cyan-100 hover:text-white hover:bg-white/10' }}">
                        <i data-lucide="bike" class="w-4 h-4 text-[#FF7A3D]"></i>
                        <span>Daftar Armada Motor</span>
                    </a>
                </nav>

                <!-- Right: Quick Action & User Profile Dropdown -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('client.requests.create') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-xl bg-[#FF7A3D] hover:bg-[#e56b32] text-white shadow-sm transition">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span>Buat Pengajuan Baru</span>
                    </a>

                    <!-- User Account / Logout -->
                    <div class="flex items-center gap-2 pl-2 border-l border-white/20">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#FF7A3D] to-[#d95a20] text-white flex items-center justify-center font-bold text-xs shadow-2xs">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden lg:block text-left">
                            <span class="block text-xs font-bold text-white leading-tight truncate max-w-[120px]">{{ Auth::user()->name }}</span>
                            <span class="text-[10px] text-cyan-200">Klien</span>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-2 text-cyan-200 hover:text-white hover:bg-white/10 rounded-xl transition" title="Keluar">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>

            </div>

            <!-- Mobile Navigation Bar Under Header -->
            <div class="md:hidden flex items-center justify-around py-2.5 border-t border-white/10 text-xs font-bold">
                <a href="{{ route('client.dashboard') }}" class="flex items-center gap-1.5 py-1 px-2.5 rounded-lg {{ request()->routeIs('client.dashboard') ? 'text-white bg-white/15' : 'text-cyan-100' }}">
                    <i data-lucide="layout-dashboard" class="w-3.5 h-3.5 text-[#FF7A3D]"></i> Dashboard
                </a>
                <a href="{{ route('client.requests.index') }}" class="flex items-center gap-1.5 py-1 px-2.5 rounded-lg {{ request()->routeIs('client.requests.*') ? 'text-white bg-white/15' : 'text-cyan-100' }}">
                    <i data-lucide="activity" class="w-3.5 h-3.5 text-[#FF7A3D]"></i> Pengajuan
                </a>
                <a href="{{ route('client.vehicles.index') }}" class="flex items-center gap-1.5 py-1 px-2.5 rounded-lg {{ request()->routeIs('client.vehicles.*') ? 'text-white bg-white/15' : 'text-cyan-100' }}">
                    <i data-lucide="bike" class="w-3.5 h-3.5 text-[#FF7A3D]"></i> Armada
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content Area Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <!-- Toast Alerts -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-3">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0"></i>
                    <span class="font-bold">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0"></i>
                    <span class="font-bold">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-xs text-slate-500 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="font-bold text-[#015471]">PT Swadharma Utama Prima</span>
                <span>&bull;</span>
                <span>Layanan Portal Klien Sewa Armada Motor</span>
            </div>
            <div class="text-[11px] text-slate-400">
                Layanan Helpdesk & Penggantian Unit: <span class="font-bold text-slate-700">0812-3456-7890</span>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        function confirmDelete(formId, itemName) {
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Data " + itemName + " akan dihapus secara permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#FF7A3D',
                cancelButtonColor: '#64748b',
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
