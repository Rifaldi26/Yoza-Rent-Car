<?php

namespace Database\Factories;

use App\Enums\StatusPayment;
use App\Models\Payment;
use App\Models\Pemesanan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * Default: pembayaran masih pending, metode belum dipilih,
     * jumlah mengikuti total_harga pemesanan terkait bila relasi
     * dibuat otomatis (lihat configure()).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pemesanan_id' => Pemesanan::factory(),
            'amount'       => fake()->numberBetween(150_000, 2_000_000),
            'metode'       => null,
            'status'       => StatusPayment::Pending->value,
            'paid_at'      => null,
            'wa_sent_at'   => null,
        ];
    }

    /**
     * State: pembayaran sudah dikonfirmasi admin (lunas).
     */
    public function dikonfirmasi(): static
    {
        return $this->state(fn (array $attributes) => [
            'metode'  => fake()->randomElement(['cash', 'transfer', 'qris', 'edc']),
            'status'  => StatusPayment::Dikonfirmasi->value,
            'paid_at' => now(),
        ]);
    }

    /**
     * State: pelanggan sudah melapor via WhatsApp, menunggu verifikasi admin.
     */
    public function menungguKonfirmasi(): static
    {
        return $this->state(fn (array $attributes) => [
            'metode'     => fake()->randomElement(['transfer', 'qris', 'edc']),
            'status'     => StatusPayment::MenungguKonfirmasi->value,
            'wa_sent_at' => now(),
        ]);
    }
}
