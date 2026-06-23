<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Mobil;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * TestCase dasar untuk seluruh suite.
 *
 * Berisi helper bersama (factory shortcut & payload valid) supaya
 * setiap test class tidak perlu mengulang boilerplate yang sama —
 * sejalan dengan prinsip DRY & maintainability proyek ini.
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Buat user dengan role 'admin' & email terverifikasi.
     */
    protected function buatAdmin(array $atribut = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'admin',
            'email_verified_at' => now(),
        ], $atribut));
    }

    /**
     * Buat user biasa (pelanggan) dengan email terverifikasi.
     */
    protected function buatUser(array $atribut = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'user',
            'email_verified_at' => now(),
        ], $atribut));
    }

    /**
     * Buat mobil yang siap dipesan (status tersedia).
     */
    protected function buatMobilTersedia(array $atribut = []): Mobil
    {
        return Mobil::factory()->create(array_merge([
            'status' => 'tersedia',
        ], $atribut));
    }

    /**
     * Payload lengkap & valid untuk POST /pemesanan (store).
     *
     * StorePemesananRequest mewajibkan banyak field tambahan (alamat,
     * tujuan_sewa, kontak_darurat, dll) — helper ini menjamin setiap
     * test pembuatan pemesanan otomatis lolos validasi dasar, kecuali
     * field yang sengaja di-override/dihapus oleh test ybs.
     *
     * @return array<string, mixed>
     */
    protected function payloadPemesananValid(Mobil $mobil, array $override = []): array
    {
        return array_merge([
            'mobil_id' => $mobil->id,
            'tipe_sewa' => 'harian',
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '08:00',
            'tanggal_mulai' => now()->addDay()->toDateString(),
            'tanggal_selesai' => now()->addDays(4)->toDateString(),
            'opsi_supir' => false,
            'catatan' => null,
            'alamat' => 'Jl. Merdeka No. 1, Jakarta',
            'tujuan_sewa' => 'Liburan keluarga',
            'kota_tujuan' => 'Bandung',
            'instagram' => '@pelanggan_yoza',
            'tiktok' => null,
            'status_pekerjaan' => 'bekerja',
            'tempat_kerja' => 'PT Maju Mundur',
            'kampus' => null,
            'sumber_info' => 'Instagram',
            'kontak_darurat' => '081234567890',
            'share_lokasi' => 'https://maps.app.goo.gl/contoh',
        ], $override);
    }
}