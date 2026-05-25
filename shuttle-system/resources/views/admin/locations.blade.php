@extends('admin.layout')

@section('content')
    <h1>Master Lokasi</h1>

    <h3>Tambah Lokasi</h3>
    <form action="/admin/locations" method="POST">
        @csrf
        <input type="text" name="name" placeholder="Nama Lokasi" required>
        <input type="text" name="latitude" placeholder="Latitude">
        <input type="text" name="longitude" placeholder="Longitude">
        <button type="submit">Simpan</button>
    </form>

    <hr>

    <h3>Daftar Lokasi</h3>
    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Lat/Long</th>
            <th>Aksi</th>
        </tr>
        @foreach($locations as $loc)
        <tr>
            <td>{{ $loc->id }}</td>
            <td>{{ $loc->name }}</td>
            <td>{{ $loc->latitude }}, {{ $loc->longitude }}</td>
            <td>
                <a href="/admin/locations/{{ $loc->id }}/edit"><button type="button">Edit</button></a>
                <form action="/admin/locations/{{ $loc->id }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Hapus lokasi ini?')">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
@endsection
