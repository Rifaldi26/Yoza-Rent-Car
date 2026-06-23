<?php

namespace Database\Factories;

use App\Models\Pesan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pesan>
 */
class PesanFactory extends Factory
{
    protected $model = Pesan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pengirim_id' => User::factory(),
            'penerima_id' => User::factory(),
            'isi' => fake()->sentence(8),
            'pemesanan_id' => null,
            'dibaca' => false,
        ];
    }

    /**
     * State: pesan sudah dibaca penerima.
     */
    public function dibaca(): static
    {
        return $this->state(fn (array $attributes) => [
            'dibaca' => true,
        ]);
    }
}
