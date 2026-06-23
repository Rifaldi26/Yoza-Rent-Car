<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\JournalEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JournalEntry>
 */
class JournalEntryFactory extends Factory
{
    protected $model = JournalEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'pemesanan_id' => null,
            'payment_id' => null,
            'debit' => fake()->randomElement([0, fake()->numberBetween(50_000, 500_000)]),
            'credit' => 0,
            'description' => fake()->sentence(4),
            'date' => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
