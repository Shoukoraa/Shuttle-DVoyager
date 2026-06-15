<?php

namespace Database\Seeders;

use App\Models\ChatbotCategory;
use App\Models\ChatbotProblem;
use Illuminate\Database\Seeder;

class ChatbotSeeder extends Seeder
{
    public function run(): void
    {
        ChatbotCategory::query()->delete();
        ChatbotProblem::query()->delete();

        $categories = [
            [
                'name' => 'Pembatalan & Refund',
                'icon' => 'close-circle-outline',
                'color' => '#ef4444',
                'sort_order' => 1,
                'problems' => [
                    [
                        'title' => 'Ingin Membatalkan Booking',
                        'solution_text' => "Kami akan menyambungkan Anda ke Customer Service untuk memproses pembatalan tiket.",
                        'additional_solution' => null,
                        'sort_order' => 1,
                    ],
                ],
            ],
            [
                'name' => 'Kendala Pembayaran',
                'icon' => 'card-outline',
                'color' => '#8b5cf6',
                'sort_order' => 2,
                'problems' => [
                    [
                        'title' => 'Pembayaran Berhasil Tapi Booking Belum Masuk',
                        'solution_text' => "Kami akan menyambungkan Anda ke Customer Service untuk memproses verifikasi transaksi pembayaran Anda.",
                        'additional_solution' => null,
                        'sort_order' => 1,
                    ],
                ],
            ],
            [
                'name' => 'Akun & Keamanan',
                'icon' => 'shield-half-outline',
                'color' => '#06b6d4',
                'sort_order' => 3,
                'problems' => [
                    [
                        'title' => 'Lupa Password',
                        'solution_text' => "Untuk mereset password akun Shuttle System Anda:\n\n1. Klik **\"Lupa Password?\"** di halaman Login aplikasi.\n2. Masukkan **alamat email** Anda yang terdaftar pada akun.\n3. Periksa email masuk Anda, dan klik **link reset password** yang dikirimkan.\n4. Masukkan password baru Anda, kemudian kembali login.",
                        'additional_solution' => null,
                        'sort_order' => 1,
                    ],
                ],
            ],
            [
                'name' => 'Barang Tertinggal',
                'icon' => 'archive-outline',
                'color' => '#10b981',
                'sort_order' => 4,
                'problems' => [
                    [
                        'title' => 'Saya Kehilangan Barang di Shuttle',
                        'solution_text' => "Kami akan menyambungkan Anda ke Customer Service untuk melacak barang berharga Anda yang tertinggal di armada shuttle.",
                        'additional_solution' => null,
                        'sort_order' => 1,
                    ],
                ],
            ],
            [
                'name' => 'Hubungi Customer Service',
                'icon' => 'headset-outline',
                'color' => '#3b82f6',
                'sort_order' => 5,
                'problems' => [],
            ],
        ];

        foreach ($categories as $catData) {
            $problems = $catData['problems'];
            unset($catData['problems']);

            $category = ChatbotCategory::create($catData);

            foreach ($problems as $problem) {
                $problem['category_id'] = $category->id;
                ChatbotProblem::create($problem);
            }
        }
    }
}
