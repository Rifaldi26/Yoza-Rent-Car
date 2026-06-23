<?php

namespace Database\Factories;

use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notifikasi>
 */
class NotifikasiFactory extends Factory
{
    protected $model = Notifikasi::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'judul' => fake()->sentence(3),
            'pesan' => fake()->sentence(10),
            'tipe' => fake()->randomElement(['info', 'success', 'warning']),
            'link' => null,
            'dibaca' => false,
        ];
    }

    /**
     * State: notifikasi sudah dibaca.
     */
    public function dibaca(): static
    {
        return $this->state(fn (array $attributes) => [
            'dibaca' => true,
        ]);
    }
}
