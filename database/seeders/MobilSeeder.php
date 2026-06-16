<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MobilSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('mobils')->insert([
            [
                'nama'                 => 'Agya TRD',
                'merek'               => 'Toyota',
                'tahun'               => 2018,
                'plat_nomor'          => 'R 1304 VA',
                'harga_per_hari'      => 300_000,
                'biaya_supir_per_hari' => 250_000,
                'status'              => 'tersedia',
                'foto'                => null,
                'deskripsi'           => null,
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'nama'                 => 'Ayla Tipe R',
                'merek'               => 'Daihatsu',
                'tahun'               => 2018,
                'plat_nomor'          => 'B 2514 UFP',
                'harga_per_hari'      => 300_000,
                'biaya_supir_per_hari' => 250_000,
                'status'              => 'tersedia',
                'foto'                => null,
                'deskripsi'           => null,
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'nama'                 => 'Calya',
                'merek'               => 'Toyota',
                'tahun'               => 2023,
                'plat_nomor'          => 'R 1156 RC',
                'harga_per_hari'      => 300_000,
                'biaya_supir_per_hari' => 250_000,
                'status'              => 'tersedia',
                'foto'                => null,
                'deskripsi'           => null,
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'nama'                 => 'All New Brio',
                'merek'               => 'Honda',
                'tahun'               => 2023,
                'plat_nomor'          => 'R 1509 NC',
                'harga_per_hari'      => 350_000,
                'biaya_supir_per_hari' => 250_000,
                'status'              => 'tersedia',
                'foto'                => null,
                'deskripsi'           => null,
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'nama'                 => 'All New Xenia',
                'merek'               => 'Daihatsu',
                'tahun'               => 2022,
                'plat_nomor'          => 'R 1253 FC',
                'harga_per_hari'      => 400_000,
                'biaya_supir_per_hari' => 250_000,
                'status'              => 'tersedia',
                'foto'                => null,
                'deskripsi'           => null,
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'nama'                 => 'All New Xenia',
                'merek'               => 'Daihatsu',
                'tahun'               => 2023,
                'plat_nomor'          => 'R 1746 NC',
                'harga_per_hari'      => 400_000,
                'biaya_supir_per_hari' => 250_000,
                'status'              => 'tersedia',
                'foto'                => null,
                'deskripsi'           => null,
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'nama'                 => 'Xpander',
                'merek'               => 'Mitsubishi',
                'tahun'               => 2024,
                'plat_nomor'          => 'R 1088 WC',
                'harga_per_hari'      => 450_000,
                'biaya_supir_per_hari' => 250_000,
                'status'              => 'tersedia',
                'foto'                => null,
                'deskripsi'           => null,
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
        ]);
    }
}
