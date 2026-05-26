@extends('admin.layout')

@section('content')
<div class="space-y-8 animate-fade-in">
    
    <!-- Header Title -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold font-outfit text-dark-900 leading-tight">Ulasan & Feedback Supir</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau umpan balik, kepuasan, rating bintang, serta komentar penumpang terhadap supir armada.</p>
        </div>
        <div class="h-12 w-12 rounded-xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.36 1.236.588 1.81l-3.974 2.89a1 1 0 00-.364 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.89a1 1 0 00-1.176 0l-3.976 2.89c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.98 10.1c-.773-.574-.373-1.81.588-1.81h4.907a1 1 0 00.95-.69l1.519-4.674z" />
            </svg>
        </div>
    </div>

    <!-- Rating Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Average Rating -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Rata-rata Rating</div>
                <div class="flex items-baseline gap-1.5">
                    <div class="text-3xl font-extrabold font-outfit text-dark-900">{{ number_format((float) $summary['average_rating'], 1) }}</div>
                    <div class="text-xs text-gray-400 font-bold uppercase">/ 5.0</div>
                </div>
                <div class="flex items-center gap-1 mt-1 text-brand-500">
                    @php
                        $rounded = round((float)$summary['average_rating']);
                    @endphp
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-4 h-4 {{ $i <= $rounded ? 'fill-current' : 'text-gray-200 fill-none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.36 1.236.588 1.81l-3.974 2.89a1 1 0 00-.364 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.89a1 1 0 00-1.176 0l-3.976 2.89c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.98 10.1c-.773-.574-.373-1.81.588-1.81h4.907a1 1 0 00.95-.69l1.519-4.674z" />
                        </svg>
                    @endfor
                </div>
            </div>
            <div class="h-12 w-12 rounded-xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
            </div>
        </div>

        <!-- Total Reviews -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Ulasan</div>
                <div class="text-3xl font-extrabold font-outfit text-dark-900">{{ number_format($summary['review_count'], 0, ',', '.') }}</div>
                <div class="text-[10px] text-gray-500 font-medium">Umpan balik terkumpul</div>
            </div>
            <div class="h-12 w-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                </svg>
            </div>
        </div>

        <!-- Low Ratings -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Rating Rendah</div>
                <div class="text-3xl font-extrabold font-outfit text-dark-900">{{ number_format($summary['low_rating_count'], 0, ',', '.') }}</div>
                <div class="text-[10px] text-gray-500 font-medium">Ulasan bintang 1 atau 2</div>
            </div>
            <div class="h-12 w-12 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>

    </div>

    <!-- Reviews Table Panel -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-50">
            <h3 class="font-bold text-dark-900 font-outfit text-base">Seluruh Feedback & Komentar</h3>
            <p class="text-xs text-gray-400">Menampilkan seluruh ulasan perjalanan dari pelanggan.</p>
        </div>

        @if($reviews->isEmpty())
            <div class="flex flex-col items-center justify-center p-16 text-center space-y-3">
                <div class="h-16 w-16 rounded-full bg-gray-50 flex items-center justify-center text-gray-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674" />
                    </svg>
                </div>
                <div class="space-y-1">
                    <h4 class="text-sm font-semibold text-dark-900">Belum Ada Ulasan</h4>
                    <p class="text-xs text-gray-400 max-w-xs">Ulasan supir dari pelanggan akan muncul di sini setelah tiket perjalanan diselesaikan.</p>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">ID</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Booking ID</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Driver</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Rute</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Rating</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Komentar</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        @foreach($reviews as $review)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-gray-400">
                                    {{ $review->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-dark-900">
                                    <span class="text-gray-400">#</span>{{ $review->booking_id ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-semibold text-dark-900">
                                    {{ $review->customer?->user?->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-semibold text-brand-650">
                                    {{ $review->driver?->user?->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-700">
                                    <div class="flex items-center gap-1">
                                        <span>{{ $review->booking?->schedule?->route?->origin?->name ?? '-' }}</span>
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                        <span>{{ $review->booking?->schedule?->route?->destination?->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border bg-amber-50 text-amber-700 border-amber-200 text-xs font-bold">
                                        <svg class="w-3.5 h-3.5 fill-current text-brand-500" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.36 1.236.588 1.81l-3.974 2.89a1 1 0 00-.364 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.89a1 1 0 00-1.176 0l-3.976 2.89c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.98 10.1c-.773-.574-.373-1.81.588-1.81h4.907a1 1 0 00.95-.69l1.519-4.674z" />
                                        </svg>
                                        {{ $review->rating }}/5
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-600 max-w-xs truncate font-medium" title="{{ $review->comment }}">
                                    {{ $review->comment ?: '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500 font-medium text-xs">
                                    {{ optional($review->created_at)->format('d M Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
