@extends('admin.layout')

@section('content')
<div class="space-y-8 animate-fade-in">
    
    <!-- Header Title -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold font-outfit text-dark-900 leading-tight">Master Jadwal</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola jadwal keberangkatan shuttle, supir pendamping, sinkronisasi armada otomatis, serta tarif operasional.</p>
        </div>
        <div class="h-12 w-12 rounded-xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
    </div>

    <!-- Forms & Config Block -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        
        <!-- Add Schedule Form Card -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm h-fit">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 rounded-lg bg-brand-50 text-brand-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="font-bold text-dark-900 font-outfit">Tambah Jadwal</h3>
            </div>

            <form action="/admin/schedules" method="POST" class="space-y-4">
                @csrf
                
                <!-- Route Select -->
                <div class="flex flex-col gap-1.5">
                    <label for="route_id" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Rute Perjalanan</label>
                    <div class="relative">
                        <select name="route_id" id="route_id" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none appearance-none cursor-pointer transition-all font-medium">
                            @foreach($routes as $r)
                                <option value="{{ $r->id }}">{{ $r->origin->name }} ➔ {{ $r->destination->name }}</option>
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
                            <option value="" disabled selected>-- Pilih Supir --</option>
                            @foreach($drivers as $d)
                                <option value="{{ $d->id }}" data-vehicle="{{ $d->vehicle->id ?? '' }}">{{ $d->user->name }} ({{ $d->license_number }})</option>
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
                            <option value="" disabled selected>-- Kendaraan Supir --</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}">{{ $v->plate_number }} ({{ $v->vehicle_type }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Departure Time -->
                <div class="flex flex-col gap-1.5">
                    <label for="departure_time" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu Keberangkatan</label>
                    <input type="datetime-local" name="departure_time" id="departure_time" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
                </div>

                <!-- Arrival Time -->
                <div class="flex flex-col gap-1.5">
                    <label for="arrival_time" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Estimasi Jam Tiba</label>
                    <input type="datetime-local" name="arrival_time" id="arrival_time" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
                </div>

                <!-- Capacity -->
                <div class="flex flex-col gap-1.5">
                    <label for="capacity" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Kapasitas Kursi</label>
                    <input type="number" name="capacity" id="capacity" placeholder="Contoh: 14" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
                </div>

                <!-- Price -->
                <div class="flex flex-col gap-1.5">
                    <label for="price" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Harga Operasional (Rp)</label>
                    <input type="number" name="price" id="price" placeholder="Contoh: 130000" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-2.5 bg-dark-900 hover:bg-dark-850 text-white font-semibold text-sm rounded-xl border border-dark-900 shadow-sm transition-all gap-2">
                    <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Jadwal
                </button>
            </form>
        </div>

        <!-- Schedules List Card -->
        <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <!-- Card Header -->
                <div class="px-6 py-5 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-dark-900 font-outfit text-base">Daftar Jadwal</h3>
                        <p class="text-xs text-gray-400">Total terdaftar: {{ $schedules->total() }} jadwal.</p>
                    </div>
                    
                    <!-- Action Bar -->
                    <div class="flex items-center gap-2">
                        <form id="bulkDeleteForm" action="{{ route('admin.schedules.bulk-delete') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" onclick="return confirm('Hapus semua jadwal terpilih?')" class="inline-flex items-center justify-center px-3.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-lg border border-rose-150 transition-colors gap-1.5 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7M10 11v6m4-4v6M1 4h22M9 4v3" />
                                </svg>
                                Hapus Terpilih (<span id="selectedCount" class="font-extrabold text-brand-600">0</span>)
                            </button>
                        </form>

                        <form action="{{ route('admin.schedules.delete-all') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus SEMUA jadwal perjalanan? Tindakan ini tidak bisa dibatalkan.')" class="inline-flex items-center justify-center px-3.5 py-1.5 bg-dark-900 hover:bg-[#A32A2A] text-white text-xs font-bold rounded-lg transition-colors gap-1.5 shadow-sm border border-dark-900">
                                Bersihkan Semua
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Table Content -->
                @if($schedules->isEmpty())
                    <div class="flex flex-col items-center justify-center p-16 text-center space-y-3">
                        <div class="h-16 w-16 rounded-full bg-gray-50 flex items-center justify-center text-gray-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5" />
                            </svg>
                        </div>
                        <div class="space-y-1">
                            <h4 class="text-sm font-semibold text-dark-900">Belum Ada Jadwal</h4>
                            <p class="text-xs text-gray-400 max-w-xs">Buat jadwal keberangkatan armada baru pada formulir penyusun di sebelah kiri.</p>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="px-5 py-3.5 text-center text-xs font-bold text-gray-400">
                                        <input type="checkbox" id="selectAllSchedules" class="rounded text-brand-500 focus:ring-brand-500 h-4 w-4 border-gray-300">
                                    </th>
                                    <th class="px-4 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">ID</th>
                                    <th class="px-5 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Rute</th>
                                    <th class="px-5 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Keberangkatan & Tiba</th>
                                    <th class="px-5 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Armada / Supir</th>
                                    <th class="px-5 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Harga</th>
                                    <th class="px-5 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Status</th>
                                    <th class="px-5 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-sm">
                                @foreach($schedules as $s)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-5 py-4 text-center whitespace-nowrap">
                                            <input form="bulkDeleteForm" type="checkbox" name="schedule_ids[]" value="{{ $s->id }}" class="schedule-checkbox rounded text-brand-500 focus:ring-brand-500 h-4 w-4 border-gray-300">
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-center font-bold text-gray-400">
                                            {{ $s->id }}
                                        </td>
                                        <td class="px-5 py-4 font-semibold text-dark-900 whitespace-nowrap">
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-bold text-dark-800">{{ $s->route->origin->name }}</span>
                                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                                </svg>
                                                <span class="font-bold text-brand-600">{{ $s->route->destination->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-gray-700 whitespace-nowrap font-medium text-xs">
                                            <div class="flex flex-col gap-1">
                                                <span class="font-semibold text-dark-900 flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    {{ \Carbon\Carbon::parse($s->departure_time)->translatedFormat('d M Y, H:i') }}
                                                </span>
                                                <span class="text-gray-450 flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                    {{ $s->arrival_time ? \Carbon\Carbon::parse($s->arrival_time)->translatedFormat('d M Y, H:i') : '-' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-gray-650 whitespace-nowrap">
                                            <div class="flex flex-col">
                                                <span class="font-mono font-bold text-xs bg-gray-50 border border-gray-200 px-2 py-0.5 rounded w-fit text-dark-800">
                                                    {{ $s->vehicle->plate_number }}
                                                </span>
                                                <span class="text-xs text-gray-400 mt-1 font-semibold">{{ $s->driver->user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 font-bold text-emerald-600 whitespace-nowrap">
                                            Rp {{ number_format($s->price ?: $s->route->price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-5 py-4 text-center whitespace-nowrap">
                                            @php
                                                $sStatus = strtolower($s->status);
                                                $sBadge = 'bg-gray-50 text-gray-600 border-gray-200';
                                                if ($sStatus === 'scheduled' || $sStatus === 'terjadwal') {
                                                    $sBadge = 'bg-blue-50 text-blue-700 border-blue-200';
                                                } elseif ($sStatus === 'on_the_way' || $sStatus === 'jalan') {
                                                    $sBadge = 'bg-amber-50 text-amber-700 border-amber-200';
                                                } elseif ($sStatus === 'completed' || $sStatus === 'selesai') {
                                                    $sBadge = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                                } elseif ($sStatus === 'cancelled' || $sStatus === 'batal') {
                                                    $sBadge = 'bg-rose-50 text-rose-700 border-rose-200';
                                                }
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 text-[10px] font-bold rounded-lg border {{ $sBadge }} uppercase tracking-wider">
                                                {{ $s->status }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 text-center whitespace-nowrap">
                                            <div class="inline-flex items-center gap-2">
                                                <a href="/admin/schedules/{{ $s->id }}/edit" class="inline-flex items-center justify-center px-2.5 py-1.5 bg-brand-500 hover:bg-brand-600 text-dark-900 text-xs font-bold rounded-lg transition-colors gap-1 shadow-sm">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M15.414 2.586a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Edit
                                                </a>
                                                
                                                <form action="/admin/schedules/{{ $s->id }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center justify-center px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-lg border border-rose-150 transition-colors gap-1">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-50">
                        {{ $schedules->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const selectAll = document.getElementById('selectAllSchedules');
        const checkboxes = Array.from(document.querySelectorAll('.schedule-checkbox'));
        const selectedCount = document.getElementById('selectedCount');

        function updateCount() {
            if (selectedCount) {
                const count = checkboxes.filter(cb => cb.checked).length;
                selectedCount.textContent = count;
            }
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
    });
</script>
@endsection
