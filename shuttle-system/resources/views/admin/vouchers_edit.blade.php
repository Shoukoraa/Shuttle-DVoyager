@extends('admin.layout')

@section('content')
<!-- Flatpickr CSS & Fonts -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<div class="max-w-xl mx-auto space-y-8 animate-fade-in">
    
    <!-- Title Section -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold font-outfit text-dark-900 leading-tight">Edit Voucher Promo</h1>
            <p class="text-sm text-gray-500 mt-1">Ubah data ketentuan, tipe diskon, minimal transaksi, atau masa berlaku kupon.</p>
        </div>
        <div class="h-12 w-12 rounded-xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
        </div>
    </div>

    <!-- Edit Form Card -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <form action="/admin/vouchers/{{ $voucher->id }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="flex flex-col gap-1.5">
                <label for="code" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Kode Voucher</label>
                <input type="text" name="code" id="code" placeholder="Kode Voucher" value="{{ $voucher->code }}" required style="text-transform: uppercase;" class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-semibold font-mono uppercase">
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="title" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Nama / Judul Voucher</label>
                <input type="text" name="title" id="title" placeholder="Nama Voucher" value="{{ $voucher->title }}" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="description" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Deskripsi Voucher</label>
                <textarea name="description" id="description" rows="3" placeholder="Deskripsi Voucher" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-medium">{{ $voucher->description }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label for="type" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tipe Diskon</label>
                    <div class="relative">
                        <select name="type" id="type" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none appearance-none cursor-pointer transition-all font-semibold">
                            <option value="percentage" {{ $voucher->type == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                            <option value="flat" {{ $voucher->type == 'flat' ? 'selected' : '' }}>Potongan Flat (Rp)</option>
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
                    <input type="number" step="0.01" name="value" id="value" placeholder="Nilai Diskon" value="{{ $voucher->value }}" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-semibold">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label for="max_discount" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Maks Diskon (Rp)</label>
                    <input type="number" step="0.01" name="max_discount" id="max_discount" placeholder="Batas Maks Potongan" value="{{ $voucher->max_discount }}" class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-semibold">
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="min_transaction" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Min Transaksi (Rp)</label>
                    <input type="number" step="0.01" name="min_transaction" id="min_transaction" placeholder="Min Transaksi" value="{{ $voucher->min_transaction }}" class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-semibold">
                </div>
            </div>

            <!-- Tanggal & Jam Baru (Keren & Layout Fit) -->
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5 relative">
                    <label for="expiry_date_day" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Kedaluwarsa</label>
                    <div class="relative">
                        <input type="text" name="expiry_date_day" id="expiry_date_day" value="{{ \Carbon\Carbon::parse($voucher->expiry_date)->format('Y-m-d') }}" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl pl-4 pr-10 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-semibold cursor-pointer">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">📅</span>
                    </div>
                </div>

                <div class="flex flex-col gap-1.5 relative">
                    <label for="expiry_time" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Jam Kedaluwarsa</label>
                    <div class="relative">
                        <input type="text" name="expiry_time" id="expiry_time" value="{{ \Carbon\Carbon::parse($voucher->expiry_date)->format('H:i') }}" required class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl pl-4 pr-10 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-semibold cursor-pointer">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">🕒</span>
                    </div>
                </div>
            </div>

            <!-- Desain Input Badge & Pilihan Icon Baru -->
            <div class="flex flex-col gap-3">
                <div class="flex flex-col gap-1.5">
                    <label for="badge_text" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Teks Badge</label>
                    <input type="text" name="badge_text" id="badge_text" placeholder="Badge Teks" value="{{ $voucher->badge_text }}" class="w-full bg-gray-50 border border-gray-200 text-dark-900 text-sm rounded-xl px-4 py-2.5 focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none transition-all font-semibold">
                </div>

                <!-- Visual Icon Selection (Pilihan Icon Keren) -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Pilih Icon Promo</label>
                    <div class="grid grid-cols-5 gap-2" id="icon-selector-grid">
                        
                        <!-- Gift Option -->
                        <label class="icon-option cursor-pointer group flex flex-col items-center justify-center p-2 rounded-xl border border-gray-200 bg-gray-50 hover:bg-brand-50 hover:border-brand-300 transition-all relative">
                            <input type="radio" name="icon" value="gift-outline" {{ $voucher->icon === 'gift-outline' ? 'checked' : '' }} class="hidden icon-radio">
                            <span class="text-xl">🎁</span>
                            <span class="text-[9px] font-bold text-gray-400 group-hover:text-brand-600 mt-1">Hadiah</span>
                            <div class="selected-dot absolute top-1 right-1 w-1.5 h-1.5 rounded-full bg-brand-500 opacity-0 transition-opacity"></div>
                        </label>
                        
                        <!-- Pricetag Option -->
                        <label class="icon-option cursor-pointer group flex flex-col items-center justify-center p-2 rounded-xl border border-gray-200 bg-gray-50 hover:bg-brand-50 hover:border-brand-300 transition-all relative">
                            <input type="radio" name="icon" value="pricetag-outline" {{ $voucher->icon === 'pricetag-outline' ? 'checked' : '' }} class="hidden icon-radio">
                            <span class="text-xl">🏷️</span>
                            <span class="text-[9px] font-bold text-gray-400 group-hover:text-brand-600 mt-1">Promo</span>
                            <div class="selected-dot absolute top-1 right-1 w-1.5 h-1.5 rounded-full bg-brand-500 opacity-0 transition-opacity"></div>
                        </label>

                        <!-- Wallet Option -->
                        <label class="icon-option cursor-pointer group flex flex-col items-center justify-center p-2 rounded-xl border border-gray-200 bg-gray-50 hover:bg-brand-50 hover:border-brand-300 transition-all relative">
                            <input type="radio" name="icon" value="wallet-outline" {{ $voucher->icon === 'wallet-outline' ? 'checked' : '' }} class="hidden icon-radio">
                            <span class="text-xl">👛</span>
                            <span class="text-[9px] font-bold text-gray-400 group-hover:text-brand-600 mt-1">Dompet</span>
                            <div class="selected-dot absolute top-1 right-1 w-1.5 h-1.5 rounded-full bg-brand-500 opacity-0 transition-opacity"></div>
                        </label>

                        <!-- Star Option -->
                        <label class="icon-option cursor-pointer group flex flex-col items-center justify-center p-2 rounded-xl border border-gray-200 bg-gray-50 hover:bg-brand-50 hover:border-brand-300 transition-all relative">
                            <input type="radio" name="icon" value="star-outline" {{ $voucher->icon === 'star-outline' ? 'checked' : '' }} class="hidden icon-radio">
                            <span class="text-xl">⭐</span>
                            <span class="text-[9px] font-bold text-gray-400 group-hover:text-brand-600 mt-1">Spesial</span>
                            <div class="selected-dot absolute top-1 right-1 w-1.5 h-1.5 rounded-full bg-brand-500 opacity-0 transition-opacity"></div>
                        </label>

                        <!-- Flame Option -->
                        <label class="icon-option cursor-pointer group flex flex-col items-center justify-center p-2 rounded-xl border border-gray-200 bg-gray-50 hover:bg-brand-50 hover:border-brand-300 transition-all relative">
                            <input type="radio" name="icon" value="flame-outline" {{ $voucher->icon === 'flame-outline' ? 'checked' : '' }} class="hidden icon-radio">
                            <span class="text-xl">🔥</span>
                            <span class="text-[9px] font-bold text-gray-400 group-hover:text-brand-600 mt-1">Hot</span>
                            <div class="selected-dot absolute top-1 right-1 w-1.5 h-1.5 rounded-full bg-brand-500 opacity-0 transition-opacity"></div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between border-t border-gray-50 pt-4">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_new_user_only" id="is_new_user_only" value="1" {{ $voucher->is_new_user_only ? 'checked' : '' }} class="rounded text-brand-500 focus:ring-brand-500 h-4.5 w-4.5 border-gray-300 cursor-pointer">
                    <label for="is_new_user_only" class="text-xs font-bold text-gray-600 uppercase tracking-wide cursor-pointer select-none">Khusus Pengguna Baru</label>
                </div>

                <div class="flex items-center gap-2">
                    <label for="theme_color" class="text-xs font-bold text-gray-500 uppercase tracking-wider">Hex Tema</label>
                    <input type="color" name="theme_color" id="theme_color" value="{{ $voucher->theme_color ?? '#FFC107' }}" class="h-8 w-12 border-0 bg-transparent rounded-lg cursor-pointer">
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-6 py-2.5 bg-dark-900 hover:bg-dark-850 text-white font-semibold text-sm rounded-xl border border-dark-900 shadow-sm transition-all gap-2 cursor-pointer">
                    <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update Voucher
                </button>
                
                <a href="/admin/vouchers" class="inline-flex items-center justify-center px-6 py-2.5 bg-white hover:bg-gray-50 text-dark-900 font-semibold text-sm rounded-xl border border-gray-200 shadow-sm transition-all cursor-pointer">
                    Batal
                </a>
            </div>

        </form>
    </div>

</div>

<!-- Flatpickr Script Initialization -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Sleek Flatpickr Date Picker
        flatpickr("#expiry_date_day", {
            locale: "id",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d F Y", // e.g. "31 Mei 2026"
            minDate: "today",
            disableMobile: "true"
        });

        // 2. Sleek Flatpickr Time Picker
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
