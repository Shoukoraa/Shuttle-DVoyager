@extends('admin.layout')

@section('content')
    <h1>Edit Kendaraan</h1>

    <form action="/admin/vehicles/{{ $vehicle->id }}" method="POST">
        @csrf
        @method('PUT')
        <input type="text" name="plate_number" placeholder="Plat Nomor" value="{{ $vehicle->plate_number }}" required>
        <input type="text" name="vehicle_type" placeholder="Tipe (Toyota Hiace, dll)" value="{{ $vehicle->vehicle_type }}" required>
        <select name="vehicle_category" required>
            <option value="family_car" {{ $vehicle->vehicle_category == 'family_car' ? 'selected' : '' }}>Mobil Keluarga</option>
            <option value="mini_bus" {{ $vehicle->vehicle_category == 'mini_bus' ? 'selected' : '' }}>Mini Bus</option>
            <option value="bus" {{ $vehicle->vehicle_category == 'bus' ? 'selected' : '' }}>BUS</option>
        </select>
        <input type="number" name="capacity" placeholder="Kapasitas" value="{{ $vehicle->capacity }}" required>
        <select name="status" required>
            <option value="active" {{ $vehicle->status == 'active' ? 'selected' : '' }}>Active</option>
            <option value="maintenance" {{ $vehicle->status == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            <option value="inactive" {{ $vehicle->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <button type="submit">Update</button>
        <a href="/admin/vehicles"><button type="button">Batal</button></a>
    </form>
@endsection
