@extends('admin.layout')

@section('content')
    <h1>Master Kendaraan</h1>

    <h3>Tambah Kendaraan</h3>
    <form action="/admin/vehicles" method="POST">
        @csrf
        <input type="text" name="plate_number" placeholder="Plat Nomor" required>
        <input type="text" name="vehicle_type" placeholder="Tipe (Toyota Hiace, dll)" required>
        <select name="vehicle_category" required>
            <option value="">Pilih Kategori</option>
            <option value="family_car">Mobil Keluarga</option>
            <option value="mini_bus" selected>Mini Bus</option>
            <option value="bus">BUS</option>
        </select>
        <input type="number" name="capacity" placeholder="Kapasitas" required>
        <button type="submit">Simpan</button>
    </form>

    <hr>

    <h3>Daftar Kendaraan</h3>
    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <tr>
            <th>ID</th>
            <th>Plat Nomor</th>
            <th>Tipe</th>
            <th>Kategori</th>
            <th>Kapasitas</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        @foreach($vehicles as $veh)
        <tr>
            <td>{{ $veh->id }}</td>
            <td>{{ $veh->plate_number }}</td>
            <td>{{ $veh->vehicle_type }}</td>
            <td>
                @if($veh->vehicle_category === 'family_car')
                    Mobil Keluarga
                @elseif($veh->vehicle_category === 'bus')
                    BUS
                @else
                    Mini Bus
                @endif
            </td>
            <td>{{ $veh->capacity }} Kursi</td>
            <td>{{ $veh->status }}</td>
            <td>
                <a href="/admin/vehicles/{{ $veh->id }}/edit"><button type="button">Edit</button></a>
                <form action="/admin/vehicles/{{ $veh->id }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Hapus kendaraan ini?')">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
@endsection
