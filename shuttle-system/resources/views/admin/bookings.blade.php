@extends('admin.layout')

@section('content')
    <h1>Laporan Booking (Pesanan)</h1>

    <p>Daftar seluruh transaksi pemesanan tiket shuttle.</p>

    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <tr>
            <th>ID Booking</th>
            <th>Waktu Pesan</th>
            <th>Pelanggan</th>
            <th>Rute</th>
            <th>Total Tagihan</th>
            <th>Total Kursi</th>
            <th>Status Transaksi</th>
            <th>Aksi</th>
        </tr>
        @foreach($bookings as $b)
        <tr>
            <td>#{{ $b->id }}</td>
            <td>{{ $b->booking_time }}</td>
            <td>{{ $b->customer->user->name ?? 'User Hapus' }}</td>
            <td>{{ $b->schedule->route->origin->name ?? '-' }} ➔ {{ $b->schedule->route->destination->name ?? '-' }}</td>
            <td>Rp {{ number_format($b->total_price ?? ($b->total_seat * ($b->schedule->route->price ?? 0)), 0, ',', '.') }}</td>
            <td>{{ $b->total_seat }} Kursi</td>
            <td><strong>{{ strtoupper($b->status) }}</strong></td>
            <td>
                <a href="/admin/bookings/{{ $b->id }}/edit"><button type="button">Ubah Status</button></a>
            </td>
        </tr>
        @endforeach
    </table>
@endsection
