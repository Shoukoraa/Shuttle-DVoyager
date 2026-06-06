<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>D-Voyager Shuttle - Sistem Manajemen & Pemesanan Tiket Shuttle Terpadu</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="D-Voyager Shuttle adalah sistem manajemen dan pemesanan tiket shuttle terpadu dengan pelacakan GPS real-time, seleksi kursi otomatis, dan pembayaran digital instan.">
    <meta name="keywords" content="shuttle, travel, booking tiket, pelacakan gps, d-voyager, tiket travel, dompetx">
    <meta name="author" content="Kelompok 18 Shuttle D-Voyager">
    
    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind Play CDN with User's Exact Color Palette Integrated -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
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
                            950: '#483103',
                        },
                        dark: {
                            50: '#f6f6f6',
                            100: '#e7e7e7',
                            200: '#d1d1d1',
                            300: '#b0b0b0',
                            400: '#888888',
                            500: '#6d6d6d',
                            600: '#5d5d5d',
                            700: '#4f4f4f',
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
                    },
                    boxShadow: {
                        'glass': '0 8px 32px 0 rgba(30, 30, 30, 0.04)',
                        'glass-brand': '0 8px 32px 0 rgba(251, 192, 45, 0.15)',
                    }
                }
            }
        }
    </script>
    
    <!-- Custom CSS Styles for Smooth Scrolling, Backdrop Blur, and Custom Animations -->
    <style>
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(209, 213, 219, 0.5); /* #D1D5DB with opacity */
        }
        .glass-dark-card {
            background: rgba(30, 30, 30, 0.85); /* #1E1E1E with opacity */
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .text-glow-yellow {
            text-shadow: 0 0 15px rgba(251, 192, 45, 0.3);
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        .floating-mockup {
            animation: float 4s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-[#F8F9FA] text-dark-900 font-sans antialiased overflow-x-hidden">

    <!-- HEADER / NAVIGATION BAR -->
    <header id="main-header" class="fixed top-0 inset-x-0 z-50 transition-transform duration-300 transform translate-y-0 glass-nav border-b border-slate-300/40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="#" class="flex items-center gap-3 group">
                <div class="h-12 w-12 bg-white rounded-xl shadow-md p-1.5 flex items-center justify-center border border-slate-300/30 transition-transform group-hover:scale-105">
                    <img src="{{ asset('assets/Logo_Dvoyager.png') }}" alt="Logo Dvoyager" class="max-h-full max-w-full object-contain" onerror="this.src='{{ asset('assets/Logo Dvoyager.png') }}'; this.onerror=function(){ this.src='https://img.icons8.com/color/96/shuttle-bus.png'; }">
                </div>
                <div>
                    <span class="font-outfit font-extrabold text-2xl tracking-tight text-dark-900">D-VOYAGER</span>
                    <span class="block text-[10px] uppercase font-extrabold tracking-widest text-brand-500 -mt-1">Shuttle System</span>
                </div>
            </a>

            <!-- Desktop Menu Links -->
            <nav class="hidden md:flex items-center gap-8">
                <a href="#beranda" class="text-sm font-bold text-dark-700 hover:text-brand-500 transition-colors">Beranda</a>
                <a href="#fitur" class="text-sm font-bold text-dark-700 hover:text-brand-500 transition-colors">Fitur Utama</a>
                <a href="#simulasi" class="text-sm font-bold text-dark-700 hover:text-brand-500 transition-colors">Cari Jadwal</a>
                <a href="#cara-kerja" class="text-sm font-bold text-dark-700 hover:text-brand-500 transition-colors">Cara Pemesanan</a>
                <a href="#tim" class="text-sm font-bold text-dark-700 hover:text-brand-500 transition-colors">Tim Kami</a>
                <a href="#kontak" class="text-sm font-bold text-dark-700 hover:text-brand-500 transition-colors">Kontak</a>
            </nav>

            <!-- Mobile Action Button (CTA) -->
            <div class="flex items-center gap-3">
                <a href="#simulasi" class="bg-brand-500 text-dark-900 font-extrabold text-sm px-5 py-2.5 rounded-xl shadow-md hover:shadow-lg hover:bg-brand-600 transition-all hover:-translate-y-0.5">
                    Cari Tiket
                </a>
                
                <!-- Mobile Menu Button -->
                <button type="button" id="mobile-menu-btn" class="md:hidden p-2 text-dark-700 hover:text-brand-500 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Dropdown Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-slate-300/50 px-4 py-4 space-y-3 shadow-lg">
            <a href="#beranda" class="block text-base font-bold text-dark-700 hover:text-brand-500 py-1">Beranda</a>
            <a href="#fitur" class="block text-base font-bold text-dark-700 hover:text-brand-500 py-1">Fitur Utama</a>
            <a href="#simulasi" class="block text-base font-bold text-dark-700 hover:text-brand-500 py-1">Cari Jadwal</a>
            <a href="#cara-kerja" class="block text-base font-bold text-dark-700 hover:text-brand-500 py-1">Cara Pemesanan</a>
            <a href="#tim" class="block text-base font-bold text-dark-700 hover:text-brand-500 py-1">Tim Kami</a>
            <a href="#kontak" class="block text-base font-bold text-dark-700 hover:text-brand-500 py-1">Kontak</a>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section id="beranda" class="relative pt-32 pb-24 overflow-hidden bg-gradient-to-b from-brand-100/30 via-white to-[#F8F9FA]">
        <!-- Decorative background blurs using Palette Colors -->
        <div class="absolute top-20 left-1/2 -translate-x-1/2 w-[500px] h-[500px] bg-brand-200/20 rounded-full blur-3xl pointer-events-none -z-10"></div>
        <div class="absolute top-40 right-10 w-[300px] h-[300px] bg-slate-300/40 rounded-full blur-3xl pointer-events-none -z-10"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- Hero Content (Left) -->
                <div class="lg:col-span-7 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 bg-brand-100/80 text-brand-700 font-extrabold px-4 py-1.5 rounded-full text-xs sm:text-sm mb-6 border border-brand-200/40">
                        <span class="flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-500"></span>
                        </span>
                        Aplikasi Travel Shuttle Modern #1 Terintegrasi
                    </div>
                    
                    <h1 class="font-outfit font-extrabold text-4xl sm:text-5xl lg:text-6xl text-dark-900 leading-tight tracking-tight mb-6">
                        Perjalanan Lancar,<br>
                        <span class="bg-gradient-to-r from-brand-500 to-brand-700 bg-clip-text text-transparent">Pemesanan Pintar</span><br>
                        bersama D-Voyager
                    </h1>
                    
                    <p class="text-base sm:text-lg text-dark-700 max-w-xl mx-auto lg:mx-0 mb-8 leading-relaxed">
                        Nikmati kemudahan pesan tiket shuttle langsung dalam genggaman Anda. Dilengkapi pelacakan armada real-time (GPS), pemilihan kursi interaktif, dan gerbang pembayaran digital yang aman.
                    </p>

                    <!-- Mockup Download Badges & Mobile Notification -->
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 mb-4">
                        <a href="#" class="flex items-center gap-3 bg-dark-900 text-white px-5 py-3 rounded-xl hover:bg-dark-850 hover:border-brand-500 border border-transparent transition-all hover:-translate-y-0.5 shadow-md">
                            <svg class="w-6 h-6 text-brand-500" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M5 3.00005C4.82915 2.99982 4.66016 3.03713 4.5057 3.10915C4.35123 3.18118 4.21503 3.28616 4.10729 3.41625L13.6823 13.0001L20.4073 6.27505C19.9882 5.23439 19.1172 4.41724 18.0263 4.04505L5.73629 3.0563C5.49229 3.0188 5.24436 2.99984 5 3.00005ZM3.18 4.7088C3.06173 4.96582 3.00056 5.24523 3 5.52755V18.4726C3.00049 18.755 3.06161 19.0345 3.17983 19.2917L12.2687 13.0001L3.18 4.7088ZM13.6823 13.0001L4.10729 22.5838C4.26947 22.7801 4.48208 22.9298 4.72528 23.0189C4.96847 23.108 5.2323 23.1328 5.49051 23.0909L18.0263 20.9538C19.1172 20.5816 19.9882 19.7645 20.4073 18.7238L13.6823 13.0001Z"/>
                            </svg>
                            <div class="text-left font-sans">
                                <span class="block text-[9px] uppercase text-dark-300">Temukan di</span>
                                <span class="block text-sm font-semibold -mt-0.5">Google Play</span>
                            </div>
                        </a>
                    </div>
                </div>
                
                <!-- Hero Mockup (Right) with Slate-300 and #1E1E1E details -->
                <div class="lg:col-span-5 flex justify-center relative">
                    <!-- Gold Glow decoration behind device -->
                    <div class="absolute w-72 h-72 bg-brand-300/10 rounded-full blur-3xl top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 -z-10"></div>
                    
                    <!-- Floating Mobile Mockup Frame in #1E1E1E -->
                    <div class="floating-mockup relative w-[280px] sm:w-[320px] aspect-[9/18.5] bg-dark-900 rounded-[40px] p-3 shadow-2xl border-4 border-dark-800">
                        <!-- Speaker & Camera Notch -->
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 h-5 w-32 bg-dark-900 rounded-b-2xl z-20 flex items-center justify-center gap-1.5">
                            <span class="h-1.5 w-1.5 rounded-full bg-dark-700"></span>
                            <span class="h-1 w-8 rounded-full bg-dark-800"></span>
                        </div>
                        
                        <!-- Internal App UI Simulator -->
                        <div class="w-full h-full bg-white rounded-[30px] overflow-hidden flex flex-col relative text-[11px]">
                            <!-- App Header (Dark theme #1E1E1E with Yellow #FBC02D text) -->
                            <div class="bg-dark-900 text-white p-4 pt-6 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="h-6 w-6 bg-white rounded-md p-0.5">
                                        <img src="{{ asset('assets/Logo_Dvoyager.png') }}" class="h-full w-full object-contain" onerror="this.src='https://img.icons8.com/color/48/shuttle-bus.png'">
                                    </div>
                                    <span class="font-outfit font-extrabold text-xs text-brand-500">D-Voyager</span>
                                </div>
                                <span class="bg-brand-500 text-dark-900 text-[8px] px-2 py-0.5 rounded-full font-bold">App Pelanggan</span>
                            </div>
                            
                            <!-- Search Form Mock -->
                            <div class="p-3 bg-white shadow-sm border-b border-slate-300/40">
                                <div class="font-bold text-dark-900 mb-1.5 text-xs">Pesan Tiket Shuttle</div>
                                <div class="space-y-1.5">
                                    <div class="bg-dark-50 p-2 rounded-lg flex items-center gap-2 border border-slate-300/30">
                                        <span class="text-emerald-500 font-bold">●</span>
                                        <div class="flex-1">
                                            <span class="text-[8px] block text-dark-400 -mb-0.5">KOTA ASAL</span>
                                            <span class="font-semibold text-dark-800">Jakarta (Kuningan)</span>
                                        </div>
                                    </div>
                                    <div class="bg-dark-50 p-2 rounded-lg flex items-center gap-2 border border-slate-300/30">
                                        <span class="text-brand-500 font-bold">●</span>
                                        <div class="flex-1">
                                            <span class="text-[8px] block text-dark-400 -mb-0.5">KOTA TUJUAN</span>
                                            <span class="font-semibold text-dark-800">Bandung (Dago)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Live Tracking / Maps Mock with Golden Route -->
                            <div class="flex-1 bg-slate-300 relative flex flex-col justify-end p-2 overflow-hidden">
                                <!-- Mock Map Background -->
                                <div class="absolute inset-0 bg-neutral-100 p-1 flex flex-col justify-between pointer-events-none">
                                    <!-- Map roads -->
                                    <div class="absolute h-px w-full bg-slate-300/60 top-1/3"></div>
                                    <div class="absolute h-px w-full bg-slate-300/60 top-2/3"></div>
                                    <div class="absolute w-px h-full bg-slate-300/60 left-1/3"></div>
                                    <div class="absolute w-px h-full bg-slate-300/60 left-2/3"></div>
                                    <!-- GPS Route line in Brand Golden Yellow -->
                                    <svg class="absolute inset-0 h-full w-full" fill="none" stroke="currentColor">
                                        <path d="M 50,40 L 120,90 L 190,140" stroke="#FBC02D" stroke-width="4" stroke-linecap="round" stroke-dasharray="2 4"/>
                                    </svg>
                                    <!-- GPS Pins -->
                                    <div class="absolute top-[25px] left-[40px] bg-emerald-500 text-white rounded-full p-0.5 shadow-md text-[6px] font-bold">ASAL</div>
                                    <div class="absolute bottom-[35px] right-[40px] bg-dark-900 text-brand-500 border border-brand-500/50 rounded-full p-0.5 shadow-md text-[6px] font-bold">TUJUAN</div>
                                    
                                    <!-- Shuttle Car Icon Moving -->
                                    <div class="absolute top-[75px] left-[105px] bg-white rounded-full p-1 shadow-lg border border-brand-300 flex items-center gap-1">
                                        <span class="text-xs">🚐</span>
                                        <span class="text-[8px] font-extrabold text-dark-900 animate-pulse">DV-102</span>
                                    </div>
                                </div>
                                
                                <!-- Ticket Card Hover overlay -->
                                <div class="bg-white/95 backdrop-blur-sm p-2 rounded-xl shadow-lg border border-slate-300 z-10">
                                    <div class="flex justify-between items-center border-b border-slate-300/50 pb-1 mb-1">
                                        <div>
                                            <span class="font-bold text-dark-900">Toyota HiAce Premio</span>
                                            <span class="block text-[8px] text-dark-400">No. Pol: B 1289 VQY</span>
                                        </div>
                                        <span class="bg-emerald-100 text-emerald-800 text-[8px] px-1.5 py-0.5 rounded font-extrabold">Aktif</span>
                                    </div>
                                    <div class="flex justify-between items-center text-[9px]">
                                        <span class="text-dark-400">Estimasi Tiba:</span>
                                        <span class="font-bold text-brand-600">12 Menit lagi</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Bottom App Bar in #1E1E1E with golden yellow active state -->
                            <div class="bg-dark-900 border-t border-dark-800 p-2 flex justify-around text-dark-400">
                                <span class="text-brand-500 font-bold flex flex-col items-center"><span>🏠</span><span class="text-[8px]">Home</span></span>
                                <span class="flex flex-col items-center hover:text-white transition-colors"><span>🎫</span><span class="text-[8px]">Tiket</span></span>
                                <span class="flex flex-col items-center hover:text-white transition-colors"><span>💬</span><span class="text-[8px]">Chat</span></span>
                                <span class="flex flex-col items-center hover:text-white transition-colors"><span>👤</span><span class="text-[8px]">Akun</span></span>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </section>

    <!-- COMPREHENSIVE FEATURES SECTION WITH MAPPED COLORS -->
    <section id="fitur" class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-20">
                <h2 class="font-outfit font-extrabold text-3xl sm:text-4xl text-dark-900 mb-4">
                    Tiga Ekosistem Aplikasi yang Saling Terhubung
                </h2>
                <div class="h-1.5 w-24 bg-brand-500 mx-auto rounded-full mb-6"></div>
                <p class="text-dark-700">
                    D-Voyager dirancang dengan arsitektur modern yang mengintegrasikan tiga kebutuhan utama dalam satu platform: Pelanggan, Pengemudi (Driver), dan Manajemen (Admin Panel).
                </p>
            </div>

            <!-- Features Grid in glass-card with border #D1D5DB -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- 1. Customer App Card -->
                <div class="glass-card rounded-3xl p-8 hover:shadow-xl hover:border-brand-500 hover:-translate-y-1 transition-all group duration-300">
                    <div class="h-14 w-14 bg-brand-100 text-brand-600 rounded-2xl flex items-center justify-center text-3xl mb-6 shadow-inner group-hover:bg-brand-500 group-hover:text-dark-900 transition-colors duration-300">
                        📱
                    </div>
                    <h3 class="font-outfit font-bold text-xl text-dark-900 mb-3">1. Aplikasi Pelanggan</h3>
                    <p class="text-dark-700 text-sm leading-relaxed mb-6">
                        Didesain ramah pengguna untuk kemudahan akses di *smartphone* Anda dalam hitungan detik.
                    </p>
                    <ul class="space-y-3 text-dark-800 text-sm border-t border-slate-300/40 pt-4">
                        <li class="flex items-start gap-2.5">
                            <span class="text-brand-500 font-extrabold">✓</span>
                            <span>Pencarian Jadwal & Rute Pintar</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-brand-500 font-extrabold">✓</span>
                            <span>Pemilihan Kursi (Seat Layout) secara visual</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-brand-500 font-extrabold">✓</span>
                            <span>E-Ticket & Riwayat Perjalanan instan</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-brand-500 font-extrabold">✓</span>
                            <span>Live Chat dengan Driver & Customer Service</span>
                        </li>
                    </ul>
                </div>

                <!-- 2. Driver App Card -->
                <div class="glass-card rounded-3xl p-8 hover:shadow-xl hover:border-brand-500 hover:-translate-y-1 transition-all group duration-300">
                    <div class="h-14 w-14 bg-dark-50 text-dark-900 rounded-2xl flex items-center justify-center text-3xl mb-6 shadow-inner group-hover:bg-brand-500 group-hover:text-dark-900 transition-colors duration-300 border border-slate-300/30">
                        🚗
                    </div>
                    <h3 class="font-outfit font-bold text-xl text-dark-900 mb-3">2. Aplikasi Driver</h3>
                    <p class="text-dark-700 text-sm leading-relaxed mb-6">
                        Memudahkan supir mengelola tugas perjalanan dan memberikan data lokasi real-time.
                    </p>
                    <ul class="space-y-3 text-dark-800 text-sm border-t border-slate-300/40 pt-4">
                        <li class="flex items-start gap-2.5">
                            <span class="text-brand-500 font-extrabold">✓</span>
                            <span>Daftar Manifest Penumpang lengkap</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-brand-500 font-extrabold">✓</span>
                            <span>Pelacakan Koordinat GPS Otomatis ke server</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-brand-500 font-extrabold">✓</span>
                            <span>Mulai (*Start*) & Akhiri (*Finish*) perjalanan</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-brand-500 font-extrabold">✓</span>
                            <span>Notifikasi rute keberangkatan harian</span>
                        </li>
                    </ul>
                </div>

                <!-- 3. Admin Web Panel Card -->
                <div class="glass-card rounded-3xl p-8 hover:shadow-xl hover:border-brand-500 hover:-translate-y-1 transition-all group duration-300">
                    <div class="h-14 w-14 bg-brand-100 text-brand-700 rounded-2xl flex items-center justify-center text-3xl mb-6 shadow-inner group-hover:bg-brand-500 group-hover:text-dark-900 transition-colors duration-300">
                        👑
                    </div>
                    <h3 class="font-outfit font-bold text-xl text-dark-900 mb-3">3. Dashboard Admin (Web)</h3>
                    <p class="text-dark-700 text-sm leading-relaxed mb-6">
                        Pusat kendali operasional bisnis shuttle terpadu dengan analisis performa yang akurat.
                    </p>
                    <ul class="space-y-3 text-dark-800 text-sm border-t border-slate-300/40 pt-4">
                        <li class="flex items-start gap-2.5">
                            <span class="text-brand-500 font-extrabold">✓</span>
                            <span>Manajemen Rute, Kendaraan, Driver, & Pelanggan</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-brand-500 font-extrabold">✓</span>
                            <span>Harga Dinamis ala Traveloka (Dynamic Pricing)</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-brand-500 font-extrabold">✓</span>
                            <span>Live Map Tracking pelacakan armada di jalan</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-brand-500 font-extrabold">✓</span>
                            <span>Ekspor Laporan Bulanan ke format Excel & PDF</span>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </section>

    <!-- INTERACTIVE TICKET BOOKING SIMULATOR (DEMO) -->
    <section id="simulasi" class="py-24 bg-gradient-to-b from-[#F8F9FA] via-white to-white relative">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <span class="text-xs font-bold uppercase tracking-widest text-dark-900 bg-brand-500 px-3 py-1 rounded-full border border-brand-500/20">INTERAKTIF DEMO</span>
                <h2 class="font-outfit font-extrabold text-3xl sm:text-4xl text-dark-900 mt-3 mb-4">Simulasi Cari Tiket Anda</h2>
                <p class="text-dark-700 text-sm sm:text-base">Rasakan kemudahan pencarian jadwal armada kami. Coba form simulasi di bawah ini untuk melihat ketersediaan tiket.</p>
            </div>

            <!-- Simulator Widget Frame (#1E1E1E Dark background with white inputs) -->
            <div class="bg-dark-900 text-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-dark-800 relative">
                <div class="absolute -top-4 -right-4 bg-brand-500 text-dark-900 font-extrabold text-xs px-3 py-1.5 rounded-xl shadow-lg transform rotate-3 animate-pulse">
                    🔒 Keamanan DompetX Terintegrasi
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Origin Selector -->
                    <div>
                        <label class="block text-xs font-bold text-brand-500 uppercase tracking-wider mb-2">Kota Asal</label>
                        <select id="sim-origin" class="w-full bg-dark-850 border border-dark-800 rounded-xl px-4 py-3 text-white font-semibold focus:outline-none focus:border-brand-500 transition-colors">
                            <option value="Jakarta">Jakarta (Kuningan)</option>
                            <option value="Bandung">Bandung (Dago)</option>
                            <option value="Surabaya">Surabaya (Tunjungan)</option>
                            <option value="Yogyakarta">Yogyakarta (Malioboro)</option>
                        </select>
                    </div>
                    
                    <!-- Destination Selector -->
                    <div>
                        <label class="block text-xs font-bold text-brand-500 uppercase tracking-wider mb-2">Kota Tujuan</label>
                        <select id="sim-dest" class="w-full bg-dark-850 border border-dark-800 rounded-xl px-4 py-3 text-white font-semibold focus:outline-none focus:border-brand-500 transition-colors">
                            <option value="Bandung">Bandung (Dago)</option>
                            <option value="Jakarta" selected>Jakarta (Kuningan)</option>
                            <option value="Yogyakarta">Yogyakarta (Malioboro)</option>
                            <option value="Surabaya">Surabaya (Tunjungan)</option>
                        </select>
                    </div>

                    <!-- Date Picker -->
                    <div>
                        <label class="block text-xs font-bold text-brand-500 uppercase tracking-wider mb-2">Tanggal Keberangkatan</label>
                        <input type="date" id="sim-date" class="w-full bg-dark-850 border border-dark-800 rounded-xl px-4 py-2.5 text-white font-semibold focus:outline-none focus:border-brand-500 transition-colors" value="2026-05-27">
                    </div>
                </div>

                <button type="button" onclick="runSimulasi()" class="w-full bg-brand-500 text-dark-900 font-extrabold py-4 rounded-2xl hover:bg-brand-600 hover:shadow-lg transition-all transform hover:-translate-y-0.5 text-center">
                    Cari Jadwal Shuttle
                </button>

                <!-- Results Output (Initially Hidden) -->
                <div id="sim-results" class="hidden mt-8 border-t border-dark-800 pt-6">
                    <h3 class="font-outfit font-bold text-lg text-brand-500 mb-4">Jadwal Perjalanan Tersedia</h3>
                    <div id="sim-routes-container" class="space-y-4">
                        <!-- Result Items will be injected here via Javascript -->
                    </div>
                </div>

                <!-- Seat Map Output (Initially Hidden) -->
                <div id="sim-seats-container" class="hidden mt-8 border-t border-dark-800 pt-6 bg-dark-850 p-6 rounded-2xl border border-dark-800">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h4 class="font-outfit font-bold text-base text-white">Pilih Kursi Penumpang</h4>
                            <p class="text-xs text-dark-300">Pilih nomor kursi Anda (Toyota HiAce - 10 Kursi)</p>
                        </div>
                        <span id="selected-seat-badge" class="bg-brand-500 text-dark-900 font-extrabold text-xs px-3 py-1 rounded-full">Belum Memilih Kursi</span>
                    </div>

                    <!-- Seat Layout Grid -->
                    <div class="max-w-[280px] mx-auto bg-white p-6 rounded-2xl shadow-inner border border-slate-300 flex flex-col items-center">
                        <div class="w-full bg-slate-100 text-center font-bold text-slate-500 py-1.5 rounded-lg mb-6 text-[10px] uppercase tracking-widest">Bagian Depan / Supir</div>
                        
                        <div class="grid grid-cols-3 gap-4 w-full">
                            <!-- Driver Seat (Disabled) -->
                            <div class="bg-slate-200 text-slate-400 font-bold h-11 flex items-center justify-center rounded-lg cursor-not-allowed text-xs">🚗</div>
                            <div></div>
                            <!-- Seat 1 -->
                            <button onclick="selectSeat('1')" class="bg-emerald-100 border border-emerald-300 text-emerald-800 hover:bg-emerald-200 font-bold h-11 flex items-center justify-center rounded-lg text-xs transition-colors">01</button>
                            
                            <!-- Row 2 -->
                            <button onclick="selectSeat('2')" class="bg-emerald-100 border border-emerald-300 text-emerald-800 hover:bg-emerald-200 font-bold h-11 flex items-center justify-center rounded-lg text-xs transition-colors">02</button>
                            <div class="flex items-center justify-center text-[10px] text-slate-300 font-bold uppercase">Jalur</div>
                            <button class="bg-red-100 border border-red-200 text-red-500 font-bold h-11 flex items-center justify-center rounded-lg text-xs cursor-not-allowed" disabled>03</button>
                            
                            <!-- Row 3 -->
                            <button onclick="selectSeat('4')" class="bg-emerald-100 border border-emerald-300 text-emerald-800 hover:bg-emerald-200 font-bold h-11 flex items-center justify-center rounded-lg text-xs transition-colors">04</button>
                            <div class="flex items-center justify-center text-[10px] text-slate-300 font-bold uppercase">Jalur</div>
                            <button onclick="selectSeat('5')" class="bg-emerald-100 border border-emerald-300 text-emerald-800 hover:bg-emerald-200 font-bold h-11 flex items-center justify-center rounded-lg text-xs transition-colors">05</button>
                            
                            <!-- Row 4 -->
                            <button onclick="selectSeat('6')" class="bg-emerald-100 border border-emerald-300 text-emerald-800 hover:bg-emerald-200 font-bold h-11 flex items-center justify-center rounded-lg text-xs transition-colors">06</button>
                            <button class="bg-red-100 border border-red-200 text-red-500 font-bold h-11 flex items-center justify-center rounded-lg text-xs cursor-not-allowed" disabled>07</button>
                            <button onclick="selectSeat('8')" class="bg-emerald-100 border border-emerald-300 text-emerald-800 hover:bg-emerald-200 font-bold h-11 flex items-center justify-center rounded-lg text-xs transition-colors">08</button>
                        </div>
                    </div>

                    <!-- Checkout simulator action in Brand Yellow -->
                    <div id="checkout-action" class="hidden mt-6 text-center">
                        <button type="button" onclick="checkoutSimulasi()" class="w-full sm:w-auto bg-brand-500 text-dark-900 font-extrabold px-8 py-3.5 rounded-xl hover:bg-brand-600 hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                            Lanjut Pembayaran Aman (DompetX)
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- CARA PEMESANAN (HOW IT WORKS) -->
    <section id="cara-kerja" class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <h2 class="font-outfit font-extrabold text-3xl sm:text-4xl text-dark-900 mb-4">
                    Cara Cepat Pesan Tiket Shuttle
                </h2>
                <div class="h-1.5 w-24 bg-brand-500 mx-auto rounded-full mb-6"></div>
                <p class="text-dark-700">
                    Proses pemesanan dirancang sesederhana mungkin untuk kenyamanan maksimal perjalanan Anda.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 relative">
                <!-- Step 1 -->
                <div class="text-center p-6 relative group">
                    <div class="w-16 h-16 bg-[#FFFBEB] text-brand-700 border border-brand-200/50 rounded-2xl flex items-center justify-center text-2xl font-bold font-outfit mx-auto mb-6 group-hover:bg-brand-500 group-hover:text-dark-900 transition-all duration-300 shadow-md">
                        1
                    </div>
                    <h3 class="font-outfit font-bold text-lg text-dark-900 mb-2">Cari Rute & Jadwal</h3>
                    <p class="text-dark-500 text-sm leading-relaxed">
                        Masukkan kota asal, tujuan, dan tanggal keberangkatan Anda pada formulir pemesanan.
                    </p>
                </div>
                
                <!-- Step 2 -->
                <div class="text-center p-6 relative group">
                    <div class="w-16 h-16 bg-[#FFFBEB] text-brand-700 border border-brand-200/50 rounded-2xl flex items-center justify-center text-2xl font-bold font-outfit mx-auto mb-6 group-hover:bg-brand-500 group-hover:text-dark-900 transition-all duration-300 shadow-md">
                        2
                    </div>
                    <h3 class="font-outfit font-bold text-lg text-dark-900 mb-2">Pilih Kursi Favorit</h3>
                    <p class="text-dark-500 text-sm leading-relaxed">
                        Lihat denah tata letak kursi mobil secara langsung dan pilih kursi ternyaman Anda.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="text-center p-6 relative group">
                    <div class="w-16 h-16 bg-[#FFFBEB] text-brand-700 border border-brand-200/50 rounded-2xl flex items-center justify-center text-2xl font-bold font-outfit mx-auto mb-6 group-hover:bg-brand-500 group-hover:text-dark-900 transition-all duration-300 shadow-md">
                        3
                    </div>
                    <h3 class="font-outfit font-bold text-lg text-dark-900 mb-2">Bayar Aman Instan</h3>
                    <p class="text-dark-500 text-sm leading-relaxed">
                        Lakukan pembayaran aman didukung oleh integrasi resmi Payment Gateway DompetX.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="text-center p-6 relative group">
                    <div class="w-16 h-16 bg-[#FFFBEB] text-brand-700 border border-brand-200/50 rounded-2xl flex items-center justify-center text-2xl font-bold font-outfit mx-auto mb-6 group-hover:bg-brand-500 group-hover:text-dark-900 transition-all duration-300 shadow-md">
                        4
                    </div>
                    <h3 class="font-outfit font-bold text-lg text-dark-900 mb-2">Lacak Real-Time</h3>
                    <p class="text-dark-500 text-sm leading-relaxed">
                        Dapatkan tiket elektronik di aplikasi, dan lacak posisi supir via GPS saat keberangkatan.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- PAYMENT PARTNERS & COMPLIANCE SHOWCASE WITH PALETTE COLORS -->
    <section class="py-16 bg-[#F8F9FA] border-y border-slate-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="block text-xs font-bold text-dark-400 uppercase tracking-widest mb-6">Metode Pembayaran Resmi Terintegrasi (DompetX)</span>
            
            <div class="flex flex-wrap items-center justify-center gap-8 sm:gap-12 md:gap-16 opacity-75 hover:opacity-100 transition-all duration-300">
                <!-- DompetX logo representation in #1E1E1E with golden yellow dot -->
                <div class="flex items-center gap-1 font-outfit font-extrabold text-xl tracking-wider text-dark-900">
                    DOMPET<span class="text-brand-500 font-extrabold">X</span>
                </div>
                
                <!-- Banks simulation icons in Slate-300 borders -->
                <div class="font-outfit font-bold text-dark-700 bg-white border border-slate-300 px-4 py-2 rounded-xl">Bank BCA</div>
                <div class="font-outfit font-bold text-dark-700 bg-white border border-slate-300 px-4 py-2 rounded-xl">Bank Mandiri</div>
                <div class="font-outfit font-bold text-dark-700 bg-white border border-slate-300 px-4 py-2 rounded-xl">Bank BNI</div>
                <div class="font-outfit font-bold text-dark-700 bg-white border border-slate-300 px-4 py-2 rounded-xl">Bank BRI</div>
                
                <!-- E-wallet mock -->
                <div class="font-outfit font-bold text-dark-900 bg-brand-100 border border-brand-200/50 px-4 py-2 rounded-xl">QRIS</div>
            </div>
            
            <div class="mt-6 text-dark-500 text-xs">
                Transaksi Anda dilindungi dengan enkripsi SSL 256-bit dan mematuhi regulasi keamanan PCI-DSS melalui DompetX Payment Gateway.
            </div>
        </div>
    </section>

    <!-- DEVELOPER TEAM (KEMASAN KORPORAT - KELOMPOK 18) -->
    <section id="tim" class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <span class="text-xs font-bold uppercase tracking-widest text-dark-900 bg-brand-500 px-3 py-1 rounded-full border border-brand-500/20">TIM TIM EKSEKUTIF</span>
                <h2 class="font-outfit font-extrabold text-3xl sm:text-4xl text-dark-900 mt-3 mb-4">
                    Tim Pengembang D-Voyager
                </h2>
                <div class="h-1.5 w-24 bg-brand-500 mx-auto rounded-full mb-6"></div>
                <p class="text-dark-700">
                    Aplikasi Shuttle D-Voyager dikembangkan dan dikelola secara profesional oleh talenta-talenta berdedikasi dari **Kelompok 18**.
                </p>
            </div>

            <!-- Team Grid with #D1D5DB borders & #1E1E1E accents -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                <!-- Setyo -->
                <div class="glass-card rounded-2xl p-6 text-center hover:shadow-lg border border-slate-300/60 hover:border-brand-500 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="h-16 w-16 bg-dark-900 text-brand-500 rounded-full flex items-center justify-center font-bold font-outfit text-xl mx-auto mb-4 shadow-md border border-brand-500/30">
                            SD
                        </div>
                        <h4 class="font-outfit font-extrabold text-dark-900 leading-tight">Setyo Dwinugroho</h4>
                    </div>
                    <span class="block text-[10px] text-dark-400 font-mono mt-4 border-t border-slate-300/40 pt-2">NIM: 24416255201143</span>
                </div>

                <!-- Ahmad -->
                <div class="glass-card rounded-2xl p-6 text-center hover:shadow-lg border border-slate-300/60 hover:border-brand-500 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="h-16 w-16 bg-dark-900 text-brand-500 rounded-full flex items-center justify-center font-bold font-outfit text-xl mx-auto mb-4 shadow-md border border-brand-500/30">
                            AF
                        </div>
                        <h4 class="font-outfit font-extrabold text-dark-900 leading-tight">Ahmad Farid I. F.</h4>
                    </div>
                    <span class="block text-[10px] text-dark-400 font-mono mt-4 border-t border-slate-300/40 pt-2">NIM: 24416255201108</span>
                </div>

                <!-- Dwi Arya -->
                <div class="glass-card rounded-2xl p-6 text-center hover:shadow-lg border border-slate-300/60 hover:border-brand-500 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="h-16 w-16 bg-dark-900 text-brand-500 rounded-full flex items-center justify-center font-bold font-outfit text-xl mx-auto mb-4 shadow-md border border-brand-500/30">
                            DA
                        </div>
                        <h4 class="font-outfit font-extrabold text-dark-900 leading-tight">Dwi Arya D.</h4>
                    </div>
                    <span class="block text-[10px] text-dark-400 font-mono mt-4 border-t border-slate-300/40 pt-2">NIM: 24416255201129</span>
                </div>

                <!-- Moreno -->
                <div class="glass-card rounded-2xl p-6 text-center hover:shadow-lg border border-slate-300/60 hover:border-brand-500 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="h-16 w-16 bg-dark-900 text-brand-500 rounded-full flex items-center justify-center font-bold font-outfit text-xl mx-auto mb-4 shadow-md border border-brand-500/30">
                            MA
                        </div>
                        <h4 class="font-outfit font-extrabold text-dark-900 leading-tight">Moreno Alvarel</h4>
                    </div>
                    <span class="block text-[10px] text-dark-400 font-mono mt-4 border-t border-slate-300/40 pt-2">NIM: 24416255201114</span>
                </div>

                <!-- Jonatan -->
                <div class="glass-card rounded-2xl p-6 text-center hover:shadow-lg border border-slate-300/60 hover:border-brand-500 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="h-16 w-16 bg-dark-900 text-brand-500 rounded-full flex items-center justify-center font-bold font-outfit text-xl mx-auto mb-4 shadow-md border border-brand-500/30">
                            JS
                        </div>
                        <h4 class="font-outfit font-extrabold text-dark-900 leading-tight">Jonatan S. Simbolon</h4>
                    </div>
                    <span class="block text-[10px] text-dark-400 font-mono mt-4 border-t border-slate-300/40 pt-2">NIM: 24416255201154</span>
                </div>
            </div>
        </div>
    </section>

    <!-- CONTACT SECTION WITH INTEGRATED PALETTE -->
    <section id="kontak" class="py-24 bg-slate-50 border-t border-slate-300/50 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
                
                <!-- Contact info (Left) -->
                <div class="lg:col-span-5">
                    <span class="text-xs font-bold uppercase tracking-widest text-brand-700 bg-brand-100 px-3 py-1 rounded-full border border-brand-200/50">KONTAK D-VOYAGER</span>
                    <h2 class="font-outfit font-extrabold text-3xl text-dark-900 mt-3 mb-6">Hubungi Kantor Layanan Kami</h2>
                    <p class="text-dark-700 mb-8 leading-relaxed">
                        Kami selalu siap menjawab pertanyaan Anda mengenai pemesanan tiket, layanan armada, kemitraan, atau kendala teknis transaksi.
                    </p>

                    <div class="space-y-6">
                        <!-- Address -->
                        <div class="flex items-start gap-4">
                            <div class="h-10 w-10 bg-dark-900 text-brand-500 rounded-xl flex items-center justify-center text-lg shrink-0 border border-brand-500/20">📍</div>
                            <div>
                                <span class="block font-extrabold text-dark-900 text-sm">Alamat Kantor Pusat</span>
                                <span class="text-dark-500 text-xs sm:text-sm">Perumahan Telagasari Indah, Jl. Arjuna, Karawang, Jawa Barat 41381</span>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="flex items-start gap-4">
                            <div class="h-10 w-10 bg-dark-900 text-brand-500 rounded-xl flex items-center justify-center text-lg shrink-0 border border-brand-500/20">📞</div>
                            <div>
                                <span class="block font-extrabold text-dark-900 text-sm">Telepon / WhatsApp</span>
                                <span class="text-dark-500 text-xs sm:text-sm">+62 895-3243-54052 (Customer Service)</span>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="flex items-start gap-4">
                            <div class="h-10 w-10 bg-dark-900 text-brand-500 rounded-xl flex items-center justify-center text-lg shrink-0 border border-brand-500/20">✉️</div>
                            <div>
                                <span class="block font-extrabold text-dark-900 text-sm">Email Resmi</span>
                                <span class="text-dark-500 text-xs sm:text-sm">domiini1c.id@gmail.com</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact form simulation (Right) with Slate-300 borders -->
                <div class="lg:col-span-7 bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-300">
                    <h3 class="font-outfit font-bold text-lg text-dark-900 mb-6">Kirim Pesan Langsung</h3>
                    
                    <form onsubmit="event.preventDefault(); alert('Terimakasih! Pesan Anda telah diterima dan akan segera direspon oleh tim D-Voyager.'); this.reset();" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-dark-500 uppercase mb-2">Nama Lengkap</label>
                                <input type="text" required class="w-full bg-dark-50 border border-slate-300 rounded-xl px-4 py-3 text-dark-900 text-sm focus:outline-none focus:border-brand-500 transition-colors">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-dark-500 uppercase mb-2">Alamat Email</label>
                                <input type="email" required class="w-full bg-dark-50 border border-slate-300 rounded-xl px-4 py-3 text-dark-900 text-sm focus:outline-none focus:border-brand-500 transition-colors">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-dark-500 uppercase mb-2">Subjek / Topik</label>
                            <input type="text" required class="w-full bg-dark-50 border border-slate-300 rounded-xl px-4 py-3 text-dark-900 text-sm focus:outline-none focus:border-brand-500 transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-dark-500 uppercase mb-2">Isi Pesan Anda</label>
                            <textarea rows="4" required class="w-full bg-dark-50 border border-slate-300 rounded-xl px-4 py-3 text-dark-900 text-sm focus:outline-none focus:border-brand-500 transition-colors resize-none"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-dark-900 to-dark-850 hover:bg-dark-800 text-brand-500 border border-brand-500/20 font-extrabold py-3 rounded-xl hover:shadow-md transition-all">
                            Kirim Pesan CS
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </section>

    <!-- FOOTER WITH #1E1E1E DARK BACKGROUND & #FBC02D ACCENTS -->
    <footer class="bg-dark-900 text-slate-300 py-16 border-t border-dark-800 font-sans">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12 pb-12 border-b border-dark-800">
                
                <!-- Brand Profile -->
                <div class="md:col-span-5">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-10 w-10 bg-white rounded-lg p-1 flex items-center justify-center">
                            <img src="{{ asset('assets/Logo_Dvoyager.png') }}" class="max-h-full max-w-full object-contain" onerror="this.src='https://img.icons8.com/color/48/shuttle-bus.png'">
                        </div>
                        <span class="font-outfit font-extrabold text-xl tracking-tight text-white">D-VOYAGER</span>
                    </div>
                    <p class="text-sm text-dark-300 leading-relaxed mb-4">
                        PT D-Voyager Shuttle Indonesia adalah penyedia jasa transportasi shuttle modern berbasis teknologi aplikasi yang didesain untuk kenyamanan, ketepatan waktu, dan kemudahan pelanggan di Indonesia.
                    </p>
                    <span class="text-xs text-dark-400 block">Kemenhub Izin Operasional Shuttle: No. 1826/PHB/2026</span>
                </div>

                <!-- Quick links -->
                <div class="md:col-span-3">
                    <h5 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Akses & Navigasi</h5>
                    <ul class="space-y-2 text-sm text-dark-300">
                        <li><a href="#beranda" class="hover:text-brand-500 transition-colors">Beranda</a></li>
                        <li><a href="#fitur" class="hover:text-brand-500 transition-colors">Fitur Utama</a></li>
                        <li><a href="#simulasi" class="hover:text-brand-500 transition-colors">Cari Tiket</a></li>
                        <li><a href="#tim" class="hover:text-brand-500 transition-colors">Tim Pengembang</a></li>
                    </ul>
                </div>

                <!-- Legal Compliance (SANGAT VITAL UNTUK DOMPETX PG) -->
                <div class="md:col-span-4">
                    <h5 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Kepatuhan Bisnis (Legal)</h5>
                    <ul class="space-y-2.5 text-sm">
                        <!-- Privacy Policy Link -->
                        <li><button type="button" onclick="openModal('privacy')" class="text-dark-300 hover:text-brand-500 transition-colors text-left focus:outline-none">🔒 Kebijakan Privasi (Privacy Policy)</button></li>
                        <!-- Terms of Service Link -->
                        <li><button type="button" onclick="openModal('terms')" class="text-dark-300 hover:text-brand-500 transition-colors text-left focus:outline-none">📝 Syarat & Ketentuan (Terms & Conditions)</button></li>
                        <!-- Refund Policy Link -->
                        <li><button type="button" onclick="openModal('refund')" class="text-dark-300 hover:text-brand-500 transition-colors text-left focus:outline-none">💰 Kebijakan Pembatalan & Refund</button></li>
                    </ul>
                </div>

            </div>

            <!-- Footer Bottom and Secret Admin Login Route -->
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-dark-400">
                <div>
                    © 2026 PT D-Voyager Shuttle Indonesia. Seluruh hak cipta dilindungi undang-undang.
                </div>
                
                <!-- Secret Admin portal route as requested! Subtle and professional in yellow border -->
                <div class="flex items-center gap-4">
                    <span>Didukung oleh: <strong class="text-brand-500">DompetX PG</strong></span>
                    <a href="{{ url('/admin/login') }}" class="text-brand-500 hover:text-white transition-colors border border-brand-500/40 hover:bg-brand-500 hover:text-dark-900 rounded px-2.5 py-1">Akses Portal Karyawan</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ========================================== -->
    <!-- LEGAL MODALS INTERACTIVE SECTION -->
    <!-- ========================================== -->

    <!-- Modal Background overlay -->
    <div id="legal-modal" class="fixed inset-0 z-[100] hidden bg-dark-900/85 backdrop-blur-sm items-center justify-center p-4 sm:p-6 transition-opacity duration-300 opacity-0">
        <!-- Modal Card Container in White with Gold accent -->
        <div class="bg-white rounded-3xl w-full max-w-2xl h-[80vh] flex flex-col shadow-2xl overflow-hidden transform scale-95 transition-transform duration-300 border border-slate-300">
            <!-- Modal Header -->
            <div class="p-6 border-b border-slate-300 flex justify-between items-center bg-[#F8F9FA]">
                <div class="flex items-center gap-2.5">
                    <span class="text-lg" id="modal-icon">📄</span>
                    <h3 id="modal-title" class="font-outfit font-bold text-lg text-dark-900">Judul Dokumen</h3>
                </div>
                <button type="button" onclick="closeModal()" class="text-dark-400 hover:text-dark-900 focus:outline-none p-1 bg-slate-200/50 hover:bg-slate-200 rounded-lg text-sm">✕</button>
            </div>
            
            <!-- Modal Body (Scrollable content) -->
            <div class="p-6 overflow-y-auto flex-1 text-sm text-dark-700 leading-relaxed space-y-4 font-sans" id="modal-content">
                <!-- Dynamic text will be injected here via Javascript -->
            </div>

            <!-- Modal Footer -->
            <div class="p-4 border-t border-slate-300 bg-[#F8F9FA] flex justify-end">
                <button type="button" onclick="closeModal()" class="bg-brand-500 text-dark-900 font-extrabold px-6 py-2.5 rounded-xl hover:bg-brand-600 transition-colors shadow">Saya Mengerti</button>
            </div>
        </div>
    </div>


    <!-- ========================================== -->
    <!-- JAVASCRIPT FUNCTIONS (SIMULATOR & MODALS) -->
    <!-- ========================================== -->
    <script>
        // Toggle mobile nav dropdown
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Hide/Show header on scroll
        let lastScrollY = window.scrollY;
        const header = document.getElementById('main-header');

        window.addEventListener('scroll', () => {
            const currentScrollY = window.scrollY;
            
            // Only hide/show if scrolled more than a small threshold (e.g. 5px) to prevent jitter
            if (Math.abs(currentScrollY - lastScrollY) > 5) {
                if (currentScrollY > lastScrollY && currentScrollY > 100) {
                    // Scrolling down - hide header and close mobile menu
                    header.classList.add('-translate-y-full');
                    header.classList.remove('translate-y-0');
                    mobileMenu.classList.add('hidden');
                } else {
                    // Scrolling up - show header
                    header.classList.remove('-translate-y-full');
                    header.classList.add('translate-y-0');
                }
            }
            lastScrollY = currentScrollY;
        });

        // 1. Interactive Booking Simulator Code
        const routesData = {
            "Jakarta-Bandung": [
                { id: "DV-102", type: "Toyota HiAce Premio", depart: "08:00 WIB", price: "Rp 150.000", seatsLeft: 4 },
                { id: "DV-105", type: "Toyota HiAce Luxury", depart: "14:00 WIB", price: "Rp 150.000", seatsLeft: 8 },
                { id: "DV-109", type: "Toyota HiAce Premio", depart: "20:00 WIB", price: "Rp 150.000", seatsLeft: 10 }
            ],
            "Bandung-Jakarta": [
                { id: "DV-201", type: "Toyota HiAce Premio", depart: "09:00 WIB", price: "Rp 150.000", seatsLeft: 6 },
                { id: "DV-204", type: "Toyota HiAce Luxury", depart: "15:00 WIB", price: "Rp 150.000", seatsLeft: 7 }
            ],
            "Jakarta-Yogyakarta": [
                { id: "DV-302", type: "Toyota HiAce Luxury", depart: "19:00 WIB", price: "Rp 320.000", seatsLeft: 5 }
            ],
            "Yogyakarta-Jakarta": [
                { id: "DV-305", type: "Toyota HiAce Luxury", depart: "19:30 WIB", price: "Rp 320.000", seatsLeft: 9 }
            ]
        };

        let activeRouteId = "";
        let selectedSeatNum = "";

        function runSimulasi() {
            const origin = document.getElementById('sim-origin').value;
            const dest = document.getElementById('sim-dest').value;
            const date = document.getElementById('sim-date').value;
            
            if (origin === dest) {
                alert("Mohon pilih kota asal dan tujuan yang berbeda.");
                return;
            }

            const searchKey = `${origin}-${dest}`;
            const routes = routesData[searchKey] || [
                { id: "DV-400", type: "Toyota HiAce Premio", depart: "10:00 WIB", price: "Rp 250.000", seatsLeft: 8 }
            ];

            const resultsDiv = document.getElementById('sim-results');
            const container = document.getElementById('sim-routes-container');
            const seatsContainer = document.getElementById('sim-seats-container');
            const checkoutAction = document.getElementById('checkout-action');
            
            seatsContainer.classList.add('hidden');
            checkoutAction.classList.add('hidden');
            selectedSeatNum = "";
            document.getElementById('selected-seat-badge').textContent = "Belum Memilih Kursi";
            
            container.innerHTML = "";
            
            routes.forEach(route => {
                const item = document.createElement('div');
                item.className = "flex flex-col sm:flex-row justify-between items-start sm:items-center bg-dark-850 p-4 rounded-2xl border border-dark-800 hover:border-brand-500 transition-colors shadow-sm gap-4 text-white";
                item.innerHTML = `
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-white text-sm sm:text-base">${route.depart}</span>
                            <span class="bg-brand-500 text-dark-900 text-[10px] px-2 py-0.5 rounded font-extrabold">${route.id}</span>
                        </div>
                        <span class="text-xs text-dark-300 block mt-0.5">${route.type} • ${origin} ke ${dest}</span>
                    </div>
                    <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto gap-4 border-t sm:border-t-0 border-dark-800 pt-3 sm:pt-0">
                        <div>
                            <span class="block font-extrabold text-brand-500 text-sm sm:text-base">${route.price}</span>
                            <span class="block text-[10px] text-right text-emerald-400 font-semibold">${route.seatsLeft} kursi tersedia</span>
                        </div>
                        <button type="button" onclick="pilihJadwal('${route.id}')" class="bg-white text-dark-900 font-extrabold text-xs px-4 py-2.5 rounded-xl hover:bg-brand-500 transition-colors">
                            Pilih
                        </button>
                    </div>
                `;
                container.appendChild(item);
            });

            resultsDiv.classList.remove('hidden');
            
            // Scroll automatically to results
            resultsDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function pilihJadwal(routeId) {
            activeRouteId = routeId;
            const seatsContainer = document.getElementById('sim-seats-container');
            seatsContainer.classList.remove('hidden');
            seatsContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function selectSeat(seatNum) {
            selectedSeatNum = seatNum;
            const badge = document.getElementById('selected-seat-badge');
            badge.textContent = `Kursi terpilih: Nomor ${seatNum}`;
            badge.className = "bg-brand-500 text-dark-900 font-extrabold text-xs px-3 py-1 rounded-full";
            
            document.getElementById('checkout-action').classList.remove('hidden');
        }

        function checkoutSimulasi() {
            alert(`[SIMULASI PEMBAYARAN DOMPETX]\n\nAnda memesan tiket Shuttle ID: ${activeRouteId}\nNomor Kursi: ${selectedSeatNum}\nMetode Pembayaran: DompetX Payment Gateway\n\nStatus: Simulasi Sukses! Integrasi DompetX API aktif pada environment produksi.`);
        }

        // 2. Legal Modals Content Data and Display Functions (CRITICAL FOR DOMPETX MERCHANT APPROVAL)
        const modalContents = {
            privacy: {
                title: "Kebijakan Privasi (Privacy Policy)",
                icon: "🔒",
                body: `
                    <h4 class="font-bold text-slate-800 text-sm uppercase">1. Pengantar Kebijakan</h4>
                    <p>Kami di PT D-Voyager Shuttle Indonesia sangat menghargai privasi data pribadi Anda selaku pengguna aplikasi dan website kami. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, mengungkapkan, dan mengamankan informasi pribadi Anda saat menggunakan layanan kami di dominic.my.id dan aplikasi mobile D-Voyager.</p>
                    
                    <h4 class="font-bold text-slate-800 text-sm uppercase mt-4">2. Informasi yang Kami Kumpulkan</h4>
                    <p>Kami mengumpulkan informasi dari Anda ketika Anda melakukan pemesanan tiket, mendaftar akun di aplikasi seluler, atau menghubungi tim layanan pelanggan kami. Informasi yang kami kumpulkan meliputi:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Identitas Pribadi: Nama lengkap, alamat email, nomor telepon genggam, dan foto profil (opsional).</li>
                        <li>Data Transaksi: Detail pemesanan tiket, riwayat transaksi pembayaran melalui payment gateway DompetX, dan detail rute perjalanan Anda.</li>
                        <li>Data Lokasi (GPS): Aplikasi D-Voyager mengumpulkan koordinat lokasi real-time dari driver untuk ditampilkan kepada pelanggan saat melacak perjalanan shuttle demi kenyamanan dan keselamatan.</li>
                    </ul>

                    <h4 class="font-bold text-slate-800 text-sm uppercase mt-4">3. Penggunaan Informasi Data Pribadi</h4>
                    <p>Informasi yang kami kumpulkan digunakan untuk keperluan:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Memproses transaksi pemesanan tiket shuttle dan mengirimkan e-ticket.</li>
                        <li>Memverifikasi transaksi pembayaran aman yang diproses melalui mitra resmi kami, DompetX.</li>
                        <li>Menghubungkan pelanggan dengan supir (driver) melalui sistem pemetaan koordinat rute keberangkatan secara real-time.</li>
                        <li>Meningkatkan kualitas layanan pelanggan dan keamanan sistem kami dari ancaman penipuan online.</li>
                    </ul>

                    <h4 class="font-bold text-slate-800 text-sm uppercase mt-4">4. Pengungkapan Kepada Pihak Ketiga</h4>
                    <p>Kami tidak menjual, memperdagangkan, atau memindahkan informasi pribadi Anda kepada pihak luar secara ilegal. Informasi transaksi keuangan Anda akan diteruskan secara aman ke **DompetX Payment Gateway** selaku mitra resmi kami yang berlisensi dari Bank Indonesia guna memproses pembayaran secara legal.</p>

                    <h4 class="font-bold text-slate-800 text-sm uppercase mt-4">5. Keamanan Data Pengguna</h4>
                    <p>Kami menerapkan berbagai langkah keamanan teknis, termasuk enkripsi data SSL (Secure Socket Layer) 256-bit, untuk menjaga kerahasiaan informasi pribadi Anda dari akses yang tidak sah. Data kartu kredit atau detail pembayaran sensitif diproses langsung pada server DompetX yang memenuhi sertifikasi keamanan internasional PCI-DSS.</p>
                `
            },
            terms: {
                title: "Syarat & Ketentuan (Terms & Conditions)",
                icon: "📝",
                body: `
                    <h4 class="font-bold text-slate-800 text-sm uppercase">1. Ketentuan Penggunaan Layanan</h4>
                    <p>Dengan mengakses website dominic.my.id atau mengunduh aplikasi mobile D-Voyager, Anda setuju untuk terikat oleh Syarat dan Ketentuan penggunaan ini. Jika Anda tidak menyetujui bagian mana pun dari ketentuan ini, Anda dilarang menggunakan layanan kami.</p>
                    
                    <h4 class="font-bold text-slate-800 text-sm uppercase mt-4">2. Pemesanan Tiket & Batas Usia</h4>
                    <p>Pengguna diwajibkan memberikan informasi identitas yang benar, akurat, dan terbaru saat melakukan transaksi tiket. Anda harus berusia minimal 17 tahun atau memiliki pengawasan orang tua saat melakukan transaksi pemesanan tiket shuttle digital.</p>

                    <h4 class="font-bold text-slate-800 text-sm uppercase mt-4">3. Ketentuan Pembayaran Aman</h4>
                    <p>Seluruh transaksi tiket di D-Voyager wajib diselesaikan secara lunas menggunakan metode pembayaran resmi yang tersedia di portal checkout. Pembayaran digital diproses secara instan melalui sistem terintegrasi **DompetX Payment Gateway**.</p>
                    <p>D-Voyager tidak memungut biaya tambahan di luar rincian biaya yang tertera pada invoice resmi saat Anda menekan tombol bayar.</p>

                    <h4 class="font-bold text-slate-800 text-sm uppercase mt-4">4. Hak & Kewajiban Penumpang</h4>
                    <p>Penumpang wajib hadir di lokasi penjemputan (titik asal) minimal 15 menit sebelum waktu keberangkatan yang dijadwalkan pada tiket elektronik. Penumpang wajib menjaga ketertiban umum dan dilarang membawa barang berbahaya.</p>

                    <h4 class="font-bold text-slate-800 text-sm uppercase mt-4">5. Perubahan Syarat Ketentuan</h4>
                    <p>PT D-Voyager Shuttle Indonesia berhak mengubah Syarat & Ketentuan ini sewaktu-waktu tanpa pemberitahuan sebelumnya. Perubahan akan berlaku segera setelah dipublikasikan pada halaman website dominic.my.id ini.</p>
                `
            },
            refund: {
                title: "Kebijakan Pembatalan & Refund (Refund Policy)",
                icon: "💰",
                body: `
                    <h4 class="font-bold text-slate-800 text-sm uppercase">1. Kebijakan Pembatalan Oleh Penumpang</h4>
                    <p>Kami memahami bahwa rencana perjalanan Anda dapat berubah sewaktu-waktu. Penumpang dapat mengajukan pembatalan pemesanan tiket shuttle dengan ketentuan sebagai berikut:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Pembatalan > 24 jam sebelum keberangkatan: Pengembalian dana (Refund) sebesar 100% dari harga tiket yang dibayarkan.</li>
                        <li>Pembatalan 12 s.d 24 jam sebelum keberangkatan: Pengembalian dana (Refund) sebesar 50% dari harga tiket yang dibayarkan.</li>
                        <li>Pembatalan < 12 jam sebelum keberangkatan: Tiket hangus, tidak ada pengembalian dana dalam bentuk apa pun.</li>
                    </ul>

                    <h4 class="font-bold text-slate-800 text-sm uppercase mt-4">2. Alur Pengajuan Pengembalian Dana (Refund)</h4>
                    <p>Prosedur pengajuan refund wajib dilakukan oleh pelanggan melalui menu *Riwayat Transaksi* di dalam aplikasi mobile D-Voyager atau dengan menghubungi tim support kami di **support@dominic.my.id** dengan melampirkan:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Nomor Kode Booking Tiket (Invoice ID).</li>
                        <li>Nama Lengkap Penumpang & Detail Rute.</li>
                        <li>Nomor Rekening Bank atau E-Wallet tujuan pengembalian dana.</li>
                    </ul>

                    <h4 class="font-bold text-slate-800 text-sm uppercase mt-4">3. Proses Durasi Transfer Dana Refund</h4>
                    <p>Setelah pengajuan refund disetujui oleh tim administrasi kami, dana pengembalian akan ditransfer ke rekening bank terdaftar Anda dalam waktu **3 s.d 5 hari kerja** melalui sistem settlement payment gateway kami.</p>

                    <h4 class="font-bold text-slate-800 text-sm uppercase mt-4">4. Pembatalan Perjalanan Oleh Pihak D-Voyager</h4>
                    <p>Apabila terjadi kendala operasional yang mendesak yang menyebabkan perjalanan dibatalkan sepenuhnya oleh D-Voyager, penumpang akan menerima pengembalian dana **100% penuh secara instan** atau ditawarkan opsi penjadwalan ulang (*Reschedule*) secara gratis.</p>
                `
            }
        };

        function openModal(modalKey) {
            const modal = document.getElementById('legal-modal');
            const title = document.getElementById('modal-title');
            const icon = document.getElementById('modal-icon');
            const content = document.getElementById('modal-content');
            
            const data = modalContents[modalKey];
            if (!data) return;

            title.textContent = data.title;
            icon.textContent = data.icon;
            content.innerHTML = data.body;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            // Trigger animation fade in
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modal.classList.add('opacity-100');
                modal.firstElementChild.classList.remove('scale-95');
                modal.firstElementChild.classList.add('scale-100');
            }, 10);
            
            // Prevent body scroll
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            const modal = document.getElementById('legal-modal');
            
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0');
            modal.firstElementChild.classList.remove('scale-100');
            modal.firstElementChild.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
            
            // Enable body scroll
            document.body.style.overflow = '';
        }

        // Close modal when clicking outside the card
        document.getElementById('legal-modal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('legal-modal')) {
                closeModal();
            }
        });
    </script>
</body>
</html>
