@extends('admin.layout')

@section('content')
<div class="max-w-xl mx-auto space-y-8 animate-fade-in">
    
    <!-- Title Section -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold font-outfit text-dark-900 leading-tight">Edit Jadwal</h1>
            <p class="text-sm text-gray-500 mt-1">Ubah data supir, waktu keberangkatan, kapasitas kursi, harga, atau status operasional.</p>
        </div>
        <div class="h-12 w-12 rounded-xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
        </div>
    </div>

    <!-- Edit Form Card -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <form action="/admin/schedules/{{ $schedule->id }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Route Select -->
            <div class="flex flex-col gap-1.5">
                <label for="route_id" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Rute Perjalanan</label>
                <div class="relative">
                    <select name="route_id" id="route_id" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none appearance-none cursor-pointer transition-all font-medium">
                        @foreach($routes as $r)
                            <option value="{{ $r->id }}" {{ $schedule->route_id == $r->id ? 'selected' : '' }}>{{ $r->origin->name }} ➔ {{ $r->destination->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Driver Select -->
            <div class="flex flex-col gap-1.5">
                <label for="driver_id" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Supir Pendamping</label>
                <div class="relative">
                    <select name="driver_id" id="driver_id" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none appearance-none cursor-pointer transition-all font-medium">
                        @foreach($drivers as $d)
                            <option value="{{ $d->id }}" data-vehicle="{{ $d->vehicle->id ?? '' }}" {{ $schedule->driver_id == $d->id ? 'selected' : '' }}>{{ $d->user->name }} ({{ $d->license_number }})</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Auto Vehicle Display -->
            <div class="flex flex-col gap-1.5">
                <label for="vehicle_id" class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                    Kendaraan Utama
                    <span class="text-[9px] bg-gray-150 px-2 py-0.5 rounded-full border text-gray-400">Sinkron Otomatis</span>
                </label>
                <div class="relative">
                    <select name="vehicle_id" id="vehicle_id" style="pointer-events: none; background-color: #f3f4f6; cursor: not-allowed;" required class="w-full bg-gray-100 border border-gray-250 text-gray-500 text-sm rounded-xl px-4 py-2.5 outline-none appearance-none font-medium">
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}" {{ $schedule->vehicle_id == $v->id ? 'selected' : '' }}>{{ $v->plate_number }} ({{ $v->vehicle_type }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Departure Time -->
            <div class="flex flex-col gap-1.5">
                <label for="departure_time" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu Keberangkatan</label>
                <input type="datetime-local" name="departure_time" id="departure_time" value="{{ date('Y-m-d\TH:i', strtotime($schedule->departure_time)) }}" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
            </div>

            <!-- Arrival Time -->
            <div class="flex flex-col gap-1.5">
                <label for="arrival_time" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Estimasi Jam Tiba</label>
                <input type="datetime-local" name="arrival_time" id="arrival_time" value="{{ $schedule->arrival_time ? date('Y-m-d\TH:i', strtotime($schedule->arrival_time)) : '' }}" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
            </div>

            <!-- Capacity -->
            <div class="flex flex-col gap-1.5">
                <label for="capacity" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Kapasitas Kursi</label>
                <input type="number" name="capacity" id="capacity" placeholder="Total Kursi" value="{{ $schedule->capacity }}" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
            </div>

            <!-- Price -->
            <div class="flex flex-col gap-1.5">
                <label for="price" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Harga Operasional (Rp)</label>
                <input type="number" name="price" id="price" placeholder="Harga untuk rute/mobil ini" value="{{ $schedule->price ?: $schedule->route->price }}" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
            </div>

            <!-- Status Select -->
            <div class="flex flex-col gap-1.5">
                <label for="status" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Status Perjalanan</label>
                <div class="relative">
                    <select name="status" id="status" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none appearance-none cursor-pointer transition-all font-medium">
                        <option value="scheduled" {{ $schedule->status == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="on_the_way" {{ $schedule->status == 'on_the_way' ? 'selected' : '' }}>On the way</option>
                        <option value="completed" {{ $schedule->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $schedule->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-6 py-2.5 bg-dark-900 hover:bg-dark-850 text-white font-semibold text-sm rounded-xl border border-dark-900 shadow-sm transition-all gap-2">
                    <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update Jadwal
                </button>
                
                <a href="/admin/schedules" class="inline-flex items-center justify-center px-6 py-2.5 bg-white hover:bg-gray-50 text-dark-900 font-semibold text-sm rounded-xl border border-gray-200 shadow-sm transition-all">
                    Batal
                </a>
            </div>

        </form>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
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
    });
</script>
@endsection
