<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Sanity check dasar bahwa aplikasi Laravel berhasil boot dan
 * merespons HTTP request, sebelum suite test fitur lain dijalankan.
 */
final class ExampleTest extends TestCase
{
    public function test_halaman_utama_dapat_diakses(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }
}