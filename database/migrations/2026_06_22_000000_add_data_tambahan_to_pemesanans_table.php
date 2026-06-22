<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahan data yang wajib diisi user saat membuat pemesanan
     * (di luar yang sudah ada: tanggal, mobil, opsi supir, catatan).
     *
     * Lampiran berupa foto (screenshot sosmed, foto ID card, KTM/KRS)
     * SENGAJA tidak punya kolom di sini — itu dikirim manual lewat
     * WhatsApp saat user klik "Konfirmasi via WA", bukan upload di web.
     */
    public function up(): void
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            $table->text('alamat')->nullable()->after('catatan');
            $table->string('tujuan_sewa')->nullable()->after('alamat');
            $table->string('kota_tujuan')->nullable()->after('tujuan_sewa');

            $table->string('instagram')->nullable()->after('kota_tujuan');
            $table->string('tiktok')->nullable()->after('instagram');

            // 'bekerja' | 'mahasiswa' — saling eksklusif, lihat StorePemesananRequest
            $table->enum('status_pekerjaan', ['bekerja', 'mahasiswa'])->nullable()->after('tiktok');
            $table->string('tempat_kerja')->nullable()->after('status_pekerjaan');
            $table->string('kampus')->nullable()->after('tempat_kerja');

            $table->string('sumber_info')->nullable()->after('kampus');
            $table->string('kontak_darurat')->nullable()->after('sumber_info');
            $table->string('share_lokasi')->nullable()->after('kontak_darurat');
        });
    }

    public function down(): void
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            $table->dropColumn([
                'alamat',
                'tujuan_sewa',
                'kota_tujuan',
                'instagram',
                'tiktok',
                'status_pekerjaan',
                'tempat_kerja',
                'kampus',
                'sumber_info',
                'kontak_darurat',
                'share_lokasi',
            ]);
        });
    }
};
