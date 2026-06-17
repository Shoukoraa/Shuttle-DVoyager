@extends('admin.layout')

@section('content')
<!-- Memuat CSS dan JS Mapbox GL JS -->
<link href="https://api.mapbox.com/mapbox-gl-js/v3.13.0/mapbox-gl.css" rel="stylesheet">
<script src="https://api.mapbox.com/mapbox-gl-js/v3.13.0/mapbox-gl.js"></script>

<!-- Memuat Pusher dan Laravel Echo untuk Real-Time WebSockets -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/8.3.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<div class="space-y-8 animate-fade-in">
    
    <!-- Header Title -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold font-outfit text-dark-900 leading-tight">Peta Kendaraan (Live Tracking)</h1>
            <p class="text-sm text-gray-500 mt-1">Memantau posisi seluruh armada shuttle yang sedang dalam perjalanan secara real-time via Laravel Reverb.</p>
        </div>
        
        <div class="flex items-center gap-2 bg-[#FBC02D]/10 px-3 py-1.5 rounded-full border border-[#FBC02D]/20 self-start md:self-auto">
            <span class="w-2.5 h-2.5 rounded-full bg-brand-500 animate-ping"></span>
            <span class="w-2.5 h-2.5 rounded-full bg-brand-500 absolute"></span>
            <span class="text-xs font-bold text-brand-650 ml-1">Live Tracking Aktif</span>
        </div>
    </div>

    <!-- Map Container -->
    <div class="relative bg-white p-4 rounded-2xl border border-gray-150 shadow-sm overflow-hidden">
        <div id="map" class="rounded-xl border border-gray-200" style="height: 520px; width: 100%; z-index: 10;"></div>
        
        <!-- Map Style Controls (Premium Mapbox Style Toggle) -->
        <div class="absolute top-8 right-8 z-20 bg-white/90 backdrop-blur-sm p-1.5 rounded-xl border border-gray-200 shadow-lg flex gap-1">
            <button id="btnStreet" onclick="setMapStyle('streets')" class="px-3 py-1.5 rounded-lg text-xs font-bold text-gray-600 hover:bg-gray-100 transition-colors">Peta</button>
            <button id="btnSatellite" onclick="setMapStyle('satellite')" class="px-3 py-1.5 rounded-lg text-xs font-bold text-gray-600 hover:bg-gray-100 transition-colors">Satelit</button>
            <button id="btnDark" onclick="setMapStyle('dark')" class="px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-dark-900 transition-colors">Malam</button>
        </div>

        <!-- Custom Map Navigation & Zoom Controls (Floating D-Pad) -->
        <div class="absolute bottom-8 right-8 z-20 w-[188px] h-[140px] select-none pointer-events-none">
            <!-- Collapsible Menu -->
            <div id="dpad-menu" class="absolute inset-0 pointer-events-none transition-all duration-300 ease-out opacity-0 scale-90 translate-x-12 translate-y-12 origin-bottom-right">
                <!-- Up Button -->
                <button type="button" onclick="panMap('up')" class="absolute top-0 left-[48px] pointer-events-auto w-11 h-11 rounded-full bg-white border border-gray-200/80 shadow-md flex items-center justify-center hover:bg-gray-50 hover:text-dark-900 active:scale-95 transition-all text-gray-700 focus:outline-none" title="Geser Atas">
                    <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5"/>
                    </svg>
                </button>
                
                <!-- Plus Button (Zoom In) -->
                <button type="button" onclick="zoomMap('in')" class="absolute top-0 left-[96px] pointer-events-auto w-11 h-11 rounded-full bg-white border border-gray-200/80 shadow-md flex items-center justify-center hover:bg-gray-50 hover:text-dark-900 active:scale-95 transition-all text-gray-700 focus:outline-none" title="Perbesar (+)">
                    <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                </button>

                <!-- Left Button -->
                <button type="button" onclick="panMap('left')" class="absolute top-[48px] left-0 pointer-events-auto w-11 h-11 rounded-full bg-white border border-gray-200/80 shadow-md flex items-center justify-center hover:bg-gray-50 hover:text-dark-900 active:scale-95 transition-all text-gray-700 focus:outline-none" title="Geser Kiri">
                    <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                    </svg>
                </button>

                <!-- Right Button -->
                <button type="button" onclick="panMap('right')" class="absolute top-[48px] left-[96px] pointer-events-auto w-11 h-11 rounded-full bg-white border border-gray-200/80 shadow-md flex items-center justify-center hover:bg-gray-50 hover:text-dark-900 active:scale-95 transition-all text-gray-700 focus:outline-none" title="Geser Kanan">
                    <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                    </svg>
                </button>

                <!-- Down Button -->
                <button type="button" onclick="panMap('down')" class="absolute top-[96px] left-[48px] pointer-events-auto w-11 h-11 rounded-full bg-white border border-gray-200/80 shadow-md flex items-center justify-center hover:bg-gray-50 hover:text-dark-900 active:scale-95 transition-all text-gray-700 focus:outline-none" title="Geser Bawah">
                    <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                    </svg>
                </button>

                <!-- Minus Button (Zoom Out) -->
                <button type="button" onclick="zoomMap('out')" class="absolute top-[96px] left-[96px] pointer-events-auto w-11 h-11 rounded-full bg-white border border-gray-200/80 shadow-md flex items-center justify-center hover:bg-gray-50 hover:text-dark-900 active:scale-95 transition-all text-gray-700 focus:outline-none" title="Perkecil (-)">
                    <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15"/>
                    </svg>
                </button>
            </div>

            <!-- Toggle Button (Always Visible in bottom-right corner) -->
            <button type="button" id="dpad-toggle-btn" onclick="toggleDpadMenu()" class="absolute top-[96px] left-[144px] pointer-events-auto w-11 h-11 rounded-full bg-white border border-gray-200/80 shadow-md flex items-center justify-center hover:bg-gray-50 hover:text-dark-900 active:scale-95 transition-all text-gray-700 focus:outline-none" title="Tampilkan/Sembunyikan Navigasi">
                <svg id="dpad-toggle-icon" class="w-5 h-5 stroke-[2.5] transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <!-- 4 arrows pointing outwards -->
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75v4.5m0-4.5h-4.5m4.5 0L15 9m5.25 11.25v-4.5m0 4.5h-4.5m4.5 0L15 15"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Detail Data Kendaraan Table Panel -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-50">
            <h3 class="font-bold text-dark-900 font-outfit text-base">Armada Aktif (On The Way)</h3>
            <p class="text-xs text-gray-400">Daftar kendaraan yang saat ini sedang melakukan perjalanan aktif di rute.</p>
        </div>

        @if(empty($activeSchedules) || count($activeSchedules) === 0)
            <div id="emptyState" class="flex flex-col items-center justify-center p-16 text-center space-y-3">
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
                    <tbody id="activeSchedulesTableBody" class="divide-y divide-gray-50 text-sm">
                        @foreach($activeSchedules as $s)
                            @php
                                $latest = $s->locations->first();
                            @endphp
                            <tr id="schedule-row-{{ $s->id }}" class="hover:bg-gray-50/50 transition-colors">
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
                                        <span id="coords-{{ $s->id }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-brand-50 border border-brand-100 rounded-lg text-xs font-bold text-brand-650">
                                            <span class="w-1.5 h-1.5 rounded-full bg-brand-500 animate-ping"></span>
                                            {{ $latest->latitude }}, {{ $latest->longitude }}
                                        </span>
                                    @else
                                        <span id="coords-{{ $s->id }}" class="text-xs text-gray-400 italic">Menunggu Supir Menyalakan GPS...</span>
                                    @endif
                                </td>
                                <td id="updated-{{ $s->id }}" class="px-6 py-4 whitespace-nowrap text-gray-500 font-medium text-xs">
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

<!-- CSS Tambahan untuk Marker Mapbox sudah dipindah ke admin-custom.css -->

<script>
    mapboxgl.accessToken = 'pk.eyJ1IjoibW9yZW4tNjciLCJhIjoiY21vam1pbWxuMDA0bDJxb2xkZTBnM2s3cSJ9.wUfxEG062R3T-AZr_m9Fvw';
    
    var map;
    var markers = {};
    var terminalMarkers = [];
    var activeSchedules = @json($activeSchedules);
    var currentStyle = 'dark';
    var currentBounds = null;

    // 1. Inisialisasi Mapbox GL JS
    function initMap() {
        map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/dark-v11',
            center: [110.3785, -7.7970], // Default Yogyakarta
            zoom: 7,
            pitch: 35,
            scrollZoom: false // Disable zoom on scroll wheel to let website scroll
        });

        map.on('load', function() {
            drawAllRoutesAndTerminals();
            setupEcho();
        });
    }

    // 2. Mengubah Style Peta
    function setMapStyle(style) {
        currentStyle = style;
        var styleUrls = {
            'streets': 'mapbox://styles/mapbox/streets-v12',
            'satellite': 'mapbox://styles/mapbox/satellite-streets-v12',
            'dark': 'mapbox://styles/mapbox/dark-v11'
        };

        map.setStyle(styleUrls[style]);

        // Atur warna teks tombol
        ['btnStreet', 'btnSatellite', 'btnDark'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el) {
                el.classList.remove('bg-dark-900', 'text-white');
                el.classList.add('text-gray-600');
            }
        });

        var activeBtnId = style === 'streets' ? 'btnStreet' : (style === 'satellite' ? 'btnSatellite' : 'btnDark');
        var activeBtn = document.getElementById(activeBtnId);
        if (activeBtn) {
            activeBtn.classList.remove('text-gray-600');
            activeBtn.classList.add('bg-dark-900', 'text-white');
        }

        // Gambar ulang rute setelah style baru selesai dimuat
        map.once('style.load', function() {
            drawAllRoutesAndTerminals();
        });
    }

    // 3. Menggambar Rute & Marker Halte untuk seluruh jadwal aktif
    function drawAllRoutesAndTerminals() {
        // Hapus marker halte lama
        terminalMarkers.forEach(function(m) { m.remove(); });
        terminalMarkers = [];

        var bounds = new mapboxgl.LngLatBounds();
        var hasPoints = false;

        activeSchedules.forEach(function(schedule) {
            var origin = schedule.route?.origin;
            var destination = schedule.route?.destination;

            if (origin && destination) {
                // Marker Halte Asal
                var elOrigin = document.createElement('div');
                elOrigin.className = 'marker-terminal';
                elOrigin.innerHTML = 'A';
                var mOri = new mapboxgl.Marker(elOrigin)
                    .setLngLat([origin.longitude, origin.latitude])
                    .setPopup(new mapboxgl.Popup({ offset: 10 }).setHTML('<b>Halte Asal:</b> ' + origin.name))
                    .addTo(map);
                terminalMarkers.push(mOri);
                bounds.extend([origin.longitude, origin.latitude]);
                hasPoints = true;

                // Marker Halte Tujuan
                var elDest = document.createElement('div');
                elDest.className = 'marker-terminal';
                elDest.style.backgroundColor = '#10dc60'; // Hijau untuk tujuan
                elDest.innerHTML = 'B';
                var mDest = new mapboxgl.Marker(elDest)
                    .setLngLat([destination.longitude, destination.latitude])
                    .setPopup(new mapboxgl.Popup({ offset: 10 }).setHTML('<b>Halte Tujuan:</b> ' + destination.name))
                    .addTo(map);
                terminalMarkers.push(mDest);
                bounds.extend([destination.longitude, destination.latitude]);

                // Gambar Garis Rute (Directions API)
                drawRoutePath(schedule.id, [origin.longitude, origin.latitude], [destination.longitude, destination.latitude]);
            }

            // Tampilkan Kendaraan Terakhir (jika ada data koordinat gps)
            if (schedule.locations && schedule.locations.length > 0) {
                var latestLoc = schedule.locations[0];
                updateVehicleOnMap(schedule.id, latestLoc.latitude, latestLoc.longitude, latestLoc.recorded_at, {
                    plate_number: schedule.vehicle.plate_number,
                    driver_name: schedule.driver && schedule.driver.user ? schedule.driver.user.name : 'Unknown'
                });
                bounds.extend([latestLoc.longitude, latestLoc.latitude]);
                hasPoints = true;
            }
        });

        if (hasPoints) {
            currentBounds = bounds;
            map.fitBounds(bounds, { padding: 80, maxZoom: 12 });
        }
    }

    // 4.1. Fungsi Control Navigasi D-Pad Kustom
    function toggleDpadMenu() {
        var menu = document.getElementById('dpad-menu');
        var toggleIcon = document.getElementById('dpad-toggle-icon');
        if (!menu || !toggleIcon) return;

        if (menu.classList.contains('opacity-0')) {
            // Expand
            menu.classList.remove('opacity-0', 'scale-90', 'translate-x-12', 'translate-y-12', 'pointer-events-none');
            menu.classList.add('opacity-100', 'scale-100', 'translate-x-0', 'translate-y-0', 'pointer-events-auto');
            // Change toggle icon to close (X)
            toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />';
            toggleIcon.classList.add('rotate-90');
        } else {
            // Collapse
            menu.classList.add('opacity-0', 'scale-90', 'translate-x-12', 'translate-y-12', 'pointer-events-none');
            menu.classList.remove('opacity-100', 'scale-100', 'translate-x-0', 'translate-y-0', 'pointer-events-auto');
            // Change toggle icon back to 4-arrows
            toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75v4.5m0-4.5h-4.5m4.5 0L15 9m5.25 11.25v-4.5m0 4.5h-4.5m4.5 0L15 15"/>';
            toggleIcon.classList.remove('rotate-90');
        }
    }

    function panMap(direction) {
        if (!map) return;
        var amount = 150; // Jarak geser dalam pixel
        var options = {
            duration: 600, // 600ms transition time for smooth panning
            essential: true
        };
        if (direction === 'up') {
            map.panBy([0, -amount], options);
        } else if (direction === 'down') {
            map.panBy([0, amount], options);
        } else if (direction === 'left') {
            map.panBy([-amount, 0], options);
        } else if (direction === 'right') {
            map.panBy([amount, 0], options);
        }
    }

    function zoomMap(type) {
        if (!map) return;
        var currentZoom = map.getZoom();
        var targetZoom = type === 'in' ? currentZoom + 1 : currentZoom - 1;
        map.easeTo({
            zoom: targetZoom,
            duration: 600, // 600ms transition time for smooth zooming
            essential: true
        });
    }

    function fitMapBounds() {
        if (map && currentBounds) {
            map.fitBounds(currentBounds, { padding: 80, maxZoom: 12, duration: 800 });
        } else {
            drawAllRoutesAndTerminals();
        }
    }

    // 4. Menggambar Jalur Rute memakai Mapbox Directions API
    function drawRoutePath(scheduleId, start, end) {
        var url = 'https://api.mapbox.com/directions/v5/mapbox/driving/' + 
            start[0] + ',' + start[1] + ';' + end[0] + ',' + end[1] + 
            '?geometries=geojson&overview=full&access_token=' + mapboxgl.accessToken;

        fetch(url)
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (!data.routes[0]) return;
                var routeCoords = data.routes[0].geometry;
                var sourceId = 'route-source-' + scheduleId;
                var layerId = 'route-layer-' + scheduleId;

                // Pastikan source/layer lama dihapus agar tidak duplikat
                if (map.getLayer(layerId)) map.removeLayer(layerId);
                if (map.getSource(sourceId)) map.removeSource(sourceId);

                map.addSource(sourceId, {
                    type: 'geojson',
                    data: {
                        type: 'Feature',
                        properties: {},
                        geometry: routeCoords
                    }
                });

                map.addLayer({
                    id: layerId,
                    type: 'line',
                    source: sourceId,
                    layout: {
                        'line-join': 'round',
                        'line-cap': 'round'
                    },
                    paint: {
                        'line-color': '#FBC02D', // Warna Amber/Kuning sesuai tema premium
                        'line-width': 4,
                        'line-opacity': 0.75
                    }
                });
            })
            .catch(function(err) { console.error('Gagal memuat rute Mapbox:', err); });
    }

    // 5. Update Posisi Marker Kendaraan di Peta
    function updateVehicleOnMap(scheduleId, lat, lng, recordedAt, vehicle) {
        var popupHTML = "<div class='space-y-1 text-xs'><b class='text-dark-900 font-outfit text-sm block border-b pb-1 mb-1'>Detail Armada</b>" +
                        "<b>Plat Nomor:</b> <span class='font-mono bg-gray-100 px-1.5 py-0.5 rounded border'>" + vehicle.plate_number + "</span><br>" +
                        "<b>Supir:</b> " + vehicle.driver_name + "<br>" +
                        "<b class='text-gray-400 block mt-1'>Update: " + new Date(recordedAt).toLocaleTimeString() + "</b></div>";

        if (markers[scheduleId]) {
            // Animasi pergerakan marker ke titik baru secara smooth
            markers[scheduleId].setLngLat([lng, lat]);
            markers[scheduleId].getPopup().setHTML(popupHTML);
        } else {
            var el = document.createElement('div');
            el.className = 'marker-vehicle';

            markers[scheduleId] = new mapboxgl.Marker(el)
                .setLngLat([lng, lat])
                .setPopup(new mapboxgl.Popup({ offset: 25 }).setHTML(popupHTML))
                .addTo(map);
        }
    }

    // 6. Inisialisasi Laravel Echo Real-Time Listener
    function setupEcho() {
        window.Pusher = Pusher;
        
        // Setup Axios for Echo Auth
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        
        var broadcastDriver = '{{ env("BROADCAST_CONNECTION", "reverb") }}';
        var echoConfig;

        if (broadcastDriver === 'pusher') {
            echoConfig = {
                broadcaster: 'pusher',
                key: '{{ env("PUSHER_APP_KEY") }}',
                cluster: '{{ env("PUSHER_APP_CLUSTER", "mt1") }}',
                forceTLS: true,
                authEndpoint: '/admin/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }
            };
        } else {
            echoConfig = {
                broadcaster: 'reverb',
                key: '{{ env("REVERB_APP_KEY") }}',
                wsHost: '{{ env("REVERB_HOST", "localhost") }}',
                wsPort: {{ env("REVERB_PORT", 8080) }},
                wssPort: {{ env("REVERB_PORT", 8080) }},
                forceTLS: false,
                enabledTransports: ['ws', 'wss'],
                authEndpoint: '/admin/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }
            };
        }

        var echoInstance = new Echo(echoConfig);

        echoInstance.private('admin.tracking')
            .listen('.App\\Events\\DriverLocationUpdated', function(e) {
                console.log('Update lokasi real-time masuk:', e);
                
                // 1. Update Marker di Peta
                updateVehicleOnMap(e.schedule_id, e.latitude, e.longitude, e.recorded_at, e.vehicle);

                // 2. Update Informasi di Tabel Secara Real-Time
                var coordsEl = document.getElementById('coords-' + e.schedule_id);
                if (coordsEl) {
                    coordsEl.className = "inline-flex items-center gap-1.5 px-2.5 py-1 bg-brand-50 border border-brand-100 rounded-lg text-xs font-bold text-brand-650";
                    coordsEl.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-brand-500 animate-ping"></span>' + 
                                         parseFloat(e.latitude).toFixed(6) + ', ' + parseFloat(e.longitude).toFixed(6);
                }

                var updatedEl = document.getElementById('updated-' + e.schedule_id);
                if (updatedEl) {
                    updatedEl.textContent = new Date(e.recorded_at).toLocaleTimeString('id-ID') + ' WIB';
                }
            });
    }

    document.addEventListener("DOMContentLoaded", initMap);
</script>
@endsection
