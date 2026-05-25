@extends('admin.layout')

@section('content')
    <h1>Edit Jadwal</h1>

    <form action="/admin/schedules/{{ $schedule->id }}" method="POST">
        @csrf
        @method('PUT')
        <label>Rute:</label>
        <select name="route_id" required>
            @foreach($routes as $r)
                <option value="{{ $r->id }}" {{ $schedule->route_id == $r->id ? 'selected' : '' }}>{{ $r->origin->name }} ➔ {{ $r->destination->name }}</option>
            @endforeach
        </select>
        
        <label>Supir:</label>
        <select name="driver_id" id="driver_id" required>
            @foreach($drivers as $d)
                <option value="{{ $d->id }}" data-vehicle="{{ $d->vehicle->id ?? '' }}" {{ $schedule->driver_id == $d->id ? 'selected' : '' }}>{{ $d->user->name }} ({{ $d->license_number }})</option>
            @endforeach
        </select>

        <label>Kendaraan (Otomatis):</label>
        <select name="vehicle_id" id="vehicle_id" style="pointer-events: none; background-color: #f3f4f6; cursor: not-allowed;" required>
            @foreach($vehicles as $v)
                <option value="{{ $v->id }}" {{ $schedule->vehicle_id == $v->id ? 'selected' : '' }}>{{ $v->plate_number }} ({{ $v->vehicle_type }})</option>
            @endforeach
        </select>

        <label>Keberangkatan:</label>
        <input type="datetime-local" name="departure_time" value="{{ date('Y-m-d\TH:i', strtotime($schedule->departure_time)) }}" required>

        <label>Jam Tiba:</label>
        <input type="datetime-local" name="arrival_time" value="{{ $schedule->arrival_time ? date('Y-m-d\TH:i', strtotime($schedule->arrival_time)) : '' }}" required>

        <label>Kapasitas:</label>
        <input type="number" name="capacity" placeholder="Total Kursi" value="{{ $schedule->capacity }}" required>

        <label>Harga (Rp):</label>
        <input type="number" name="price" placeholder="Harga untuk rute/mobil ini" value="{{ $schedule->price ?: $schedule->route->price }}" required>

        <label>Status:</label>
        <select name="status" required>
            <option value="scheduled" {{ $schedule->status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
            <option value="on_the_way" {{ $schedule->status == 'on_the_way' ? 'selected' : '' }}>On the way</option>
            <option value="completed" {{ $schedule->status == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ $schedule->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>

        <button type="submit">Update</button>
        <a href="/admin/schedules"><button type="button">Batal</button></a>
    </form>

    <script>
        (function () {
            // Driver to Vehicle Sync Logic
            const driverSelect = document.getElementById('driver_id');
            const vehicleSelect = document.getElementById('vehicle_id');

            if (driverSelect && vehicleSelect) {
                driverSelect.addEventListener('change', function() {
                    const selectedOption = driverSelect.options[driverSelect.selectedIndex];
                    const vehicleId = selectedOption.getAttribute('data-vehicle');
                    if (vehicleId) {
                        vehicleSelect.value = vehicleId;
                    } else {
                        vehicleSelect.value = "";
                    }
                });
            }
        })();
    </script>
@endsection
