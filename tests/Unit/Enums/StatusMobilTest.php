<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\StatusMobil;
use Tests\TestCase;

/**
 * Unit test StatusMobil.
 */
final class StatusMobilTest extends TestCase
{
    public function test_setiap_status_memiliki_label(): void
    {
        foreach (StatusMobil::cases() as $status) {
            $this->assertNotEmpty($status->label());
        }
    }

    public function test_setiap_status_memiliki_warna_badge(): void
    {
        foreach (StatusMobil::cases() as $status) {
            $this->assertNotEmpty($status->warnaBadge());
        }
    }

    public function test_enum_dapat_dibuat_dari_string_value(): void
    {
        $this->assertSame(StatusMobil::Tersedia, StatusMobil::from('tersedia'));
        $this->assertSame(StatusMobil::Disewa, StatusMobil::from('disewa'));
        $this->assertSame(StatusMobil::Perawatan, StatusMobil::from('perawatan'));
    }
}
