<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Membuat seluruh tabel aplikasi Yoza Rent Car sesuai struktur database aktual.
 *
 * Urutan pembuatan mengikuti dependency foreign key:
 *   users → mobils → pemesanans → payments → journal_entries
 *                              → pesans
 *                              → notifikasis
 *                  → favorits
 *   accounts → journal_entries
 *   pages (tidak ada FK)
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Accounts ───────────────────────────────────────────────────────
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama');
            $table->enum('tipe', ['aset', 'pendapatan', 'pengeluaran', 'liabilitas']);
            $table->decimal('balance', 12, 2)->default(0);
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        // ── 2. Mobils ─────────────────────────────────────────────────────────
        Schema::create('mobils', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('merek');
            $table->year('tahun');
            $table->string('plat_nomor')->unique();
            $table->decimal('harga_per_hari', 10, 2);
            $table->decimal('biaya_supir_per_hari', 10, 2)->nullable();
            $table->enum('status', ['tersedia', 'disewa', 'perawatan'])->default('tersedia');
            $table->string('foto')->nullable();
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // ── 3. Pemesanans ─────────────────────────────────────────────────────
        Schema::create('pemesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mobil_id')->constrained('mobils')->cascadeOnDelete();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->time('waktu_mulai')->nullable();
            $table->enum('tipe_sewa', ['harian', '12_jam'])->default('harian');
            $table->boolean('opsi_supir')->default(false);
            $table->decimal('biaya_supir', 10, 2)->nullable();
            $table->decimal('total_harga', 10, 2);
            $table->enum('status', [
                'pending',
                'menunggu_konfirmasi_admin',
                'dikonfirmasi',
                'selesai',
                'dibatalkan',
                'kadaluarsa',
            ])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Index performa
            $table->index(['mobil_id', 'tanggal_mulai', 'tanggal_selesai']);
            $table->index(['user_id', 'status']);
            $table->index('status');
            $table->index(['mobil_id', 'status', 'tanggal_mulai', 'tanggal_selesai']);
        });

        // ── 4. Payments ───────────────────────────────────────────────────────
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemesanan_id')->constrained('pemesanans')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('metode', ['cash', 'transfer', 'qris', 'edc'])->nullable();
            $table->enum('status', [
                'pending',
                'menunggu_konfirmasi',
                'dikonfirmasi',
                'dibatalkan',
            ])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('wa_sent_at')->nullable();
            $table->timestamps();

            // Index performa
            $table->index(['pemesanan_id', 'status']);
            $table->index(['status', 'paid_at']);
        });

        // ── 5. Journal Entries ────────────────────────────────────────────────
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('pemesanan_id')->nullable()->constrained('pemesanans')->nullOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->decimal('debit', 12, 2)->default(0);
            $table->decimal('credit', 12, 2)->default(0);
            $table->string('description', 500)->nullable();
            $table->date('date');
            $table->timestamps();

            // Index performa
            $table->index(['account_id', 'date']);
            $table->index('pemesanan_id');
        });

        // ── 6. Notifikasis ────────────────────────────────────────────────────
        Schema::create('notifikasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('judul');
            $table->text('pesan');
            $table->string('tipe')->default('info');
            $table->string('link')->nullable();
            $table->boolean('dibaca')->default(false);
            $table->timestamps();

            // Index performa
            $table->index(['user_id', 'dibaca']);
        });

        // ── 7. Favorits ───────────────────────────────────────────────────────
        Schema::create('favorits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mobil_id')->constrained('mobils')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'mobil_id']);
        });

        // ── 8. Pesans (chat) ──────────────────────────────────────────────────
        Schema::create('pesans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengirim_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('penerima_id')->constrained('users')->cascadeOnDelete();
            $table->text('isi');
            $table->foreignId('pemesanan_id')->nullable()->constrained('pemesanans')->nullOnDelete();
            $table->boolean('dibaca')->default(false);
            $table->timestamps();

            // Index performa
            $table->index(['pengirim_id', 'penerima_id']);
            $table->index(['penerima_id', 'dibaca']);
        });

        // ── 9. Pages (CMS) ────────────────────────────────────────────────────
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->longText('content');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Drop dalam urutan terbalik untuk menghindari FK constraint error
        Schema::dropIfExists('pages');
        Schema::dropIfExists('pesans');
        Schema::dropIfExists('favorits');
        Schema::dropIfExists('notifikasis');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('pemesanans');
        Schema::dropIfExists('mobils');
        Schema::dropIfExists('accounts');
    }
};
