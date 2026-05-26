@extends('admin.layout')

@section('content')
<!-- Memuat CSS dan JS dari Leaflet (OpenStreetMap) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<div class="space-y-8 animate-fade-in">
    
    <!-- Header Title -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold font-outfit text-dark-900 leading-tight">Peta Kendaraan (Live Tracking)</h1>
            <p class="text-sm text-gray-500 mt-1">Memantau posisi seluruh armada shuttle yang sedang dalam perjalanan secara real-time. Halaman memuat ulang otomatis setiap 10 detik.</p>
        </div>
        
        <div class="flex items-center gap-2 bg-[#FBC02D]/10 px-3 py-1.5 rounded-full border border-[#FBC02D]/20 self-start md:self-auto">
            <span class="w-2.5 h-2.5 rounded-full bg-brand-500 animate-ping"></span>
            <span class="w-2.5 h-2.5 rounded-full bg-brand-500 absolute"></span>
            <span class="text-xs font-bold text-brand-650 ml-1">Live Tracking Aktif</span>
        </div>
    </div>

    <!-- Map Container -->
    <div class="bg-white p-4 rounded-2xl border border-gray-150 shadow-sm overflow-hidden">
        <div id="map" class="rounded-xl border border-gray-200" style="height: 480px; width: 100%; z-index: 10;"></div>
    </div>

    <!-- Leaflet GPS JS Engine -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
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
                    var popupText = "<div class='space-y-1 text-xs'><b class='text-dark-900 font-outfit text-sm block border-b pb-1 mb-1'>Detail Armada</b>" +
                                    "<b>Plat Nomor:</b> <span class='font-mono bg-gray-100 px-1.5 py-0.5 rounded border'>" + schedule.vehicle.plate_number + "</span><br>" +
                                    "<b>Supir:</b> " + (schedule.driver && schedule.driver.user ? schedule.driver.user.name : 'Unknown') + "<br>" +
                                    "<b class='text-gray-400 block mt-1'>Update: " + latestLocation.recorded_at + "</b></div>";

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
        });
    </script>

    <!-- Detail Data Kendaraan Table Panel -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-50">
            <h3 class="font-bold text-dark-900 font-outfit text-base">Armada Aktif (On The Way)</h3>
            <p class="text-xs text-gray-400">Daftar kendaraan yang saat ini sedang melakukan perjalanan aktif di rute.</p>
        </div>

        @if(empty($activeSchedules) || count($activeSchedules) === 0)
            <div class="flex flex-col items-center justify-center p-16 text-center space-y-3">
                <div class="h-16 w-16 rounded-full bg-gray-50 flex items-center justify-center text-gray-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0" />
                    </svg>
                </div>
                <div class="space-y-1">
                    <h4 class="text-sm font-semibold text-dark-900">Tidak Ada Perjalanan Aktif</h4>
                    <p class="text-xs text-gray-400 max-w-xs">Saat ini tidak ada supir yang menyalakan status perjalanan di aplikasi seluler.</p>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Jadwal ID</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Plat Nomor</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Supir</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Koordinat (Lat, Lng)</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Waktu Kirim Terakhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        @foreach($activeSchedules as $s)
                            @php
                                $latest = $s->locations->first();
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-gray-450">
                                    #{{ $s->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-dark-900">
                                    <span class="inline-flex items-center px-2.5 py-1 bg-gray-100 border border-gray-200 rounded-lg font-mono">
                                        {{ $s->vehicle->plate_number }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-semibold text-dark-900">
                                    {{ $s->driver->user->name ?? 'Unknown' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-gray-650 font-medium">
                                    @if($latest)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-brand-50 border border-brand-100 rounded-lg text-xs font-bold text-brand-650">
                                            <span class="w-1.5 h-1.5 rounded-full bg-brand-500 animate-ping"></span>
                                            {{ $latest->latitude }}, {{ $latest->longitude }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Menunggu Supir Menyalakan GPS...</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500 font-medium text-xs">
                                    {{ $latest ? $latest->recorded_at : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
