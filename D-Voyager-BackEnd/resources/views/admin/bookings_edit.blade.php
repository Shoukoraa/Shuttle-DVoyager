@extends('admin.layout')

@section('content')
<div class="max-w-xl mx-auto space-y-8 animate-fade-in">
    
    <!-- Title Section -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold font-outfit text-dark-900 leading-tight">Ubah Status Transaksi</h1>
            <p class="text-sm text-gray-500 mt-1">Verifikasi pembayaran pelanggan atau batalkan pesanan tiket shuttle.</p>
        </div>
        <div class="h-12 w-12 rounded-xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
            </svg>
        </div>
    </div>

    <!-- Booking Detail Card -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-4">
        <h3 class="font-bold text-dark-900 font-outfit text-base border-b border-gray-50 pb-3 flex items-center gap-2">
            <span class="h-6 w-1.5 rounded bg-brand-500"></span>
            Detail Pemesanan #{{ $booking->id }}
        </h3>
        
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div class="space-y-1">
                <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Waktu Pesan</span>
                <span class="block font-semibold text-dark-900">{{ \Carbon\Carbon::parse($booking->booking_time)->translatedFormat('d M Y, H:i') }}</span>
            </div>

            <div class="space-y-1">
                <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Pelanggan</span>
                <span class="block font-semibold text-dark-900">{{ $booking->customer->user->name ?? 'Data Terhapus' }}</span>
            </div>

            <div class="space-y-1 col-span-2">
                <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Rute Perjalanan</span>
                <span class="block font-semibold text-dark-900 flex items-center gap-1.5 mt-0.5">
                    <span>{{ $booking->schedule->route->origin->name ?? '-' }}</span>
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-brand-600">{{ $booking->schedule->route->destination->name ?? '-' }}</span>
                </span>
            </div>

            <div class="space-y-1">
                <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Keberangkatan</span>
                <span class="block font-semibold text-dark-900">{{ \Carbon\Carbon::parse($booking->schedule->departure_time)->translatedFormat('d M Y, H:i') }}</span>
            </div>

            <div class="space-y-1">
                <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Kuantitas Kursi</span>
                <span class="block font-semibold text-dark-900">{{ $booking->total_seat }} Kursi</span>
            </div>

            <div class="space-y-1 col-span-2">
                <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Total Tagihan</span>
                <span class="block text-lg font-extrabold text-emerald-600">Rp {{ number_format(($booking->total_seat * ($booking->schedule->route->price ?? 0)), 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Edit Form Card -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-5">
        <form action="/admin/bookings/{{ $booking->id }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="flex flex-col gap-1.5">
                <label for="status" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Ubah Status Transaksi</label>
                <div class="relative">
                    <select name="status" id="status" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none appearance-none cursor-pointer transition-all font-medium">
                        <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>PENDING (Menunggu Pembayaran)</option>
                        <option value="paid" {{ $booking->status == 'paid' ? 'selected' : '' }}>PAID (Lunas)</option>
                        <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>CANCELLED (Batal)</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-6 py-2.5 bg-dark-900 hover:bg-dark-850 text-white font-semibold text-sm rounded-xl border border-dark-900 shadow-sm transition-all gap-2">
                    <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update Status
                </button>
                
                <a href="/admin/bookings" class="inline-flex items-center justify-center px-6 py-2.5 bg-white hover:bg-gray-50 text-dark-900 font-semibold text-sm rounded-xl border border-gray-200 shadow-sm transition-all">
                    Kembali
                </a>
            </div>

        </form>

        @if($booking->status != 'cancelled')
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-100 text-rose-700 flex gap-3 text-xs font-medium mt-4">
                <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>
                    <strong>Peringatan Keamanan:</strong> Jika status transaksi diubah menjadi <strong>CANCELLED</strong>, maka {{ $booking->total_seat }} nomor kursi yang dipesan sebelumnya akan dilepas secara otomatis agar dapat dipesan kembali oleh pelanggan lain.
                </span>
            </div>
        @endif
    </div>

</div>
@endsection
