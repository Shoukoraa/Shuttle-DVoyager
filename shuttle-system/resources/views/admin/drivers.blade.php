@extends('admin.layout')

@section('content')
    <h1>Daftar Supir</h1>

    <h3>Tambah Supir Baru</h3>
    <form action="/admin/drivers" method="POST">
        @csrf
        <input type="text" name="name" placeholder="Nama Lengkap" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="tel" name="phone" placeholder="Nomor HP" optional>
        <input type="password" name="password" placeholder="Password" required minlength="6">
        <input type="text" name="license_number" placeholder="Nomor SIM" required>
        <label for="vehicle_id">Pilih Kendaraan (Opsional)</label>
        <select name="vehicle_id" id="vehicle_id">
            <option value="">-- Belum Ada Kendaraan --</option>
            @foreach($vehicles ?? [] as $vehicle)
                <option value="{{ $vehicle->id }}" {{ ($vehicle->driver_id === null) ? '' : 'disabled' }}>
                    {{ $vehicle->plate_number }} - {{ $vehicle->vehicle_type }}
                </option>
            @endforeach
        </select>
        <button type="submit">Simpan Supir</button>
    </form>

    <hr>

    <h3>Data Supir</h3>
    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Nomor SIM</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        @foreach($drivers as $d)
        <tr>
            <td>{{ $d->id }}</td>
            <td>{{ $d->user->name ?? 'User Terhapus' }}</td>
            <td>{{ $d->user->email ?? '-' }}</td>
            <td>{{ $d->license_number }}</td>
            <td>{{ $d->status }}</td>
            <td>
                <a href="/admin/drivers/{{ $d->id }}/edit"><button type="button">Edit</button></a>
                <form action="/admin/drivers/{{ $d->id }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Pecat supir ini dan hapus akunnya secara permanen?')">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
@endsection
