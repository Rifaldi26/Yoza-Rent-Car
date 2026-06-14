<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan index pada kolom yang sering difilter/diurutkan
 * untuk meningkatkan performa query di tabel pemesanans dan notifikasis.
 *
 * Diverifikasi sebelum dibuat agar migrasi aman untuk dijalankan
 * berulang kali (idempotent via hasIndex check).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            // Filter status adalah query paling sering dijalankan
            if (! $this->hasIndex('pemesanans', 'pemesanans_status_index')) {
                $table->index('status');
            }

            // Composite index untuk cek konflik tanggal: adaKonflik()
            if (! $this->hasIndex('pemesanans', 'pemesanans_mobil_status_tanggal_index')) {
                $table->index(['mobil_id', 'status', 'tanggal_mulai', 'tanggal_selesai'], 'pemesanans_mobil_status_tanggal_index');
            }

            // Filter per user di halaman daftar pemesanan user
            if (! $this->hasIndex('pemesanans', 'pemesanans_user_id_status_index')) {
                $table->index(['user_id', 'status']);
            }
        });

        Schema::table('notifikasis', function (Blueprint $table) {
            // Kueri unread count dijalankan di setiap halaman
            if (! $this->hasIndex('notifikasis', 'notifikasis_user_id_dibaca_index')) {
                $table->index(['user_id', 'dibaca']);
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            // Filter laporan pendapatan per bulan/tahun
            if (! $this->hasIndex('payments', 'payments_status_paid_at_index')) {
                $table->index(['status', 'paid_at']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            $table->dropIndexIfExists('pemesanans_status_index');
            $table->dropIndexIfExists('pemesanans_mobil_status_tanggal_index');
            $table->dropIndexIfExists('pemesanans_user_id_status_index');
        });

        Schema::table('notifikasis', function (Blueprint $table) {
            $table->dropIndexIfExists('notifikasis_user_id_dibaca_index');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndexIfExists('payments_status_paid_at_index');
        });
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        return collect(Schema::getIndexes($table))
            ->pluck('name')
            ->contains($indexName);
    }
};
