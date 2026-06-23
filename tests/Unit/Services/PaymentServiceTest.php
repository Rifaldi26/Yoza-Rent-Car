<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Contracts\NotifikasiServiceInterface;
use App\Enums\StatusMobil;
use App\Enums\StatusPayment;
use App\Enums\StatusPemesanan;
use App\Exceptions\PaymentException;
use App\Jobs\KirimEmailPemesanan;
use App\Models\Mobil;
use App\Models\Payment;
use App\Models\Pemesanan;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * Unit test PaymentService.
 *
 * Mencakup: pemilihan metode pembayaran, konfirmasi oleh admin,
 * dan pembangunan URL WhatsApp dari template konfigurasi.
 */
final class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaymentService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $notifikasiService = $this->createMock(NotifikasiServiceInterface::class);
        $this->service = new PaymentService($notifikasiService);
    }

    // ── pilihMetode ─────────────────────────────────────────────────────────

    public function test_pilih_metode_membuat_record_payment_dan_mengubah_status_pemesanan(): void
    {
        Bus::fake();

        $user = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $user->id,
            'status' => StatusPemesanan::Pending->value,
            'total_harga' => 500_000,
        ]);

        $payment = $this->service->pilihMetode($pemesanan, 'transfer');

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'pemesanan_id' => $pemesanan->id,
            'amount' => '500000.00',
            'metode' => 'transfer',
            'status' => StatusPayment::MenungguKonfirmasi->value,
        ]);

        $this->assertEquals(
            StatusPemesanan::MenungguKonfirmasiAdmin->value,
            $pemesanan->fresh()->status,
        );

        Bus::assertDispatched(KirimEmailPemesanan::class);
    }

    public function test_pilih_metode_kedua_kali_meng_update_record_yang_sama(): void
    {
        Bus::fake();

        $pemesanan = Pemesanan::factory()->create(['status' => StatusPemesanan::Pending->value]);

        $payment1 = $this->service->pilihMetode($pemesanan, 'cash');
        $payment2 = $this->service->pilihMetode($pemesanan->fresh(), 'qris');

        $this->assertEquals($payment1->id, $payment2->id);
        $this->assertEquals('qris', $payment2->fresh()->metode);
        $this->assertDatabaseCount('payments', 1);
    }

    // ── konfirmasiPembayaran ─────────────────────────────────────────────────

    public function test_konfirmasi_pembayaran_mengubah_status_payment_dan_pemesanan(): void
    {
        Bus::fake();

        $mobil = Mobil::factory()->create(['status' => StatusMobil::Tersedia->value]);
        $pemesanan = Pemesanan::factory()->create([
            'mobil_id' => $mobil->id,
            'status' => StatusPemesanan::MenungguKonfirmasiAdmin->value,
        ]);
        Payment::factory()->create([
            'pemesanan_id' => $pemesanan->id,
            'status' => StatusPayment::MenungguKonfirmasi->value,
        ]);

        $this->service->konfirmasiPembayaran($pemesanan);

        $this->assertEquals(
            StatusPayment::Dikonfirmasi->value,
            $pemesanan->payment->fresh()->status,
        );
        $this->assertNotNull($pemesanan->payment->fresh()->paid_at);
        $this->assertEquals(StatusPemesanan::Dikonfirmasi->value, $pemesanan->fresh()->status);
        $this->assertEquals(StatusMobil::Disewa->value, $mobil->fresh()->status);

        Bus::assertDispatched(KirimEmailPemesanan::class);
    }

    public function test_konfirmasi_pembayaran_gagal_jika_payment_tidak_ada(): void
    {
        $this->expectException(PaymentException::class);

        $pemesanan = Pemesanan::factory()->create();

        $this->service->konfirmasiPembayaran($pemesanan);
    }

    public function test_konfirmasi_pembayaran_gagal_jika_sudah_dikonfirmasi_sebelumnya(): void
    {
        $this->expectException(PaymentException::class);

        $pemesanan = Pemesanan::factory()->create();
        Payment::factory()->dikonfirmasi()->create(['pemesanan_id' => $pemesanan->id]);

        $this->service->konfirmasiPembayaran($pemesanan);
    }

    // ── bangunUrlWhatsApp ────────────────────────────────────────────────────

    public function test_bangun_url_whatsapp_mengandung_nomor_wa_dan_terenkode(): void
    {
        $pemesanan = Pemesanan::factory()->create([
            'tanggal_mulai' => '2026-07-10',
            'tanggal_selesai' => '2026-07-13',
            'total_harga' => 600_000,
        ]);

        $url = $this->service->bangunUrlWhatsApp($pemesanan, 'cash');

        $this->assertStringStartsWith('https://wa.me/', $url);
        $this->assertStringContainsString(config('payment.wa_number'), $url);
        $this->assertStringContainsString('text=', $url);
    }

    public function test_bangun_url_whatsapp_berbeda_per_metode_pembayaran(): void
    {
        $pemesanan = Pemesanan::factory()->create();

        $urlCash = $this->service->bangunUrlWhatsApp($pemesanan, 'cash');
        $urlTransfer = $this->service->bangunUrlWhatsApp($pemesanan, 'transfer');

        $this->assertNotEquals($urlCash, $urlTransfer);
    }
}
