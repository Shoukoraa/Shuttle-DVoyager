@extends('admin.layout')

@section('content')
    <h1>Edit Rute</h1>

    <form action="/admin/routes/{{ $route->id }}" method="POST">
        @csrf
        @method('PUT')
        <label>Asal:</label>
        <select name="origin_location_id" required>
            @foreach($locations as $loc)
                <option value="{{ $loc->id }}" {{ $route->origin_location_id == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
            @endforeach
        </select>
        
        <label>Tujuan:</label>
        <select name="destination_location_id" required>
            @foreach($locations as $loc)
                <option value="{{ $loc->id }}" {{ $route->destination_location_id == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
            @endforeach
        </select>

        <input type="number" step="0.1" name="distance_km" placeholder="Jarak (km)" value="{{ $route->distance_km }}">
        <input type="number" name="price" placeholder="Harga (Rp)" value="{{ $route->price }}" required>
        <button type="submit">Update</button>
        <a href="/admin/routes"><button type="button">Batal</button></a>
    </form>
@endsection
