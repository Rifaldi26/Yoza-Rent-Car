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
}

public function down(): void
{
    Schema::dropIfExists('mobils');
}
};
