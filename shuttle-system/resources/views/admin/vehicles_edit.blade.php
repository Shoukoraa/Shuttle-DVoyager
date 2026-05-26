@extends('admin.layout')

@section('content')
<div class="max-w-xl mx-auto space-y-8 animate-fade-in">
    
    <!-- Title Section -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold font-outfit text-dark-900 leading-tight">Edit Kendaraan</h1>
            <p class="text-sm text-gray-500 mt-1">Ubah data tipe, kategori, kapasitas, atau status perawatan armada.</p>
        </div>
        <div class="h-12 w-12 rounded-xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
        </div>
    </div>

    <!-- Edit Form Card -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <form action="/admin/vehicles/{{ $vehicle->id }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="flex flex-col gap-1.5">
                <label for="plate_number" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Plat Nomor</label>
                <input type="text" name="plate_number" id="plate_number" placeholder="Plat Nomor" value="{{ $vehicle->plate_number }}" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium font-mono">
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="vehicle_type" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tipe / Model</label>
                <input type="text" name="vehicle_type" id="vehicle_type" placeholder="Tipe (Toyota Hiace, dll)" value="{{ $vehicle->vehicle_type }}" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="vehicle_category" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Kategori Armada</label>
                <div class="relative">
                    <select name="vehicle_category" id="vehicle_category" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none appearance-none cursor-pointer transition-all font-medium">
                        <option value="family_car" {{ $vehicle->vehicle_category == 'family_car' ? 'selected' : '' }}>Mobil Keluarga</option>
                        <option value="mini_bus" {{ $vehicle->vehicle_category == 'mini_bus' ? 'selected' : '' }}>Mini Bus</option>
                        <option value="bus" {{ $vehicle->vehicle_category == 'bus' ? 'selected' : '' }}>BUS</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="capacity" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Kapasitas Kursi</label>
                <input type="number" name="capacity" id="capacity" placeholder="Kapasitas" value="{{ $vehicle->capacity }}" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="status" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Status Armada</label>
                <div class="relative">
                    <select name="status" id="status" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none appearance-none cursor-pointer transition-all font-medium">
                        <option value="active" {{ $vehicle->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="maintenance" {{ $vehicle->status == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="inactive" {{ $vehicle->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                    Update Armada
                </button>
                
                <a href="/admin/vehicles" class="inline-flex items-center justify-center px-6 py-2.5 bg-white hover:bg-gray-50 text-dark-900 font-semibold text-sm rounded-xl border border-gray-200 shadow-sm transition-all">
                    Batal
                </a>
            </div>

        </form>
    </div>

</div>
@endsection
