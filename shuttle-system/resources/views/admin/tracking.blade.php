@extends('admin.layout')

@section('content')
<!-- Memuat CSS dan JS dari Leaflet (OpenStreetMap) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<h1>Peta Kendaraan (Live Tracking)</h1>
<p>Memantau posisi seluruh armada *shuttle* yang sedang dalam perjalanan (*On The Way*). Halaman ini akan memuat ulang secara otomatis setiap 10 detik.</p>

<div id="map" style="height: 500px; width: 100%; border: 2px solid #333; margin-bottom: 20px;"></div>

<script>
    // 1. Inisialisasi Peta (Default titik awal di Jakarta)
    var map = L.map('map').setView([-6.200000, 106.816666], 10);

    // 2. Menggunakan tile layer gratis dari OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // 3. Melempar data PHP (Active Schedules) ke bentuk JSON agar bisa dibaca Javascript
    var activeSchedules = @json($activeSchedules);

    var bounds = [];

    // 4. Melakukan perulangan untuk setiap jadwal kendaraan yang sedang jalan
    activeSchedules.forEach(function(schedule) {
        // Cek apakah kendaraan ini pernah mengirim lokasinya ke server
        if (schedule.locations && schedule.locations.length > 0) {
            var latestLocation = schedule.locations[0]; // Mengambil data lokasi teratas (terbaru)
            
            var lat = latestLocation.latitude;
            var lng = latestLocation.longitude;
            
            // Informasi saat PIN peta diklik
            var popupText = "<b>Plat Nomor:</b> " + schedule.vehicle.plate_number + "<br>" +
                            "<b>Supir:</b> " + (schedule.driver && schedule.driver.user ? schedule.driver.user.name : 'Unknown') + "<br>" +
                            "<b>Waktu Update:</b> " + latestLocation.recorded_at;

            // Membuat Marker/Pin di peta
            var marker = L.marker([lat, lng]).addTo(map).bindPopup(popupText);
                
            bounds.push([lat, lng]);
        }
    });

    // 5. Jika ada titik di peta, atur zoom peta agar semua titik kendaraan terlihat
    if (bounds.length > 0) {
        map.fitBounds(bounds);
    }

    // 6. Auto-Refresh Halaman tiap 10 detik
    setTimeout(function() {
        window.location.reload();
    }, 10000);
</script>

<hr>
<h3>Detail Data Kendaraan (On The Way)</h3>
<table border="1" cellpadding="5" cellspacing="0" width="100%">
    <tr>
        <th>Jadwal ID</th>
        <th>Plat Nomor</th>
        <th>Supir</th>
        <th>Koordinat (Lat, Lng)</th>
        <th>Waktu Kirim Terakhir</th>
    </tr>
    @forelse($activeSchedules as $s)
        @php
            $latest = $s->locations->first();
        @endphp
        <tr>
            <td>#{{ $s->id }}</td>
            <td>{{ $s->vehicle->plate_number }}</td>
            <td>{{ $s->driver->user->name ?? 'Unknown' }}</td>
            <td>
                @if($latest)
                    {{ $latest->latitude }}, {{ $latest->longitude }}
                @else
                    <i>Menunggu Supir Menyalakan GPS...</i>
                @endif
            </td>
            <td>{{ $latest ? $latest->recorded_at : '-' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="5" style="text-align: center;">Tidak ada armada yang sedang berjalan saat ini.</td>
        </tr>
    @endforelse
</table>
@endsection
