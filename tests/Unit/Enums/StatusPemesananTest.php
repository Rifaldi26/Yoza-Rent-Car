<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\StatusPemesanan;
use Tests\TestCase;

/**
 * Unit test StatusPemesanan.
 *
 * Memastikan label, warna badge, dan state helper (bisaDibatalkan,
 * bisaDikonfirmasiAdmin, bisaSelesai, aktif) konsisten — enum ini
 * jadi satu-satunya sumber kebenaran status pemesanan di seluruh app.
 */
final class StatusPemesananTest extends TestCase
{
    public function test_setiap_status_memiliki_label(): void
    {
        foreach (StatusPemesanan::cases() as $status) {
            $this->assertNotEmpty($status->label());
        }
    }

    public function test_setiap_status_memiliki_warna_badge(): void
    {
        foreach (StatusPemesanan::cases() as $status) {
            $this->assertNotEmpty($status->warnaBadge());
        }
    }

    public function test_label_pending_benar(): void
    {
        $this->assertEquals('Menunggu Pembayaran', StatusPemesanan::Pending->label());
    }

    public function test_label_dikonfirmasi_benar(): void
    {
        $this->assertEquals('Dikonfirmasi', StatusPemesanan::Dikonfirmasi->label());
    }

    public function test_status_aktif_mencakup_pending_menunggu_dan_dikonfirmasi(): void
    {
        $aktif = StatusPemesanan::aktif();

        $this->assertContains(StatusPemesanan::Pending->value, $aktif);
        $this->assertContains(StatusPemesanan::MenungguKonfirmasiAdmin->value, $aktif);
        $this->assertContains(StatusPemesanan::Dikonfirmasi->value, $aktif);
        $this->assertNotContains(StatusPemesanan::Selesai->value, $aktif);
        $this->assertNotContains(StatusPemesanan::Dibatalkan->value, $aktif);
        $this->assertNotContains(StatusPemesanan::Kadaluarsa->value, $aktif);
    }

    public function test_hanya_pending_yang_bisa_dibatalkan(): void
    {
        $this->assertTrue(StatusPemesanan::Pending->bisaDibatalkan());
        $this->assertFalse(StatusPemesanan::MenungguKonfirmasiAdmin->bisaDibatalkan());
        $this->assertFalse(StatusPemesanan::Dikonfirmasi->bisaDibatalkan());
        $this->assertFalse(StatusPemesanan::Selesai->bisaDibatalkan());
    }

    public function test_hanya_menunggu_konfirmasi_admin_yang_bisa_dikonfirmasi_admin(): void
    {
        $this->assertTrue(StatusPemesanan::MenungguKonfirmasiAdmin->bisaDikonfirmasiAdmin());
        $this->assertFalse(StatusPemesanan::Pending->bisaDikonfirmasiAdmin());
        $this->assertFalse(StatusPemesanan::Dikonfirmasi->bisaDikonfirmasiAdmin());
    }

    public function test_hanya_dikonfirmasi_yang_bisa_diselesaikan(): void
    {
        $this->assertTrue(StatusPemesanan::Dikonfirmasi->bisaSelesai());
        $this->assertFalse(StatusPemesanan::Pending->bisaSelesai());
        $this->assertFalse(StatusPemesanan::MenungguKonfirmasiAdmin->bisaSelesai());
    }

    public function test_enum_dapat_dibuat_dari_string_value(): void
    {
        $this->assertSame(StatusPemesanan::Selesai, StatusPemesanan::from('selesai'));
        $this->assertSame(StatusPemesanan::Dibatalkan, StatusPemesanan::from('dibatalkan'));
    }
}
