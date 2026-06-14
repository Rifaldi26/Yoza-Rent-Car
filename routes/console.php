<?php

use App\Console\Commands\DispatchRentalReminders;
use App\Console\Commands\ExpireStaleBookings;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Scheduled Tasks — Yoza Rent Car
|--------------------------------------------------------------------------
|
| Semua jadwal task otomatis sistem didefinisikan di sini.
|
| Untuk menjalankan scheduler secara lokal:
|   php artisan schedule:work
|
| Di server production, tambahkan satu cron entry:
|   * * * * * cd /path-to-app && php artisan schedule:run >> /dev/null 2>&1
|
*/

// ── Pengingat Sewa ────────────────────────────────────────────────────────────
//
// Dispatch pengingat H-1 dan H-3 ke pelanggan setiap hari.
// Interval dikonfigurasi via config/rental.php → reminder_intervals.
//
Schedule::command(DispatchRentalReminders::class)
    ->dailyAt(config('rental.reminder_dispatch_time', '08:00'))
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/reminders.log'));

// ── Kadaluarsa Pemesanan ──────────────────────────────────────────────────────
//
// Tandai pemesanan 'pending' yang melewati batas waktu pembayaran
// sebagai 'kadaluarsa'. Batas waktu dikonfigurasi via config/rental.php
// → payment_expiry_hours.
//
Schedule::command(ExpireStaleBookings::class)
    ->everyMinute(config('rental.expiry_check_interval_minutes', 30))
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/bookings-expire.log'));
