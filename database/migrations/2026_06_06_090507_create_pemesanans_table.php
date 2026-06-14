<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pemesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mobil_id')->constrained('mobils')->cascadeOnDelete();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
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

            $table->index(['mobil_id', 'tanggal_mulai', 'tanggal_selesai']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemesanans');
    }
};
