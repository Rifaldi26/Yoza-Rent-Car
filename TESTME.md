# Tests — Yoza Rent Car

Folder ini berisi suite pengujian otomatis (PHPUnit) yang mencakup
seluruh fitur aplikasi: katalog mobil, pemesanan, pembayaran,
favorit, notifikasi, chat, ulasan, manajemen mobil/pemesanan/user
oleh admin, pembukuan double-entry, laporan, dan halaman statis.

## Cara menjalankan

Proyek ini **tidak** menggunakan syntax Pest — gunakan PHPUnit murni
melalui Artisan. Jalankan **per folder** (Unit lalu Feature), bukan
sekaligus, untuk menghindari konflik nama class antar test runner:

```bash
composer test
```

Atau manual:

```bash
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
```

Untuk menjalankan satu file/folder tertentu saat development:

```bash
php artisan test tests/Unit/Services/PemesananServiceTest.php
php artisan test tests/Feature/Admin
```

## Sebelum menjalankan test

1. Pastikan dependency sudah terpasang:
   ```bash
   composer install
   ```
2. Hapus `pestphp/pest-plugin` dari `allow-plugins` di `composer.json`
   sudah dilakukan di file ini — proyek tidak memakai syntax Pest,
   dan plugin tersebut adalah sumber bug "Cannot declare class
   ExampleTest" saat test dijalankan gabungan.
3. Test memakai SQLite in-memory (`:memory:`) sesuai konfigurasi di
   `phpunit.xml` — tidak menyentuh database MySQL development Anda.

## Struktur

```
tests/
├── TestCase.php                 ← helper bersama (buatAdmin, buatUser,
│                                   buatMobilTersedia, payloadPemesananValid)
├── Unit/
│   ├── Enums/                   ← StatusPemesanan, StatusPayment, StatusMobil
│   ├── Models/                  ← Mobil, Pemesanan, User, Payment, Ulasan
│   ├── Policies/                ← PemesananPolicy, MobilPolicy
│   ├── Services/                ← PemesananService, PaymentService,
│   │                               NotifikasiService
│   └── ExampleTest.php
└── Feature/
    ├── Auth/                    ← login, register, verifikasi email,
    │                               reset & update password
    ├── Public/                  ← katalog mobil, halaman statis,
    │                               redirect Google OAuth
    ├── User/                    ← alur pemesanan, pembayaran, favorit,
    │                               notifikasi, chat, ulasan
    ├── Admin/                   ← manajemen mobil/pemesanan/user/halaman,
    │                               pembukuan, laporan, moderasi ulasan,
    │                               notifikasi & chat admin, dashboard
    ├── ProfilTest.php
    └── ExampleTest.php
```

## Catatan penting

- **Bug lama diperbaiki**: `tests/Unit/ExampleTest.php` sebelumnya
  memiliki namespace yang salah (`Tests\Feature`), menyebabkan
  konflik nama class dengan `tests/Feature/ExampleTest.php` saat
  dijalankan bersamaan. Sudah diperbaiki menjadi `Tests\Unit`.
- Beberapa factory baru ditambahkan agar seluruh model punya
  factory siap pakai: `AccountFactory`, `JournalEntryFactory`,
  `NotifikasiFactory`, `PesanFactory`, `PageFactory`.
- Event broadcast (`PesanTerkirim`) dan job email
  (`KirimEmailPemesanan`) di-fake pada test yang relevan — test
  tidak memerlukan koneksi Reverb/WebSocket atau SMTP sungguhan.
- Semua skenario otorisasi (admin-only, pemilik-only) diuji baik di
  level Policy (unit, lebih presisi & cepat) maupun di level HTTP
  (feature, memastikan middleware & route benar-benar menegakkannya).

## Checklist sebelum hosting

Setelah seluruh suite **passed**, langkah lanjutan yang disarankan
sebelum deploy ke production:

1. Jalankan `php artisan test` (per folder) di lingkungan lokal —
   pastikan 0 failures.
2. Jalankan `vendor/bin/pint` untuk memastikan code style konsisten.
3. Set `APP_ENV=production`, `APP_DEBUG=false` di `.env` produksi.
4. Jalankan `php artisan config:cache`, `route:cache`, `view:cache`.
5. Pastikan `php artisan migrate --force` dijalankan di database
   produksi sebelum aplikasi menerima trafik.
