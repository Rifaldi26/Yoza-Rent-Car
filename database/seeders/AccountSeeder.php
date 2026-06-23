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
                'kode' => '1-001',
                'nama' => 'Kas',
                'tipe' => 'aset',
                'balance' => 0,
                'is_system' => true,
            ],
            [
                'kode' => '1-002',
                'nama' => 'Piutang Sewa',
                'tipe' => 'aset',
                'balance' => 0,
                'is_system' => true,
            ],
            [
                'kode' => '1-003',
                'nama' => 'Perlengkapan Kantor',
                'tipe' => 'aset',
                'balance' => 0,
                'is_system' => false,
            ],
            [
                'kode' => '1-101',
                'nama' => 'Armada Mobil',
                'tipe' => 'aset',
                'balance' => 0,
                'is_system' => false,
            ],
            [
                'kode' => '1-102',
                'nama' => 'Akumulasi Penyusutan Mobil',
                'tipe' => 'aset',
                'balance' => 0,
                'is_system' => false,
            ],
            [
                'kode' => '1-103',
                'nama' => 'Peralatan Kantor',
                'tipe' => 'aset',
                'balance' => 0,
                'is_system' => false,
            ],

            // ── LIABILITAS ────────────────────────────────────
            [
                'kode' => '2-001',
                'nama' => 'Utang Usaha',
                'tipe' => 'liabilitas',
                'balance' => 0,
                'is_system' => false,
            ],
            [
                'kode' => '2-002',
                'nama' => 'Utang Gaji',
                'tipe' => 'liabilitas',
                'balance' => 0,
                'is_system' => false,
            ],
            [
                'kode' => '2-003',
                'nama' => 'Pendapatan Diterima Dimuka',
                'tipe' => 'liabilitas',
                'balance' => 0,
                'is_system' => false,
            ],
            [
                'kode' => '2-101',
                'nama' => 'Utang Bank',
                'tipe' => 'liabilitas',
                'balance' => 0,
                'is_system' => false,
            ],

            // ── MODAL (EKUITAS) ───────────────────────────────
            [
                'kode' => '3-001',
                'nama' => 'Modal Pemilik',
                'tipe' => 'modal',
                'balance' => 0,
                'is_system' => false,
            ],
            [
                'kode' => '3-002',
                'nama' => 'Prive',
                'tipe' => 'modal',
                'balance' => 0,
                'is_system' => false,
            ],

            // ── PENDAPATAN ────────────────────────────────────
            [
                'kode' => '4-001',
                'nama' => 'Pendapatan Sewa',
                'tipe' => 'pendapatan',
                'balance' => 0,
                'is_system' => true,
            ],
            [
                'kode' => '4-002',
                'nama' => 'Pendapatan Jasa Supir',
                'tipe' => 'pendapatan',
                'balance' => 0,
                'is_system' => true,
            ],
            [
                'kode' => '4-003',
                'nama' => 'Pendapatan Denda',
                'tipe' => 'pendapatan',
                'balance' => 0,
                'is_system' => false,
            ],

            // ── PENGELUARAN ───────────────────────────────────
            [
                'kode' => '5-001',
                'nama' => 'Biaya Servis & Perawatan',
                'tipe' => 'pengeluaran',
                'balance' => 0,
                'is_system' => false,
            ],
            [
                'kode' => '5-002',
                'nama' => 'Biaya Bahan Bakar',
                'tipe' => 'pengeluaran',
                'balance' => 0,
                'is_system' => false,
            ],
            [
                'kode' => '5-003',
                'nama' => 'Biaya Asuransi',
                'tipe' => 'pengeluaran',
                'balance' => 0,
                'is_system' => false,
            ],
            [
                'kode' => '5-004',
                'nama' => 'Biaya Gaji Supir',
                'tipe' => 'pengeluaran',
                'balance' => 0,
                'is_system' => false,
            ],
            [
                'kode' => '5-005',
                'nama' => 'Biaya Administrasi',
                'tipe' => 'pengeluaran',
                'balance' => 0,
                'is_system' => false,
            ],
            [
                'kode' => '5-006',
                'nama' => 'Biaya Penyusutan Mobil',
                'tipe' => 'pengeluaran',
                'balance' => 0,
                'is_system' => false,
            ],
            [
                'kode' => '5-007',
                'nama' => 'Biaya Pajak dan STNK',
                'tipe' => 'pengeluaran',
                'balance' => 0,
                'is_system' => false,
            ],
            [
                'kode' => '5-008',
                'nama' => 'Biaya Pemasaran',
                'tipe' => 'pengeluaran',
                'balance' => 0,
                'is_system' => false,
            ],
            [
                'kode' => '5-009',
                'nama' => 'Biaya Listrik dan Air',
                'tipe' => 'pengeluaran',
                'balance' => 0,
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
