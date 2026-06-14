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
        Schema::create('pesans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengirim_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('penerima_id')->constrained('users')->cascadeOnDelete();
            $table->text('isi');
            $table->foreignId('pemesanan_id')->nullable()->constrained('pemesanans')->nullOnDelete();
            $table->boolean('dibaca')->default(false);
            $table->timestamps();

            $table->index(['pengirim_id', 'penerima_id']);
            $table->index(['penerima_id', 'dibaca']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesans');
    }
};
