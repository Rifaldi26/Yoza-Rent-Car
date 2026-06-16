<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * DummyActivitySeeder
 *
 * Mengisi data aktivitas realistis rentang 11–26 Juni 2026 meliputi:
 *   - 5 user pelanggan
 *   - 15 pemesanan (mix harian & 12 jam, berbagai status)
 *   - Payments terkait
 *   - Journal entries untuk pemesanan yang selesai
 *   - Notifikasi per event
 *   - Chat antar user & admin
 *   - Favorit
 *
 * Jalankan SETELAH: UserSeeder, AccountSeeder, MobilSeeder
 */
class DummyActivitySeeder extends Seeder
{
    // ── ID referensi setelah insert ───────────────────────────────────────────
    private int $adminId;
    private array $userIds   = [];
    private array $mobilIds  = [];
    private array $accountIds = [];

    public function run(): void
    {
        $this->seedUsers();
        $this->resolveRefs();
        $this->seedFavorits();
        $this->seedPemesanan();
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function seedUsers(): void
    {
        $users = [
            [
                'name'              => 'Rina Marlina',
                'email'             => 'rina.marlina@gmail.com',
                'no_hp'             => '081211112222',
                'password'          => Hash::make('user123'),
                'role'              => 'user',
                'email_verified_at' => '2026-06-01 08:00:00',
                'created_at'        => '2026-06-01 08:00:00',
                'updated_at'        => '2026-06-01 08:00:00',
            ],
            [
                'name'              => 'Doni Prasetyo',
                'email'             => 'doni.prasetyo@gmail.com',
                'no_hp'             => '081322223333',
                'password'          => Hash::make('user123'),
                'role'              => 'user',
                'email_verified_at' => '2026-06-03 10:00:00',
                'created_at'        => '2026-06-03 10:00:00',
                'updated_at'        => '2026-06-03 10:00:00',
            ],
            [
                'name'              => 'Siti Rahayu',
                'email'             => 'siti.rahayu@gmail.com',
                'no_hp'             => '082133334444',
                'password'          => Hash::make('user123'),
                'role'              => 'user',
                'email_verified_at' => '2026-06-05 09:00:00',
                'created_at'        => '2026-06-05 09:00:00',
                'updated_at'        => '2026-06-05 09:00:00',
            ],
            [
                'name'              => 'Bagas Firmansyah',
                'email'             => 'bagas.firmansyah@gmail.com',
                'no_hp'             => '083144445555',
                'password'          => Hash::make('user123'),
                'role'              => 'user',
                'email_verified_at' => '2026-06-07 14:00:00',
                'created_at'        => '2026-06-07 14:00:00',
                'updated_at'        => '2026-06-07 14:00:00',
            ],
            [
                'name'              => 'Dewi Anggraini',
                'email'             => 'dewi.anggraini@gmail.com',
                'no_hp'             => '085155556666',
                'password'          => Hash::make('user123'),
                'role'              => 'user',
                'email_verified_at' => '2026-06-08 11:00:00',
                'created_at'        => '2026-06-08 11:00:00',
                'updated_at'        => '2026-06-08 11:00:00',
            ],
        ];

        foreach ($users as $u) {
            DB::table('users')->insertOrIgnore($u);
        }
    }

    private function resolveRefs(): void
    {
        $this->adminId = DB::table('users')->where('role', 'admin')->value('id');

        $this->userIds = DB::table('users')
            ->where('role', 'user')
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        $this->mobilIds = DB::table('mobils')
            ->orderBy('harga_per_hari')
            ->pluck('id')
            ->toArray();

        $this->accountIds = DB::table('accounts')
            ->pluck('id', 'kode')
            ->toArray();
    }

    private function seedFavorits(): void
    {
        $favorits = [
            [$this->userIds[0], $this->mobilIds[4]], // Rina suka Xenia 2022
            [$this->userIds[0], $this->mobilIds[6]], // Rina suka Xpander
            [$this->userIds[1], $this->mobilIds[6]], // Doni suka Xpander
            [$this->userIds[2], $this->mobilIds[3]], // Siti suka Brio
            [$this->userIds[3], $this->mobilIds[5]], // Bagas suka Xenia 2023
            [$this->userIds[4], $this->mobilIds[0]], // Dewi suka Agya
        ];

        foreach ($favorits as [$uid, $mid]) {
            DB::table('favorits')->insertOrIgnore([
                'user_id'    => $uid,
                'mobil_id'   => $mid,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedPemesanan(): void
    {
        /**
         * Definisi 15 pemesanan. Setiap entry menggambarkan satu skenario
         * berbeda (status, tipe, mobil, user, supir) agar data terasa hidup.
         *
         * Kolom:
         *   user_idx      → index ke $this->userIds[]
         *   mobil_idx     → index ke $this->mobilIds[]
         *   mulai / selesai → tanggal string
         *   tipe          → harian | 12_jam
         *   waktu_mulai   → HH:MM (hanya untuk 12_jam)
         *   supir         → true/false
         *   catatan       → nullable string
         *   status        → pending|menunggu_konfirmasi_admin|dikonfirmasi|selesai|dibatalkan|kadaluarsa
         *   metode_bayar  → cash|transfer|qris|edc|null
         *   book_at       → waktu pemesanan dibuat
         */
        $defs = [
            // ── SELESAI ────────────────────────────────────────────────────
            [
                'user_idx'    => 0, // Rina
                'mobil_idx'   => 0, // Agya TRD 300rb
                'mulai'       => '2026-06-11',
                'selesai'     => '2026-06-13',
                'tipe'        => 'harian',
                'waktu_mulai' => null,
                'supir'       => true,
                'catatan'     => null,
                'status'      => 'selesai',
                'metode_bayar'=> 'transfer',
                'book_at'     => '2026-06-10 19:30:00',
            ],
            [
                'user_idx'    => 1, // Doni
                'mobil_idx'   => 3, // All New Brio 350rb
                'mulai'       => '2026-06-12',
                'selesai'     => '2026-06-14',
                'tipe'        => 'harian',
                'waktu_mulai' => null,
                'supir'       => false,
                'catatan'     => 'Tolong siapkan kendaraan pagi-pagi.',
                'status'      => 'selesai',
                'metode_bayar'=> 'cash',
                'book_at'     => '2026-06-11 08:15:00',
            ],
            [
                'user_idx'    => 2, // Siti
                'mobil_idx'   => 4, // Xenia 2022 400rb
                'mulai'       => '2026-06-13',
                'selesai'     => '2026-06-15',
                'tipe'        => 'harian',
                'waktu_mulai' => null,
                'supir'       => true,
                'catatan'     => null,
                'status'      => 'selesai',
                'metode_bayar'=> 'qris',
                'book_at'     => '2026-06-12 14:00:00',
            ],
            [
                'user_idx'    => 3, // Bagas
                'mobil_idx'   => 6, // Xpander 450rb
                'mulai'       => '2026-06-14',
                'selesai'     => '2026-06-16',
                'tipe'        => 'harian',
                'waktu_mulai' => null,
                'supir'       => true,
                'catatan'     => 'Perjalanan ke Yogyakarta.',
                'status'      => 'selesai',
                'metode_bayar'=> 'transfer',
                'book_at'     => '2026-06-13 20:00:00',
            ],
            [
                'user_idx'    => 0, // Rina
                'mobil_idx'   => 2, // Calya 300rb
                'mulai'       => '2026-06-16',
                'selesai'     => '2026-06-16',
                'tipe'        => '12_jam',
                'waktu_mulai' => '08:00',
                'supir'       => false,
                'catatan'     => null,
                'status'      => 'selesai',
                'metode_bayar'=> 'cash',
                'book_at'     => '2026-06-15 21:00:00',
            ],
            [
                'user_idx'    => 4, // Dewi
                'mobil_idx'   => 1, // Ayla 300rb
                'mulai'       => '2026-06-17',
                'selesai'     => '2026-06-19',
                'tipe'        => 'harian',
                'waktu_mulai' => null,
                'supir'       => false,
                'catatan'     => null,
                'status'      => 'selesai',
                'metode_bayar'=> 'edc',
                'book_at'     => '2026-06-16 10:00:00',
            ],
            // ── DIKONFIRMASI (sedang berjalan) ─────────────────────────────
            [
                'user_idx'    => 1, // Doni
                'mobil_idx'   => 5, // Xenia 2023 400rb
                'mulai'       => '2026-06-20',
                'selesai'     => '2026-06-23',
                'tipe'        => 'harian',
                'waktu_mulai' => null,
                'supir'       => true,
                'catatan'     => 'Butuh kursi bayi kalau ada.',
                'status'      => 'dikonfirmasi',
                'metode_bayar'=> 'transfer',
                'book_at'     => '2026-06-19 09:00:00',
            ],
            [
                'user_idx'    => 2, // Siti
                'mobil_idx'   => 6, // Xpander 450rb
                'mulai'       => '2026-06-21',
                'selesai'     => '2026-06-24',
                'tipe'        => 'harian',
                'waktu_mulai' => null,
                'supir'       => true,
                'catatan'     => null,
                'status'      => 'dikonfirmasi',
                'metode_bayar'=> 'qris',
                'book_at'     => '2026-06-20 15:30:00',
            ],
            [
                'user_idx'    => 3, // Bagas
                'mobil_idx'   => 2, // Calya 300rb
                'mulai'       => '2026-06-23',
                'selesai'     => '2026-06-23',
                'tipe'        => '12_jam',
                'waktu_mulai' => '07:00',
                'supir'       => false,
                'catatan'     => null,
                'status'      => 'dikonfirmasi',
                'metode_bayar'=> 'cash',
                'book_at'     => '2026-06-22 20:00:00',
            ],
            // ── MENUNGGU KONFIRMASI ADMIN ──────────────────────────────────
            [
                'user_idx'    => 4, // Dewi
                'mobil_idx'   => 3, // Brio 350rb
                'mulai'       => '2026-06-24',
                'selesai'     => '2026-06-25',
                'tipe'        => 'harian',
                'waktu_mulai' => null,
                'supir'       => false,
                'catatan'     => null,
                'status'      => 'menunggu_konfirmasi_admin',
                'metode_bayar'=> 'transfer',
                'book_at'     => '2026-06-23 11:00:00',
            ],
            [
                'user_idx'    => 0, // Rina
                'mobil_idx'   => 4, // Xenia 2022 400rb
                'mulai'       => '2026-06-25',
                'selesai'     => '2026-06-27',
                'tipe'        => 'harian',
                'waktu_mulai' => null,
                'supir'       => true,
                'catatan'     => 'Antar jemput stasiun.',
                'status'      => 'menunggu_konfirmasi_admin',
                'metode_bayar'=> 'cash',
                'book_at'     => '2026-06-24 08:00:00',
            ],
            // ── PENDING (belum bayar) ──────────────────────────────────────
            [
                'user_idx'    => 1, // Doni
                'mobil_idx'   => 0, // Agya TRD 300rb
                'mulai'       => '2026-06-26',
                'selesai'     => '2026-06-26',
                'tipe'        => '12_jam',
                'waktu_mulai' => '09:00',
                'supir'       => false,
                'catatan'     => null,
                'status'      => 'pending',
                'metode_bayar'=> null,
                'book_at'     => '2026-06-25 19:00:00',
            ],
            // ── DIBATALKAN ────────────────────────────────────────────────
            [
                'user_idx'    => 2, // Siti
                'mobil_idx'   => 1, // Ayla 300rb
                'mulai'       => '2026-06-18',
                'selesai'     => '2026-06-19',
                'tipe'        => 'harian',
                'waktu_mulai' => null,
                'supir'       => false,
                'catatan'     => 'Batal karena ada keperluan mendadak.',
                'status'      => 'dibatalkan',
                'metode_bayar'=> null,
                'book_at'     => '2026-06-17 13:00:00',
            ],
            // ── KADALUARSA ────────────────────────────────────────────────
            [
                'user_idx'    => 3, // Bagas
                'mobil_idx'   => 3, // Brio 350rb
                'mulai'       => '2026-06-19',
                'selesai'     => '2026-06-20',
                'tipe'        => 'harian',
                'waktu_mulai' => null,
                'supir'       => false,
                'catatan'     => null,
                'status'      => 'kadaluarsa',
                'metode_bayar'=> null,
                'book_at'     => '2026-06-18 22:00:00',
            ],
            [
                'user_idx'    => 4, // Dewi
                'mobil_idx'   => 0, // Agya TRD 300rb
                'mulai'       => '2026-06-22',
                'selesai'     => '2026-06-22',
                'tipe'        => '12_jam',
                'waktu_mulai' => '13:00',
                'supir'       => false,
                'catatan'     => null,
                'status'      => 'kadaluarsa',
                'metode_bayar'=> null,
                'book_at'     => '2026-06-21 18:00:00',
            ],
        ];

        foreach ($defs as $def) {
            $this->insertPemesanan($def);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function insertPemesanan(array $def): void
    {
        $userId  = $this->userIds[$def['user_idx']];
        $mobilId = $this->mobilIds[$def['mobil_idx']];
        $mobil   = DB::table('mobils')->find($mobilId);
        $user    = DB::table('users')->find($userId);

        $adalah12Jam = $def['tipe'] === '12_jam';
        $mulai       = $def['mulai'];
        $selesai     = $def['selesai'];
        $bookAt      = $def['book_at'];
        $status      = $def['status'];

        // ── Hitung harga ──────────────────────────────────────────────────
        if ($adalah12Jam) {
            $hargaSewa  = $mobil->harga_per_hari / 2;
            $durasi     = 0;
        } else {
            $durasi    = (int) (strtotime($selesai) - strtotime($mulai)) / 86400;
            $hargaSewa = $mobil->harga_per_hari * $durasi;
        }

        $biayaSupir = 0;
        if ($def['supir'] && $mobil->biaya_supir_per_hari) {
            $biayaSupir = $mobil->biaya_supir_per_hari * max($durasi, 1);
        }

        $totalHarga = $hargaSewa + $biayaSupir;

        // ── Insert pemesanan ──────────────────────────────────────────────
        $pemesananId = DB::table('pemesanans')->insertGetId([
            'user_id'         => $userId,
            'mobil_id'        => $mobilId,
            'tanggal_mulai'   => $mulai,
            'tanggal_selesai' => $selesai,
            'waktu_mulai'     => $def['waktu_mulai'] ? $def['waktu_mulai'] . ':00' : null,
            'tipe_sewa'       => $def['tipe'],
            'opsi_supir'      => $def['supir'],
            'biaya_supir'     => $def['supir'] ? $biayaSupir : null,
            'total_harga'     => $totalHarga,
            'status'          => $status,
            'catatan'         => $def['catatan'],
            'created_at'      => $bookAt,
            'updated_at'      => $bookAt,
        ]);

        // ── Notifikasi: pemesanan dibuat ──────────────────────────────────
        $this->notif($userId, 'Pemesanan Dibuat',
            "Pemesanan #{$pemesananId} untuk {$mobil->nama} berhasil dibuat. Silakan selesaikan pembayaran.",
            'info', $bookAt);

        // ── Alur lanjutan berdasarkan status ──────────────────────────────
        match ($status) {
            'menunggu_konfirmasi_admin' => $this->alurMenungguKonfirmasi($pemesananId, $userId, $mobilId, $totalHarga, $hargaSewa, $biayaSupir, $def, $mobil, $user, $bookAt),
            'dikonfirmasi'              => $this->alurDikonfirmasi($pemesananId, $userId, $mobilId, $totalHarga, $hargaSewa, $biayaSupir, $def, $mobil, $user, $bookAt),
            'selesai'                   => $this->alurSelesai($pemesananId, $userId, $mobilId, $totalHarga, $hargaSewa, $biayaSupir, $def, $mobil, $user, $bookAt),
            default                     => null,
        };
    }

    // ── Alur: menunggu konfirmasi admin ──────────────────────────────────────

    private function alurMenungguKonfirmasi(
        int $pemesananId, int $userId, int $mobilId,
        float $total, float $hargaSewa, float $biayaSupir,
        array $def, object $mobil, object $user, string $bookAt
    ): void {
        $bayarAt = date('Y-m-d H:i:s', strtotime($bookAt) + 1800); // +30 menit

        DB::table('payments')->insert([
            'pemesanan_id' => $pemesananId,
            'amount'       => $total,
            'metode'       => $def['metode_bayar'],
            'status'       => 'menunggu_konfirmasi',
            'paid_at'      => null,
            'wa_sent_at'   => $bayarAt,
            'created_at'   => $bayarAt,
            'updated_at'   => $bayarAt,
        ]);

        $this->notif($userId, 'Menunggu Konfirmasi',
            "Pemesanan #{$pemesananId} sedang menunggu konfirmasi admin setelah Anda mengirim pesan WhatsApp.",
            'info', $bayarAt);

        $this->notif($this->adminId, 'Pesanan Baru via WhatsApp',
            "Pemesanan #{$pemesananId} dari {$user->name}. Cek WhatsApp.",
            'info', $bayarAt);
    }

    // ── Alur: dikonfirmasi ────────────────────────────────────────────────────

    private function alurDikonfirmasi(
        int $pemesananId, int $userId, int $mobilId,
        float $total, float $hargaSewa, float $biayaSupir,
        array $def, object $mobil, object $user, string $bookAt
    ): void {
        $bayarAt      = date('Y-m-d H:i:s', strtotime($bookAt) + 1800);
        $konfirmasiAt = date('Y-m-d H:i:s', strtotime($bayarAt) + 3600);

        DB::table('payments')->insert([
            'pemesanan_id' => $pemesananId,
            'amount'       => $total,
            'metode'       => $def['metode_bayar'],
            'status'       => 'dikonfirmasi',
            'paid_at'      => $konfirmasiAt,
            'wa_sent_at'   => $bayarAt,
            'created_at'   => $bayarAt,
            'updated_at'   => $konfirmasiAt,
        ]);

        $this->notif($userId, 'Menunggu Konfirmasi',
            "Pemesanan #{$pemesananId} sedang menunggu konfirmasi admin.",
            'info', $bayarAt);

        $this->notif($this->adminId, 'Pesanan Baru via WhatsApp',
            "Pemesanan #{$pemesananId} dari {$user->name}. Cek WhatsApp.",
            'info', $bayarAt);

        $this->notif($userId, 'Pemesanan Dikonfirmasi',
            "Pemesanan #{$pemesananId} untuk {$mobil->nama} telah dikonfirmasi.",
            'success', $konfirmasiAt);

        // Chat singkat setelah konfirmasi
        $this->chat($userId, $this->adminId, $pemesananId,
            'Terima kasih, min! Sudah dikonfirmasi ya?', $konfirmasiAt);
        $this->chat($this->adminId, $userId, null,
            'Iya, sudah dikonfirmasi. Selamat menikmati perjalanan!',
            date('Y-m-d H:i:s', strtotime($konfirmasiAt) + 120));
    }

    // ── Alur: selesai ─────────────────────────────────────────────────────────

    private function alurSelesai(
        int $pemesananId, int $userId, int $mobilId,
        float $total, float $hargaSewa, float $biayaSupir,
        array $def, object $mobil, object $user, string $bookAt
    ): void {
        $bayarAt      = date('Y-m-d H:i:s', strtotime($bookAt) + 1800);
        $konfirmasiAt = date('Y-m-d H:i:s', strtotime($bayarAt) + 3600);
        $selesaiAt    = date('Y-m-d H:i:s', strtotime($def['selesai'] . ' 17:00:00'));

        $paymentId = DB::table('payments')->insertGetId([
            'pemesanan_id' => $pemesananId,
            'amount'       => $total,
            'metode'       => $def['metode_bayar'],
            'status'       => 'dikonfirmasi',
            'paid_at'      => $konfirmasiAt,
            'wa_sent_at'   => $bayarAt,
            'created_at'   => $bayarAt,
            'updated_at'   => $konfirmasiAt,
        ]);

        $this->notif($userId, 'Menunggu Konfirmasi',
            "Pemesanan #{$pemesananId} sedang menunggu konfirmasi admin.",
            'info', $bayarAt);

        $this->notif($this->adminId, 'Pesanan Baru via WhatsApp',
            "Pemesanan #{$pemesananId} dari {$user->name}. Cek WhatsApp.",
            'info', $bayarAt);

        $this->notif($userId, 'Pemesanan Dikonfirmasi',
            "Pemesanan #{$pemesananId} untuk {$mobil->nama} telah dikonfirmasi.",
            'success', $konfirmasiAt);

        $this->notif($userId, 'Pemesanan Selesai',
            "Terima kasih telah menggunakan Yoza Rent Car! Pemesanan #{$pemesananId} telah selesai.",
            'success', $selesaiAt);

        // ── Journal entries ───────────────────────────────────────────────
        $tanggalJurnal = $def['selesai'];

        // Debit Kas
        $this->jurnal($this->accountIds['1-001'], $pemesananId, $paymentId,
            $total, 0, "Kas masuk — Pemesanan #{$pemesananId}", $tanggalJurnal, $selesaiAt);

        // Kredit Pendapatan Sewa
        $this->jurnal($this->accountIds['4-001'], $pemesananId, $paymentId,
            0, $hargaSewa, "Pendapatan sewa — Pemesanan #{$pemesananId}", $tanggalJurnal, $selesaiAt);

        // Kredit Pendapatan Supir (jika ada)
        if ($biayaSupir > 0) {
            $this->jurnal($this->accountIds['4-002'], $pemesananId, $paymentId,
                0, $biayaSupir, "Pendapatan jasa supir — Pemesanan #{$pemesananId}", $tanggalJurnal, $selesaiAt);
        }

        // Update balance akun
        DB::table('accounts')->where('id', $this->accountIds['1-001'])
            ->increment('balance', $total);
        DB::table('accounts')->where('id', $this->accountIds['4-001'])
            ->increment('balance', $hargaSewa);
        if ($biayaSupir > 0) {
            DB::table('accounts')->where('id', $this->accountIds['4-002'])
                ->increment('balance', $biayaSupir);
        }

        // Chat setelah selesai
        $this->chat($userId, $this->adminId, $pemesananId,
            'Terima kasih min, mobilnya bagus dan bersih!',
            date('Y-m-d H:i:s', strtotime($selesaiAt) + 600));
        $this->chat($this->adminId, $userId, null,
            'Sama-sama! Semoga perjalanannya menyenangkan. Jangan lupa kembali lagi 😊',
            date('Y-m-d H:i:s', strtotime($selesaiAt) + 900));
    }

    // ── Helper insert ─────────────────────────────────────────────────────────

    private function notif(int $userId, string $judul, string $pesan, string $tipe, string $at): void
    {
        DB::table('notifikasis')->insert([
            'user_id'    => $userId,
            'judul'      => $judul,
            'pesan'      => $pesan,
            'tipe'       => $tipe,
            'link'       => null,
            'dibaca'     => false,
            'created_at' => $at,
            'updated_at' => $at,
        ]);
    }

    private function chat(int $pengirimId, int $penerimaId, ?int $pemesananId, string $isi, string $at): void
    {
        DB::table('pesans')->insert([
            'pengirim_id'  => $pengirimId,
            'penerima_id'  => $penerimaId,
            'pemesanan_id' => $pemesananId,
            'isi'          => $isi,
            'dibaca'       => true,
            'created_at'   => $at,
            'updated_at'   => $at,
        ]);
    }

    private function jurnal(
        int $accountId, int $pemesananId, int $paymentId,
        float $debit, float $credit, string $desc, string $date, string $at
    ): void {
        DB::table('journal_entries')->insert([
            'account_id'   => $accountId,
            'pemesanan_id' => $pemesananId,
            'payment_id'   => $paymentId,
            'debit'        => $debit,
            'credit'       => $credit,
            'description'  => $desc,
            'date'         => $date,
            'created_at'   => $at,
            'updated_at'   => $at,
        ]);
    }
}
