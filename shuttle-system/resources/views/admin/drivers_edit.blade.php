@extends('admin.layout')

@section('content')
<div class="max-w-xl mx-auto space-y-8 animate-fade-in">
    
    <!-- Title Section -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold font-outfit text-dark-900 leading-tight">Edit Supir</h1>
            <p class="text-sm text-gray-500 mt-1">Perbarui profil supir, nomor lisensi mengemudi, tugas kendaraan, atau status aktif.</p>
        </div>
        <div class="h-12 w-12 rounded-xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
        </div>
    </div>

    <!-- Edit Form Card -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <form action="/admin/drivers/{{ $driver->id }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="flex flex-col gap-1.5">
                <label for="name" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Lengkap</label>
                <input type="text" name="name" id="name" placeholder="Nama Lengkap" value="{{ $driver->user->name ?? '' }}" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="email" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Email</label>
                <input type="email" name="email" id="email" placeholder="Email" value="{{ $driver->user->email ?? '' }}" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="phone" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Nomor HP</label>
                <input type="tel" name="phone" id="phone" placeholder="Nomor HP" value="{{ $driver->user->phone ?? '' }}" class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="password" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Password (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" id="password" placeholder="Kosongkan jika tidak ingin mengubah password" minlength="6" class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="license_number" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Nomor SIM</label>
                <input type="text" name="license_number" id="license_number" placeholder="Nomor SIM" value="{{ $driver->license_number }}" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="vehicle_id" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Kendaraan Terkait</label>
                <div class="relative">
                    <select name="vehicle_id" id="vehicle_id" class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none appearance-none cursor-pointer transition-all font-medium">
                        <option value="">-- Belum Ada Kendaraan --</option>
                        @foreach($vehicles ?? [] as $vehicle)
                            @php
                                $isCurrentVehicle = $driver->vehicle && $driver->vehicle->id === $vehicle->id;
                                $isOtherDriverVehicle = $vehicle->driver_id !== null && $vehicle->driver_id !== $driver->id;
                            @endphp
                            <option value="{{ $vehicle->id }}" 
                                {{ $isCurrentVehicle ? 'selected' : '' }}
                                {{ $isOtherDriverVehicle ? 'disabled' : '' }}>
                                {{ $vehicle->plate_number }} - {{ $vehicle->vehicle_type }}
                                {{ $isCurrentVehicle ? '(Saat ini)' : '' }}
                            </option>
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
                <label for="status" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Status Aktif</label>
                <div class="relative">
                    <select name="status" id="status" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none appearance-none cursor-pointer transition-all font-medium">
                        <option value="active" {{ $driver->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $driver->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                    Update Supir
                </button>
                
                <a href="/admin/drivers" class="inline-flex items-center justify-center px-6 py-2.5 bg-white hover:bg-gray-50 text-dark-900 font-semibold text-sm rounded-xl border border-gray-200 shadow-sm transition-all">
                    Batal
                </a>
            </div>

        </form>
    </div>

</div>
@endsection
