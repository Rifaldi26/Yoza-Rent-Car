# Changelog

Semua perubahan penting pada proyek ini didokumentasikan di file ini.

Format mengacu pada [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [Unreleased]

### Diperbaiki
- Menghapus file controller duplikat yang tidak terpakai (`UlasanController_admin.php`, `UlasanController_user.php`) yang berisiko menyebabkan konflik nama class saat optimisasi autoload.
- Menghapus berkas backup sisa refactor (`DatabaseSeeder.php.bak`).
- Memperbaiki tautan dokumentasi di README yang menunjuk ke path salah (`docs/deployment.md` → `deployment.md`).

## [0.1.0] - MVP

### Ditambahkan
- Katalog dan detail mobil dengan status ketersediaan (tersedia/disewa/perawatan).
- Alur pemesanan dengan pengecekan konflik tanggal otomatis, dukungan sewa harian dan 12 jam, serta opsi supir.
- Alur pembayaran manual via WhatsApp (cash, transfer, QRIS, EDC) dengan template pesan otomatis dan konfirmasi oleh admin.
- Auto-kadaluarsa pemesanan `pending` yang melewati batas waktu pembayaran (`config/rental.php`).
- Pengingat sewa otomatis H-3 dan H-1 sebelum tanggal mulai.
- Pencatatan akuntansi double-entry (kas, pendapatan sewa, pendapatan supir) saat pemesanan selesai.
- Panel admin: manajemen armada, pemesanan, pengguna, ulasan, laporan, dan pembukuan.
- Ekspor laporan ke Excel dan PDF.
- Notifikasi in-app dan email transaksional untuk setiap perubahan status pemesanan.
- Chat realtime (Laravel Reverb) antara pelanggan dan admin.
- Sistem ulasan mobil dengan moderasi admin.
- CMS sederhana untuk halaman Syarat & Ketentuan dan Kebijakan Privasi.
- Autentikasi via Laravel Breeze dan Google OAuth (Socialite).
- Dukungan bahasa Indonesia / Inggris (i18n).
