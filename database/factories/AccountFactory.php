<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * Default: akun pengeluaran biasa (bukan akun sistem), supaya aman
     * dipakai pada test yang mencoba mengubah/menghapusnya.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode' => '5-'.fake()->unique()->numberBetween(100, 999),
            'nama' => fake()->words(2, true),
            'tipe' => 'pengeluaran',
            'balance' => 0,
            'is_system' => false,
        ];
    }

    /**
     * State: akun Kas (1-001) — dipakai banyak alur pembukuan.
     */
    public function kas(): static
    {
        return $this->state(fn (array $attributes) => [
            'kode' => '1-001',
            'nama' => 'Kas',
            'tipe' => 'aset',
            'is_system' => true,
        ]);
    }

    /**
     * State: akun Pendapatan Sewa (4-001).
     */
    public function pendapatanSewa(): static
    {
        return $this->state(fn (array $attributes) => [
            'kode' => '4-001',
            'nama' => 'Pendapatan Sewa',
            'tipe' => 'pendapatan',
            'is_system' => true,
        ]);
    }

    /**
     * State: akun Pendapatan Jasa Supir (4-002).
     */
    public function pendapatanSupir(): static
    {
        return $this->state(fn (array $attributes) => [
            'kode' => '4-002',
            'nama' => 'Pendapatan Jasa Supir',
            'tipe' => 'pendapatan',
            'is_system' => true,
        ]);
    }

    /**
     * State: akun bertanda sistem (tidak dapat diedit/hapus).
     */
    public function system(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_system' => true,
        ]);
    }
}
