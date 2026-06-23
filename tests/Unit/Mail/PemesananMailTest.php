<?php

declare(strict_types=1);

namespace Tests\Unit\Mail;

use App\Mail\PemesananDibatalkan;
use App\Mail\PemesananDibayar;
use App\Mail\PemesananDibuat;
use App\Mail\PemesananDikonfirmasi;
use App\Mail\PemesananDitolak;
use App\Mail\PemesananSelesai;
use App\Mail\PengingatSewa;
use App\Mail\PesananBaruAdmin;
use App\Models\Pemesanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PemesananMailTest extends TestCase
{
    use RefreshDatabase;

    private Pemesanan $pemesanan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pemesanan = Pemesanan::factory()->create();
    }

    public function test_pemesanan_dibuat_envelope(): void
    {
        $mail = new PemesananDibuat($this->pemesanan);

        $this->assertStringContainsString('Berhasil Dibuat', $mail->envelope()->subject);
    }

    public function test_pemesanan_dibayar_envelope(): void
    {
        $mail = new PemesananDibayar($this->pemesanan);

        $this->assertStringContainsString('Pembayaran Diterima', $mail->envelope()->subject);
    }

    public function test_pemesanan_dikonfirmasi_envelope(): void
    {
        $mail = new PemesananDikonfirmasi($this->pemesanan);

        $this->assertStringContainsString('Dikonfirmasi', $mail->envelope()->subject);
    }

    public function test_pemesanan_ditolak_envelope(): void
    {
        $mail = new PemesananDitolak($this->pemesanan);

        $this->assertStringContainsString('Informasi', $mail->envelope()->subject);
    }

    public function test_pemesanan_dibatalkan_envelope(): void
    {
        $mail = new PemesananDibatalkan($this->pemesanan);

        $this->assertStringContainsString('Dibatalkan', $mail->envelope()->subject);
    }

    public function test_pemesanan_selesai_envelope(): void
    {
        $mail = new PemesananSelesai($this->pemesanan);

        $this->assertStringContainsString('Selesai', $mail->envelope()->subject);
    }

    public function test_pengingat_sewa_envelope(): void
    {
        $mail = new PengingatSewa($this->pemesanan, 1);

        $this->assertStringContainsString('Besok', $mail->envelope()->subject);
    }

    public function test_pengingat_sewa_h3_envelope(): void
    {
        $mail = new PengingatSewa($this->pemesanan, 3);

        $this->assertStringContainsString('3 Hari Lagi', $mail->envelope()->subject);
    }

    public function test_pesanan_baru_admin_envelope(): void
    {
        $mail = new PesananBaruAdmin($this->pemesanan);

        $this->assertStringContainsString('Pesanan Baru', $mail->envelope()->subject);
    }
}