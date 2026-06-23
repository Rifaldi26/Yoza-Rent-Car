<?php

namespace Database\Factories;

use App\Enums\StatusPemesanan;
use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pemesanan>
 */
class PemesananFactory extends Factory
{
    protected $model = Pemesanan::class;

    /**
     * Define the model's default state.
     *
     * Default: sewa harian 3 hari dimulai besok, status pending,
     * tanpa supir. relasi user & mobil dibuat otomatis bila tidak
     * di-override (mis. via ->for() atau ['mobil_id' => $mobil->id]).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tanggalMulai = fake()->dateTimeBetween('+1 day', '+10 days');
        $tanggalSelesai = (clone $tanggalMulai)->modify('+3 days');
        $hargaPerHari = 200_000;

        return [
            'user_id'         => User::factory(),
            'mobil_id'        => Mobil::factory(),
            'tanggal_mulai'   => $tanggalMulai->format('Y-m-d'),
            'tanggal_selesai' => $tanggalSelesai->format('Y-m-d'),
            'waktu_mulai'     => null,
            'waktu_selesai'   => null,
            'tipe_sewa'       => 'harian',
            'opsi_supir'      => false,
            'biaya_supir'     => null,
            'total_harga'     => $hargaPerHari * 3,
            'status'          => StatusPemesanan::Pending->value,
            'catatan'         => null,
        ];
    }

    /**
     * State: pemesanan sudah dikonfirmasi admin.
     */
    public function dikonfirmasi(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusPemesanan::Dikonfirmasi->value,
        ]);
    }

    /**
     * State: pemesanan sudah selesai (dipakai untuk uji ulasan/pembukuan).
     */
    public function selesai(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusPemesanan::Selesai->value,
        ]);
    }

    /**
     * State: sewa tipe 12 jam (tanggal_mulai == tanggal_selesai).
     */
    public function dua12Jam(): static
    {
        return $this->state(function (array $attributes) {
            $tanggal = fake()->dateTimeBetween('+1 day', '+10 days')->format('Y-m-d');

            return [
                'tipe_sewa'       => '12_jam',
                'tanggal_mulai'   => $tanggal,
                'tanggal_selesai' => $tanggal,
                'waktu_mulai'     => '08:00',
                'waktu_selesai'   => '20:00',
            ];
        });
    }
}