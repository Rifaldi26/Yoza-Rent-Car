# Yoza Rent Car

Aplikasi manajemen rental mobil berbasis web. Memungkinkan pelanggan menelusuri armada, memesan kendaraan, dan melakukan pembayaran; serta memberikan panel admin untuk mengelola pemesanan, konfirmasi pembayaran, dan laporan keuangan.

## Teknologi

| Layer      | Teknologi                              |
|------------|----------------------------------------|
| Backend    | PHP 8.2, Laravel 12, Laravel Breeze    |
| Frontend   | Tailwind CSS v3, Alpine.js v3, Vite    |
| Realtime   | Laravel Reverb (WebSocket)             |
| Database   | MySQL 8 (production), SQLite (test)    |
| Queue      | Redis + Supervisor (production)        |
| Auth       | Session + Google OAuth (Socialite)     |

## Fitur Utama

- Katalog mobil dengan filter ketersediaan dan favorit
- Pemesanan dengan pengecekan konflik tanggal otomatis
- Alur pembayaran via WhatsApp (cash, transfer, QRIS, EDC)
- Notifikasi in-app dan email transaksional
- Chat realtime antara pelanggan dan admin
- Panel admin: manajemen armada, pemesanan, laporan, pembukuan double-entry
- Ekspor laporan ke Excel dan PDF
- Dukungan bahasa Indonesia / Inggris (i18n)

---

## Cara Setup Lokal

### Prasyarat

- PHP 8.2+
- Composer
- Node.js 20+ & npm
- SQLite (untuk development) atau MySQL 8

### Langkah instalasi

```bash
# 1. Clone repositori
git clone https://github.com/username/yoza-rent-car.git
cd yoza-rent-car

# 2. Install dependensi PHP
composer install

# 3. Salin dan isi file konfigurasi
cp .env.example .env
php artisan key:generate

# 4. Jalankan migrasi dan seed data awal
php artisan migrate --seed

# 5. Install dependensi frontend dan build aset
npm install
npm run build

# 6. Buat symlink storage
php artisan storage:link
```

### Menjalankan server development

```bash
composer dev
```

Perintah ini menjalankan empat proses sekaligus: server PHP, queue listener, log viewer (Pail), dan Vite dev server.

### Konfigurasi wajib di .env

```dotenv
# Nomor WhatsApp admin (format: 62xxxxxxxx)
PAYMENT_WA_NUMBER=628xxxxxxxxx

# Rekening transfer
PAYMENT_TRANSFER_BANK=BCA
PAYMENT_TRANSFER_REKENING=1234567890
PAYMENT_TRANSFER_ATAS_NAMA="Nama Pemilik"

# Google OAuth
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# Mail (gunakan log untuk dev, SMTP untuk production)
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@yozarentcar.com
MAIL_FROM_NAME="Yoza Rent Car"
```

---

## Menjalankan Test

```bash
# Jalankan semua test
php artisan test

# Dengan laporan coverage
php artisan test --coverage

# Hanya unit test
php artisan test --testsuite=Unit

# Hanya feature test
php artisan test --testsuite=Feature
```

Target coverage minimum: **70%** pada alur bisnis kritis.

---

## Arsitektur Kode

```
app/
├── Console/Commands/       # Perintah artisan terjadwal
├── Enums/                  # StatusPemesanan, StatusPayment, StatusMobil
├── Events/                 # PesanTerkirim (Reverb broadcasting)
├── Exceptions/             # PemesananException, PaymentException
├── Exports/                # Excel export (Maatwebsite)
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Panel admin
│   │   ├── Auth/           # Autentikasi (Breeze + Google)
│   │   └── User/           # Panel pelanggan
│   ├── Middleware/         # IsAdmin, SetLocale, EnsureEmailVerified
│   └── Requests/           # Form Request per domain
├── Jobs/                   # KirimEmailPemesanan, SendRentalReminder
├── Mail/                   # Mailable untuk setiap event pemesanan
├── Models/                 # Eloquent models
├── Policies/               # PemesananPolicy, MobilPolicy
├── Providers/              # AppServiceProvider
└── Services/               # PemesananService, PaymentService, NotifikasiService

resources/views/
├── admin/                  # Tampilan panel admin
├── components/             # Blade components (icon, button, input, dll)
├── emails/                 # Template email transaksional
├── layouts/                # Layout utama (app, admin, guest)
├── pdf/                    # Template PDF invoice dan laporan
└── user/                   # Tampilan panel pelanggan
```

### Konvensi

- **Controller**: hanya menerima input, memanggil Service, mengembalikan response.
- **Service**: seluruh logika bisnis. Dapat di-mock dalam pengujian.
- **Model**: relasi, scope, cast, dan helper sederhana. Tidak ada kiriman email atau notifikasi.
- **Form Request**: seluruh validasi. Tidak ada `$request->validate()` di Controller.
- **Policy**: seluruh otorisasi. Tidak ada pemeriksaan `$user->isAdmin()` di Controller.

---

## Deployment Production

Lihat [docs/deployment.md](docs/deployment.md) untuk panduan lengkap setup VPS, Nginx, Supervisor, CI/CD, dan monitoring.

### Checklist cepat sebelum go-live

```bash
# Konfigurasi production
APP_ENV=production
APP_DEBUG=false

# Optimasi
php artisan optimize
php artisan storage:link

# Jalankan migrasi
php artisan migrate --force
```

---

## Changelog

Lihat [CHANGELOG.md](CHANGELOG.md).

## Lisensi

Hak cipta © 2026 Yoza Rent Car. Seluruh hak dilindungi.
