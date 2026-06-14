# Changelog

Semua perubahan penting pada proyek ini didokumentasikan di file ini.

Format mengikuti [Keep a Changelog](https://keepachangelog.com/id/1.0.0/).

---

## [Unreleased]

### Ditambahkan
- `app/Services/PemesananService.php` — logika bisnis pemesanan dipindahkan dari Controller
- `app/Services/PaymentService.php` — logika pembayaran dan URL WhatsApp
- `app/Services/NotifikasiService.php` — wrapper injectable untuk notifikasi in-app
- `app/Enums/StatusPemesanan.php` — menggantikan string literal status pemesanan
- `app/Enums/StatusPayment.php` — menggantikan string literal status pembayaran
- `app/Enums/StatusMobil.php` — menggantikan string literal status mobil
- `app/Policies/PemesananPolicy.php` — otorisasi aksi pada resource Pemesanan
- `app/Policies/MobilPolicy.php` — otorisasi manajemen armada (admin only)
- `app/Http/Requests/User/StorePemesananRequest.php` — Form Request pembuatan pemesanan
- `app/Http/Requests/Admin/StoreMobilRequest.php` — Form Request penambahan mobil
- `app/Http/Requests/Admin/UpdateMobilRequest.php` — Form Request pembaruan mobil
- `app/Exceptions/PemesananException.php` — exception domain untuk aturan bisnis pemesanan
- `app/Exceptions/PaymentException.php` — exception domain untuk aturan bisnis pembayaran
- `database/migrations/..._add_performance_indexes.php` — index composite untuk performa query
- `.github/workflows/ci.yml` — GitHub Actions CI: pint + test + build aset
- `tests/Feature/User/PemesananFlowTest.php` — test alur pemesanan sisi pengguna
- `tests/Feature/Admin/PemesananAdminTest.php` — test panel admin pemesanan
- `tests/Unit/Services/PemesananServiceTest.php` — unit test logika bisnis Service

### Diubah
- `vite.config.js` — HMR host tidak lagi di-hardcode; dibaca dari env `VITE_HMR_HOST`
- `app/Http/Controllers/User/PemesananController.php` — delegasi ke `PemesananService`
- `app/Http/Controllers/Admin/PemesananController.php` — delegasi ke Service, hapus `buatJurnalSelesai` private
- `README.md` — menggantikan README default Laravel dengan dokumentasi proyek

---

## [0.1.0] — 2026-06-13

### Ditambahkan
- Autentikasi email + Google OAuth (Laravel Socialite)
- Katalog mobil publik dengan detail halaman
- Pemesanan dengan cek konflik tanggal otomatis
- Alur pembayaran via WhatsApp (cash, transfer, QRIS, EDC)
- Notifikasi in-app dan email transaksional (6 event)
- Panel admin: manajemen mobil, pemesanan, laporan, pembukuan
- Chat realtime antara pelanggan dan admin (Laravel Reverb)
- Favorit mobil per pengguna
- Ekspor laporan ke Excel dan PDF
- Pembukuan double-entry (debit/kredit) saat pemesanan selesai
- Pengingat sewa otomatis H-3 dan H-1
- Kadaluarsa otomatis pemesanan pending setelah 24 jam
- CMS sederhana untuk halaman Syarat & Ketentuan dan Kebijakan Privasi
- Dukungan bahasa Indonesia dan Inggris (i18n)
