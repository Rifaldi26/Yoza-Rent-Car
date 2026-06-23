<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Enums\StatusPayment;
use App\Models\Payment;
use App\Models\Pemesanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit test model Payment: helper status & delegasi ke enum.
 */
final class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_dikonfirmasi_true_jika_status_dikonfirmasi(): void
    {
        $payment = Payment::factory()->create(['status' => StatusPayment::Dikonfirmasi->value]);

        $this->assertTrue($payment->isDikonfirmasi());
        $this->assertTrue($payment->isPaid());
    }

    public function test_is_dikonfirmasi_false_untuk_status_lain(): void
    {
        $payment = Payment::factory()->create(['status' => StatusPayment::Pending->value]);

        $this->assertFalse($payment->isDikonfirmasi());
        $this->assertFalse($payment->isPaid());
    }

    public function test_status_enum_dan_label_status_konsisten(): void
    {
        $payment = Payment::factory()->create(['status' => StatusPayment::MenungguKonfirmasi->value]);

        $this->assertSame(StatusPayment::MenungguKonfirmasi, $payment->statusEnum());
        $this->assertEquals('Menunggu Konfirmasi Admin', $payment->labelStatus());
    }

    public function test_label_metode_mengembalikan_dash_untuk_metode_null(): void
    {
        $payment = Payment::factory()->create(['metode' => null]);

        $this->assertEquals('-', $payment->labelMetode());
    }

    public function test_label_metode_mengembalikan_label_dari_config(): void
    {
        $payment = Payment::factory()->create(['metode' => 'transfer']);

        $this->assertEquals('Transfer Bank', $payment->labelMetode());
    }

    public function test_relasi_pemesanan_termuat_dengan_benar(): void
    {
        $pemesanan = Pemesanan::factory()->create();
        $payment = Payment::factory()->create(['pemesanan_id' => $pemesanan->id]);

        $this->assertTrue($payment->pemesanan->is($pemesanan));
    }
}
