<?php

/**
 * Konfigurasi aturan bisnis rental.
 *
 * Semua konstanta yang berhubungan dengan logika sewa kendaraan
 * dipusatkan di sini agar mudah diubah tanpa menyentuh kode.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Interval Pengingat Sewa (hari sebelum tanggal mulai)
    |--------------------------------------------------------------------------
    |
    | Daftar berapa hari sebelum tanggal mulai sewa, pelanggan akan mendapat
    | pengingat. Urutan tidak berpengaruh — command akan memproses semua.
    |
    | Contoh: [3, 1] = kirim pengingat H-3 dan H-1 sebelum sewa dimulai.
    |
    */
    'reminder_intervals' => [3, 1],

    /*
    |--------------------------------------------------------------------------
    | Jadwal Pengiriman Pengingat
    |--------------------------------------------------------------------------
    |
    | Jam (format HH:MM, timezone Asia/Jakarta) saat command pengingat
    | dijalankan oleh scheduler setiap harinya.
    |
    */
    'reminder_dispatch_time' => '08:00',

    /*
    |--------------------------------------------------------------------------
    | Batas Waktu Pembayaran (jam)
    |--------------------------------------------------------------------------
    |
    | Pemesanan berstatus 'pending' yang tidak dibayar dalam rentang waktu ini
    | (dihitung sejak created_at) akan otomatis diubah menjadi 'kadaluarsa'.
    |
    */
    'payment_expiry_hours' => 24,

    /*
    |--------------------------------------------------------------------------
    | Jadwal Pengecekan Kadaluarsa
    |--------------------------------------------------------------------------
    |
    | Seberapa sering (dalam menit) command expire dijalankan oleh scheduler.
    | Nilai umum: 30 atau 60. Gunakan nilai lebih kecil untuk UX yang lebih
    | responsif, namun pertimbangkan beban query database.
    |
    */
    'expiry_check_interval_minutes' => 30,

];
