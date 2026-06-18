<?php

namespace Database\Factories;

use App\Enums\StatusMobil;
use App\Models\Mobil;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mobil>
 */
class MobilFactory extends Factory
{
    protected $model = Mobil::class;

    /**
     * Daftar merek & nama mobil yang umum dipakai untuk rental,
     * supaya data uji terasa realistis tanpa perlu paket fake() tambahan.
     */
    private const MEREK_DAN_NAMA = [
        ['merek' => 'Toyota', 'nama' => 'Avanza'],
        ['merek' => 'Toyota', 'nama' => 'Innova'],
        ['merek' => 'Toyota', 'nama' => 'Fortuner'],
        ['merek' => 'Honda', 'nama' => 'Brio'],
        ['merek' => 'Honda', 'nama' => 'HR-V'],
        ['merek' => 'Daihatsu', 'nama' => 'Xenia'],
        ['merek' => 'Mitsubishi', 'nama' => 'Xpander'],
        ['merek' => 'Suzuki', 'nama' => 'Ertiga'],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pilihan = fake()->randomElement(self::MEREK_DAN_NAMA);

        return [
            'nama'                  => $pilihan['nama'],
            'merek'                 => $pilihan['merek'],
            'tahun'                 => fake()->numberBetween(2018, 2025),
            'plat_nomor'            => $this->buatPlatNomorUnik(),
            'harga_per_hari'        => fake()->numberBetween(150_000, 800_000),
            'biaya_supir_per_hari'  => null,
            'status'                => StatusMobil::Tersedia->value,
            'foto'                  => null,
            'deskripsi'             => fake()->sentence(12),
        ];
    }

    /**
     * State: mobil menyediakan opsi supir.
     */
    public function denganSupir(): static
    {
        return $this->state(fn (array $attributes) => [
            'biaya_supir_per_hari' => fake()->numberBetween(75_000, 200_000),
        ]);
    }

    /**
     * State: mobil sedang disewa (tidak tersedia untuk booking baru).
     */
    public function disewa(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusMobil::Disewa->value,
        ]);
    }

    /**
     * State: mobil dalam perawatan.
     */
    public function perawatan(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusMobil::Perawatan->value,
        ]);
    }

    /**
     * Plat nomor unik bergaya Indonesia, mis. "B 1234 ABC".
     * Dibuat manual (bukan fake()->bothify saja) untuk menghindari
     * kemungkinan tabrakan dalam jumlah seeding besar.
     */
    private function buatPlatNomorUnik(): string
    {
        do {
            $plat = sprintf(
                '%s %d %s',
                fake()->randomElement(['B', 'D', 'F', 'L', 'N']),
                fake()->numberBetween(1000, 9999),
                fake()->lexify('???'),
            );
        } while (Mobil::where('plat_nomor', $plat)->exists());

        return strtoupper($plat);
    }
}
