@extends('admin.layout')

@section('content')
    <h1>Edit Lokasi</h1>

    <form action="/admin/locations/{{ $location->id }}" method="POST">
        @csrf
        @method('PUT')
        <input type="text" name="name" placeholder="Nama Lokasi" value="{{ $location->name }}" required>
        <input type="text" name="latitude" placeholder="Latitude" value="{{ $location->latitude }}">
        <input type="text" name="longitude" placeholder="Longitude" value="{{ $location->longitude }}">
        <button type="submit">Update</button>
        <a href="/admin/locations"><button type="button">Batal</button></a>
    </form>
@endsection
