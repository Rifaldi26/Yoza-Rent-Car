<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\StatusPayment;
use Tests\TestCase;

/**
 * Unit test StatusPayment.
 */
final class StatusPaymentTest extends TestCase
{
    public function test_setiap_status_memiliki_label(): void
    {
        foreach (StatusPayment::cases() as $status) {
            $this->assertNotEmpty($status->label());
        }
    }

    public function test_hanya_dikonfirmasi_yang_dianggap_sudah_dibayar(): void
    {
        $this->assertTrue(StatusPayment::Dikonfirmasi->sudahDibayar());
        $this->assertFalse(StatusPayment::Pending->sudahDibayar());
        $this->assertFalse(StatusPayment::MenungguKonfirmasi->sudahDibayar());
        $this->assertFalse(StatusPayment::Dibatalkan->sudahDibayar());
    }

    public function test_enum_dapat_dibuat_dari_string_value(): void
    {
        $this->assertSame(StatusPayment::Dikonfirmasi, StatusPayment::from('dikonfirmasi'));
    }
}
