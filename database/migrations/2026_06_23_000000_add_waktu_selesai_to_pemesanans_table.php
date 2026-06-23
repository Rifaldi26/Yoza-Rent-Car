<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah jam selesai sewa.
     *
     * Sebelumnya kolom `waktu_mulai` hanya diisi untuk sewa 12 jam.
     * Mulai revisi ini, jam mulai & jam selesai wajib diisi untuk SEMUA
     * tipe sewa (harian maupun 12 jam) — lihat StorePemesananRequest
     * dan PemesananService.
     */
    public function up(): void
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            $table->time('waktu_selesai')->nullable()->after('waktu_mulai');
        });
    }

    public function down(): void
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            $table->dropColumn('waktu_selesai');
        });
    }
};
