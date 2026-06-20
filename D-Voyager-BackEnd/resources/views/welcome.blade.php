<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>D-Voyager Shuttle - Sistem Manajemen & Pemesanan Tiket Shuttle Terpadu</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png?v=1') }}">
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="D-Voyager Shuttle adalah sistem manajemen dan pemesanan tiket shuttle terpadu dengan pelacakan GPS real-time, seleksi kursi otomatis, dan pembayaran digital instan.">
    <meta name="keywords" content="shuttle, travel, booking tiket, pelacakan gps, d-voyager, tiket travel, dompetx">
    <meta name="author" content="Kelompok 18 Shuttle D-Voyager">
    
    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Flatpickr (Premium Date Picker) CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

    <!-- SweetAlert2 (Premium Alerts) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        /* Custom Flatpickr Premium Styling */
        .flatpickr-calendar {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(209, 213, 219, 0.8) !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
            border-radius: 20px !important;
            font-family: 'Inter', sans-serif !important;
        }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange, .flatpickr-day.selected:focus, .flatpickr-day.startRange:focus, .flatpickr-day.endRange:focus, .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover, .flatpickr-day.prevMonthDay.selected, .flatpickr-day.nextMonthDay.selected {
            background: #FBC02D !important;
            border-color: #FBC02D !important;
            color: #1E1E1E !important;
            font-weight: 800 !important;
        }
        .flatpickr-months .flatpickr-month {
            color: #1E1E1E !important;
        }
        .flatpickr-current-month .numInputWrapper span.arrowUp:after {
            border-bottom-color: #1E1E1E !important;
        }
        .flatpickr-current-month .numInputWrapper span.arrowDown:after {
            border-top-color: #1E1E1E !important;
        }
        .flatpickr-day:hover {
            background: #fec625 !important;
            color: #1E1E1E !important;
        }

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
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.4s ease-out forwards;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        .floating-mockup {
            animation: float 4s ease-in-out infinite;
        }

        /* Mobile Ticket Simulation Style */
        .ticket-card {
            background: white;
            border-radius: 20px;
            position: relative;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
            overflow: visible;
        }
        .ticket-cutout {
            position: absolute;
            top: 68%;
            width: 24px;
            height: 24px;
            background: #F8F9FA; /* Matches section bg */
            border-radius: 50%;
            transform: translateY(-50%);
            z-index: 10;
        }
        .ticket-cutout.left { left: -14px; box-shadow: inset -4px 0 6px rgba(0,0,0,0.02); }
        .ticket-cutout.right { right: -14px; box-shadow: inset 4px 0 6px rgba(0,0,0,0.02); }
        
        .ticket-divider {
            border-top: 2px dashed #E5E7EB;
            width: calc(100% - 32px);
            margin: 0 auto;
        }

        /* Mobile Seat Simulation Style */
        .seat-btn {
            width: 44px;
            height: 48px;
            border-radius: 12px;
            position: relative;
            overflow: hidden;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 13px;
            border: 2px solid #E5E7EB;
            background: white;
            color: #1E1E1E;
        }
        .seat-btn::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 12px;
            background: rgba(0, 0, 0, 0.04);
        }
        .seat-btn.selected {
            background: #FBC02D;
            border-color: #FBC02D;
            box-shadow: 0 8px 16px rgba(251, 192, 45, 0.3);
        }
        .seat-btn.selected::before {
            background: rgba(255, 255, 255, 0.3);
        }
        .seat-btn.unavailable {
            background: #f3f4f6;
            color: #d1d5db;
            cursor: not-allowed;
            border-color: #e5e7eb;
        }
        .seat-btn:active:not(.unavailable) {
            transform: scale(0.92);
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
                        
                        <!-- Internal App UI Slider (Screenshots 1web.jpg to 5web.jpg) -->
                        <div id="mockup-slider-container" class="w-full h-full bg-white rounded-[30px] overflow-hidden relative">
                            <!-- Dual layers for cross-fade -->
                            <img id="mockup-img-1" src="{{ asset('assets/1web.jpg') }}" alt="D-Voyager App Screenshot" class="absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 opacity-100 z-10">
                            <img id="mockup-img-2" src="{{ asset('assets/2web.jpg') }}" alt="D-Voyager App Screenshot" class="absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 opacity-0 z-0">
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
                        <i class="fa-solid fa-mobile-screen-button"></i>
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
                    <div class="h-14 w-14 bg-brand-100 text-brand-600 rounded-2xl flex items-center justify-center text-3xl mb-6 shadow-inner group-hover:bg-brand-500 group-hover:text-dark-900 transition-colors duration-300">
                        <i class="fa-solid fa-bus"></i>
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
                        <i class="fa-solid fa-user-tie"></i>
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

            <!-- Simulator Widget Frame (Match Mobile Design: Light themed with premium cards) -->
            <div class="bg-white rounded-[32px] p-6 sm:p-10 shadow-2xl border border-slate-300/40 relative">
                <div class="absolute -top-1 right-10 md:right-16 h-8 w-24 bg-slate-100 rounded-b-2xl border-x border-b border-slate-200/50 flex items-center justify-center gap-1">
                    <span class="h-1 w-8 rounded-full bg-slate-300"></span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 mt-4">
                    <!-- Origin Selector -->
                    <div>
                        <label class="block text-[10px] font-extrabold text-dark-400 uppercase tracking-[0.1em] mb-2 px-1">Kota Asal</label>
                        <select id="sim-origin" class="hidden">
                            <option value="Jakarta">Jakarta (Kuningan)</option>
                            <option value="Bandung">Bandung (Dago)</option>
                            <option value="Surabaya">Surabaya (Tunjungan)</option>
                            <option value="Yogyakarta">Yogyakarta (Malioboro)</option>
                        </select>
                        <div class="relative custom-dropdown" data-select-id="sim-origin">
                            <button type="button" class="dropdown-trigger w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3.5 text-dark-900 font-bold focus:outline-none focus:border-brand-500 transition-all flex items-center justify-between cursor-pointer hover:bg-slate-100/50">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-location-dot text-brand-500 text-sm"></i>
                                    <span class="dropdown-value-text">Jakarta (Kuningan)</span>
                                </div>
                                <i class="fa-solid fa-chevron-down text-dark-400 text-xs transition-transform duration-300"></i>
                            </button>
                            <div class="dropdown-menu absolute left-0 right-0 mt-2 bg-white rounded-2xl shadow-xl border border-slate-200/80 p-2 z-50 transition-all duration-200 scale-95 opacity-0 pointer-events-none origin-top">
                                <div class="dropdown-option px-4 py-2.5 rounded-xl text-sm font-bold text-dark-800 hover:bg-brand-50 hover:text-dark-900 cursor-pointer flex items-center justify-between" data-value="Jakarta">
                                    <span>Jakarta (Kuningan)</span>
                                    <i class="fa-solid fa-check text-brand-500 text-xs opacity-0"></i>
                                </div>
                                <div class="dropdown-option px-4 py-2.5 rounded-xl text-sm font-bold text-dark-800 hover:bg-brand-50 hover:text-dark-900 cursor-pointer flex items-center justify-between" data-value="Bandung">
                                    <span>Bandung (Dago)</span>
                                    <i class="fa-solid fa-check text-brand-500 text-xs opacity-0"></i>
                                </div>
                                <div class="dropdown-option px-4 py-2.5 rounded-xl text-sm font-bold text-dark-800 hover:bg-brand-50 hover:text-dark-900 cursor-pointer flex items-center justify-between" data-value="Surabaya">
                                    <span>Surabaya (Tunjungan)</span>
                                    <i class="fa-solid fa-check text-brand-500 text-xs opacity-0"></i>
                                </div>
                                <div class="dropdown-option px-4 py-2.5 rounded-xl text-sm font-bold text-dark-800 hover:bg-brand-50 hover:text-dark-900 cursor-pointer flex items-center justify-between" data-value="Yogyakarta">
                                    <span>Yogyakarta (Malioboro)</span>
                                    <i class="fa-solid fa-check text-brand-500 text-xs opacity-0"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Destination Selector -->
                    <div>
                        <label class="block text-[10px] font-extrabold text-dark-400 uppercase tracking-[0.1em] mb-2 px-1">Kota Tujuan</label>
                        <select id="sim-dest" class="hidden">
                            <option value="Bandung">Bandung (Dago)</option>
                            <option value="Jakarta" selected>Jakarta (Kuningan)</option>
                            <option value="Yogyakarta">Yogyakarta (Malioboro)</option>
                            <option value="Surabaya">Surabaya (Tunjungan)</option>
                        </select>
                        <div class="relative custom-dropdown" data-select-id="sim-dest">
                            <button type="button" class="dropdown-trigger w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3.5 text-dark-900 font-bold focus:outline-none focus:border-brand-500 transition-all flex items-center justify-between cursor-pointer hover:bg-slate-100/50">
                                <div class="flex items-center gap-3">
                                    <i class="fa-solid fa-location-dot text-brand-500 text-sm"></i>
                                    <span class="dropdown-value-text">Jakarta (Kuningan)</span>
                                </div>
                                <i class="fa-solid fa-chevron-down text-dark-400 text-xs transition-transform duration-300"></i>
                            </button>
                            <div class="dropdown-menu absolute left-0 right-0 mt-2 bg-white rounded-2xl shadow-xl border border-slate-200/80 p-2 z-50 transition-all duration-200 scale-95 opacity-0 pointer-events-none origin-top">
                                <div class="dropdown-option px-4 py-2.5 rounded-xl text-sm font-bold text-dark-800 hover:bg-brand-50 hover:text-dark-900 cursor-pointer flex items-center justify-between" data-value="Bandung">
                                    <span>Bandung (Dago)</span>
                                    <i class="fa-solid fa-check text-brand-500 text-xs opacity-0"></i>
                                </div>
                                <div class="dropdown-option px-4 py-2.5 rounded-xl text-sm font-bold text-dark-800 hover:bg-brand-50 hover:text-dark-900 cursor-pointer flex items-center justify-between" data-value="Jakarta">
                                    <span>Jakarta (Kuningan)</span>
                                    <i class="fa-solid fa-check text-brand-500 text-xs opacity-0"></i>
                                </div>
                                <div class="dropdown-option px-4 py-2.5 rounded-xl text-sm font-bold text-dark-800 hover:bg-brand-50 hover:text-dark-900 cursor-pointer flex items-center justify-between" data-value="Yogyakarta">
                                    <span>Yogyakarta (Malioboro)</span>
                                    <i class="fa-solid fa-check text-brand-500 text-xs opacity-0"></i>
                                </div>
                                <div class="dropdown-option px-4 py-2.5 rounded-xl text-sm font-bold text-dark-800 hover:bg-brand-50 hover:text-dark-900 cursor-pointer flex items-center justify-between" data-value="Surabaya">
                                    <span>Surabaya (Tunjungan)</span>
                                    <i class="fa-solid fa-check text-brand-500 text-xs opacity-0"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Date Picker -->
                    <div>
                        <label class="block text-[10px] font-extrabold text-dark-400 uppercase tracking-[0.1em] mb-2 px-1">Tanggal</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-brand-500 text-sm z-10 pointer-events-none">
                                <i class="fa-solid fa-calendar-days"></i>
                            </span>
                            <input type="date" id="sim-date" class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-11 pr-4 py-3.5 text-dark-900 font-bold focus:outline-none focus:border-brand-500 transition-all cursor-pointer" value="2026-06-12">
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <button type="button" onclick="runSimulasi()" class="w-full bg-brand-500 text-dark-900 font-extrabold py-4 rounded-2xl hover:bg-brand-600 shadow-lg shadow-brand-500/20 hover:shadow-brand-500/30 transition-all transform hover:-translate-y-0.5 text-center flex items-center justify-center gap-2 group">
                        Cari Tiket Sekarang
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>

                <!-- Results Output (Initially Hidden) -->
                <div id="sim-results" class="hidden mt-12 bg-slate-50/50 -mx-6 sm:-mx-10 p-6 sm:p-10 border-t border-slate-200/60">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="font-outfit font-extrabold text-xl text-dark-900">Jadwal Tersedia</h3>
                        <span id="results-count" class="text-xs font-bold text-dark-400 bg-white border border-slate-200 px-3 py-1 rounded-full">3 Armada Ditemukan</span>
                    </div>
                    <div id="sim-routes-container" class="space-y-6">
                        <!-- Result Items in Ticket Style will be injected here -->
                    </div>
                </div>

                <!-- Seat Map Output (Initially Hidden, Match Mobile Seat Selection UI) -->
                <div id="sim-seats-container" class="hidden mt-4 bg-slate-100/50 -mx-6 sm:-mx-10 p-6 sm:p-10 border-t border-slate-200/60 transition-all duration-500">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                        <div>
                            <h4 class="font-outfit font-extrabold text-xl text-dark-900">Pilih Kursi</h4>
                            <p class="text-xs font-bold text-dark-400 mt-1">Ketuk kursi yang tersedia untuk membooking</p>
                        </div>
                        <div id="selected-seat-badge" class="bg-white border border-slate-200 text-dark-900 font-bold text-sm px-5 py-2.5 rounded-2xl shadow-sm flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-slate-300"></span> Belum Memilih Kursi
                        </div>
                    </div>

                    <!-- Premium Seat Layout Container -->
                    <div class="max-w-[320px] mx-auto bg-white p-8 rounded-[40px] shadow-xl border border-slate-200/60 relative flex flex-col items-center">
                        <div class="w-full bg-slate-50 text-center font-black text-slate-400 py-2.5 rounded-2xl mb-8 text-[10px] uppercase tracking-[0.2em] border border-slate-200/40">BAGIAN DEPAN / SUPIR</div>
                        
                        <!-- The Seat grid mimicking the mobile experience -->
                        <div class="grid grid-cols-3 gap-6 w-full">
                            <!-- Driver Space -->
                            <div class="w-[44px] h-[48px] border-2 border-dashed border-slate-200 bg-slate-50 rounded-xl flex items-center justify-center text-slate-300 text-lg opacity-60">
                                <i class="fa-solid fa-user-tie"></i>  
                            </div>
                            <div class="w-[44px]"></div>
                            <!-- Seat 1 -->
                            <button id="seat-1" onclick="selectSeat('1')" class="seat-btn">01</button>
                            
                            <!-- Row 2 -->
                            <button id="seat-2" onclick="selectSeat('2')" class="seat-btn">02</button>
                            <div class="flex items-center justify-center"></div>
                            <button class="seat-btn unavailable" disabled>03</button>
                            
                            <!-- Row 3 -->
                            <button id="seat-4" onclick="selectSeat('4')" class="seat-btn">04</button>
                            <div class="flex items-center justify-center"></div>
                            <button id="seat-5" onclick="selectSeat('5')" class="seat-btn">05</button>
                            
                            <!-- Row 4 -->
                            <button id="seat-6" onclick="selectSeat('6')" class="seat-btn">06</button>
                            <button class="seat-btn unavailable" disabled>07</button>
                            <button id="seat-8" onclick="selectSeat('8')" class="seat-btn">08</button>
                        </div>
                    </div>

                    <!-- Action Footer -->
                    <div id="checkout-action" class="hidden mt-10 text-center animate-bounce-short">
                        <button type="button" onclick="checkoutSimulasi()" class="w-full sm:w-auto bg-dark-900 text-brand-500 font-black px-10 py-4 rounded-2xl hover:bg-dark-850 shadow-2xl transition-all transform hover:-translate-y-1 active:translate-y-0.5 flex items-center justify-center gap-3 mx-auto">
                            LANJUT PEMBAYARAN
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
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
                    Aplikasi Shuttle D-Voyager dikembangkan dan dikelola secara profesional oleh talenta-talenta berdedikasi dari (Kelompok 18).
                </p>
            </div>

            <!-- Team Grid: Executive Profile Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8">
                <!-- Setyo -->
                <div class="group relative bg-white rounded-3xl p-8 text-center border border-slate-200 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 flex flex-col h-full">
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-24 h-1 bg-brand-500 rounded-b-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    
                    <div class="relative inline-block mb-6">
                        <div class="h-20 w-20 bg-dark-900 border-4 border-white shadow-xl rounded-2xl flex items-center justify-center font-black font-outfit text-2xl text-brand-500 transform rotate-3 group-hover:rotate-0 transition-transform duration-500">
                            SD
                        </div>
                    </div>

                    <div class="flex-1 flex flex-col justify-center mb-6">
                        <h4 class="font-outfit font-black text-dark-900 text-lg leading-tight">Setyo Dwinugroho</h4>
                    </div>
                    
                    <div class="pt-6 border-t border-slate-100">
                        <span class="text-[9px] font-mono text-slate-400 block mb-1 uppercase tracking-widest">NIM Mahasiswa</span>
                        <span class="text-xs font-bold text-dark-400">24416255201143</span>
                    </div>
                </div>

                <!-- Ahmad -->
                <div class="group relative bg-white rounded-3xl p-8 text-center border border-slate-200 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 flex flex-col h-full">
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-24 h-1 bg-brand-500 rounded-b-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    
                    <div class="relative inline-block mb-6">
                        <div class="h-20 w-20 bg-dark-900 border-4 border-white shadow-xl rounded-2xl flex items-center justify-center font-black font-outfit text-2xl text-brand-500 transform -rotate-3 group-hover:rotate-0 transition-transform duration-500">
                            AF
                        </div>
                    </div>

                    <div class="flex-1 flex flex-col justify-center mb-6">
                        <h4 class="font-outfit font-black text-dark-900 text-lg leading-tight">Ahmad Farid I. F.</h4>
                    </div>
                    
                    <div class="pt-6 border-t border-slate-100">
                        <span class="text-[9px] font-mono text-slate-400 block mb-1 uppercase tracking-widest">NIM Mahasiswa</span>
                        <span class="text-xs font-bold text-dark-400">24416255201108</span>
                    </div>
                </div>

                <!-- Dwi Arya -->
                <div class="group relative bg-white rounded-3xl p-8 text-center border border-slate-200 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 flex flex-col h-full">
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-24 h-1 bg-brand-500 rounded-b-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    
                    <div class="relative inline-block mb-6">
                        <div class="h-20 w-20 bg-dark-900 border-4 border-white shadow-xl rounded-2xl flex items-center justify-center font-black font-outfit text-2xl text-brand-500 transform rotate-6 group-hover:rotate-0 transition-transform duration-500">
                            DA
                        </div>
                    </div>

                    <div class="flex-1 flex flex-col justify-center mb-6">
                        <h4 class="font-outfit font-black text-dark-900 text-lg leading-tight">Dwi Arya D.</h4>
                    </div>
                    
                    <div class="pt-6 border-t border-slate-100">
                        <span class="text-[9px] font-mono text-slate-400 block mb-1 uppercase tracking-widest">NIM Mahasiswa</span>
                        <span class="text-xs font-bold text-dark-400">24416255201129</span>
                    </div>
                </div>

                <!-- Moreno -->
                <div class="group relative bg-white rounded-3xl p-8 text-center border border-slate-200 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 flex flex-col h-full">
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-24 h-1 bg-brand-500 rounded-b-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    
                    <div class="relative inline-block mb-6">
                        <div class="h-20 w-20 bg-dark-900 border-4 border-white shadow-xl rounded-2xl flex items-center justify-center font-black font-outfit text-2xl text-brand-500 transform -rotate-6 group-hover:rotate-0 transition-transform duration-500">
                            MA
                        </div>
                    </div>

                    <div class="flex-1 flex flex-col justify-center mb-6">
                        <h4 class="font-outfit font-black text-dark-900 text-lg leading-tight">Moreno Alvarel</h4>
                    </div>
                    
                    <div class="pt-6 border-t border-slate-100">
                        <span class="text-[9px] font-mono text-slate-400 block mb-1 uppercase tracking-widest">NIM Mahasiswa</span>
                        <span class="text-xs font-bold text-dark-400">24416255201114</span>
                    </div>
                </div>

                <!-- Jonatan -->
                <div class="group relative bg-white rounded-3xl p-8 text-center border border-slate-200 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 flex flex-col h-full">
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-24 h-1 bg-brand-500 rounded-b-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    
                    <div class="relative inline-block mb-6">
                        <div class="h-20 w-20 bg-dark-900 border-4 border-white shadow-xl rounded-2xl flex items-center justify-center font-black font-outfit text-2xl text-brand-500 transform rotate-2 group-hover:rotate-0 transition-transform duration-500">
                            JS
                        </div>
                    </div>

                    <div class="flex-1 flex flex-col justify-center mb-6">
                        <h4 class="font-outfit font-black text-dark-900 text-lg leading-tight">Jonatan S. Simbolon</h4>
                    </div>
                    
                    <div class="pt-6 border-t border-slate-100">
                        <span class="text-[9px] font-mono text-slate-400 block mb-1 uppercase tracking-widest">NIM Mahasiswa</span>
                        <span class="text-xs font-bold text-dark-400">24416255201154</span>
                    </div>
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
                            <div class="h-10 w-10 bg-dark-900 text-brand-500 rounded-xl flex items-center justify-center text-lg shrink-0 border border-brand-500/20"><i class="fa-solid fa-location-dot"></i></div>
                            <div>
                                <span class="block font-extrabold text-dark-900 text-sm">Alamat Kantor Pusat</span>
                                <span class="text-dark-500 text-xs sm:text-sm">Perumahan Telagasari Indah, Jl. Arjuna, Karawang, Jawa Barat 41381</span>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="flex items-start gap-4">
                            <div class="h-10 w-10 bg-dark-900 text-brand-500 rounded-xl flex items-center justify-center text-lg shrink-0 border border-brand-500/20"><i class="fa-solid fa-phone"></i></div>
                            <div>
                                <span class="block font-extrabold text-dark-900 text-sm">Telepon / WhatsApp</span>
                                <span class="text-dark-500 text-xs sm:text-sm">+62 895-3243-54052 (Customer Service)</span>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="flex items-start gap-4">
                            <div class="h-10 w-10 bg-dark-900 text-brand-500 rounded-xl flex items-center justify-center text-lg shrink-0 border border-brand-500/20"><i class="fa-solid fa-envelope"></i></div>
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
                    
                    <form onsubmit="handleContactSubmit(event)" class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-[10px] font-extrabold text-dark-400 uppercase tracking-widest mb-2 px-1">Nama Lengkap</label>
                                <input type="text" id="contact-name" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-dark-900 text-sm focus:outline-none focus:border-brand-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-extrabold text-dark-400 uppercase tracking-widest mb-2 px-1">Alamat Email</label>
                                <input type="email" id="contact-email" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-dark-900 text-sm focus:outline-none focus:border-brand-500 transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-dark-400 uppercase tracking-widest mb-2 px-1">Subjek / Topik</label>
                            <div class="relative mb-3">
                                <select id="contact-subject" required onchange="toggleOtherSubject(this.value)" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-dark-900 text-sm focus:outline-none focus:border-brand-500 transition-all appearance-none cursor-pointer font-bold">
                                    <option value="" disabled selected>Pilih Topik Pertanyaan</option>
                                    <option value="Pemesanan Tiket">Pemesanan Tiket</option>
                                    <option value="Layanan Armada">Layanan Armada</option>
                                    <option value="Kemitraan">Kemitraan</option>
                                    <option value="Kendala Teknis Transaksi">Kendala Teknis Transaksi</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-dark-400">▼</div>
                            </div>
                            
                            <!-- Dynamic Other Subject Input -->
                            <div id="other-subject-container" class="hidden animate-fade-in">
                                <input type="text" id="contact-subject-other" placeholder="Tuliskan subjek lainnya di sini..." class="w-full bg-white border border-brand-300 rounded-2xl px-4 py-3.5 text-dark-900 text-sm focus:outline-none focus:border-brand-500 transition-all shadow-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold text-dark-400 uppercase tracking-widest mb-2 px-1">Isi Pesan Anda</label>
                            <textarea id="contact-message" rows="4" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-dark-900 text-sm focus:outline-none focus:border-brand-500 transition-all resize-none"></textarea>
                        </div>
                        <button type="submit" id="contact-submit-btn" class="w-full bg-dark-900 text-brand-500 font-black py-4 rounded-2xl hover:bg-dark-850 shadow-xl transition-all transform hover:-translate-y-1 active:translate-y-0.5 flex items-center justify-center gap-2">
                            <span id="btn-text">KIRIM PESAN CS</span>
                            <span id="btn-loader" class="hidden h-5 w-5 border-2 border-brand-500 border-t-transparent rounded-full animate-spin"></span>
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
                        D-Voyager Shuttle Indonesia adalah penyedia jasa transportasi shuttle modern berbasis teknologi aplikasi yang didesain untuk kenyamanan, ketepatan waktu, dan kemudahan pelanggan di Indonesia.
                    </p>
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
                        <li><button type="button" onclick="openModal('privacy')" class="text-dark-300 hover:text-brand-500 transition-colors text-left focus:outline-none"><i class="fa-solid fa-lock"></i> Kebijakan Privasi (Privacy Policy)</button></li>
                        <!-- Terms of Service Link -->
                        <li><button type="button" onclick="openModal('terms')" class="text-dark-300 hover:text-brand-500 transition-colors text-left focus:outline-none"><i class="fa-solid fa-file-lines"></i> Syarat & Ketentuan (Terms & Conditions)</button></li>
                        <!-- Refund Policy Link -->
                        <li><button type="button" onclick="openModal('refund')" class="text-dark-300 hover:text-brand-500 transition-colors text-left focus:outline-none"><i class="fa-solid fa-money-bill-wave"></i> Kebijakan Pembatalan & Refund</button></li>
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
                    <span>Didukung oleh: <strong class="text-brand-500">DompetX</strong></span>
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
                    <span class="text-lg" id="modal-icon"><i class="fa-solid fa-lock"></i></span>
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
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Mohon pilih kota asal dan tujuan yang berbeda.',
                    confirmButtonColor: '#FBC02D',
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'rounded-3xl font-sans'
                    }
                });
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
                item.className = "ticket-card p-6 flex flex-col md:flex-row gap-6 relative group cursor-pointer hover:-translate-y-1 transition-transform duration-300";
                item.onclick = () => pilihJadwal(route.id);
                item.innerHTML = `
                    <div class="ticket-cutout left"></div>
                    <div class="ticket-cutout right"></div>
                    
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="bg-brand-500 text-dark-900 text-[10px] font-black px-2.5 py-1 rounded-lg shadow-sm">${route.id}</div>
                            <div class="flex items-center gap-1.5 bg-slate-100 px-2 py-0.5 rounded-md">
                                <span class="text-[10px] font-bold text-dark-400">STATUS:</span>
                                <span class="flex h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                <span class="text-[10px] font-black text-emerald-600 uppercase">Aktif</span>
                            </div>
                        </div>
                        
                        <div class="flex items-end gap-6 mb-6">
                            <div class="text-3xl font-black text-dark-900 leading-none">${route.depart.split(' ')[0]}</div>
                            <div class="flex-1 h-px bg-slate-200 mb-2 relative">
                                <div class="absolute -top-1.5 left-1/2 -translate-x-1/2 bg-white px-2 text-[10px] font-extrabold text-slate-300">D-VOYAGER</div>
                            </div>
                            <div class="text-right">
                                <div class="text-[10px] font-extrabold text-dark-400 uppercase tracking-widest pl-4">Estimasi</div>
                                <div class="text-xs font-black text-dark-800">Cepat & Aman</div>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap gap-4 pt-4 border-t border-slate-100">
                            <div class="flex items-center gap-2 text-xs font-bold text-dark-700">
                                <span class="p-1.5 bg-brand-50 rounded-lg"><i class="fa-solid fa-bus"></i></span>
                                <span>${route.type}</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs font-bold text-dark-700">
                                <span class="p-1.5 bg-slate-50 rounded-lg"><i class="fa-solid fa-location-dot"></i></span>
                                <span>${origin} → ${dest}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="md:w-px md:h-24 bg-slate-100 self-center hidden md:block"></div>
                    <div class="ticket-divider md:hidden"></div>

                    <div class="md:w-48 flex flex-col justify-center items-center md:items-end gap-3 pt-6 md:pt-0">
                        <div class="text-right">
                            <div class="text-[10px] font-extrabold text-dark-400 uppercase tracking-widest">Total Harga</div>
                            <div class="text-2xl font-black text-dark-900">${route.price}</div>
                        </div>
                        <div class="bg-emerald-50 text-emerald-600 text-[10px] font-black px-3 py-1.5 rounded-full border border-emerald-100/50">
                            ${route.seatsLeft} KURSI TERSEDIA
                        </div>
                        <button type="button" class="w-full bg-dark-900 text-brand-500 font-extrabold text-[11px] py-3 rounded-xl group-hover:bg-brand-500 group-hover:text-dark-900 transition-all shadow-lg active:scale-95">
                            PILIH JADWAL
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
            // Unselect previous
            if (selectedSeatNum) {
                const prevSeat = document.getElementById(`seat-${selectedSeatNum}`);
                if (prevSeat) prevSeat.classList.remove('selected');
            }

            selectedSeatNum = seatNum;
            const currentSeat = document.getElementById(`seat-${seatNum}`);
            if (currentSeat) currentSeat.classList.add('selected');

            const badge = document.getElementById('selected-seat-badge');
            badge.innerHTML = `<span class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span> Kursi Terpilih: ${seatNum}`;
            badge.classList.add('border-brand-500', 'bg-brand-50/50');
            
            document.getElementById('checkout-action').classList.remove('hidden');
            document.getElementById('checkout-action').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function checkoutSimulasi() {
            Swal.fire({
                icon: 'success',
                title: 'Simulasi Pembayaran DompetX',
                html: `
                    <div class="text-left text-sm space-y-2 text-dark-800">
                        <p><strong>ID Shuttle:</strong> ${activeRouteId}</p>
                        <p><strong>Nomor Kursi:</strong> ${selectedSeatNum}</p>
                        <p><strong>Metode Pembayaran:</strong> DompetX Payment Gateway</p>
                        <hr class="border-slate-200 my-2">
                        <p class="text-xs text-emerald-600 font-bold bg-emerald-50 p-2.5 rounded-xl border border-emerald-100/50">✓ Simulasi Sukses! Integrasi DompetX API aktif pada environment produksi.</p>
                    </div>
                `,
                confirmButtonColor: '#FBC02D',
                confirmButtonText: 'Selesai',
                customClass: {
                    popup: 'rounded-3xl font-sans'
                }
            });
        }

        // 2. Legal Modals Content Data and Display Functions (CRITICAL FOR DOMPETX MERCHANT APPROVAL)
        const modalContents = {
            privacy: {
                title: "Kebijakan Privasi (Privacy Policy)",
                icon: '<i class="fa-solid fa-lock"></i>',
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
                icon: '<i class="fa-solid fa-file-lines"></i>',
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
                icon: '<i class="fa-solid fa-money-bill-wave"></i>',
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
            icon.innerHTML = data.icon;
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

        function toggleOtherSubject(value) {
            const container = document.getElementById('other-subject-container');
            const otherInput = document.getElementById('contact-subject-other');
            
            if (value === 'Lainnya') {
                container.classList.remove('hidden');
                otherInput.setAttribute('required', 'required');
            } else {
                container.classList.add('hidden');
                otherInput.removeAttribute('required');
            }
        }

        async function handleContactSubmit(event) {
            event.preventDefault();
            
            const btn = document.getElementById('contact-submit-btn');
            const btnText = document.getElementById('btn-text');
            const btnLoader = document.getElementById('btn-loader');
            const form = event.target;
            
            const name = document.getElementById('contact-name').value;
            const email = document.getElementById('contact-email').value;
            let subject = document.getElementById('contact-subject').value;
            const message = document.getElementById('contact-message').value;
            
            if (subject === 'Lainnya') {
                subject = document.getElementById('contact-subject-other').value;
            }

            // Set Loading state
            btn.disabled = true;
            btnText.textContent = "SEDANG MENGIRIM...";
            btnLoader.classList.remove('hidden');

            try {
                const response = await fetch('/contact', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ name, email, subject, message })
                });

                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Pesan Terkirim',
                        text: `Terimakasih ${name}! Pesan Anda telah berhasil dikirim langsung ke tim D-Voyager melalui sistem kami.`,
                        confirmButtonColor: '#FBC02D',
                        confirmButtonText: 'Sama-sama',
                        customClass: {
                            popup: 'rounded-3xl font-sans'
                        }
                    });
                    form.reset();
                    toggleOtherSubject(''); // Hide custom input if it was open
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengirim',
                    text: 'Maaf, terjadi masalah saat mengirim pesan: ' + error.message + '. Silakan coba lagi nanti.',
                    confirmButtonColor: '#FBC02D',
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'rounded-3xl font-sans'
                    }
                });
            } finally {
                // Reset state
                btn.disabled = false;
                btnText.textContent = "KIRIM PESAN CS";
                btnLoader.classList.add('hidden');
            }
        }

        // Premium Cross-Fade Slider Logic
        const img1 = document.getElementById('mockup-img-1');
        const img2 = document.getElementById('mockup-img-2');
        const images = [
            "{{ asset('assets/1web.jpg') }}",
            "{{ asset('assets/2web.jpg') }}",
            "{{ asset('assets/3web.jpg') }}",
            "{{ asset('assets/4web.jpg') }}",
            "{{ asset('assets/5web.jpg') }}"
        ];
        let currentIdx = 0;
        let isFirstActive = true;

        function rotateMockupImage() {
            currentIdx = (currentIdx + 1) % images.length;
            const nextSrc = images[currentIdx];

            if (isFirstActive) {
                // Fade out 1, Bring in 2
                img2.src = nextSrc;
                img2.onload = () => {
                    img1.classList.replace('opacity-100', 'opacity-0');
                    img1.classList.replace('z-10', 'z-0');
                    img2.classList.replace('opacity-0', 'opacity-100');
                    img2.classList.replace('z-0', 'z-10');
                    isFirstActive = false;
                };
            } else {
                // Fade out 2, Bring in 1
                img1.src = nextSrc;
                img1.onload = () => {
                    img2.classList.replace('opacity-100', 'opacity-0');
                    img2.classList.replace('z-10', 'z-0');
                    img1.classList.replace('opacity-0', 'opacity-100');
                    img1.classList.replace('z-0', 'z-10');
                    isFirstActive = true;
                };
            }
        }

        // Start Rotation every 4 seconds
        setInterval(rotateMockupImage, 4000);

        // Close modal when clicking outside the card
        document.getElementById('legal-modal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('legal-modal')) {
                closeModal();
            }
        });

        // Initialize Custom Dropdowns
        document.querySelectorAll('.custom-dropdown').forEach(dropdown => {
            const trigger = dropdown.querySelector('.dropdown-trigger');
            const menu = dropdown.querySelector('.dropdown-menu');
            const options = dropdown.querySelectorAll('.dropdown-option');
            const selectId = dropdown.dataset.selectId;
            const hiddenSelect = document.getElementById(selectId);
            const valueText = dropdown.querySelector('.dropdown-value-text');
            const arrow = dropdown.querySelector('.fa-chevron-down');

            // Set initial selected value text in UI
            const initialSelectedOption = hiddenSelect.options[hiddenSelect.selectedIndex];
            if (initialSelectedOption) {
                valueText.textContent = initialSelectedOption.textContent;
            }

            // Open/Close
            trigger.addEventListener('click', (e) => {
                e.stopPropagation();
                // Close other dropdowns
                document.querySelectorAll('.custom-dropdown .dropdown-menu').forEach(otherMenu => {
                    if (otherMenu !== menu) {
                        otherMenu.classList.add('scale-95', 'opacity-0', 'pointer-events-none');
                        otherMenu.parentElement.querySelector('.fa-chevron-down').classList.remove('rotate-180');
                    }
                });
                menu.classList.toggle('scale-95');
                menu.classList.toggle('opacity-0');
                menu.classList.toggle('pointer-events-none');
                arrow.classList.toggle('rotate-180');
            });

            // Select Option
            options.forEach(option => {
                // Set initial active state based on hiddenSelect value
                if (option.dataset.value === hiddenSelect.value) {
                    option.classList.add('bg-brand-50', 'text-dark-900');
                    option.querySelector('.fa-check').classList.remove('opacity-0');
                }

                option.addEventListener('click', () => {
                    const value = option.dataset.value;
                    hiddenSelect.value = value;
                    valueText.textContent = option.querySelector('span').textContent;

                    // Trigger change event for select just in case
                    hiddenSelect.dispatchEvent(new Event('change'));

                    // Update UI active states
                    options.forEach(opt => {
                        opt.classList.remove('bg-brand-50', 'text-dark-900');
                        opt.querySelector('.fa-check').classList.add('opacity-0');
                    });
                    option.classList.add('bg-brand-50', 'text-dark-900');
                    option.querySelector('.fa-check').classList.remove('opacity-0');

                    // Close menu
                    menu.classList.add('scale-95', 'opacity-0', 'pointer-events-none');
                    arrow.classList.remove('rotate-180');
                });
            });
        });

        // Close on click outside
        document.addEventListener('click', () => {
            document.querySelectorAll('.custom-dropdown .dropdown-menu').forEach(menu => {
                menu.classList.add('scale-95', 'opacity-0', 'pointer-events-none');
                menu.parentElement.querySelector('.fa-chevron-down').classList.remove('rotate-180');
            });
        });

        // Initialize Flatpickr Premium Date Picker
        flatpickr("#sim-date", {
            locale: "id",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d/m/Y",
            minDate: "today",
            defaultDate: "today",
            disableMobile: "true"
        });
    </script>
</body>
</html>
