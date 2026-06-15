<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan kolom waktu_mulai dan tipe_sewa ke tabel pemesanans.
 *
 * waktu_mulai  : waktu mulai sewa dalam format HH:MM, hanya diisi
 *                untuk sewa 12 jam (tanggal_mulai == tanggal_selesai).
 * tipe_sewa    : 'harian' (default) atau '12_jam'.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            // Waktu mulai sewa — digunakan saat tipe_sewa = '12_jam'
            $table->time('waktu_mulai')->nullable()->after('tanggal_selesai');

            // Tipe sewa: harian (per hari) atau 12_jam (half-day)
            $table->enum('tipe_sewa', ['harian', '12_jam'])
                ->default('harian')
                ->after('waktu_mulai');
        });
    }

    public function down(): void
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            $table->dropColumn(['waktu_mulai', 'tipe_sewa']);
        });
    }
};
