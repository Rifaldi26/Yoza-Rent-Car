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
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pemesanan_id')->constrained('pemesanans')->cascadeOnDelete();
        $table->string('midtrans_order_id')->unique();
        $table->string('midtrans_transaction_id')->nullable();
        $table->decimal('amount', 10, 2);
        $table->string('payment_method', 100)->nullable();
        $table->enum('status', ['pending', 'paid', 'expired', 'failed', 'refunded'])->default('pending');
        $table->timestamp('paid_at')->nullable();
        $table->json('midtrans_payload')->nullable();
        $table->timestamps();

        $table->index('midtrans_order_id');
        $table->index(['pemesanan_id', 'status']);
    });
}

public function down(): void
{
    Schema::dropIfExists('payments');
}
};
