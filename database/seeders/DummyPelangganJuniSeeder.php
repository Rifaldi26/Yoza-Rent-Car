<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * DummyPelangganJuniSeeder
 *
 * Mengisi data dummy realistis untuk 3 pelanggan baru:
 *   - Retno Wulandari (ibu rumah tangga bekerja)
 *   - Joko Susilo     (pemilik toko bangunan)
 *   - Agus Kurniawan  (mahasiswa)
 *
 * Setiap pelanggan punya MAKSIMAL 2 pemesanan. Tanggal pemesanan
 * (book_at) tersebar acak antara 11–22 Juni 2026, dengan variasi
 * status, mobil, metode bayar, dan opsi supir agar terasa alami
 * — tidak semua "selesai", ada yang masih berjalan, menunggu
 * konfirmasi, bahkan dibatalkan.
 *
 * Setiap pemesanan otomatis melahirkan data turunan yang konsisten
 * dengan alur nyata aplikasi: payment, notifikasi, chat dengan admin,
 * dan jurnal akuntansi (khusus status "selesai").
 *
 * Jalankan SETELAH: UserSeeder, AccountSeeder, MobilSeeder
 *   php artisan db:seed --class="Database\Seeders\DummyPelangganJuniSeeder"
 */
class DummyPelangganJuniSeeder extends Seeder
{
    // ── ID referensi setelah insert ───────────────────────────────────────────
    private int $adminId;

    /** @var array<string,int> email => user_id */
    private array $userIds = [];

    /** @var array<int,int> index (urut harga termurah→termahal) => mobil_id */
    private array $mobilIds = [];

    /** @var array<string,int> kode akun => account_id */
    private array $accountIds = [];

    /**
     * Profil tetap tiap pelanggan — diisi sesuai kolom wajib pada
     * StorePemesananRequest (alamat, status pekerjaan, kontak, dst).
     *
     * @var array<string,array<string,mixed>>
     */
    private array $profil = [
        'retno.wulandari@gmail.com' => [
            'nama'             => 'Retno Wulandari',
            'no_hp'            => '081298765432',
            'alamat'           => 'Jl. Gerilya No. 45, Purwokerto Selatan, Banyumas',
            'status_pekerjaan' => 'bekerja',
            'tempat_kerja'     => 'PT Mitra Sejahtera Logistik',
            'kampus'           => null,
            'instagram'        => '@retno.wulandari29',
            'tiktok'           => null,
            'sumber_info'      => 'Instagram',
            'kontak_darurat'   => '081298765433',
        ],
        'joko.susilo@gmail.com' => [
            'nama'             => 'Joko Susilo',
            'no_hp'            => '082187654321',
            'alamat'           => 'Jl. Jenderal Sudirman No. 88, Purwokerto',
            'status_pekerjaan' => 'bekerja',
            'tempat_kerja'     => 'Toko Bangunan Sumber Rejeki',
            'kampus'           => null,
            'instagram'        => null,
            'tiktok'           => '@jokosusilo.id',
            'sumber_info'      => 'Rekomendasi teman',
            'kontak_darurat'   => '082187654322',
        ],
        'agus.kurniawan@gmail.com' => [
            'nama'             => 'Agus Kurniawan',
            'no_hp'            => '085311223344',
            'alamat'           => 'Kos Melati, Jl. Prof. Soeharso, Grendeng, Purwokerto Utara',
            'status_pekerjaan' => 'mahasiswa',
            'tempat_kerja'     => null,
            'kampus'           => 'Universitas Jenderal Soedirman',
            'instagram'        => '@aguskurniawan_',
            'tiktok'           => null,
            'sumber_info'      => 'TikTok',
            'kontak_darurat'   => '085311223345',
        ],
    ];

    public function run(): void
    {
        $this->seedUsers();
        $this->resolveRefs();
        $this->seedPemesanan();
    }

    // ── 1. Pelanggan ────────────────────────────────────────────────────────

    private function seedUsers(): void
    {
        $waktuDaftar = [
            'retno.wulandari@gmail.com' => '2026-06-02 10:00:00',
            'joko.susilo@gmail.com'     => '2026-06-04 08:30:00',
            'agus.kurniawan@gmail.com'  => '2026-06-06 19:45:00',
        ];

        foreach ($this->profil as $email => $p) {
            $waktu = $waktuDaftar[$email];

            DB::table('users')->insertOrIgnore([
                'name'              => $p['nama'],
                'email'             => $email,
                'no_hp'             => $p['no_hp'],
                'password'          => Hash::make('user123'),
                'role'              => 'user',
                'email_verified_at' => $waktu,
                'created_at'        => $waktu,
                'updated_at'        => $waktu,
            ]);
        }
    }

    private function resolveRefs(): void
    {
        $this->adminId = DB::table('users')->where('role', 'admin')->value('id');

        $this->userIds = DB::table('users')
            ->whereIn('email', array_keys($this->profil))
            ->pluck('id', 'email')
            ->toArray();

        $this->mobilIds = DB::table('mobils')
            ->orderBy('harga_per_hari')
            ->pluck('id')
            ->toArray();

        $this->accountIds = DB::table('accounts')
            ->pluck('id', 'kode')
            ->toArray();
    }

    // ── 2. Pemesanan ────────────────────────────────────────────────────────

    private function seedPemesanan(): void
    {
        /**
         * 5 pemesanan total — Retno (2), Joko (1), Agus (2) — semua
         * masih dalam batas maksimal 2 per pelanggan. book_at tersebar
         * acak di sepanjang 11–22 Juni: 11, 12, 14, 19, 21.
         *
         * Kolom:
         *   email         → kunci ke $this->profil
         *   mobil_idx     → index ke $this->mobilIds[] (urut harga termurah)
         *   mulai/selesai → tanggal sewa
         *   tipe          → harian | 12_jam
         *   waktu_mulai/waktu_selesai → HH:MM
         *   supir         → true/false
         *   catatan       → nullable
         *   tujuan_sewa/kota_tujuan/share_lokasi → konteks perjalanan
         *   status        → pending|menunggu_konfirmasi_admin|dikonfirmasi|selesai|dibatalkan|kadaluarsa
         *   metode_bayar  → cash|transfer|qris|edc|null
         *   book_at       → tanggal & jam pemesanan dibuat (11–22 Juni 2026)
         */
        $defs = [
            // ── Retno Wulandari — pemesanan #1: liburan keluarga (selesai) ──
            [
                'email'        => 'retno.wulandari@gmail.com',
                'mobil_idx'    => 5, // All New Xenia 2023, 400rb/hari
                'mulai'        => '2026-06-13',
                'selesai'      => '2026-06-15',
                'tipe'         => 'harian',
                'waktu_mulai'  => '08:00',
                'waktu_selesai'=> '08:00',
                'supir'        => true,
                'catatan'      => 'Mohon dibersihkan dulu sebelum dijemput, terima kasih.',
                'tujuan_sewa'  => 'Liburan keluarga ke rumah nenek',
                'kota_tujuan'  => 'Yogyakarta',
                'share_lokasi' => 'https://maps.app.goo.gl/RetnoRumah1',
                'status'       => 'selesai',
                'metode_bayar' => 'transfer',
                'book_at'      => '2026-06-11 09:20:00',
            ],
            // ── Retno Wulandari — pemesanan #2: jemput orang tua (dikonfirmasi) ──
            [
                'email'        => 'retno.wulandari@gmail.com',
                'mobil_idx'    => 3, // All New Brio, 350rb/hari
                'mulai'        => '2026-06-21',
                'selesai'      => '2026-06-22',
                'tipe'         => 'harian',
                'waktu_mulai'  => '09:00',
                'waktu_selesai'=> '09:00',
                'supir'        => false,
                'catatan'      => null,
                'tujuan_sewa'  => 'Menjemput orang tua dari terminal',
                'kota_tujuan'  => 'Purwokerto',
                'share_lokasi' => 'https://maps.app.goo.gl/RetnoRumah2',
                'status'       => 'dikonfirmasi',
                'metode_bayar' => 'qris',
                'book_at'      => '2026-06-19 16:45:00',
            ],
            // ── Joko Susilo — pemesanan #1: perjalanan dinas (selesai) ──
            [
                'email'        => 'joko.susilo@gmail.com',
                'mobil_idx'    => 0, // Agya TRD, 300rb/hari
                'mulai'        => '2026-06-16',
                'selesai'      => '2026-06-17',
                'tipe'         => 'harian',
                'waktu_mulai'  => '07:30',
                'waktu_selesai'=> '07:30',
                'supir'        => false,
                'catatan'      => 'Tolong full tank ya, biar gampang ngembaliinnya.',
                'tujuan_sewa'  => 'Perjalanan dinas mengantar barang',
                'kota_tujuan'  => 'Solo',
                'share_lokasi' => 'https://maps.app.goo.gl/JokoToko',
                'status'       => 'selesai',
                'metode_bayar' => 'cash',
                'book_at'      => '2026-06-14 08:10:00',
            ],
            // ── Agus Kurniawan — pemesanan #1: acara kampus (dibatalkan) ──
            [
                'email'        => 'agus.kurniawan@gmail.com',
                'mobil_idx'    => 2, // Calya, 300rb/hari
                'mulai'        => '2026-06-13',
                'selesai'      => '2026-06-13',
                'tipe'         => '12_jam',
                'waktu_mulai'  => '08:00',
                'waktu_selesai'=> '20:00',
                'supir'        => false,
                'catatan'      => 'Jadi batal ya min, teman saya mendadak sakit.',
                'tujuan_sewa'  => 'Acara ospek fakultas',
                'kota_tujuan'  => 'Purwokerto',
                'share_lokasi' => 'https://maps.app.goo.gl/AgusKos1',
                'status'       => 'dibatalkan',
                'metode_bayar' => null,
                'book_at'      => '2026-06-12 21:10:00',
            ],
            // ── Agus Kurniawan — pemesanan #2: liburan akhir semester (menunggu konfirmasi) ──
            [
                'email'        => 'agus.kurniawan@gmail.com',
                'mobil_idx'    => 6, // Xpander, 450rb/hari
                'mulai'        => '2026-06-24',
                'selesai'      => '2026-06-26',
                'tipe'         => 'harian',
                'waktu_mulai'  => '10:00',
                'waktu_selesai'=> '10:00',
                'supir'        => true,
                'catatan'      => 'Mau liburan bareng teman kos, semoga supirnya hafal jalan ke pantai.',
                'tujuan_sewa'  => 'Liburan akhir semester bersama teman kuliah',
                'kota_tujuan'  => 'Pangandaran',
                'share_lokasi' => 'https://maps.app.goo.gl/AgusKos2',
                'status'       => 'menunggu_konfirmasi_admin',
                'metode_bayar' => 'transfer',
                'book_at'      => '2026-06-21 14:30:00',
            ],
        ];

        foreach ($defs as $def) {
            $this->insertPemesanan($def);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function insertPemesanan(array $def): void
    {
        $userId  = $this->userIds[$def['email']];
        $mobilId = $this->mobilIds[$def['mobil_idx']];
        $mobil   = DB::table('mobils')->find($mobilId);
        $user    = DB::table('users')->find($userId);
        $profil  = $this->profil[$def['email']];

        $adalah12Jam = $def['tipe'] === '12_jam';
        $mulai       = $def['mulai'];
        $selesai     = $def['selesai'];
        $bookAt      = $def['book_at'];
        $status      = $def['status'];

        // ── Hitung harga ──────────────────────────────────────────────────
        if ($adalah12Jam) {
            $hargaSewa = $mobil->harga_per_hari / 2;
            $durasi    = 0;
        } else {
            $durasi    = (int) ((strtotime($selesai) - strtotime($mulai)) / 86400);
            $hargaSewa = $mobil->harga_per_hari * $durasi;
        }

        $biayaSupir = 0;
        if ($def['supir'] && $mobil->biaya_supir_per_hari) {
            $biayaSupir = $mobil->biaya_supir_per_hari * max($durasi, 1);
        }

        $totalHarga = $hargaSewa + $biayaSupir;

        // ── Insert pemesanan (termasuk data wajib StorePemesananRequest) ───
        $pemesananId = DB::table('pemesanans')->insertGetId([
            'user_id'          => $userId,
            'mobil_id'         => $mobilId,
            'tanggal_mulai'    => $mulai,
            'tanggal_selesai'  => $selesai,
            'waktu_mulai'      => $def['waktu_mulai'] . ':00',
            'waktu_selesai'    => $def['waktu_selesai'] . ':00',
            'tipe_sewa'        => $def['tipe'],
            'opsi_supir'       => $def['supir'],
            'biaya_supir'      => $def['supir'] ? $biayaSupir : null,
            'total_harga'      => $totalHarga,
            'status'           => $status,
            'catatan'          => $def['catatan'],
            'alamat'           => $profil['alamat'],
            'tujuan_sewa'      => $def['tujuan_sewa'],
            'kota_tujuan'      => $def['kota_tujuan'],
            'instagram'        => $profil['instagram'],
            'tiktok'           => $profil['tiktok'],
            'status_pekerjaan' => $profil['status_pekerjaan'],
            'tempat_kerja'     => $profil['tempat_kerja'],
            'kampus'           => $profil['kampus'],
            'sumber_info'      => $profil['sumber_info'],
            'kontak_darurat'   => $profil['kontak_darurat'],
            'share_lokasi'     => $def['share_lokasi'],
            'created_at'       => $bookAt,
            'updated_at'       => $bookAt,
        ]);

        // ── Notifikasi: pemesanan dibuat ────────────────────────────────────
        $this->notif($userId, 'Pemesanan Dibuat',
            "Pemesanan #{$pemesananId} untuk {$mobil->nama} berhasil dibuat. Silakan selesaikan pembayaran.",
            'info', $bookAt);

        // ── Alur lanjutan berdasarkan status ────────────────────────────────
        match ($status) {
            'menunggu_konfirmasi_admin' => $this->alurMenungguKonfirmasi($pemesananId, $userId, $totalHarga, $def, $user, $bookAt),
            'dikonfirmasi'              => $this->alurDikonfirmasi($pemesananId, $userId, $totalHarga, $def, $mobil, $bookAt),
            'selesai'                   => $this->alurSelesai($pemesananId, $userId, $totalHarga, $hargaSewa, $biayaSupir, $def, $mobil, $bookAt),
            'dibatalkan'                => $this->alurDibatalkan($pemesananId, $userId, $mobil, $bookAt),
            default                     => null,
        };
    }

    // ── Alur: menunggu konfirmasi admin ────────────────────────────────────

    private function alurMenungguKonfirmasi(
        int $pemesananId, int $userId, float $total, array $def, object $user, string $bookAt
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

    // ── Alur: dikonfirmasi ──────────────────────────────────────────────────

    private function alurDikonfirmasi(
        int $pemesananId, int $userId, float $total, array $def, object $mobil, string $bookAt
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
            "Pemesanan #{$pemesananId} dari " . DB::table('users')->find($userId)->name . '. Cek WhatsApp.',
            'info', $bayarAt);

        $this->notif($userId, 'Pemesanan Dikonfirmasi',
            "Pemesanan #{$pemesananId} untuk {$mobil->nama} telah dikonfirmasi.",
            'success', $konfirmasiAt);

        $this->chat($userId, $this->adminId, $pemesananId,
            'Terima kasih min, sudah dikonfirmasi ya pesanannya?', $konfirmasiAt);
        $this->chat($this->adminId, $userId, null,
            'Iya, sudah dikonfirmasi. Selamat menikmati perjalanannya!',
            date('Y-m-d H:i:s', strtotime($konfirmasiAt) + 120));
    }

    // ── Alur: selesai ───────────────────────────────────────────────────────

    private function alurSelesai(
        int $pemesananId, int $userId, float $total, float $hargaSewa, float $biayaSupir,
        array $def, object $mobil, string $bookAt
    ): void {
        $bayarAt      = date('Y-m-d H:i:s', strtotime($bookAt) + 1800);
        $konfirmasiAt = date('Y-m-d H:i:s', strtotime($bayarAt) + 3600);
        $selesaiAt    = date('Y-m-d H:i:s', strtotime($def['selesai'] . ' 17:00:00'));
        $user         = DB::table('users')->find($userId);

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

        // ── Jurnal akuntansi (double-entry) ──────────────────────────────
        $tanggalJurnal = $def['selesai'];

        $this->jurnal($this->accountIds['1-001'], $pemesananId, $paymentId,
            $total, 0, "Kas masuk — Pemesanan #{$pemesananId}", $tanggalJurnal, $selesaiAt);

        $this->jurnal($this->accountIds['4-001'], $pemesananId, $paymentId,
            0, $hargaSewa, "Pendapatan sewa — Pemesanan #{$pemesananId}", $tanggalJurnal, $selesaiAt);

        if ($biayaSupir > 0) {
            $this->jurnal($this->accountIds['4-002'], $pemesananId, $paymentId,
                0, $biayaSupir, "Pendapatan jasa supir — Pemesanan #{$pemesananId}", $tanggalJurnal, $selesaiAt);
        }

        DB::table('accounts')->where('id', $this->accountIds['1-001'])->increment('balance', $total);
        DB::table('accounts')->where('id', $this->accountIds['4-001'])->increment('balance', $hargaSewa);
        if ($biayaSupir > 0) {
            DB::table('accounts')->where('id', $this->accountIds['4-002'])->increment('balance', $biayaSupir);
        }

        $this->chat($userId, $this->adminId, $pemesananId,
            'Terima kasih min, mobilnya nyaman dan bersih!',
            date('Y-m-d H:i:s', strtotime($selesaiAt) + 600));
        $this->chat($this->adminId, $userId, null,
            'Sama-sama! Semoga perjalanannya menyenangkan. Jangan lupa pesan lagi ya',
            date('Y-m-d H:i:s', strtotime($selesaiAt) + 900));
    }

    // ── Alur: dibatalkan ────────────────────────────────────────────────────

    private function alurDibatalkan(int $pemesananId, int $userId, object $mobil, string $bookAt): void
    {
        $batalAt = date('Y-m-d H:i:s', strtotime($bookAt) + 3600); // dibatalkan ±1 jam setelah dipesan

        $this->notif($userId, 'Pemesanan Dibatalkan',
            "Pemesanan #{$pemesananId} untuk {$mobil->nama} telah dibatalkan.",
            'warning', $batalAt);

        $this->chat($userId, $this->adminId, $pemesananId,
            'Maaf min, jadi batal ya pesanannya. Teman saya mendadak sakit.', $batalAt);
        $this->chat($this->adminId, $userId, null,
            'Baik, tidak masalah. Ditunggu pemesanan selanjutnya ya!',
            date('Y-m-d H:i:s', strtotime($batalAt) + 180));
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
