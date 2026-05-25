@extends('admin.layout')

@section('content')
    <h1>Master Rute</h1>

    <h3>Tambah Rute</h3>
    <form action="/admin/routes" method="POST">
        @csrf
        <label>Asal:</label>
        <select name="origin_location_id" required>
            @foreach($locations as $loc)
                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
            @endforeach
        </select>
        
        <label>Tujuan:</label>
        <select name="destination_location_id" required>
            @foreach($locations as $loc)
                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
            @endforeach
        </select>

        <input type="number" step="0.1" name="distance_km" placeholder="Jarak (km)">
        <input type="number" name="price" placeholder="Harga (Rp)" required>
        <button type="submit">Simpan</button>
    </form>

    <hr>

    <h3>Daftar Rute</h3>
    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <tr>
            <th>ID</th>
            <th>Rute</th>
            <th>Jarak</th>
            <th>Harga</th>
            <th>Aksi</th>
        </tr>
        @foreach($routes as $route)
        <tr>
            <td>{{ $route->id }}</td>
            <td>{{ $route->origin->name }} ➔ {{ $route->destination->name }}</td>
            <td>{{ $route->distance_km }} km</td>
            <td>Rp {{ number_format($route->price, 0, ',', '.') }}</td>
            <td>
                <a href="/admin/routes/{{ $route->id }}/edit"><button type="button">Edit</button></a>
                <form action="/admin/routes/{{ $route->id }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Hapus rute ini?')">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
@endsection
