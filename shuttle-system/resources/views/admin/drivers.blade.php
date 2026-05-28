@extends('admin.layout')

@section('content')
<div class="space-y-8 animate-fade-in">
    
    <!-- Header Title -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold font-outfit text-dark-900 leading-tight">Master Supir (Driver)</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola data supir, nomor lisensi mengemudi (SIM), serta penugasan armada kendaraan.</p>
        </div>
        <div class="h-12 w-12 rounded-xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Add Driver Form -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm h-fit">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 rounded-lg bg-brand-50 text-brand-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="font-bold text-dark-900 font-outfit">Tambah Supir Baru</h3>
            </div>

            <form action="/admin/drivers" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <div class="flex flex-col gap-1.5">
                    <label for="name" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Lengkap</label>
                    <input type="text" name="name" id="name" placeholder="Nama Lengkap" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="email" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Email</label>
                    <input type="email" name="email" id="email" placeholder="Email" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="phone" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Nomor HP (Opsional)</label>
                    <input type="tel" name="phone" id="phone" placeholder="Contoh: 08123456789" class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="password" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Password</label>
                    <input type="password" name="password" id="password" placeholder="Minimal 6 karakter" required minlength="6" class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="license_number" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Nomor SIM</label>
                    <input type="text" name="license_number" id="license_number" placeholder="Nomor SIM A/B" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="profile_photo" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Foto Profil</label>
                    <input type="file" name="profile_photo" id="profile_photo" accept="image/*" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 cursor-pointer">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="vehicle_id" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Pilih Kendaraan (Opsional)</label>
                    <div class="relative">
                        <select name="vehicle_id" id="vehicle_id" class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none appearance-none cursor-pointer transition-all font-medium">
                            <option value="">-- Belum Ada Kendaraan --</option>
                            @foreach($vehicles ?? [] as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ ($vehicle->driver_id === null) ? '' : 'disabled' }}>
                                    {{ $vehicle->plate_number }} - {{ $vehicle->vehicle_type }}
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

                <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-2.5 bg-dark-900 hover:bg-dark-850 text-white font-semibold text-sm rounded-xl border border-dark-900 shadow-sm transition-all gap-2">
                    <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Supir
                </button>
            </form>
        </div>

        <!-- Drivers List Table -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="font-bold text-dark-900 font-outfit text-base">Data Supir</h3>
                    <p class="text-xs text-gray-400">Total terdaftar: {{ $drivers->count() }} supir.</p>
                </div>

                <!-- Action Bar -->
                <div class="flex items-center gap-2">
                    <form id="bulkDeleteForm" action="{{ route('admin.drivers.bulk-delete') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Hapus semua supir terpilih (akun terkait juga akan terhapus)?')" class="inline-flex items-center justify-center px-3.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-lg border border-rose-150 transition-colors gap-1.5 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7M10 11v6m4-4v6M1 4h22M9 4v3" />
                            </svg>
                            Hapus Terpilih (<span id="selectedCount" class="font-extrabold text-brand-600">0</span>)
                        </button>
                    </form>

                    <form action="{{ route('admin.drivers.delete-all') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin memecat SEMUA supir? Tindakan ini tidak bisa dibatalkan dan akan menghapus akun login mereka.')" class="inline-flex items-center justify-center px-3.5 py-1.5 bg-dark-900 hover:bg-[#A32A2A] text-white text-xs font-bold rounded-lg transition-colors gap-1.5 shadow-sm border border-dark-900">
                            Bersihkan Semua
                        </button>
                    </form>
                </div>
            </div>

            @if($drivers->isEmpty())
                <div class="flex flex-col items-center justify-center p-12 text-center space-y-3">
                    <div class="h-16 w-16 rounded-full bg-gray-50 flex items-center justify-center text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-sm font-semibold text-dark-900">Belum Ada Supir</h4>
                        <p class="text-xs text-gray-400 max-w-xs">Tambahkan supir baru pada formulir di sebelah kiri.</p>
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
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Nama / Email</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Nomor SIM</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Status</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-sm">
                            @foreach($drivers as $d)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-5 py-4 text-center whitespace-nowrap">
                                        <input form="bulkDeleteForm" type="checkbox" name="driver_ids[]" value="{{ $d->id }}" class="item-checkbox rounded text-brand-500 focus:ring-brand-500 h-4 w-4 border-gray-300">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-gray-400">
                                        {{ $d->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-dark-900">{{ $d->user->name ?? 'User Terhapus' }}</span>
                                            <span class="text-xs text-gray-400">{{ $d->user->email ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 whitespace-nowrap font-medium font-mono">
                                        {{ $d->license_number }}
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        @php
                                            $dStatus = strtolower($d->status);
                                            $dBadge = 'bg-gray-50 text-gray-600 border-gray-200';
                                            if ($dStatus === 'active' || $dStatus === 'aktif') {
                                                $dBadge = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                            } elseif ($dStatus === 'inactive' || $dStatus === 'nonaktif') {
                                                $dBadge = 'bg-rose-50 text-rose-700 border-rose-200';
                                            }
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-lg border {{ $dBadge }} uppercase tracking-wider">
                                            {{ $d->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="/admin/drivers/{{ $d->id }}/edit" class="inline-flex items-center justify-center px-3 py-1.5 bg-brand-500 hover:bg-brand-600 text-dark-900 text-xs font-bold rounded-lg transition-colors gap-1 shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </a>
                                            
                                            <form action="/admin/drivers/{{ $d->id }}" method="POST" onsubmit="return confirm('Pecat supir ini dan hapus akunnya secara permanen?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-lg border border-rose-150 transition-colors gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Pecat
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
