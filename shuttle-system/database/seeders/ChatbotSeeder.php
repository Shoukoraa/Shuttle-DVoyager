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
                        'solution_text' => "Untuk membatalkan booking shuttle Anda, ikuti langkah berikut:\n\n1. Buka aplikasi Shuttle System dan masuk ke menu **\"Riwayat Booking\"**.\n2. Pilih booking perjalanan yang ingin Anda batalkan.\n3. Klik tombol **\"Batalkan Booking\"** pada rincian tiket.\n4. Tuliskan alasan pembatalan Anda, lalu lakukan konfirmasi.\n\n⚠️ Pembatalan dapat dilakukan maksimal **2 jam sebelum waktu keberangkatan**.",
                        'additional_solution' => "Jika tombol pembatalan tidak muncul, kemungkinan perjalanan Anda sudah di bawah 2 jam dari jadwal keberangkatan, atau status armada sudah mulai berjalan (On-going). Silakan hubungi live agent CS kami untuk bantuan lebih lanjut.",
                        'sort_order' => 1,
                    ],
                    [
                        'title' => 'Status Refund Belum Masuk',
                        'solution_text' => "Jika Anda telah membatalkan booking dan refund belum masuk:\n\n1. **Transfer Bank / Virtual Account**: Membutuhkan **3-5 hari kerja**.\n2. **E-Wallet (Dana/OVO/GoPay)**: Membutuhkan waktu **1-2 hari kerja**.\n3. **Kartu Kredit**: Membutuhkan waktu **7-14 hari kerja**.\n\nHari sabtu, minggu, dan hari libur nasional tidak dihitung sebagai hari kerja perbankan.",
                        'additional_solution' => "Apabila batas waktu di atas telah terlewati tetapi dana refund belum masuk ke rekening Anda, harap hubungi live agent kami dengan melampirkan **nomor invoice refund** dan **bukti rekening koran**.",
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'name' => 'Perubahan Jadwal & Data',
                'icon' => 'calendar-outline',
                'color' => '#f59e0b',
                'sort_order' => 2,
                'problems' => [
                    [
                        'title' => 'Ingin Mengubah Jadwal Shuttle',
                        'solution_text' => "Untuk melakukan Reschedule / Perubahan Jadwal shuttle Anda:\n\n1. Masuk ke halaman **\"Riwayat Booking\"** di aplikasi.\n2. Pilih tiket aktif Anda yang ingin diubah jadwalnya.\n3. Klik opsi **\"Ubah Jadwal\"**.\n4. Pilih tanggal, waktu, dan rute baru yang tersedia.\n5. Bayar biaya selisih harga tiket (jika ada) dan konfirmasi.\n\n⏰ Perubahan jadwal hanya dapat dilakukan **minimal 3 jam** sebelum keberangkatan.",
                        'additional_solution' => "Jika tombol Reschedule tidak aktif atau mengalami kegagalan, kuota untuk jadwal baru mungkin sudah penuh, atau waktu pengajuan sudah kurang dari 3 jam dari keberangkatan.",
                        'sort_order' => 1,
                    ],
                    [
                        'title' => 'Kesalahan Nama atau Data Penumpang',
                        'solution_text' => "Jika terdapat kesalahan ejaan nama, nomor telepon, atau email pada tiket Anda:\n\n1. Masuk ke halaman **\"Riwayat Booking\"**.\n2. Pilih tiket Anda dan klik **\"Edit Data Penumpang\"**.\n3. Koreksi kesalahan ejaan data secara detail sesuai KTP/identitas.\n4. Simpan perubahan Anda.\n\n✅ Koreksi data ejaan ini bersifat gratis dan tidak memotong biaya apa pun.",
                        'additional_solution' => "Perubahan nama penumpang secara penuh ke orang lain tidak diperbolehkan demi alasan keamanan manifes penumpang. Jika Anda ingin mengalihkan tiket, silakan batalkan tiket terlebih dahulu.",
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'name' => 'Kendala Pembayaran',
                'icon' => 'card-outline',
                'color' => '#8b5cf6',
                'sort_order' => 3,
                'problems' => [
                    [
                        'title' => 'Pembayaran Berhasil Tapi Booking Belum Masuk',
                        'solution_text' => "Jika saldo Anda telah terpotong namun status tiket masih pending / belum terbit:\n\n1. Tunggu **5-10 menit** agar sistem payment gateway memproses verifikasi.\n2. **Refresh** aplikasi Anda atau coba keluar akun dan login kembali.\n3. Periksa kotak masuk **Email** Anda untuk melihat apakah e-ticket PDF sudah terkirim secara otomatis.\n\n⚠️ Jangan melakukan transaksi ulang untuk menghindari pemotongan ganda.",
                        'additional_solution' => "Bila status tidak berubah setelah 15 menit, silakan hubungi CS kami dengan mengirimkan **bukti transfer berstempel berhasil** agar kami bantu terbitkan tiket secara manual.",
                        'sort_order' => 1,
                    ],
                    [
                        'title' => 'Kode Promo Tidak Bisa Digunakan',
                        'solution_text' => "Jika kode voucher atau promo Anda ditolak oleh sistem:\n\n1. **Cek syarat & ketentuan**: Beberapa promo memiliki batasan rute, minimum transaksi, atau tipe armada tertentu.\n2. **Cek masa berlaku**: Pastikan masa kedaluwarsa voucher belum lewat.\n3. **Cek kuota**: Beberapa promo memiliki kuota harian yang bisa habis di waktu tertentu.\n4. Perhatikan penulisan kode promo (sensitif terhadap huruf kapital dan angka).",
                        'additional_solution' => "Apabila syarat promo terpenuhi namun tetap error, Anda bisa mengirimkan screenshot kendala tersebut ke CS untuk kami verifikasi ke tim teknis.",
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'name' => 'Akun & Keamanan',
                'icon' => 'shield-half-outline',
                'color' => '#06b6d4',
                'sort_order' => 4,
                'problems' => [
                    [
                        'title' => 'Lupa Password',
                        'solution_text' => "Untuk mereset password akun Shuttle System Anda:\n\n1. Klik **\"Lupa Password?\"** di halaman Login aplikasi.\n2. Masukkan **alamat email** Anda yang terdaftar pada akun.\n3. Periksa email masuk (atau folder spam) Anda, dan klik **link reset password** yang dikirimkan.\n4. Masukkan password baru Anda (minimal 8 karakter dengan kombinasi angka).\n5. Kembali ke aplikasi dan login menggunakan password baru Anda.",
                        'additional_solution' => "Link reset password hanya berlaku selama **15 menit** demi keamanan akun Anda. Jika kadaluwarsa, silakan ajukan ulang kembali.",
                        'sort_order' => 1,
                    ],
                    [
                        'title' => 'OTP Tidak Masuk',
                        'solution_text' => "Jika Anda tidak menerima kode verifikasi OTP via SMS/Email:\n\n1. Pastikan nomor HP aktif, memiliki sinyal seluler yang baik, dan dapat menerima SMS.\n2. Cek apakah kotak masuk email Anda penuh atau masuk ke folder **Promosi / Spam**.\n3. Tunggu hingga penghitung waktu mundur habis (**60 detik**), lalu klik **\"Kirim Ulang OTP\"**.\n4. Hindari meminta OTP berkali-kali dalam waktu singkat agar tidak dianggap spam.",
                        'additional_solution' => "Bila OTP tetap tidak masuk setelah 3 kali percobaan kirim ulang, kemungkinan provider seluler Anda sedang mengalami gangguan pengiriman pesan OTP massal. Harap hubungi live agent CS kami untuk verifikasi manual.",
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'name' => 'Dokumen Perjalanan',
                'icon' => 'document-text-outline',
                'color' => '#10b981',
                'sort_order' => 5,
                'problems' => [
                    [
                        'title' => 'E-Ticket Belum Muncul',
                        'solution_text' => "Jika transaksi Anda lunas namun e-ticket belum muncul di aplikasi:\n\n1. Masuk ke tab **\"Riwayat Booking\"** dan lakukan geser ke bawah (*pull-to-refresh*) untuk menyegarkan data.\n2. Cek apakah Anda menerima **Email E-Ticket** dengan file lampiran PDF.\n3. Pastikan pembayaran Anda benar-benar sukses dan tidak dibatalkan otomatis karena melebihi batas waktu bayar.",
                        'additional_solution' => "Manifes driver akan terupdate otomatis. Namun demi kenyamanan, Anda dapat meminta live agent menerbitkan tiket manual dalam hitungan menit.",
                        'sort_order' => 1,
                    ],
                    [
                        'title' => 'Minta Invoice atau Bukti Pembayaran',
                        'solution_text' => "Untuk mengunduh invoice resmi perjalanan Anda:\n\n1. Masuk ke menu **\"Riwayat Booking\"** di aplikasi.\n2. Pilih transaksi perjalanan yang telah selesai.\n3. Ketuk opsi **\"Lihat Invoice\"** atau **\"Unduh Bukti Pembayaran\"**.\n4. File akan otomatis tersimpan di memori ponsel Anda dalam format **PDF** resmi.",
                        'additional_solution' => "Invoice ini sah digunakan sebagai bukti pembayaran perjalanan bisnis Anda (*reimbursement*). Jika Anda memerlukan cap basah digital dari kantor pusat kami, hubungi CS kami.",
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'name' => 'Tracking Shuttle',
                'icon' => 'location-outline',
                'color' => '#3b82f6',
                'sort_order' => 6,
                'problems' => [
                    [
                        'title' => 'Lokasi Shuttle Tidak Bergerak',
                        'solution_text' => "Jika peta pelacakan tracking shuttle menunjukkan lokasi armada tidak update / tidak bergerak:\n\n1. Sistem tracking GPS diperbarui setiap **30 detik** secara berkala.\n2. Pastikan koneksi internet ponsel Anda berjalan dengan stabil.\n3. Driver mungkin sedang mengalami kemacetan lalu lintas parah, atau sedang berhenti di tempat istirahat (Rest Area) resmi.\n\n⚠️ Fitur tracking GPS di aplikasi hanya aktif sejak **30 menit sebelum** jam keberangkatan.",
                        'additional_solution' => "Jika lokasi stuck lebih dari 10 menit setelah jam keberangkatan dimulai, silakan hubungi CS live chat agar kami langsung melacak armada melalui GPS pusat perusahaan.",
                        'sort_order' => 1,
                    ],
                    [
                        'title' => 'Driver Belum Memulai Perjalanan',
                        'solution_text' => "Bila jam keberangkatan telah lewat namun trip belum dimulai di aplikasi:\n\n1. Driver mungkin sedang memuat barang bawaan penumpang ke bagasi atau membantu penumpang lain naik.\n2. Anda dapat menghubungi driver secara langsung melalui tombol **\"Hubungi Driver\"** (aktif H-30 menit keberangkatan).\n3. Driver diwajibkan melakukan konfirmasi keberangkatan di sistem sebelum roda berputar.",
                        'additional_solution' => "Jika driver tidak dapat dihubungi dan keberangkatan terlambat lebih dari **15 menit** dari jadwal tanpa konfirmasi, hubungi CS segera agar kami dapat melakukan intervensi armada pengganti.",
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'name' => 'Seat & Booking',
                'icon' => 'bus-outline',
                'color' => '#f97316',
                'sort_order' => 7,
                'problems' => [
                    [
                        'title' => 'Tidak Bisa Memilih Seat',
                        'solution_text' => "Jika Anda tidak bisa memilih atau merubah kursi yang diinginkan:\n\n1. Kursi bertanda **abu-abu** menandakan kursi tersebut telah dipesan oleh penumpang lain.\n2. Pemilihan kursi mandiri hanya bisa dilakukan maksimal **1 jam sebelum** keberangkatan.\n3. Cek kembali status tiket Anda, pastikan berstatus terbayar dan aktif.",
                        'additional_solution' => "Jika denah kursi tidak termuat karena kendala teknis aplikasi, hubungi CS kami dengan menyebutkan nomor kursi yang Anda inginkan (jika masih kosong) agar kami alokasikan manual melalui sistem.",
                        'sort_order' => 1,
                    ],
                    [
                        'title' => 'Seat Sudah Penuh',
                        'solution_text' => "Jika seluruh armada pada jam keberangkatan yang Anda pilih sudah terisi penuh:\n\n1. Silakan cari jadwal keberangkatan di jam sebelum atau sesudahnya.\n2. Gunakan fitur **\"Ingatkan Saya\"** untuk mendapatkan notifikasi instan apabila ada penumpang lain yang membatalkan pesanan mereka.\n3. Rata-rata terdapat pembatalan reservasi kursi sekitar 10% menjelang hari keberangkatan.",
                        'additional_solution' => "Pada musim ramai (*high season*), perusahaan biasanya akan menambah unit armada tambahan. Silakan cek menu pemesanan secara berkala atau hubungi CS.",
                        'sort_order' => 2,
                    ],
                ],
            ],
            [
                'name' => 'Barang Tertinggal',
                'icon' => 'archive-outline',
                'color' => '#10b981',
                'sort_order' => 8,
                'problems' => [
                    [
                        'title' => ' Saya Kehilangan Barang di Shuttle',
                        'solution_text' => "Jika barang berharga Anda tertinggal di dalam armada shuttle kami:\n\n1. Siapkan **nomor booking**, rincian rute, dan jam keberangkatan Anda.\n2. Rincikan deskripsi barang yang hilang secara detail (misal: Tas punggung warna hitam merk X).\n3. Segera laporkan hal ini kepada CS kami agar kami bisa langsung menahan armada dan menghubungi driver sebelum penumpang trip berikutnya naik.",
                        'additional_solution' => "Kecepatan Anda melapor sangat menentukan. Barang temuan yang berhasil diamankan akan disimpan di kantor Pool tujuan akhir selama maksimal 30 hari untuk Anda ambil.",
                        'sort_order' => 1,
                    ],
                    [
                        'title' => 'Cara Menghubungi Driver',
                        'solution_text' => "Untuk menghubungi driver yang membawa perjalanan Anda:\n\n1. Tombol telepon/chat driver akan aktif di aplikasi mulai **30 menit sebelum keberangkatan** hingga **30 menit setelah perjalanan selesai**.\n2. Buka tiket aktif Anda di menu **\"Riwayat Booking\"**.\n3. Ketuk ikon telepon / pesan di dekat nama driver Anda.\n\n🔐 Kontak langsung dinonaktifkan demi privasi driver setelah batas waktu 30 menit pasca trip selesai.",
                        'additional_solution' => "Jika Anda butuh menghubungi driver di luar waktu aktif tersebut (misal terkait barang tertinggal), Anda harus menghubungi live agent CS kami untuk menjembatani komunikasi.",
                        'sort_order' => 2,
                    ],
                ],
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
