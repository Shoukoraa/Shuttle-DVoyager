@extends('admin.layout')

@section('content')
    <h1>Edit Supir</h1>

    <form action="/admin/drivers/{{ $driver->id }}" method="POST">
        @csrf
        @method('PUT')
        <input type="text" name="name" placeholder="Nama Lengkap" value="{{ $driver->user->name ?? '' }}" required>
        <input type="email" name="email" placeholder="Email" value="{{ $driver->user->email ?? '' }}" required>
        <input type="tel" name="phone" placeholder="Nomor HP" value="{{ $driver->user->phone ?? '' }}">
        <input type="password" name="password" placeholder="Password (Kosongkan jika tidak ingin mengubah)" minlength="6">
        <input type="text" name="license_number" placeholder="Nomor SIM" value="{{ $driver->license_number }}" required>
        
        <label for="vehicle_id">Kendaraan Saat Ini</label>
        <select name="vehicle_id" id="vehicle_id">
            <option value="">-- Belum Ada Kendaraan --</option>
            @foreach($vehicles ?? [] as $vehicle)
                @php
                    $isCurrentVehicle = $driver->vehicle && $driver->vehicle->id === $vehicle->id;
                    $isOtherDriverVehicle = $vehicle->driver_id !== null && $vehicle->driver_id !== $driver->id;
                @endphp
                <option value="{{ $vehicle->id }}" 
                    {{ $isCurrentVehicle ? 'selected' : '' }}
                    {{ $isOtherDriverVehicle ? 'disabled' : '' }}>
                    {{ $vehicle->plate_number }} - {{ $vehicle->vehicle_type }}
                    {{ $isCurrentVehicle ? '(Saat ini)' : '' }}
                </option>
            @endforeach
        </select>
        
        <select name="status" required>
            <option value="active" {{ $driver->status == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ $driver->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        <button type="submit">Update</button>
        <a href="/admin/drivers"><button type="button">Batal</button></a>
    </form>
@endsection
