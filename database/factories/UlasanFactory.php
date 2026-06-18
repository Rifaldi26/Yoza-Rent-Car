<?php

namespace Database\Factories;

use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Models\Ulasan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ulasan>
 */
class UlasanFactory extends Factory
{
    protected $model = Ulasan::class;

    /**
     * Define the model's default state.
     *
     * Default: ulasan rating acak, belum disetujui admin (menunggu moderasi),
     * sesuai state awal yang dihasilkan User\UlasanController::store().
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'      => User::factory(),
            'mobil_id'     => Mobil::factory(),
            'pemesanan_id' => Pemesanan::factory()->selesai(),
            'rating'       => fake()->numberBetween(1, 5),
            'komentar'     => fake()->sentence(15),
            'disetujui'    => false,
        ];
    }

    /**
     * State: ulasan sudah disetujui admin dan tayang publik.
     */
    public function disetujui(): static
    {
        return $this->state(fn (array $attributes) => [
            'disetujui' => true,
        ]);
    }
}
