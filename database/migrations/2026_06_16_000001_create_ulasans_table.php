<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ulasans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mobil_id')->constrained('mobils')->cascadeOnDelete();
            $table->foreignId('pemesanan_id')->constrained('pemesanans')->cascadeOnDelete();
            $table->tinyInteger('rating');             // 1–5
            $table->text('komentar')->nullable();
            $table->boolean('disetujui')->default(false); // moderasi admin
            $table->timestamps();

            // Satu pemesanan hanya boleh satu ulasan
            $table->unique('pemesanan_id');

            // Index performa
            $table->index(['mobil_id', 'disetujui']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ulasans');
    }
};
