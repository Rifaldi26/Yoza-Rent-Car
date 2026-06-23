<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah tipe akun 'modal' (ekuitas) — dibutuhkan untuk akun seperti
     * "Modal Pemilik" & "Prive" yang tidak masuk kategori aset/
     * pendapatan/pengeluaran/liabilitas.
     *
     * Memakai Blueprint::change() (didukung native sejak Laravel 11,
     * tanpa doctrine/dbal) agar tetap berjalan di MySQL (production)
     * maupun SQLite (testing).
     */
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->enum('tipe', ['aset', 'pendapatan', 'pengeluaran', 'liabilitas', 'modal'])->change();
        });
    }

    public function down(): void
    {
        // Hapus dulu akun bertipe 'modal' supaya tidak ditolak saat
        // enum dikembalikan ke daftar yang lebih sempit.
        DB::table('accounts')->where('tipe', 'modal')->delete();

        Schema::table('accounts', function (Blueprint $table) {
            $table->enum('tipe', ['aset', 'pendapatan', 'pengeluaran', 'liabilitas'])->change();
        });
    }
};
