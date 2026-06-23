<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Sanity check dasar bahwa environment testing PHPUnit berjalan
 * normal, tanpa menyentuh Laravel application container.
 *
 * Catatan: sebelumnya file ini memiliki namespace yang salah
 * (Tests\Feature) sehingga bertabrakan dengan
 * tests/Feature/ExampleTest.php saat dijalankan bersamaan
 * (`php artisan test` tanpa filter folder) — root cause dari
 * error "Cannot declare class ExampleTest". Sudah diperbaiki
 * dengan namespace yang benar: Tests\Unit.
 */
final class ExampleTest extends TestCase
{
    public function test_yang_benar_selalu_benar(): void
    {
        $this->assertTrue(true);
    }
}