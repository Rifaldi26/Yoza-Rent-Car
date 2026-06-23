<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Sanity check dasar bahwa aplikasi Laravel berhasil boot dan
 * merespons HTTP request, sebelum suite test fitur lain dijalankan.
 *
 * Memakai RefreshDatabase karena halaman utama ('/') menampilkan
 * katalog mobil (lihat User\MobilController::index()) yang melakukan
 * query ke tabel 'mobils' — tanpa migrasi, query ini gagal.
 */
final class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_utama_dapat_diakses(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }
}