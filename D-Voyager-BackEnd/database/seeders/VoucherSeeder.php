<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Voucher::updateOrCreate(
            ['code' => 'VOYAGERNEW'],
            [
                'title' => 'Diskon 25% Perjalanan Pertama',
                'description' => 'Gunakan kode voucher VOYAGERNEW saat transaksi untuk mendapatkan potongan 25% (maksimal Rp 50.000).',
                'type' => 'percentage',
                'value' => 25,
                'max_discount' => 50000,
                'min_transaction' => 0,
                'expiry_date' => '2026-12-31 23:59:59',
                'is_new_user_only' => true,
                'badge_text' => 'PENGGUNA BARU',
                'icon' => 'gift-outline',
                'theme_color' => '#FFC107'
            ]
        );

        \App\Models\Voucher::updateOrCreate(
            ['code' => 'VOYAGER10K'],
            [
                'title' => 'Diskon Rp 10.000 Rute Bebas',
                'description' => 'Gunakan kode voucher VOYAGER10K saat transaksi untuk mendapatkan potongan langsung Rp 10.000.',
                'type' => 'flat',
                'value' => 10000,
                'max_discount' => 10000,
                'min_transaction' => 10000,
                'expiry_date' => '2026-12-31 23:59:59',
                'is_new_user_only' => false,
                'badge_text' => 'E-WALLET',
                'icon' => 'wallet-outline',
                'theme_color' => '#8B5CF6'
            ]
        );
    }
}
