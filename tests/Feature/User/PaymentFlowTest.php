<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use App\Enums\StatusPemesanan;
use App\Jobs\KirimEmailPemesanan;
use App\Models\Mobil;
use App\Models\Payment;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * Feature test alur pembayaran dari sisi pengguna.
 *
 * Mencakup: halaman checkout, pemilihan metode pembayaran
 * (redirect ke WhatsApp), halaman setelah-wa, dan unduh invoice.
 */
final class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Mobil $mobil;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->buatUser();
        $this->mobil = $this->buatMobilTersedia();
    }

    // ── Checkout ────────────────────────────────────────────────────────

    public function test_user_dapat_melihat_halaman_checkout_untuk_pemesanan_pending(): void
    {
        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $this->user->id,
            'mobil_id' => $this->mobil->id,
            'status' => StatusPemesanan::Pending->value,
        ]);

        $this->actingAs($this->user)
            ->get(route('payment.checkout', $pemesanan))
            ->assertOk()
            ->assertViewIs('user.payment.checkout');
    }

    public function test_checkout_redirect_jika_pemesanan_bukan_pending(): void
    {
        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $this->user->id,
            'mobil_id' => $this->mobil->id,
            'status' => StatusPemesanan::Dikonfirmasi->value,
        ]);

        $this->actingAs($this->user)
            ->get(route('payment.checkout', $pemesanan))
            ->assertRedirect(route('pemesanan.show', $pemesanan));
    }

    public function test_user_tidak_dapat_checkout_pemesanan_milik_orang_lain(): void
    {
        $userLain = $this->buatUser();
        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $userLain->id,
            'mobil_id' => $this->mobil->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('payment.checkout', $pemesanan))
            ->assertForbidden();
    }

    // ── Pilih metode ──────────────────────────────────────────────────────

    public function test_user_dapat_memilih_metode_pembayaran_dan_diarahkan_ke_whatsapp(): void
    {
        Bus::fake();

        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $this->user->id,
            'mobil_id' => $this->mobil->id,
            'status' => StatusPemesanan::Pending->value,
        ]);

        $response = $this->actingAs($this->user)->post(
            route('payment.pilih-metode', $pemesanan),
            ['metode' => 'transfer'],
        );

        $response->assertRedirect();
        $this->assertStringContainsString('wa.me', $response->headers->get('Location'));

        $this->assertEquals(
            StatusPemesanan::MenungguKonfirmasiAdmin->value,
            $pemesanan->fresh()->status,
        );

        $this->assertDatabaseHas('payments', [
            'pemesanan_id' => $pemesanan->id,
            'metode' => 'transfer',
        ]);

        Bus::assertDispatched(KirimEmailPemesanan::class);
    }

    public function test_metode_pembayaran_harus_salah_satu_yang_valid(): void
    {
        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $this->user->id,
            'mobil_id' => $this->mobil->id,
            'status' => StatusPemesanan::Pending->value,
        ]);

        $response = $this->actingAs($this->user)->post(
            route('payment.pilih-metode', $pemesanan),
            ['metode' => 'metode-tidak-valid'],
        );

        $response->assertSessionHasErrors('metode');
    }

    public function test_tidak_dapat_pilih_metode_jika_pemesanan_bukan_pending(): void
    {
        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $this->user->id,
            'mobil_id' => $this->mobil->id,
            'status' => StatusPemesanan::Dikonfirmasi->value,
        ]);

        $response = $this->actingAs($this->user)->post(
            route('payment.pilih-metode', $pemesanan),
            ['metode' => 'cash'],
        );

        $response->assertSessionHas('error');
    }

    // ── Halaman setelah WA & invoice ───────────────────────────────────────

    public function test_halaman_setelah_wa_dapat_diakses_pemilik_pemesanan(): void
    {
        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $this->user->id,
            'mobil_id' => $this->mobil->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('payment.setelah-wa', $pemesanan))
            ->assertOk();
    }

    public function test_invoice_dapat_diunduh_untuk_pemesanan_yang_sudah_dikonfirmasi(): void
    {
        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $this->user->id,
            'mobil_id' => $this->mobil->id,
            'status' => StatusPemesanan::Dikonfirmasi->value,
        ]);
        Payment::factory()->dikonfirmasi()->create(['pemesanan_id' => $pemesanan->id]);

        $response = $this->actingAs($this->user)->get(route('payment.invoice', $pemesanan));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_invoice_ditolak_untuk_pemesanan_berstatus_pending(): void
    {
        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $this->user->id,
            'mobil_id' => $this->mobil->id,
            'status' => StatusPemesanan::Pending->value,
        ]);

        $this->actingAs($this->user)
            ->get(route('payment.invoice', $pemesanan))
            ->assertForbidden();
    }

    public function test_user_tidak_dapat_mengunduh_invoice_pemesanan_orang_lain(): void
    {
        $userLain = $this->buatUser();
        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $userLain->id,
            'mobil_id' => $this->mobil->id,
            'status' => StatusPemesanan::Dikonfirmasi->value,
        ]);

        $this->actingAs($this->user)
            ->get(route('payment.invoice', $pemesanan))
            ->assertForbidden();
    }
}
