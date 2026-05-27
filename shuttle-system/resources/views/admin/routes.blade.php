@extends('admin.layout')

@section('content')
<div class="space-y-8 animate-fade-in">
    
    <!-- Header Title -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold font-outfit text-dark-900 leading-tight">Master Rute</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola data rute perjalanan shuttle, jarak tempuh, dan tarif dasar tiket.</p>
        </div>
        <div class="h-12 w-12 rounded-xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
            </svg>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Add Route Form -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm h-fit">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 rounded-lg bg-brand-50 text-brand-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="font-bold text-dark-900 font-outfit">Tambah Rute</h3>
            </div>

            <form action="/admin/routes" method="POST" class="space-y-4">
                @csrf
                
                <div class="flex flex-col gap-1.5">
                    <label for="origin_location_id" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Lokasi Asal (Origin)</label>
                    <div class="relative">
                        <select name="origin_location_id" id="origin_location_id" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none appearance-none cursor-pointer transition-all font-medium">
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="destination_location_id" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Lokasi Tujuan (Destination)</label>
                    <div class="relative">
                        <select name="destination_location_id" id="destination_location_id" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none appearance-none cursor-pointer transition-all font-medium">
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="distance_km" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Jarak Tempuh (km)</label>
                    <input type="number" step="0.1" name="distance_km" id="distance_km" placeholder="Contoh: 150.5" class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="price" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Harga Tiket Dasar (Rp)</label>
                    <input type="number" name="price" id="price" placeholder="Contoh: 125000" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-2.5 bg-dark-900 hover:bg-dark-850 text-white font-semibold text-sm rounded-xl border border-dark-900 shadow-sm transition-all gap-2">
                    <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Rute
                </button>
            </form>
        </div>

        <!-- Routes List Table -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="font-bold text-dark-900 font-outfit text-base">Daftar Rute</h3>
                    <p class="text-xs text-gray-400">Total terdaftar: {{ $routes->count() }} rute perjalanan.</p>
                </div>

                <!-- Action Bar -->
                <div class="flex items-center gap-2">
                    <form id="bulkDeleteForm" action="{{ route('admin.routes.bulk-delete') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Hapus semua rute terpilih?')" class="inline-flex items-center justify-center px-3.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-lg border border-rose-150 transition-colors gap-1.5 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7M10 11v6m4-4v6M1 4h22M9 4v3" />
                            </svg>
                            Hapus Terpilih (<span id="selectedCount" class="font-extrabold text-brand-600">0</span>)
                        </button>
                    </form>

                    <form action="{{ route('admin.routes.delete-all') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus SEMUA rute? Tindakan ini tidak bisa dibatalkan.')" class="inline-flex items-center justify-center px-3.5 py-1.5 bg-dark-900 hover:bg-[#A32A2A] text-white text-xs font-bold rounded-lg transition-colors gap-1.5 shadow-sm border border-dark-900">
                            Bersihkan Semua
                        </button>
                    </form>
                </div>
            </div>

            @if($routes->isEmpty())
                <div class="flex flex-col items-center justify-center p-12 text-center space-y-3">
                    <div class="h-16 w-16 rounded-full bg-gray-50 flex items-center justify-center text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-sm font-semibold text-dark-900">Belum Ada Rute</h4>
                        <p class="text-xs text-gray-400 max-w-xs">Tambahkan rute perjalanan baru pada formulir di sebelah kiri.</p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-5 py-3.5 text-center text-xs font-bold text-gray-400">
                                    <input type="checkbox" id="selectAllItems" class="rounded text-brand-500 focus:ring-brand-500 h-4 w-4 border-gray-300">
                                </th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">ID</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Rute Perjalanan</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Jarak Tempuh</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Harga Tiket</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-sm">
                            @foreach($routes as $route)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-5 py-4 text-center whitespace-nowrap">
                                        <input form="bulkDeleteForm" type="checkbox" name="route_ids[]" value="{{ $route->id }}" class="item-checkbox rounded text-brand-500 focus:ring-brand-500 h-4 w-4 border-gray-300">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-gray-400">
                                        {{ $route->id }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-dark-900 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-dark-900">{{ $route->origin->name }}</span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                            </svg>
                                            <span class="font-bold text-brand-600">{{ $route->destination->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 whitespace-nowrap font-medium">
                                        @if($route->distance_km)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-50 border border-gray-200/50 rounded-lg text-xs font-bold">
                                                {{ $route->distance_km }} km
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Jarak tidak diatur</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-bold text-dark-900 whitespace-nowrap text-emerald-600">
                                        Rp {{ number_format($route->price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="/admin/routes/{{ $route->id }}/edit" class="inline-flex items-center justify-center px-3 py-1.5 bg-brand-500 hover:bg-brand-600 text-dark-900 text-xs font-bold rounded-lg transition-colors gap-1 shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </a>
                                            
                                            <form action="/admin/routes/{{ $route->id }}" method="POST" onsubmit="return confirm('Hapus rute ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-lg border border-rose-150 transition-colors gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
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
            @endif
        </div>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const selectAll = document.getElementById('selectAllItems');
        const checkboxes = Array.from(document.querySelectorAll('.item-checkbox'));
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
    });
</script>
@endsection
