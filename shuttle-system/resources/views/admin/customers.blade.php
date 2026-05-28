@extends('admin.layout')

@section('content')
<div class="space-y-8 animate-fade-in">
    
    <!-- Header Title -->
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold font-outfit text-dark-900 leading-tight">Master Pelanggan (Customer)</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau dan kelola data pelanggan terdaftar. Anda dapat melakukan moderasi akun jika diperlukan.</p>
        </div>
        <div class="h-12 w-12 rounded-xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </div>
    </div>

    <!-- Stats summary for customers -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Pelanggan</div>
                <div class="text-3xl font-extrabold font-outfit text-dark-900">{{ $customers->total() }}</div>
                <div class="text-[10px] text-gray-500 font-medium">Terdaftar di database</div>
            </div>
            <div class="h-12 w-12 rounded-xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm col-span-2 flex items-center">
            <div class="flex items-center gap-3 text-sm text-gray-500">
                <div class="h-10 w-10 rounded-full bg-amber-50 flex items-center justify-center text-amber-600 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div>
                    <span class="font-bold text-dark-900 block">Kebijakan Moderasi Admin</span>
                    Sebagai administrator, Anda memegang hak untuk menyunting data atau menangguhkan akun pelanggan secara permanen jika terbukti melanggar aturan penyalahgunaan tiket atau penyebaran spam.
                </div>
            </div>
        </div>
    </div>

    <!-- Customers List Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-dark-900 font-outfit text-base">Daftar Pelanggan</h3>
                <p class="text-xs text-gray-400">Total terdaftar: {{ $customers->total() }} akun pelanggan.</p>
            </div>

            <!-- Action Bar -->
            <div class="flex items-center gap-2">
                <form id="bulkDeleteForm" action="{{ route('admin.customers.bulk-delete') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" onclick="return confirm('Hapus semua pelanggan terpilih (akun terkait juga akan terhapus)?')" class="inline-flex items-center justify-center px-3.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold rounded-lg border border-rose-150 transition-colors gap-1.5 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7M10 11v6m4-4v6M1 4h22M9 4v3" />
                        </svg>
                        Hapus Terpilih (<span id="selectedCount" class="font-extrabold text-brand-600">0</span>)
                    </button>
                </form>

                <form action="{{ route('admin.customers.delete-all') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus SEMUA pelanggan? Tindakan ini tidak bisa dibatalkan dan akan menghapus akun login mereka.')" class="inline-flex items-center justify-center px-3.5 py-1.5 bg-dark-900 hover:bg-[#A32A2A] text-white text-xs font-bold rounded-lg transition-colors gap-1.5 shadow-sm border border-dark-900">
                        Bersihkan Semua
                    </button>
                </form>
            </div>
        </div>

        @if($customers->isEmpty())
            <div class="flex flex-col items-center justify-center p-16 text-center space-y-3">
                <div class="h-16 w-16 rounded-full bg-gray-50 flex items-center justify-center text-gray-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1" />
                    </svg>
                </div>
                <div class="space-y-1">
                    <h4 class="text-sm font-semibold text-dark-900">Belum Ada Pelanggan</h4>
                    <p class="text-xs text-gray-400 max-w-xs">Data pelanggan akan otomatis bertambah ketika ada user yang melakukan registrasi di aplikasi.</p>
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
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Pelanggan</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Alamat Email</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider">Nomor HP</th>
                            <th class="px-6 py-3.5 text-xs font-bold text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 text-sm">
                        @foreach($customers as $c)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    <input form="bulkDeleteForm" type="checkbox" name="customer_ids[]" value="{{ $c->id }}" class="item-checkbox rounded text-brand-500 focus:ring-brand-500 h-4 w-4 border-gray-300">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-gray-400">
                                    {{ $c->id }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-dark-900 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-brand-500/10 text-brand-650 flex items-center justify-center font-bold text-xs uppercase">
                                            {{ substr($c->user->name ?? 'U', 0, 2) }}
                                        </div>
                                        <span>{{ $c->user->name ?? 'User Terhapus' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600 whitespace-nowrap font-medium">
                                    {{ $c->user->email ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-gray-600 whitespace-nowrap font-mono font-medium text-xs">
                                    {{ $c->user->phone ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="/admin/customers/{{ $c->id }}/edit" class="inline-flex items-center justify-center px-3 py-1.5 bg-brand-500 hover:bg-brand-600 text-dark-900 text-xs font-bold rounded-lg transition-colors gap-1 shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </a>
                                        
                                        <form action="/admin/customers/{{ $c->id }}" method="POST" onsubmit="return confirm('Hapus pelanggan ini secara permanen?')">
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
            <div class="px-6 py-4 border-t border-gray-50">
                {{ $customers->links() }}
            </div>
        @endif
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
