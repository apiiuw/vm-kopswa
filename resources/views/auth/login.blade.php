<!DOCTYPE html>
<html lang="id" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Internal | PT Swadharma Utama Prima</title>
    
    <!-- Favicon / Icon -->
    <link rel="icon" type="image/png" href="{{ asset('img/icon/sup-icon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/icon/sup-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Ambient floating animated orbs */
        @keyframes float-slow {
            0%, 100% { transform: translateY(0px) rotate(0deg) scale(1); }
            50% { transform: translateY(-25px) rotate(8deg) scale(1.05); }
        }
        @keyframes float-reverse {
            0%, 100% { transform: translateY(0px) rotate(0deg) scale(1); }
            50% { transform: translateY(28px) rotate(-10deg) scale(0.95); }
        }
        @keyframes pulse-glow {
            0%, 100% { opacity: 0.35; transform: scale(1); }
            50% { opacity: 0.65; transform: scale(1.12); }
        }
        @keyframes subtle-drift {
            0% { transform: translate(0, 0); }
            50% { transform: translate(-30px, 20px); }
            100% { transform: translate(0, 0); }
        }
        @keyframes shimmer-line {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        @keyframes card-entry {
            0% { opacity: 0; transform: translateY(30px) scale(0.96); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes float-badge {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        .animate-float-slow {
            animation: float-slow 14s ease-in-out infinite;
        }
        .animate-float-reverse {
            animation: float-reverse 18s ease-in-out infinite;
        }
        .animate-pulse-glow {
            animation: pulse-glow 7s ease-in-out infinite;
        }
        .animate-subtle-drift {
            animation: subtle-drift 22s ease-in-out infinite;
        }
        .animate-card-entry {
            animation: card-entry 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        .animate-float-badge {
            animation: float-badge 4s ease-in-out infinite;
        }

        /* Modern subtle grid background */
        .grid-pattern {
            background-image: radial-gradient(rgba(1, 84, 113, 0.12) 1.2px, transparent 1.2px);
            background-size: 28px 28px;
        }

        /* Glassmorphic card styling */
        .glass-card {
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        /* Button ripple & glow effect */
        .btn-glow {
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }
        .btn-glow:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px -6px rgba(255, 122, 61, 0.45), 0 0 0 1px rgba(255, 122, 61, 0.3);
        }
        .btn-glow:active {
            transform: translateY(0px) scale(0.99);
        }
        .btn-glow::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.25), transparent);
            transform: translateX(-100%);
        }
        .btn-glow:hover::after {
            animation: shimmer-line 1.2s infinite;
        }
    </style>
</head>
<body class="h-full relative overflow-x-hidden bg-white text-black selection:bg-[#FF7A3D] selection:text-white">

    <!-- Interactive Canvas Background for Animated Floating Network/Particles -->
    <canvas id="particleCanvas" class="fixed inset-0 w-full h-full pointer-events-none z-0 opacity-70"></canvas>

    <!-- Ambient Glowing Blurs (Full Animation Background) -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <!-- Floating Orb 1 (Secondary #015471) -->
        <div class="absolute -top-32 -left-32 w-[520px] h-[520px] rounded-full bg-[#015471]/15 blur-[120px] animate-float-slow"></div>
        <!-- Floating Orb 2 (Primary #FF7A3D) -->
        <div class="absolute top-1/4 -right-40 w-[600px] h-[600px] rounded-full bg-[#FF7A3D]/18 blur-[140px] animate-float-reverse"></div>
        <!-- Floating Orb 3 (Bottom Center Blend) -->
        <div class="absolute -bottom-40 left-1/3 w-[550px] h-[550px] rounded-full bg-[#015471]/12 blur-[130px] animate-pulse-glow"></div>
        <!-- Grid pattern overlay -->
        <div class="absolute inset-0 grid-pattern opacity-60"></div>
    </div>

    <!-- Main Container -->
    <div class="min-h-full flex items-center justify-center p-4 sm:p-6 lg:p-8 relative z-10">
        <div class="max-w-md w-full mx-auto">
            
            <!-- Login Card with Animation -->
            <div class="glass-card rounded-3xl p-8 sm:p-10 border-2 border-[#015471]/25 shadow-2xl shadow-[#015471]/15 space-y-6 animate-card-entry relative overflow-hidden">
                
                <!-- Decorative Top Ribbon -->
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-[#015471] via-[#FF7A3D] to-[#015471]"></div>

                <!-- Logo Header -->
                <div class="text-center space-y-3 pt-2">
                    <div class="inline-flex justify-center items-center p-2.5 rounded-2xl bg-white border border-slate-200 shadow-sm mx-auto transition-transform hover:scale-105 duration-300">
                        <img src="{{ asset('img/logo/sup-logo.png') }}" alt="Logo" class="h-12 w-auto object-contain max-w-[220px]" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="items-center gap-2 hidden px-3 py-1">
                            <img src="{{ asset('img/icon/sup-icon.png') }}" alt="Icon" class="w-8 h-8 object-contain">
                            <span class="font-black text-[#015471] text-base">Fleet System</span>
                        </div>
                    </div>
                    
                    <div>
                        <h1 class="text-2xl font-black text-black tracking-tight">Portal Monitoring Kendaraan</h1>
                        <p class="text-xs text-[#015471] font-bold tracking-wide uppercase mt-1">Sistem Manajemen Operasional & Armada</p>
                    </div>
                </div>

                <!-- Login Form -->
                <form action="{{ route('login.post') }}" method="POST" class="space-y-4 text-xs">
                    @csrf

                    <!-- Email Field -->
                    <div>
                        <label class="block font-bold text-black mb-1.5 flex items-center justify-between">
                            <span>Alamat Email</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-black/40 group-focus-within:text-[#015471] transition-colors">
                                <i data-lucide="mail" class="w-4 h-4"></i>
                            </div>
                            <input type="email" name="email" value="{{ old('email') }}" required placeholder="email@domain.com" class="w-full pl-10 pr-4 py-3 rounded-xl bg-white border-2 border-slate-200 text-black placeholder-black/40 focus:border-[#015471] focus:ring-4 focus:ring-[#015471]/10 focus:outline-none transition-all font-medium text-xs">
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="font-bold text-black">Kata Sandi</label>
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-black/40 group-focus-within:text-[#015471] transition-colors">
                                <i data-lucide="lock" class="w-4 h-4"></i>
                            </div>
                            <input type="password" id="passwordInput" name="password" required placeholder="••••••••" class="w-full pl-10 pr-10 py-3 rounded-xl bg-white border-2 border-slate-200 text-black placeholder-black/40 focus:border-[#015471] focus:ring-4 focus:ring-[#015471]/10 focus:outline-none transition-all font-medium text-xs">
                            <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-black/40 hover:text-[#015471] transition-colors">
                                <i data-lucide="eye" id="eyeIcon" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between text-black/80 text-[11px] pt-1">
                        <label class="flex items-center gap-2 cursor-pointer font-medium select-none">
                            <input type="checkbox" name="remember" checked class="w-4 h-4 rounded border-slate-300 text-[#FF7A3D] focus:ring-[#FF7A3D] focus:ring-offset-0">
                            <span>Ingat saya di perangkat ini</span>
                        </label>
                        <span class="text-[#015471] font-semibold text-[10px]">Akses Terproteksi</span>
                    </div>

                    <!-- Submit Button with Pulse & Glow Animation -->
                    <button type="submit" class="btn-glow w-full py-3.5 px-4 rounded-xl bg-[#FF7A3D] text-white font-extrabold text-sm shadow-xl shadow-[#FF7A3D]/30 flex items-center justify-center gap-2 cursor-pointer">
                        <span>Masuk ke Dashboard</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
                    </button>
                </form>

                <!-- Footer Note -->
                <div class="text-center text-[10px] text-black/50 border-t border-slate-100 pt-3">
                    Sistem Monitoring & Pemeliharaan Kendaraan Bermotor
                </div>

            </div>
        </div>
    </div>

    <!-- Background Particle Simulation Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Canvas Particle Simulation with Theme Colors (#015471 & #FF7A3D)
            const canvas = document.getElementById('particleCanvas');
            if (canvas) {
                const ctx = canvas.getContext('2d');
                let width = canvas.width = window.innerWidth;
                let height = canvas.height = window.innerHeight;

                window.addEventListener('resize', () => {
                    width = canvas.width = window.innerWidth;
                    height = canvas.height = window.innerHeight;
                });

                const particles = [];
                const particleCount = Math.min(Math.floor(window.innerWidth / 20), 55);
                const colors = ['#015471', '#FF7A3D'];

                for (let i = 0; i < particleCount; i++) {
                    particles.push({
                        x: Math.random() * width,
                        y: Math.random() * height,
                        radius: Math.random() * 2.8 + 1.2,
                        color: colors[Math.floor(Math.random() * colors.length)],
                        vx: (Math.random() - 0.5) * 0.7,
                        vy: (Math.random() - 0.5) * 0.7,
                        alpha: Math.random() * 0.5 + 0.2
                    });
                }

                function draw() {
                    ctx.clearRect(0, 0, width, height);

                    // Draw connecting lines
                    for (let i = 0; i < particles.length; i++) {
                        for (let j = i + 1; j < particles.length; j++) {
                            const dx = particles[i].x - particles[j].x;
                            const dy = particles[i].y - particles[j].y;
                            const dist = Math.sqrt(dx * dx + dy * dy);

                            if (dist < 130) {
                                ctx.beginPath();
                                ctx.moveTo(particles[i].x, particles[i].y);
                                ctx.lineTo(particles[j].x, particles[j].y);
                                ctx.strokeStyle = `rgba(1, 84, 113, ${0.12 * (1 - dist / 130)})`;
                                ctx.lineWidth = 0.8;
                                ctx.stroke();
                            }
                        }
                    }

                    // Draw & update particles
                    particles.forEach(p => {
                        ctx.beginPath();
                        ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                        ctx.fillStyle = p.color;
                        ctx.globalAlpha = p.alpha;
                        ctx.fill();
                        ctx.globalAlpha = 1.0;

                        p.x += p.vx;
                        p.y += p.vy;

                        if (p.x < 0) p.x = width;
                        if (p.x > width) p.x = 0;
                        if (p.y < 0) p.y = height;
                        if (p.y > height) p.y = 0;
                    });

                    requestAnimationFrame(draw);
                }

                draw();
            }
        });

        // Toggle password visibility
        function togglePasswordVisibility() {
            const pass = document.getElementById('passwordInput');
            const icon = document.getElementById('eyeIcon');
            if (pass.type === 'password') {
                pass.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                pass.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        // SweetAlert2 Toast Notification Configuration
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        @if(session('info'))
            Toast.fire({
                icon: 'info',
                title: '{{ session('info') }}',
                color: '#015471',
                iconColor: '#015471',
                background: '#ffffff',
                customClass: {
                    popup: 'border-2 border-[#015471] rounded-2xl shadow-xl'
                }
            });
        @endif

        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}',
                color: '#065f46',
                iconColor: '#10b981',
                background: '#ffffff',
                customClass: {
                    popup: 'border-2 border-emerald-500 rounded-2xl shadow-xl'
                }
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Gagal Masuk',
                text: '{{ $errors->first() }}',
                confirmButtonColor: '#FF7A3D',
                confirmButtonText: 'Coba Lagi',
                customClass: {
                    popup: 'rounded-3xl border-2 border-rose-200'
                }
            });
        @endif
    </script>
</body>
</html>
