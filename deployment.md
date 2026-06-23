# Checklist Deployment — Yoza Rent Car

Daftar ini melengkapi 4 poin yang ditemukan saat review kesiapan hosting.

## 1. Environment production (.env)

Sudah disiapkan template: **`.env.production.example`**.

Langkah di server:
```bash
cp .env.production.example .env
# isi semua baris bertanda "ISI_INI" dengan nilai asli
php artisan key:generate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Perbedaan utama vs `.env.example` (yang dipakai untuk dev lokal):
| Variabel | Lokal (.env.example) | Production |
|---|---|---|
| `APP_ENV` | `local` | `production` |
| `APP_DEBUG` | `true` | `false` |
| `LOG_LEVEL` | `debug` | `error` |
| `QUEUE_CONNECTION` | `database` | `redis` |
| `CACHE_STORE` | `database` | `redis` |
| `SESSION_SECURE_COOKIE` | — | `true` |

⚠️ **`APP_DEBUG=true` di production = stack trace & isi `.env` bisa bocor ke publik saat terjadi error.** Ini wajib `false`.

## 2. Test suite

Sudah diperbaiki:
- `tests/Feature/User/PemesananFlowTest.php` — sekarang mengirim payload lengkap (alamat, tujuan sewa, jam mulai/selesai, dll) sesuai validasi `StorePemesananRequest` terkini. Ditambah 2 test baru: validasi jam wajib diisi, dan anti pesan-ganda.
- `tests/Unit/Services/PemesananServiceTest.php` — ditambah 3 test baru untuk `adaKonflikUser()` (cek konflik per-user) dan skenario double-booking di `PemesananService::buat()`.

Jalankan sebelum deploy:
```bash
php artisan test
```
> Catatan: saya tidak bisa menjalankan PHP di sandbox ini untuk memverifikasi langsung — jalankan di lokal/CI Anda sebelum deploy untuk memastikan semua hijau.

## 3. Reverb & Queue Worker (proses background)

Sudah disiapkan template Supervisor:
- `deploy/supervisor/yoza-queue.conf` — queue worker (`queue:work redis`, 2 proses, auto-restart).
- `deploy/supervisor/yoza-reverb.conf` — server WebSocket (`reverb:start`, harus jalan terus).

Langkah di server (Ubuntu/Debian + Supervisor):
```bash
sudo apt install supervisor   # jika belum ada
sudo cp deploy/supervisor/yoza-queue.conf /etc/supervisor/conf.d/
sudo cp deploy/supervisor/yoza-reverb.conf /etc/supervisor/conf.d/
# sesuaikan path /var/www/yoza-rent-car & user www-data di kedua file
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start yoza-queue:* yoza-reverb:*
```

Setiap kali ada deploy/update kode:
```bash
php artisan queue:restart   # worker akan reload otomatis (supervisor menghidupkan ulang)
```

Web server (Nginx/Apache + PHP-FPM) tetap diatur terpisah seperti biasa — `php artisan serve` **tidak** dipakai di production.

## 4. Kredensial production — cek manual

Saya tidak bisa mengisi ini untuk Anda. Pastikan semua sudah nilai **asli**, bukan placeholder dari `.env.example`:

- [ ] `DB_*` — database production (bukan SQLite, bukan `:memory:`)
- [ ] `MAIL_*` — SMTP transaksional asli (bukan `log`/`array`/mailtrap), agar email konfirmasi/invoice ke pelanggan benar-benar terkirim
- [ ] `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET` / `GOOGLE_REDIRECT_URI` — dari Google Cloud Console, redirect URI memakai domain production (`https://...`)
- [ ] `PAYMENT_WA_NUMBER` — ⚠️ nilai default di `.env.example` (`6285728015695`) **bukan nomor production**, wajib diganti dengan nomor WA admin yang sebenarnya
- [ ] `PAYMENT_TRANSFER_BANK` / `PAYMENT_TRANSFER_REKENING` / `PAYMENT_TRANSFER_ATAS_NAMA` — rekening transfer asli
- [ ] `PAYMENT_QRIS_IMAGE` — pastikan file QRIS asli sudah diupload ke `storage/app/public/payment/qris.png` lalu `php artisan storage:link`
- [ ] `REVERB_APP_ID/KEY/SECRET` — generate ulang untuk production (jangan reuse dari lokal)

## Setelah semua di atas selesai

```bash
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan migrate --force
php artisan storage:link
```
