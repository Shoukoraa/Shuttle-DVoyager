@extends('admin.layout')

@section('content')
<div class="space-y-8 animate-fade-in">
    
    <!-- Top Action Banner -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <h1 class="text-2xl font-extrabold font-outfit text-dark-900 leading-tight">Dashboard Bulanan</h1>
            <p class="text-sm text-gray-500 mt-1">Ringkasan laporan operasional dan pendapatan untuk <span class="font-semibold text-brand-600">{{ $selected_label }}</span>.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.reports.export.excel', ['year' => $selected_year, 'month' => $selected_month]) }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-sm font-semibold rounded-xl border border-emerald-200/50 transition-all shadow-sm gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Ekspor Excel
            </a>
            
            <a href="{{ route('admin.reports.export.pdf', ['year' => $selected_year, 'month' => $selected_month]) }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-sm font-semibold rounded-xl border border-rose-200/50 transition-all shadow-sm gap-2">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Ekspor PDF
            </a>
        </div>
    </div>

    <!-- Filter Form Panel -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <form method="GET" action="{{ route('admin.dashboard') }}" class="flex flex-col md:flex-row md:items-end gap-4">
            
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Select Month -->
                <div class="flex flex-col gap-1.5">
                    <label for="month" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Bulan Laporan</label>
                    <div class="relative">
                        <select name="month" id="month" class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none appearance-none transition-all cursor-pointer font-medium">
                            @foreach($available_months as $monthOption)
                                <option value="{{ $monthOption['value'] }}" @selected((int) $selected_month === (int) $monthOption['value'])>
                                    {{ $monthOption['label'] }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Select Year -->
                <div class="flex flex-col gap-1.5">
                    <label for="year" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tahun Laporan</label>
                    <div class="relative">
                        <select name="year" id="year" class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none appearance-none transition-all cursor-pointer font-medium">
                            @foreach($available_years as $yearOption)
                                <option value="{{ $yearOption }}" @selected((int) $selected_year === (int) $yearOption)>
                                    {{ $yearOption }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-3">
                <button type="submit" class="flex-1 md:flex-initial inline-flex items-center justify-center px-6 py-2.5 bg-dark-900 hover:bg-dark-850 text-white font-semibold text-sm rounded-xl border border-dark-900 shadow-sm transition-all gap-2">
                    <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Tampilkan
                </button>
                
                <a href="{{ route('admin.dashboard') }}" class="flex-1 md:flex-initial inline-flex items-center justify-center px-6 py-2.5 bg-white hover:bg-gray-50 text-dark-900 font-semibold text-sm rounded-xl border border-gray-200 shadow-sm transition-all">
                    Bulan Ini
                </a>
            </div>

        </form>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Card 1: Bookings -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between">
            <div class="space-y-1">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Booking</div>
                <div class="text-3xl font-extrabold font-outfit text-dark-900">{{ number_format($monthly_booking_count, 0, ',', '.') }}</div>
                <div class="text-[10px] text-gray-500 font-medium">Bulan {{ $selected_label }}</div>
            </div>
            <div class="h-12 w-12 rounded-xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
            </div>
        </div>

        <!-- Card 2: Revenue -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between">
            <div class="space-y-1">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Pendapatan</div>
                <div class="text-2xl font-extrabold font-outfit text-dark-900">Rp {{ number_format($monthly_revenue, 0, ',', '.') }}</div>
                <div class="text-[10px] text-gray-500 font-medium">Bulan {{ $selected_label }}</div>
            </div>
            <div class="h-12 w-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Card 3: Schedules Monthly -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between">
            <div class="space-y-1">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Jadwal Bulan Ini</div>
                <div class="text-3xl font-extrabold font-outfit text-dark-900">{{ number_format($monthly_schedules, 0, ',', '.') }}</div>
                <div class="text-[10px] text-gray-500 font-medium">Bulan {{ $selected_label }}</div>
            </div>
            <div class="h-12 w-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>

        <!-- Card 4: Schedules Today -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between">
            <div class="space-y-1">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Jadwal Hari Ini</div>
                <div class="text-3xl font-extrabold font-outfit text-dark-900">{{ number_format($schedules_today, 0, ',', '.') }}</div>
                <div class="text-[10px] text-gray-500 font-medium">Hari ini, {{ date('d M Y') }}</div>
            </div>
            <div class="h-12 w-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

    </div>

    <!-- Booking Table Panel -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-brand-50 text-brand-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold font-outfit text-dark-900">Booking Bulan Terpilih</h2>
                    <p class="text-xs text-gray-400">Menampilkan hingga 10 transaksi terbaru pada periode ini.</p>
                </div>
            </div>
        </div>

        @if($bookings->isEmpty())
            <div class="flex flex-col items-center justify-center px-6 py-12 text-center space-y-3">
                <div class="h-16 w-16 rounded-full bg-gray-50 flex items-center justify-center text-gray-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
                <div class="space-y-1">
                    <h3 class="text-sm font-semibold text-dark-900">Tidak Ada Data Booking</h3>
                    <p class="text-xs text-gray-400 max-w-sm">Belum ada pemesanan tiket terdaftar pada periode bulan dan tahun yang Anda pilih.</p>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Pelanggan</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Rute</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Kursi</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        @foreach($bookings->take(10) as $booking)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-600">
                                    {{ \Carbon\Carbon::parse($booking->booking_time)->translatedFormat('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-dark-900">{{ data_get($booking, 'customer.user.name', 'Pelanggan dihapus') }}</span>
                                        <span class="text-xs text-gray-400">{{ data_get($booking, 'customer.user.email', '') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-700">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-dark-900">{{ data_get($booking, 'schedule.route.origin.name', '-') }}</span>
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                        </svg>
                                        <span class="font-semibold text-brand-600">{{ data_get($booking, 'schedule.route.destination.name', '-') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-dark-900 whitespace-nowrap">
                                    {{ $booking->total_seat }}
                                </td>
                                <td class="px-6 py-4 font-bold text-dark-900 whitespace-nowrap">
                                    Rp {{ number_format((int) ($booking->total_price ?? 0), 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @php
                                        $status = strtoupper($booking->status);
                                        $badgeClass = 'bg-gray-50 text-gray-600 border-gray-200';
                                        if (str_contains($status, 'COMPLETED') || str_contains($status, 'SUCCESS') || str_contains($status, 'SELESAI')) {
                                            $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                        } elseif (str_contains($status, 'PAID') || str_contains($status, 'BAYAR')) {
                                            $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                        } elseif (str_contains($status, 'PENDING') || str_contains($status, 'WAITING')) {
                                            $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                        } elseif (str_contains($status, 'CANCEL') || str_contains($status, 'BATAL') || str_contains($status, 'EXPIRED')) {
                                            $badgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-lg border {{ $badgeClass }} uppercase tracking-wider">
                                        {{ $status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Archive Panel -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-lg bg-indigo-50 text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold font-outfit text-dark-900">Arsip Laporan Bulanan</h2>
                    <p class="text-xs text-gray-400">Ringkasan riwayat performa operasional dari bulan-bulan sebelumnya.</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Total Booking</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Total Pendapatan</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Total Jadwal</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @foreach($monthly_archives as $archive)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-dark-900">
                                {{ $archive['label'] }}
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap font-semibold text-gray-700">
                                {{ number_format($archive['booking_count'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-emerald-600">
                                Rp {{ number_format($archive['revenue'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap font-semibold text-gray-700">
                                {{ number_format($archive['schedule_count'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <a href="{{ route('admin.dashboard', ['year' => $archive['year'], 'month' => $archive['month']]) }}" class="inline-flex items-center justify-center px-4 py-1.5 bg-brand-500 hover:bg-brand-600 text-dark-900 text-xs font-bold rounded-lg transition-colors gap-1.5 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Lihat Laporan
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
