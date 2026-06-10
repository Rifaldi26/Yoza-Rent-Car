<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Hapus kolom Midtrans
            $table->dropColumn([
                'midtrans_order_id',
                'midtrans_transaction_id',
                'payment_method',
                'midtrans_payload',
            ]);

            // Tambah kolom baru
            $table->enum('metode', ['cash', 'transfer', 'qris', 'edc'])
                ->after('amount')
                ->nullable();
            $table->timestamp('wa_sent_at')->nullable()->after('paid_at');

            // Update enum status — hapus refunded, tambah menunggu_konfirmasi
            $table->enum('status', [
                'pending',
                'menunggu_konfirmasi',
                'dikonfirmasi',
                'dibatalkan',
            ])->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['metode', 'wa_sent_at']);
            $table->string('midtrans_order_id')->nullable()->unique();
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('payment_method', 100)->nullable();
            $table->json('midtrans_payload')->nullable();
            $table->enum('status', ['pending','paid','expired','failed','refunded'])
                ->default('pending')->change();
        });
    }
};