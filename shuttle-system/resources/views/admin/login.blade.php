<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Keamanan Admin - Shuttle D-Voyager</title>
    
    <!-- Google Fonts: Outfit (Brand & Headings) & Inter (Typography) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind Play CDN with User's Exact Color Palette Integrated -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fffdf0',
                            100: '#fef7cc',
                            200: '#fdec96',
                            300: '#fdda55',
                            400: '#fdc625',
                            500: '#FBC02D', // Vibrant Golden Yellow from User's Palette
                            600: '#e2ab1f', // Hover Gold
                            700: '#be8c12',
                            800: '#996e10',
                            900: '#7d590e',
                        },
                        dark: {
                            50: '#f6f6f6',
                            100: '#e7e7e7',
                            200: '#d1d1d1',
                            300: '#b0b0b0',
                            800: '#333333',
                            850: '#262626',
                            900: '#1E1E1E', // Dark Charcoal from User's Palette
                        },
                        slate: {
                            300: '#D1D5DB', // Light Gray from User's Palette
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        .split-left-bg {
            background-color: #1E1E1E;
            background-image: 
                radial-gradient(circle at 80% 20%, rgba(251, 192, 45, 0.08) 0%, transparent 60%),
                radial-gradient(circle at 20% 80%, rgba(251, 192, 45, 0.04) 0%, transparent 60%);
        }
    </style>
</head>
<body class="bg-gray-50 text-dark-900 font-sans min-h-screen flex flex-col md:flex-row overflow-x-hidden">

    <!-- LEFT SIDE: Brand & Feature Showcase (Hidden on Mobile) -->
    <div class="hidden md:flex md:w-5/12 lg:w-1/2 split-left-bg text-white flex-col justify-between p-12 relative overflow-hidden">
        
        <!-- Top Branding -->
        <div class="flex items-center gap-3 relative z-10">
            <div class="h-10 w-10 bg-white rounded-xl p-1.5 flex items-center justify-center border border-white/20 shadow-md">
                <img src="{{ asset('assets/Logo_Dvoyager.png') }}" alt="Logo Dvoyager" class="max-h-full max-w-full object-contain" onerror="this.src='https://placehold.co/100x100?text=DV'">
            </div>
            <div class="flex flex-col">
                <span class="font-outfit font-extrabold text-lg text-brand-500 leading-none tracking-wide">D-VOYAGER</span>
                <span class="text-[9px] text-gray-400 mt-1 uppercase tracking-widest font-semibold">Sistem Shuttle Terpadu</span>
            </div>
        </div>

        <!-- Center Feature Pitch -->
        <div class="space-y-6 max-w-md my-auto relative z-10">
            <h2 class="text-3xl lg:text-4xl font-extrabold font-outfit leading-tight">Sistem Administrasi Armada Shuttle Anda</h2>
            <p class="text-gray-300 text-sm leading-relaxed">
                Kelola jadwal keberangkatan, ketersediaan pengemudi, pemantauan rute real-time, laporan pemesanan tiket, serta analisis analitik pendapatan dalam satu dashboard terpadu.
            </p>
            
            <!-- Bullet Features -->
            <div class="space-y-3.5 pt-4">
                <div class="flex items-center gap-3">
                    <span class="h-6 w-6 rounded-lg bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-500 text-xs">✓</span>
                    <span class="text-xs font-semibold text-gray-200">GPS Live Tracking Real-Time Terintegrasi</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="h-6 w-6 rounded-lg bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-500 text-xs">✓</span>
                    <span class="text-xs font-semibold text-gray-200">Manajemen Kursi & Transaksi Pembayaran Otomatis</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="h-6 w-6 rounded-lg bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-500 text-xs">✓</span>
                    <span class="text-xs font-semibold text-gray-200">Laporan Keuangan & Ekspor PDF / Excel Instan</span>
                </div>
            </div>
        </div>

        <!-- Bottom Corporate License -->
        <div class="text-[10px] text-gray-500 relative z-10">
            &copy; 2026 PT D-Voyager Shuttle Indonesia. Seluruh hak cipta dilindungi.
        </div>
        
        <!-- Decorative Glow Overlay -->
        <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-brand-500/5 rounded-full blur-[80px] pointer-events-none"></div>
    </div>

    <!-- RIGHT SIDE: Authentication Form Canvas -->
    <div class="w-full md:w-7/12 lg:w-1/2 bg-white flex flex-col justify-between p-6 sm:p-12 relative">
        
        <!-- Floating Back Button (Top Left) -->
        <div class="flex items-center justify-between w-full">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-xs font-extrabold text-gray-500 hover:text-brand-500 transition-colors uppercase tracking-wider group">
                <span class="bg-gray-50 p-2 rounded-xl border border-gray-200 group-hover:border-brand-500/30 group-hover:bg-brand-50 transition-all transform group-hover:-translate-x-1">←</span> Kembali ke Beranda
            </a>
            
            <div class="flex items-center gap-2 bg-gray-50 border border-gray-150 px-3 py-1.5 rounded-full">
                <span class="h-2 w-2 rounded-full bg-brand-500 animate-pulse"></span>
                <span class="text-[9px] text-gray-500 font-extrabold uppercase tracking-widest">Portal Karyawan</span>
            </div>
        </div>

        <!-- Center Login Card container -->
        <div class="w-full max-w-sm mx-auto my-auto py-8">
            
            <!-- Header Text -->
            <div class="space-y-2 mb-8">
                <h1 class="text-2xl font-black font-outfit text-dark-900 leading-tight">Selamat Datang Admin</h1>
                <p class="text-sm text-gray-500">Silakan masukkan email resmi dan kata sandi Anda untuk mengakses dashboard manajemen.</p>
            </div>

            <!-- Laravel Alerts (Session Message) -->
            @if(session('message'))
                <div class="bg-rose-50 border border-rose-100 rounded-xl p-4 mb-6 flex items-start gap-3 shadow-sm">
                    <span class="text-rose-600 text-sm shrink-0">⚠️</span>
                    <div class="text-xs text-rose-800 leading-normal">
                        <span class="font-bold block text-rose-700 mb-0.5">Akses Ditolak</span>
                        {{ session('message') }}
                    </div>
                </div>
            @endif

            <!-- Laravel Validation Errors -->
            @if($errors->any())
                <div class="bg-rose-50 border border-rose-100 rounded-xl p-4 mb-6 flex items-start gap-3 shadow-sm">
                    <span class="text-rose-600 text-sm shrink-0">⚠️</span>
                    <div class="text-xs text-rose-800 leading-normal">
                        <span class="font-bold block text-rose-700 mb-0.5">Terjadi Kesalahan Pengisian:</span>
                        <ul class="list-disc pl-4 space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Form -->
            <form action="/admin/login" method="POST" class="space-y-5">
                @csrf
                
                <!-- Email Input -->
                <div class="flex flex-col gap-1.5">
                    <label for="email" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Alamat Email Resmi</label>
                    <div class="relative flex items-center bg-gray-50 rounded-xl border border-gray-200 focus-within:border-brand-500 focus-within:ring-1 focus-within:ring-brand-500 transition-all duration-200">
                        <div class="h-11 w-11 flex items-center justify-center text-gray-400 border-r border-gray-200/50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                            </svg>
                        </div>
                        <input type="email" id="email" name="email" required placeholder="nama@email.com" class="flex-1 bg-transparent px-3 py-2.5 text-dark-900 placeholder-gray-400 text-sm focus:outline-none font-medium">
                    </div>
                </div>

                <!-- Password Input -->
                <div class="flex flex-col gap-1.5">
                    <div class="flex justify-between items-center">
                        <label for="password" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Kata Sandi</label>
                        <span class="text-[10px] text-gray-400 hover:text-brand-650 font-bold uppercase tracking-wider cursor-help transition-colors">Lupa Sandi? CS</span>
                    </div>
                    <div class="relative flex items-center bg-gray-50 rounded-xl border border-gray-200 focus-within:border-brand-500 focus-within:ring-1 focus-within:ring-brand-500 transition-all duration-200">
                        <div class="h-11 w-11 flex items-center justify-center text-gray-400 border-r border-gray-200/50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input type="password" id="password" name="password" required placeholder="••••••••" class="flex-1 bg-transparent pl-3 pr-11 py-2.5 text-dark-900 placeholder-gray-400 text-sm focus:outline-none font-medium">
                        
                        <!-- Toggle Visibility Eye Button -->
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3.5 text-gray-400 hover:text-brand-600 focus:outline-none p-1 transition-colors" title="Tampilkan/Sembunyikan Sandi">
                            <svg id="eye-open-icon" class="h-4.5 w-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eye-closed-icon" class="h-4.5 w-4.5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember me -->
                <div class="flex items-center text-xs text-gray-500 pt-0.5">
                    <label class="flex items-center gap-2.5 cursor-pointer select-none group">
                        <input type="checkbox" class="rounded border-gray-300 text-brand-500 focus:ring-brand-500/20 h-4 w-4 transition-colors">
                        <span class="group-hover:text-dark-900 transition-colors font-medium">Ingat sesi saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-brand-500 hover:bg-brand-600 text-dark-900 font-extrabold py-3 rounded-xl shadow-sm transition-all transform hover:-translate-y-0.5 text-sm uppercase tracking-wider mt-4">
                    Masuk Dashboard
                </button>
            </form>
        </div>

        <!-- Footer Showcase on Mobile -->
        <footer class="w-full text-center text-xs text-gray-400 md:hidden pt-8 border-t border-gray-150">
            &copy; 2026 PT D-Voyager Shuttle Indonesia.
        </footer>
    </div>

    <!-- Eye Toggle Script -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeOpenIcon = document.getElementById('eye-open-icon');
            const eyeClosedIcon = document.getElementById('eye-closed-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpenIcon.classList.add('hidden');
                eyeClosedIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpenIcon.classList.remove('hidden');
                eyeClosedIcon.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
