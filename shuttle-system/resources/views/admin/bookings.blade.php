@extends('admin.layout')

@section('content')
<div class="space-y-8 animate-fade-in">
    
    <!-- Header Title -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold font-outfit text-dark-900 leading-tight">Laporan Booking (Pesanan)</h1>
            <p class="text-sm text-gray-500 mt-1">Daftar seluruh transaksi pemesanan tiket shuttle, verifikasi pembayaran, dan detail kursi.</p>
        </div>
        <div class="h-12 w-12 rounded-xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
        </div>
    </div>

    <!-- Bookings Ledger List Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-dark-900 font-outfit text-base">Seluruh Pemesanan</h3>
                <p class="text-xs text-gray-400">Total terdaftar: {{ $bookings->count() }} pesanan tiket.</p>
            </div>
        </div>

        @if($bookings->isEmpty())
            <div class="flex flex-col items-center justify-center p-16 text-center space-y-3">
                <div class="h-16 w-16 rounded-full bg-gray-50 flex items-center justify-center text-gray-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2" />
                    </svg>
                </div>
                <div class="space-y-1">
                    <h4 class="text-sm font-semibold text-dark-900">Belum Ada Booking</h4>
                    <p class="text-xs text-gray-400 max-w-xs">Data transaksi pemesanan tiket shuttle akan tercatat otomatis ketika pelanggan memesan tiket.</p>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">ID Booking</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Waktu Pesan</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Pelanggan</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Rute Perjalanan</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Jumlah Kursi</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Total Tagihan</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Status</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        @foreach($bookings as $b)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-dark-900">
                                    <span class="text-gray-400">#</span>{{ $b->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-600">
                                    {{ \Carbon\Carbon::parse($b->booking_time)->translatedFormat('d M Y, H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-semibold text-dark-900">
                                    {{ $b->customer->user->name ?? 'User Hapus' }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-dark-900 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <span>{{ $b->schedule->route->origin->name ?? '-' }}</span>
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                        </svg>
                                        <span class="text-brand-600">{{ $b->schedule->route->destination->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-dark-900 whitespace-nowrap">
                                    {{ $b->total_seat }} Kursi
                                </td>
                                <td class="px-6 py-4 font-bold text-emerald-600 whitespace-nowrap">
                                    Rp {{ number_format($b->total_price ?? ($b->total_seat * ($b->schedule->route->price ?? 0)), 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    @php
                                        $status = strtoupper($b->status);
                                        $badgeClass = 'bg-gray-50 text-gray-600 border-gray-200';
                                        if (str_contains($status, 'COMPLETED') || str_contains($status, 'SUCCESS') || str_contains($status, 'SELESAI') || str_contains($status, 'PAID')) {
                                            $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                        } elseif (str_contains($status, 'PENDING') || str_contains($status, 'WAITING')) {
                                            $badgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                        } elseif (str_contains($status, 'CANCEL') || str_contains($status, 'BATAL') || str_contains($status, 'EXPIRED')) {
                                            $badgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-lg border {{ $badgeClass }} uppercase tracking-wider">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <a href="/admin/bookings/{{ $b->id }}/edit" class="inline-flex items-center justify-center px-3 py-1.5 bg-brand-500 hover:bg-brand-600 text-dark-900 text-xs font-bold rounded-lg transition-colors gap-1 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                        Ubah Status
                                    </a>
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
