@extends('admin.layout')

@section('content')
<!-- Flatpickr CSS & Fonts -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<div class="space-y-8 animate-fade-in">
    
    <!-- Header Title -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold font-outfit text-dark-900 leading-tight">Master Promo & Voucher</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola kupon promosi, diskon pengguna baru, potongan flat, minimal transaksi, dan masa berlaku voucher.</p>
        </div>
        <div class="h-12 w-12 rounded-xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
            </svg>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Add Voucher Form -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm h-fit">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2 rounded-lg bg-brand-50 text-brand-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="font-bold text-dark-900 font-outfit">Buat Voucher Baru</h3>
            </div>

            <form action="{{ route('admin.vouchers.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <div class="flex flex-col gap-1.5">
                    <label for="code" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Kode Voucher</label>
                    <input type="text" name="code" id="code" placeholder="Contoh: VOYAGERMERDEKA" required style="text-transform: uppercase;" class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-semibold uppercase">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="title" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Nama / Judul Voucher</label>
                    <input type="text" name="title" id="title" placeholder="Contoh: Diskon 25% Perjalanan Pertama" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="description" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Deskripsi Voucher</label>
                    <textarea name="description" id="description" rows="3" placeholder="Jelaskan detail diskon dan cara pemakaian..." required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label for="type" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tipe Diskon</label>
                        <div class="relative">
                            <select name="type" id="type" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none appearance-none cursor-pointer transition-all font-semibold">
                                <option value="percentage">Persentase (%)</option>
                                <option value="flat">Potongan Flat (Rp)</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="value" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Nilai Potongan</label>
                        <input type="number" step="0.01" name="value" id="value" placeholder="Nominal" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-semibold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5">
                        <label for="max_discount" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Maks Diskon (Rp)</label>
                        <input type="number" step="0.01" name="max_discount" id="max_discount" placeholder="Batas Maks Potongan" class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-semibold">
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="min_transaction" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Min Transaksi (Rp)</label>
                        <input type="number" step="0.01" name="min_transaction" id="min_transaction" placeholder="Min nominal tiket" value="0" class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-semibold">
                    </div>
                </div>

                <!-- Modern Luxury Flatpickr Date & Time Picker Controls -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1.5 relative">
                        <label for="expiry_date_day" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Kedaluwarsa</label>
                        <div class="relative">
                            <input type="text" name="expiry_date_day" id="expiry_date_day" placeholder="Pilih tanggal..." required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl pl-4 pr-10 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-semibold cursor-pointer">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">📅</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-1.5 relative">
                        <label for="expiry_time" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Jam Kedaluwarsa</label>
                        <div class="relative">
                            <input type="text" name="expiry_time" id="expiry_time" placeholder="Pilih jam..." required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl pl-4 pr-10 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-semibold cursor-pointer">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">🕒</span>
                        </div>
                    </div>
                </div>

                <!-- Desain Input Badge & Pilihan Icon Baru -->
                <div class="flex flex-col gap-3">
                    <div class="flex flex-col gap-1.5">
                        <label for="badge_text" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Teks Badge</label>
                        <input type="text" name="badge_text" id="badge_text" placeholder="Contoh: PENGGUNA BARU" class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-semibold">
                    </div>

                    <!-- Visual Icon Selection (Pilihan Icon Keren) -->
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Pilih Icon Promo</label>
                        <div class="grid grid-cols-5 gap-2" id="icon-selector-grid">
                            
                            <!-- Gift Option -->
                            <label class="icon-option cursor-pointer group flex flex-col items-center justify-center p-2 rounded-xl border border-gray-200 bg-gray-50 hover:bg-brand-50 hover:border-brand-300 transition-all relative">
                                <input type="radio" name="icon" value="gift-outline" checked class="hidden icon-radio">
                                <span class="text-xl">🎁</span>
                                <span class="text-[9px] font-bold text-gray-400 group-hover:text-brand-600 mt-1">Hadiah</span>
                                <div class="selected-dot absolute top-1 right-1 w-1.5 h-1.5 rounded-full bg-brand-500 opacity-0 transition-opacity"></div>
                            </label>
                            
                            <!-- Pricetag Option -->
                            <label class="icon-option cursor-pointer group flex flex-col items-center justify-center p-2 rounded-xl border border-gray-200 bg-gray-50 hover:bg-brand-50 hover:border-brand-300 transition-all relative">
                                <input type="radio" name="icon" value="pricetag-outline" class="hidden icon-radio">
                                <span class="text-xl">🏷️</span>
                                <span class="text-[9px] font-bold text-gray-400 group-hover:text-brand-600 mt-1">Promo</span>
                                <div class="selected-dot absolute top-1 right-1 w-1.5 h-1.5 rounded-full bg-brand-500 opacity-0 transition-opacity"></div>
                            </label>

                            <!-- Wallet Option -->
                            <label class="icon-option cursor-pointer group flex flex-col items-center justify-center p-2 rounded-xl border border-gray-200 bg-gray-50 hover:bg-brand-50 hover:border-brand-300 transition-all relative">
                                <input type="radio" name="icon" value="wallet-outline" class="hidden icon-radio">
                                <span class="text-xl">👛</span>
                                <span class="text-[9px] font-bold text-gray-400 group-hover:text-brand-600 mt-1">Dompet</span>
                                <div class="selected-dot absolute top-1 right-1 w-1.5 h-1.5 rounded-full bg-brand-500 opacity-0 transition-opacity"></div>
                            </label>

                            <!-- Star Option -->
                            <label class="icon-option cursor-pointer group flex flex-col items-center justify-center p-2 rounded-xl border border-gray-200 bg-gray-50 hover:bg-brand-50 hover:border-brand-300 transition-all relative">
                                <input type="radio" name="icon" value="star-outline" class="hidden icon-radio">
                                <span class="text-xl">⭐</span>
                                <span class="text-[9px] font-bold text-gray-400 group-hover:text-brand-600 mt-1">Spesial</span>
                                <div class="selected-dot absolute top-1 right-1 w-1.5 h-1.5 rounded-full bg-brand-500 opacity-0 transition-opacity"></div>
                            </label>

                            <!-- Flame Option -->
                            <label class="icon-option cursor-pointer group flex flex-col items-center justify-center p-2 rounded-xl border border-gray-200 bg-gray-50 hover:bg-brand-50 hover:border-brand-300 transition-all relative">
                                <input type="radio" name="icon" value="flame-outline" class="hidden icon-radio">
                                <span class="text-xl">🔥</span>
                                <span class="text-[9px] font-bold text-gray-400 group-hover:text-brand-600 mt-1">Hot</span>
                                <div class="selected-dot absolute top-1 right-1 w-1.5 h-1.5 rounded-full bg-brand-500 opacity-0 transition-opacity"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between border-t border-gray-50 pt-4">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_new_user_only" id="is_new_user_only" value="1" class="rounded text-brand-500 focus:ring-brand-500 h-4.5 w-4.5 border-gray-300 cursor-pointer">
                        <label for="is_new_user_only" class="text-xs font-bold text-gray-600 uppercase tracking-wide cursor-pointer select-none">Khusus Pengguna Baru</label>
                    </div>

                    <div class="flex items-center gap-2">
                        <label for="theme_color" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Hex Tema</label>
                        <input type="color" name="theme_color" id="theme_color" value="#FFC107" class="h-8 w-12 border-0 bg-transparent rounded-lg cursor-pointer">
                    </div>
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-2.5 bg-dark-900 hover:bg-dark-850 text-white font-semibold text-sm rounded-xl border border-dark-900 shadow-sm transition-all gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Voucher
                </button>
            </form>
        </div>

        <!-- Vouchers List Table -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-dark-900 font-outfit text-base">Daftar Voucher Aktif</h3>
                    <p class="text-xs text-gray-400">Total terdaftar: {{ $vouchers->total() }} promo.</p>
                </div>
            </div>

            @if($vouchers->isEmpty())
                <div class="flex flex-col items-center justify-center p-16 text-center space-y-3">
                    <div class="h-16 w-16 rounded-full bg-gray-50 flex items-center justify-center text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-sm font-semibold text-dark-900">Belum Ada Voucher</h4>
                        <p class="text-xs text-gray-400 max-w-xs">Tambahkan voucher baru menggunakan formulir di sebelah kiri.</p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">ID</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Kupon</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Judul & Ketentuan</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Nilai Diskon</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Kedaluwarsa</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Status</th>
                                <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-sm">
                            @foreach($vouchers as $voucher)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-gray-400">
                                        {{ $voucher->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="h-9 w-9 rounded-xl border flex items-center justify-center text-lg shadow-inner" 
                                                 style="background-color: {{ $voucher->theme_color }}1a; border-color: {{ $voucher->theme_color }}33; color: {{ $voucher->theme_color }}">
                                                <span class="flex items-center justify-center font-mono">
                                                    @if($voucher->icon === 'gift-outline') 🎁 
                                                    @elseif($voucher->icon === 'pricetag-outline') 🏷️ 
                                                    @elseif($voucher->icon === 'wallet-outline') 👛 
                                                    @elseif($voucher->icon === 'star-outline') ⭐ 
                                                    @else 🔥 
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="inline-flex items-center px-2 py-0.5 bg-gray-100 border border-gray-200 text-dark-900 rounded font-mono font-extrabold uppercase text-xs">
                                                    {{ $voucher->code }}
                                                </span>
                                                @if($voucher->badge_text)
                                                    <span class="text-[10px] font-bold mt-1 uppercase tracking-wider px-1.5 py-0.5 rounded" style="background-color: {{ $voucher->theme_color }}; color: white; width: fit-content;">
                                                        {{ $voucher->badge_text }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col space-y-1">
                                            <span class="font-bold text-dark-900">{{ $voucher->title }}</span>
                                            <span class="text-xs text-gray-400 max-w-xs truncate" title="{{ $voucher->description }}">{{ $voucher->description }}</span>
                                            
                                            <!-- Ketentuan Detail -->
                                            <div class="flex items-center gap-2 pt-1">
                                                <span class="text-[10px] font-semibold bg-gray-50 border border-gray-100 text-gray-500 px-1.5 py-0.5 rounded">
                                                    Min: Rp {{ number_format($voucher->min_transaction, 0, ',', '.') }}
                                                </span>
                                                @if($voucher->max_discount)
                                                    <span class="text-[10px] font-semibold bg-gray-50 border border-gray-100 text-gray-500 px-1.5 py-0.5 rounded">
                                                        Maks: Rp {{ number_format($voucher->max_discount, 0, ',', '.') }}
                                                    </span>
                                                @endif
                                                @if($voucher->is_new_user_only)
                                                    <span class="text-[10px] font-semibold bg-amber-50 border border-amber-100 text-amber-600 px-1.5 py-0.5 rounded">
                                                        New User Only
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-dark-900 whitespace-nowrap">
                                        @if($voucher->type === 'percentage')
                                            <span class="text-brand-600 font-extrabold text-base">{{ round($voucher->value) }}%</span>
                                        @else
                                            <span class="text-emerald-600 font-extrabold text-base">Rp {{ number_format($voucher->value, 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($voucher->expiry_date)->locale('id-ID')->isoFormat('D MMM YYYY, HH:mm') }}
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        @php
                                            $isExpired = \Carbon\Carbon::parse($voucher->expiry_date)->isPast();
                                            $statusClass = $isExpired 
                                                ? 'bg-rose-50 text-rose-700 border-rose-200' 
                                                : 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                            $statusText = $isExpired ? 'KEDALUWARSA' : 'AKTIF';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold rounded-lg border {{ $statusClass }} uppercase tracking-wider">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="/admin/vouchers/{{ $voucher->id }}/edit" class="inline-flex items-center justify-center px-3 py-1.5 bg-brand-500 hover:bg-brand-600 text-dark-900 text-xs font-bold rounded-lg transition-colors gap-1 shadow-sm border border-brand-500 cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </a>
                                            
                                            <form action="/admin/vouchers/{{ $voucher->id }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus voucher promo ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-lg border border-rose-150 transition-colors gap-1 cursor-pointer">
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
                <div class="px-6 py-4 border-t border-gray-50">
                    {{ $vouchers->links() }}
                </div>
            @endif
        </div>

    </div>
</div>

<!-- Flatpickr Script Initialization -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Sleek Flatpickr Date Picker (Modern Calendar popover)
        flatpickr("#expiry_date_day", {
            locale: "id",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d F Y", // e.g. "31 Mei 2026"
            minDate: "today",
            disableMobile: "true"
        });

        // 2. Sleek Flatpickr Time Picker (Modern Time scroll list popover)
        flatpickr("#expiry_time", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            disableMobile: "true"
        });

        // 3. Dynamic Visual Icon Selection Highlighting
        const radios = document.querySelectorAll('.icon-radio');
        radios.forEach(radio => {
            radio.addEventListener('change', function () {
                document.querySelectorAll('.icon-option').forEach(opt => {
                    opt.classList.remove('border-brand-500', 'bg-brand-50/50', 'ring-1', 'ring-brand-500');
                    opt.querySelector('.selected-dot').classList.add('opacity-0');
                });
                if (this.checked) {
                    const parent = this.closest('.icon-option');
                    parent.classList.add('border-brand-500', 'bg-brand-50/50', 'ring-1', 'ring-brand-500');
                    parent.querySelector('.selected-dot').classList.remove('opacity-0');
                }
            });
            
            if (radio.checked) {
                const parent = radio.closest('.icon-option');
                parent.classList.add('border-brand-500', 'bg-brand-50/50', 'ring-1', 'ring-brand-500');
                parent.querySelector('.selected-dot').classList.remove('opacity-0');
            }
        });
    });
</script>
@endsection
