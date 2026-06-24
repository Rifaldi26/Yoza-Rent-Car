<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        // ── Privacy Policy ──────────────────────────────────────────────────

        Page::firstOrCreate(
            ['slug' => 'privacy'],
            [
                'title' => 'Pemberitahuan Privasi',
                'content' => json_encode([
                    'sections' => [
                        [
                            'title' => 'Pengumpulan Data Pribadi',
                            'intro' => 'Untuk menyediakan layanan penyewaan kendaraan yang aman dan terpercaya, kami mengumpulkan beberapa informasi pribadi Anda pada saat melakukan pemesanan, yang meliputi namun tidak terbatas pada:',
                            'items' => [
                                [
                                    'label' => 'Informasi Identitas',
                                    'text' => 'Kartu Tanda Penduduk (e-KTP).',
                                ],
                                [
                                    'label' => 'Informasi Pekerjaan/Pendidikan',
                                    'text' => 'Kartu Tanda Mahasiswa (KTM), Kartu Rencana Studi (KRS), ID Card Karyawan, atau Surat Tugas.',
                                ],
                                [
                                    'label' => 'Informasi Perjalanan',
                                    'text' => 'Tiket kedatangan transportasi umum (Kereta/Bus) dan bukti pemesanan hotel (khusus untuk pengguna dari luar kota).',
                                ],
                                [
                                    'label' => 'Informasi Kontak dan Lokasi',
                                    'text' => 'Nomor telepon aktif, alamat email, dan alamat pengiriman/penjemputan kendaraan.',
                                ],
                            ],
                        ],
                        [
                            'title' => 'Penggunaan Data Pribadi',
                            'intro' => 'Data pribadi yang Anda berikan akan digunakan secara khusus untuk keperluan berikut:',
                            'items' => [
                                [
                                    'label' => 'Verifikasi Identitas',
                                    'text' => 'Memastikan keabsahan profil penyewa untuk memitigasi risiko penipuan atau penggelapan armada kendaraan.',
                                ],
                                [
                                    'label' => 'Pelaksanaan Layanan',
                                    'text' => 'Memfasilitasi proses antar-jemput armada ke lokasi yang telah ditentukan.',
                                ],
                                [
                                    'label' => 'Komunikasi Layanan',
                                    'text' => 'Menghubungi Anda terkait status pemesanan, konfirmasi pembayaran, dan bantuan darurat selama masa sewa.',
                                ],
                            ],
                        ],
                        [
                            'title' => 'Keamanan dan Penyimpanan Data',
                            'intro' => '',
                            'items' => [
                                [
                                    'label' => '',
                                    'text' => 'Kami berkomitmen untuk melindungi dokumen jaminan fisik (seperti KTP atau STNK yang dititipkan) dan data digital Anda dengan standar keamanan yang ketat.',
                                ],
                                [
                                    'label' => '',
                                    'text' => 'Dokumen fisik akan dikembalikan kepada Anda segera setelah masa sewa berakhir dan kendaraan telah dikembalikan dalam kondisi baik.',
                                ],
                                [
                                    'label' => '',
                                    'text' => 'Kami tidak akan menjual, menyewakan, atau menukar data pribadi Anda kepada pihak ketiga untuk tujuan pemasaran tanpa persetujuan eksplisit dari Anda. Data hanya akan diserahkan kepada pihak berwajib (Kepolisian) apabila terjadi indikasi tindak pidana atau pelanggaran hukum selama masa sewa.',
                                ],
                            ],
                        ],
                    ],
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ]
        );

        // ── Terms & Conditions ──────────────────────────────────────────────

        Page::firstOrCreate(
            ['slug' => 'terms'],
            [
                'title' => 'Syarat dan Ketentuan Penggunaan',
                'content' => json_encode([
                    'sections' => [
                        [
                            'title' => 'Ketentuan Umum',
                            'intro' => '',
                            'items' => [
                                [
                                    'label' => '',
                                    'text' => 'Penyewa menyetujui bahwa harga sewa kendaraan yang tertera pada aplikasi DriveEase tidak termasuk biaya Bahan Bakar Minyak (BBM), tol, parkir, dan retribusi lainnya.',
                                ],
                                [
                                    'label' => '',
                                    'text' => 'Penyewa wajib mengembalikan kendaraan dengan kondisi BBM yang sama seperti saat awal serah terima.',
                                ],
                                [
                                    'label' => '',
                                    'text' => 'Penyewa bertanggung jawab penuh atas segala kerusakan, kehilangan, atau pelanggaran lalu lintas selama masa sewa kendaraan (untuk opsi Lepas Kunci).',
                                ],
                            ],
                        ],
                        [
                            'title' => 'Ketentuan Durasi dan Wilayah Pemakaian',
                            'intro' => '',
                            'items' => [
                                [
                                    'label' => 'Paket Sewa 12 Jam',
                                    'text' => 'Hanya berlaku pada hari kerja (Senin hingga Kamis) dan tidak berlaku pada akhir pekan (Jumat hingga Minggu) atau hari libur nasional. Area pemakaian dibatasi hanya untuk wilayah Banyumas, Purbalingga, Cilacap, Kebumen, Pemalang, dan Banjarnegara.',
                                ],
                                [
                                    'label' => 'Paket Sewa 24 Jam',
                                    'text' => 'Berlaku dengan batas maksimal area pemakaian meliputi seluruh wilayah Jawa Tengah dan Daerah Istimewa Yogyakarta.',
                                ],
                                [
                                    'label' => '',
                                    'text' => 'Pemakaian kendaraan dengan tujuan ke luar provinsi atau di luar batas area yang telah ditentukan wajib mengambil durasi pemakaian minimal 2 (dua) hari.',
                                ],
                            ],
                        ],
                        [
                            'title' => 'Syarat Penyewaan Lepas Kunci',
                            'intro' => '',
                            'items' => [
                                [
                                    'label' => 'Penyewa Domisili Lokal (Purwokerto & Purbalingga)',
                                    'text' => 'Wajib menyerahkan e-KTP asli. Kendaraan wajib diantar ke alamat rumah penyewa. Penyewa meminjamkan kendaraan roda dua (sepeda motor) beserta STNK asli kepada petugas kami untuk kemudahan mobilitas antar-jemput armada.',
                                ],
                                [
                                    'label' => 'Karyawan / Mahasiswa Lokal',
                                    'text' => 'Wajib melampirkan e-KTP asli, beserta KTM dan KRS aktif bagi mahasiswa, atau ID Card, Kartu Nama, maupun Surat Tugas untuk karyawan.',
                                ],
                                [
                                    'label' => 'Penyewa Pendatang (Luar Kota)',
                                    'text' => 'Wajib melampirkan e-KTP asli, KTM/KRS aktif atau ID Card karyawan, tiket kedatangan (Kereta, Bus, atau Pesawat), dan bukti pemesanan akomodasi (Booking Hotel).',
                                ],
                            ],
                        ],
                        [
                            'title' => 'Pengiriman dan Penjemputan Armada',
                            'intro' => '',
                            'items' => [
                                [
                                    'label' => '',
                                    'text' => 'Untuk penyewa domisili lokal, pengiriman armada ke rumah tidak dikenakan biaya tambahan (syarat jaminan motor dan STNK berlaku).',
                                ],
                                [
                                    'label' => '',
                                    'text' => 'Untuk penyewa luar kota, kendaraan dapat diantar dan dijemput di titik kedatangan (Stasiun Purwokerto, Terminal Bus Purwokerto/Purbalingga, atau Hotel) dengan biaya tambahan flat sebesar Rp 50.000 per transaksi sewa.',
                                ],
                            ],
                        ],
                    ],
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ]
        );
    }
}
