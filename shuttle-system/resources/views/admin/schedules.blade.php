@extends('admin.layout')

@section('content')
    <h1>Master Jadwal</h1>

    <h3>Tambah Jadwal</h3>
    <form action="/admin/schedules" method="POST">
        @csrf
        <label>Rute:</label>
        <select name="route_id" required>
            @foreach($routes as $r)
                <option value="{{ $r->id }}">{{ $r->origin->name }} ➔ {{ $r->destination->name }}</option>
            @endforeach
        </select>
        
        <label>Supir:</label>
        <select name="driver_id" id="driver_id" required>
            <option value="" disabled selected>-- Pilih Supir --</option>
            @foreach($drivers as $d)
                <option value="{{ $d->id }}" data-vehicle="{{ $d->vehicle->id ?? '' }}">{{ $d->user->name }} ({{ $d->license_number }})</option>
            @endforeach
        </select>

        <label>Kendaraan (Otomatis):</label>
        <select name="vehicle_id" id="vehicle_id" style="pointer-events: none; background-color: #f3f4f6; cursor: not-allowed;" required>
            <option value="" disabled selected>-- Kendaraan Supir --</option>
            @foreach($vehicles as $v)
                <option value="{{ $v->id }}">{{ $v->plate_number }} ({{ $v->vehicle_type }})</option>
            @endforeach
        </select>

        <label>Keberangkatan:</label>
        <input type="datetime-local" name="departure_time" required>

        <label>Jam Tiba:</label>
        <input type="datetime-local" name="arrival_time" required>

        <label>Kapasitas:</label>
        <input type="number" name="capacity" placeholder="Total Kursi" required>

        <label>Harga (Rp):</label>
        <input type="number" name="price" placeholder="Harga untuk rute/mobil ini" required>

        <button type="submit">Simpan</button>
    </form>

    <hr>

    <h3>Daftar Jadwal</h3>
    <div style="margin-bottom: 10px; display: flex; gap: 8px; align-items: center;">
        <form id="bulkDeleteForm" action="{{ route('admin.schedules.bulk-delete') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" onclick="return confirm('Hapus semua jadwal yang dipilih?')">Hapus Terpilih</button>
        </form>

        <form action="{{ route('admin.schedules.delete-all') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" onclick="return confirm('Yakin ingin menghapus SEMUA jadwal?')">Hapus Semua</button>
        </form>

        <small id="selectedCount" style="color:#666;">0 dipilih</small>
    </div>

    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <tr>
            <th><input type="checkbox" id="selectAllSchedules"></th>
            <th>ID</th>
            <th>Rute</th>
            <th>Keberangkatan</th>
            <th>Tiba</th>
            <th>Armada & Supir</th>
            <th>Harga</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        @foreach($schedules as $s)
        <tr>
            <td>
                <input form="bulkDeleteForm" type="checkbox" name="schedule_ids[]" value="{{ $s->id }}" class="schedule-checkbox">
            </td>
            <td>{{ $s->id }}</td>
            <td>{{ $s->route->origin->name }} ➔ {{ $s->route->destination->name }}</td>
            <td>{{ $s->departure_time }}</td>
            <td>{{ $s->arrival_time ?? '-' }}</td>
            <td>{{ $s->vehicle->plate_number }} | {{ $s->driver->user->name }}</td>
            <td>Rp {{ number_format($s->price ?: $s->route->price, 0, ',', '.') }}</td>
            <td>{{ $s->status }}</td>
            <td>
                <a href="/admin/schedules/{{ $s->id }}/edit"><button type="button">Edit</button></a>
                <form action="/admin/schedules/{{ $s->id }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Hapus jadwal ini?')">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>

    <script>
        (function () {
            const selectAll = document.getElementById('selectAllSchedules');
            const checkboxes = Array.from(document.querySelectorAll('.schedule-checkbox'));
            const selectedCount = document.getElementById('selectedCount');

            function updateCount() {
                const count = checkboxes.filter(cb => cb.checked).length;
                selectedCount.textContent = count + ' dipilih';
            }

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    checkboxes.forEach(cb => {
                        cb.checked = selectAll.checked;
                    });
                    updateCount();
                });
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', function () {
                    const allChecked = checkboxes.length > 0 && checkboxes.every(x => x.checked);
                    if (selectAll) {
                        selectAll.checked = allChecked;
                    }
                    updateCount();
                });
            });

            updateCount();

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
