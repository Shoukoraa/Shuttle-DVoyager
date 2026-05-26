@extends('admin.layout')

@section('content')
<div class="max-w-xl mx-auto space-y-8 animate-fade-in">
    
    <!-- Title Section -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold font-outfit text-dark-900 leading-tight">Edit Lokasi</h1>
            <p class="text-sm text-gray-500 mt-1">Ubah data koordinat atau nama untuk lokasi terpilih.</p>
        </div>
        <div class="h-12 w-12 rounded-xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
        </div>
    </div>

    <!-- Edit Form Card -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <form action="/admin/locations/{{ $location->id }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="flex flex-col gap-1.5">
                <label for="name" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Lokasi</label>
                <input type="text" name="name" id="name" placeholder="Nama Lokasi" value="{{ $location->name }}" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="latitude" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Latitude</label>
                <input type="text" name="latitude" id="latitude" placeholder="Latitude" value="{{ $location->latitude }}" class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="longitude" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Longitude</label>
                <input type="text" name="longitude" id="longitude" placeholder="Longitude" value="{{ $location->longitude }}" class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-6 py-2.5 bg-dark-900 hover:bg-dark-850 text-white font-semibold text-sm rounded-xl border border-dark-900 shadow-sm transition-all gap-2">
                    <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update Lokasi
                </button>
                
                <a href="/admin/locations" class="inline-flex items-center justify-center px-6 py-2.5 bg-white hover:bg-gray-50 text-dark-900 font-semibold text-sm rounded-xl border border-gray-200 shadow-sm transition-all">
                    Batal
                </a>
            </div>

        </form>
    </div>

</div>
@endsection
