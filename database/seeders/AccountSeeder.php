<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // ── ASET ──────────────────────────────────────────
            [
                'kode'      => '1-001',
                'nama'      => 'Kas',
                'tipe'      => 'aset',
                'balance'   => 0,
                'is_system' => true,
            ],
            [
                'kode'      => '1-002',
                'nama'      => 'Piutang Sewa',
                'tipe'      => 'aset',
                'balance'   => 0,
                'is_system' => true,
            ],

            // ── PENDAPATAN ────────────────────────────────────
            [
                'kode'      => '4-001',
                'nama'      => 'Pendapatan Sewa',
                'tipe'      => 'pendapatan',
                'balance'   => 0,
                'is_system' => true,
            ],
            [
                'kode'      => '4-002',
                'nama'      => 'Pendapatan Jasa Supir',
                'tipe'      => 'pendapatan',
                'balance'   => 0,
                'is_system' => true,
            ],

            // ── PENGELUARAN ───────────────────────────────────
            [
                'kode'      => '5-001',
                'nama'      => 'Biaya Servis & Perawatan',
                'tipe'      => 'pengeluaran',
                'balance'   => 0,
                'is_system' => false,
            ],
            [
                'kode'      => '5-002',
                'nama'      => 'Biaya Bahan Bakar',
                'tipe'      => 'pengeluaran',
                'balance'   => 0,
                'is_system' => false,
            ],
            [
                'kode'      => '5-003',
                'nama'      => 'Biaya Asuransi',
                'tipe'      => 'pengeluaran',
                'balance'   => 0,
                'is_system' => false,
            ],
            [
                'kode'      => '5-004',
                'nama'      => 'Biaya Gaji Supir',
                'tipe'      => 'pengeluaran',
                'balance'   => 0,
                'is_system' => false,
            ],
            [
                'kode'      => '5-005',
                'nama'      => 'Biaya Administrasi',
                'tipe'      => 'pengeluaran',
                'balance'   => 0,
                'is_system' => false,
            ],
        ];

        foreach ($accounts as $account) {
            Account::updateOrCreate(
                ['kode' => $account['kode']],
                $account
            );
        }
    }
}