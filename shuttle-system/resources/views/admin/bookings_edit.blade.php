@extends('admin.layout')

@section('content')
    <h1>Ubah Status Transaksi</h1>

    <div style="background: #f9f9f9; padding: 15px; margin-bottom: 20px; border: 1px solid #ddd;">
        <h3>Detail Pemesanan #{{ $booking->id }}</h3>
        <p><strong>Waktu Pesan:</strong> {{ $booking->booking_time }}</p>
        <p><strong>Pelanggan:</strong> {{ $booking->customer->user->name ?? 'Data Terhapus' }}</p>
        <p><strong>Rute:</strong> {{ $booking->schedule->route->origin->name ?? '-' }} ➔ {{ $booking->schedule->route->destination->name ?? '-' }}</p>
        <p><strong>Keberangkatan:</strong> {{ $booking->schedule->departure_time ?? '-' }}</p>
        <p><strong>Total Kursi:</strong> {{ $booking->total_seat }} Kursi</p>
        <p><strong>Total Tagihan:</strong> Rp {{ number_format(($booking->total_seat * ($booking->schedule->route->price ?? 0)), 0, ',', '.') }}</p>
    </div>

    <form action="/admin/bookings/{{ $booking->id }}" method="POST">
        @csrf
        @method('PUT')
        
        <label><strong>Ubah Status:</strong></label>
        <select name="status" required style="padding: 5px; font-size: 16px;">
            <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>PENDING (Menunggu Pembayaran)</option>
            <option value="paid" {{ $booking->status == 'paid' ? 'selected' : '' }}>PAID (Lunas)</option>
            <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>CANCELLED (Batal)</option>
        </select>
        
        <br><br>
        <button type="submit" style="padding: 8px 15px; font-weight: bold;">Update Status</button>
        <a href="/admin/bookings"><button type="button" style="padding: 8px 15px;">Kembali</button></a>
    </form>

    @if($booking->status != 'cancelled')
    <p style="color: red; font-size: 14px; margin-top: 10px;">
        <em>Peringatan: Jika status diubah menjadi CANCELLED, maka {{ $booking->total_seat }} kursi yang dipesan akan dilepas secara otomatis agar tersedia kembali bagi pelanggan lain.</em>
    </p>
    @endif
@endsection
