@extends('admin.layout')

@section('content')
<div class="space-y-8 animate-fade-in">
    
    <!-- Header Title -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold font-outfit text-dark-900 leading-tight">Master Lokasi</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola data lokasi asal dan tujuan operasional Shuttle D-Voyager.</p>
        </div>
        <div class="h-12 w-12 rounded-xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Add Location Form -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm h-fit">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 rounded-lg bg-brand-50 text-brand-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="font-bold text-dark-900 font-outfit">Tambah Lokasi</h3>
            </div>

            <form action="/admin/locations" method="POST" class="space-y-4">
                @csrf
                <div class="flex flex-col gap-1.5">
                    <label for="name" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Lokasi</label>
                    <input type="text" name="name" id="name" placeholder="Contoh: Bandung (Pasteur)" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="latitude" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Latitude (Opsional)</label>
                    <input type="text" name="latitude" id="latitude" placeholder="Contoh: -6.8976" class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="longitude" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Longitude (Opsional)</label>
                    <input type="text" name="longitude" id="longitude" placeholder="Contoh: 107.6186" class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-2.5 bg-dark-900 hover:bg-dark-850 text-white font-semibold text-sm rounded-xl border border-dark-900 shadow-sm transition-all gap-2">
                    <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Lokasi
                </button>
            </form>
        </div>

        <!-- Locations List Table -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-50">
                <h3 class="font-bold text-dark-900 font-outfit text-base">Daftar Lokasi</h3>
                <p class="text-xs text-gray-400">Total terdaftar: {{ $locations->count() }} lokasi.</p>
            </div>

            @if($locations->isEmpty())
                <div class="flex flex-col items-center justify-center p-12 text-center space-y-3">
                    <div class="h-16 w-16 rounded-full bg-gray-50 flex items-center justify-center text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-sm font-semibold text-dark-900">Belum Ada Lokasi</h4>
                        <p class="text-xs text-gray-400 max-w-xs">Tambahkan lokasi baru pada formulir di sebelah kiri.</p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">ID</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Lokasi</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Koordinat (Lat, Long)</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-sm">
                            @foreach($locations as $loc)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-gray-400">
                                        {{ $loc->id }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-dark-900 whitespace-nowrap">
                                        {{ $loc->name }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 whitespace-nowrap font-medium">
                                        @if($loc->latitude && $loc->longitude)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-50 border border-gray-200/50 rounded-lg text-xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                                                {{ $loc->latitude }}, {{ $loc->longitude }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Tidak ada koordinat</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="/admin/locations/{{ $loc->id }}/edit" class="inline-flex items-center justify-center px-3 py-1.5 bg-brand-500 hover:bg-brand-600 text-dark-900 text-xs font-bold rounded-lg transition-colors gap-1 shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </a>
                                            
                                            <form action="/admin/locations/{{ $loc->id }}" method="POST" onsubmit="return confirm('Hapus lokasi ini?')">
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
@endsection
