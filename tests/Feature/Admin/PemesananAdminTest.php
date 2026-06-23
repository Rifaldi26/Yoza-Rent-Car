<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Enums\StatusMobil;
use App\Enums\StatusPayment;
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
 * Feature test manajemen pemesanan oleh admin (Admin\PemesananController).
 */
final class PemesananAdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->buatAdmin();
    }

    public function test_admin_dapat_melihat_daftar_pemesanan(): void
    {
        Pemesanan::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.pemesanan.index'))
            ->assertOk()
            ->assertViewIs('admin.pemesanan.index');
    }

    public function test_daftar_pemesanan_dapat_difilter_berdasarkan_status(): void
    {
        Pemesanan::factory()->create(['status' => StatusPemesanan::Pending->value]);
        Pemesanan::factory()->create(['status' => StatusPemesanan::Selesai->value]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.pemesanan.index', ['status' => 'selesai']));

        $response->assertOk();
        $pemesanans = $response->viewData('pemesanans');
        $this->assertTrue($pemesanans->every(fn ($p) => $p->status === 'selesai'));
    }

    public function test_daftar_pemesanan_dapat_dicari_berdasarkan_nama_user(): void
    {
        $user = User::factory()->create(['name' => 'Budi Santoso']);
        Pemesanan::factory()->create(['user_id' => $user->id]);
        Pemesanan::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.pemesanan.index', ['search' => 'Budi']));

        $response->assertOk();
        $this->assertCount(1, $response->viewData('pemesanans'));
    }

    public function test_admin_dapat_melihat_detail_pemesanan(): void
    {
        $pemesanan = Pemesanan::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.pemesanan.show', $pemesanan))
            ->assertOk()
            ->assertViewIs('admin.pemesanan.show');
    }

    // ── Konfirmasi ────────────────────────────────────────────────────────

    public function test_admin_dapat_mengkonfirmasi_pemesanan_menunggu(): void
    {
        Bus::fake();

        $mobil = Mobil::factory()->create(['status' => StatusMobil::Tersedia->value]);
        $pemesanan = Pemesanan::factory()->create([
            'mobil_id' => $mobil->id,
            'status' => StatusPemesanan::MenungguKonfirmasiAdmin->value,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.pemesanan.konfirmasi', $pemesanan));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals(StatusPemesanan::Dikonfirmasi->value, $pemesanan->fresh()->status);
        $this->assertEquals(StatusMobil::Disewa->value, $mobil->fresh()->status);
    }

    public function test_konfirmasi_gagal_untuk_pemesanan_yang_masih_pending(): void
    {
        $pemesanan = Pemesanan::factory()->create(['status' => StatusPemesanan::Pending->value]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.pemesanan.konfirmasi', $pemesanan));

        $response->assertSessionHas('error');
        $this->assertEquals(StatusPemesanan::Pending->value, $pemesanan->fresh()->status);
    }

    // ── Tolak ─────────────────────────────────────────────────────────────

    public function test_admin_dapat_menolak_pemesanan(): void
    {
        Bus::fake();

        $pemesanan = Pemesanan::factory()->create([
            'status' => StatusPemesanan::MenungguKonfirmasiAdmin->value,
        ]);

        $this->actingAs($this->admin)
            ->patch(route('admin.pemesanan.tolak', $pemesanan))
            ->assertSessionHas('success');

        $this->assertEquals(StatusPemesanan::Dibatalkan->value, $pemesanan->fresh()->status);
    }

    // ── Selesai ───────────────────────────────────────────────────────────

    public function test_admin_dapat_menandai_pemesanan_selesai(): void
    {
        Bus::fake();

        $mobil = Mobil::factory()->create(['status' => StatusMobil::Disewa->value]);
        $pemesanan = Pemesanan::factory()->create([
            'mobil_id' => $mobil->id,
            'status' => StatusPemesanan::Dikonfirmasi->value,
        ]);

        $this->actingAs($this->admin)
            ->patch(route('admin.pemesanan.selesai', $pemesanan))
            ->assertSessionHas('success');

        $this->assertEquals(StatusPemesanan::Selesai->value, $pemesanan->fresh()->status);
        $this->assertEquals(StatusMobil::Tersedia->value, $mobil->fresh()->status);
    }

    public function test_selesai_gagal_jika_status_belum_dikonfirmasi(): void
    {
        $pemesanan = Pemesanan::factory()->create(['status' => StatusPemesanan::Pending->value]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.pemesanan.selesai', $pemesanan));

        $response->assertSessionHas('error');
    }

    // ── Konfirmasi pembayaran ────────────────────────────────────────────

    public function test_admin_dapat_mengkonfirmasi_pembayaran(): void
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

        $this->actingAs($this->admin)
            ->patch(route('admin.pemesanan.konfirmasi-bayar', $pemesanan))
            ->assertSessionHas('success');

        $this->assertEquals(StatusPayment::Dikonfirmasi->value, $pemesanan->payment->fresh()->status);
        $this->assertEquals(StatusPemesanan::Dikonfirmasi->value, $pemesanan->fresh()->status);
    }

    public function test_konfirmasi_pembayaran_gagal_jika_tidak_ada_record_payment(): void
    {
        $pemesanan = Pemesanan::factory()->create();

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.pemesanan.konfirmasi-bayar', $pemesanan));

        $response->assertSessionHas('error');
    }

    // ── Invoice ───────────────────────────────────────────────────────────

    public function test_admin_dapat_mengunduh_invoice_pemesanan(): void
    {
        $pemesanan = Pemesanan::factory()->create();
        Payment::factory()->dikonfirmasi()->create(['pemesanan_id' => $pemesanan->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.pemesanan.invoice', $pemesanan));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    // ── Otorisasi ─────────────────────────────────────────────────────────

    public function test_user_biasa_tidak_dapat_mengakses_panel_pemesanan_admin(): void
    {
        $user = $this->buatUser();
        $pemesanan = Pemesanan::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.pemesanan.show', $pemesanan))
            ->assertForbidden();
    }
}
