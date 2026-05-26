<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Shuttle System D-Voyager</title>
    
    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind Play CDN -->
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
                    }
                }
            }
        }
    </script>
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #1e1e1e;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 3px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #FBC02D;
        }
    </style>
</head>
<body class="bg-[#F9FAFB] text-dark-800 font-sans antialiased overflow-x-hidden min-h-screen flex flex-col md:flex-row">

    <!-- Mobile Header -->
    <header class="md:hidden w-full bg-dark-900 border-b border-dark-800 text-white flex items-center justify-between px-5 py-4 z-50">
        <div class="flex items-center gap-3">
            <img src="{{ asset('assets/Logo_Dvoyager.png') }}" alt="Logo" class="h-8 w-auto object-contain" onerror="this.src='https://placehold.co/100x100?text=DV'">
            <span class="font-outfit font-bold text-lg text-brand-500 tracking-wide">D-Voyager</span>
        </div>
        <button id="mobile-menu-btn" class="text-white hover:text-brand-500 focus:outline-none transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>
    </header>

    <!-- Sidebar Container -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out md:static md:flex flex-col w-64 min-h-screen bg-dark-900 text-white z-40 shadow-xl flex-shrink-0 border-r border-dark-850">
        
        <!-- Sidebar Branding -->
        <div class="px-6 py-6 border-b border-dark-850 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/Logo_Dvoyager.png') }}" alt="Logo" class="h-10 w-auto object-contain" onerror="this.src='https://placehold.co/100x100?text=DV'">
                <div class="flex flex-col">
                    <span class="font-outfit font-extrabold text-lg text-brand-500 leading-none tracking-wide">D-VOYAGER</span>
                    <span class="text-[10px] text-gray-400 mt-1 uppercase tracking-widest font-semibold">Admin Panel</span>
                </div>
            </div>
            <button id="close-sidebar-btn" class="md:hidden text-gray-400 hover:text-brand-500 focus:outline-none transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-grow py-6 px-4 space-y-1.5 overflow-y-auto custom-scrollbar">
            
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.dashboard*') ? 'bg-[#2D2D2D] text-brand-500 border-l-4 border-brand-500 shadow-inner font-semibold' : 'text-gray-400 hover:text-white hover:bg-dark-850' }}">
                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('admin.dashboard*') ? 'text-brand-500' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                </svg>
                Dashboard
            </a>

            <!-- Divider -->
            <div class="px-4 py-2 text-[10px] uppercase font-semibold text-gray-500 tracking-widest">
                Data Master
            </div>

            <!-- Locations -->
            <a href="{{ route('admin.locations') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.locations*') ? 'bg-[#2D2D2D] text-brand-500 border-l-4 border-brand-500 shadow-inner font-semibold' : 'text-gray-400 hover:text-white hover:bg-dark-850' }}">
                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('admin.locations*') ? 'text-brand-500' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Locations
            </a>

            <!-- Vehicles -->
            <a href="{{ route('admin.vehicles') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.vehicles*') ? 'bg-[#2D2D2D] text-brand-500 border-l-4 border-brand-500 shadow-inner font-semibold' : 'text-gray-400 hover:text-white hover:bg-dark-850' }}">
                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('admin.vehicles*') ? 'text-brand-500' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16H9m8-3v-3a2 2 0 00-2-2H9a2 2 0 00-2 2v3m12 0h1a1 1 0 011 1v2a1 1 0 01-1 1h-1m-12 0H4a1 1 0 01-1-1v-2a1 1 0 011-1h1m10-5h4l-2-3h-2v3zM7 9h3V6H8a1 1 0 00-1 1v2z" />
                </svg>
                Vehicles
            </a>

            <!-- Routes -->
            <a href="{{ route('admin.routes') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.routes*') ? 'bg-[#2D2D2D] text-brand-500 border-l-4 border-brand-500 shadow-inner font-semibold' : 'text-gray-400 hover:text-white hover:bg-dark-850' }}">
                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('admin.routes*') ? 'text-brand-500' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
                Routes
            </a>

            <!-- Schedules -->
            <a href="{{ route('admin.schedules') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.schedules*') ? 'bg-[#2D2D2D] text-brand-500 border-l-4 border-brand-500 shadow-inner font-semibold' : 'text-gray-400 hover:text-white hover:bg-dark-850' }}">
                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('admin.schedules*') ? 'text-brand-500' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Schedules
            </a>

            <!-- Drivers -->
            <a href="{{ route('admin.drivers') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.drivers*') ? 'bg-[#2D2D2D] text-brand-500 border-l-4 border-brand-500 shadow-inner font-semibold' : 'text-gray-400 hover:text-white hover:bg-dark-850' }}">
                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('admin.drivers*') ? 'text-brand-500' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Drivers
            </a>

            <!-- Divider -->
            <div class="px-4 py-2 text-[10px] uppercase font-semibold text-gray-500 tracking-widest">
                Pelanggan & Booking
            </div>

            <!-- Customers -->
            <a href="{{ route('admin.customers') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.customers*') ? 'bg-[#2D2D2D] text-brand-500 border-l-4 border-brand-500 shadow-inner font-semibold' : 'text-gray-400 hover:text-white hover:bg-dark-850' }}">
                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('admin.customers*') ? 'text-brand-500' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Customers
            </a>

            <!-- Bookings -->
            <a href="{{ route('admin.bookings') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.bookings*') ? 'bg-[#2D2D2D] text-brand-500 border-l-4 border-brand-500 shadow-inner font-semibold' : 'text-gray-400 hover:text-white hover:bg-dark-850' }}">
                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('admin.bookings*') ? 'text-brand-500' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                Bookings
            </a>

            <!-- Reviews -->
            <a href="{{ route('admin.reviews') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.reviews*') ? 'bg-[#2D2D2D] text-brand-500 border-l-4 border-brand-500 shadow-inner font-semibold' : 'text-gray-400 hover:text-white hover:bg-dark-850' }}">
                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('admin.reviews*') ? 'text-brand-500' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.36 1.236.588 1.81l-3.974 2.89a1 1 0 00-.364 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.89a1 1 0 00-1.176 0l-3.976 2.89c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.98 10.1c-.773-.574-.373-1.81.588-1.81h4.907a1 1 0 00.95-.69l1.519-4.674z" />
                </svg>
                Reviews
            </a>

            <!-- Live Tracking -->
            <a href="{{ route('admin.tracking') }}" class="group flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('admin.tracking*') ? 'bg-[#2D2D2D] text-brand-500 border-l-4 border-brand-500 shadow-inner font-semibold' : 'text-gray-400 hover:text-white hover:bg-dark-850' }}">
                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('admin.tracking*') ? 'text-brand-500' : 'text-gray-400 group-hover:text-white' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
                Live Tracking (Maps)
            </a>

        </nav>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-dark-850 bg-dark-950 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-full bg-[#2D2D2D] flex items-center justify-center text-xs font-semibold text-brand-500 border border-dark-800">
                    AD
                </div>
                <div class="flex flex-col">
                    <span class="text-xs font-semibold text-white">Administrator</span>
                    <span class="text-[10px] text-gray-500">Shuttle D-Voyager</span>
                </div>
            </div>
            
            <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar?')">
                @csrf
                <button type="submit" class="p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-500/10 transition-all focus:outline-none" title="Log Out">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
    </aside>

    <!-- Content Canvas -->
    <div class="flex-grow flex flex-col min-w-0 min-h-screen">
        
        <!-- Header -->
        <header class="hidden md:flex bg-white border-b border-gray-100 h-16 items-center justify-between px-8 z-30 shadow-sm">
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Halaman Admin</span>
                <span class="text-gray-300">/</span>
                <span class="text-sm font-semibold text-dark-900 font-outfit" id="header-page-title">Dashboard</span>
            </div>

            <div class="flex items-center gap-6">
                <!-- Notifications or status -->
                <div class="flex items-center gap-2 bg-[#FBC02D]/10 px-3 py-1.5 rounded-full border border-[#FBC02D]/20">
                    <span class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span>
                    <span class="text-xs font-semibold text-brand-600">Sistem Berjalan</span>
                </div>

                <div class="h-8 w-px bg-gray-200"></div>

                <div class="flex items-center gap-3">
                    <div class="flex flex-col text-right">
                        <span class="text-xs font-bold text-dark-900">Administrator Utama</span>
                        <span class="text-[10px] text-gray-400">admin@shuttle.com</span>
                    </div>
                    <div class="h-9 w-9 rounded-full bg-dark-900 border border-brand-500 flex items-center justify-center text-brand-500 font-bold font-outfit text-sm">
                        A
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Wrapper -->
        <main class="flex-grow p-5 md:p-8">
            
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-550/10 border border-emerald-500/20 text-emerald-700 flex items-center gap-3 shadow-sm bg-green-50">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-100 text-rose-700 flex items-center gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-100 text-rose-700 shadow-sm">
                    <div class="flex items-center gap-3 mb-2">
                        <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="text-sm font-bold">Terjadi Kesalahan Pengisian:</span>
                    </div>
                    <ul class="list-disc pl-8 text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </main>
    </div>

    <!-- Mobile Overlay and Menu Script -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-30 hidden transition-opacity duration-300 md:hidden"></div>

    <script>
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const closeSidebarBtn = document.getElementById('close-sidebar-btn');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const headerPageTitle = document.getElementById('header-page-title');

        // Dynamic page title based on active sidebar item
        const activeItem = document.querySelector('aside nav a.bg-\\[\\#2D2D2D\\]');
        if (activeItem && headerPageTitle) {
            headerPageTitle.textContent = activeItem.textContent.trim();
        }

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
        }

        if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', toggleSidebar);
        if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', toggleSidebar);
        if (sidebarOverlay) sidebarOverlay.addEventListener('click', toggleSidebar);
    </script>
</body>
</html>
