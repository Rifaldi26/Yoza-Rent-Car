<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Enums\StatusMobil;
use App\Enums\StatusPemesanan;
use App\Jobs\KirimEmailPemesanan;
use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * Test manajemen pemesanan dari panel admin.
 */
final class PemesananAdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $user;

    private Mobil $mobil;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->user = User::factory()->create([
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $this->mobil = Mobil::factory()->create(['status' => 'tersedia']);
    }

    // ── Otorisasi panel admin ─────────────────────────────────────────────

    public function test_non_admin_tidak_bisa_akses_panel_admin(): void
    {
        $this->actingAs($this->user)
            ->get(route('admin.pemesanan.index'))
            ->assertForbidden();
    }

    public function test_admin_dapat_melihat_daftar_pemesanan(): void
    {
        Pemesanan::factory()->count(5)->create(['mobil_id' => $this->mobil->id]);

        $this->actingAs($this->admin)
            ->get(route('admin.pemesanan.index'))
            ->assertOk()
            ->assertViewHas('pemesanans');
    }

    // ── Alur konfirmasi ───────────────────────────────────────────────────

    public function test_admin_dapat_mengkonfirmasi_pemesanan(): void
    {
        Bus::fake();

        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $this->user->id,
            'mobil_id' => $this->mobil->id,
            'status' => StatusPemesanan::MenungguKonfirmasiAdmin->value,
        ]);

        $this->actingAs($this->admin)
            ->patch(route('admin.pemesanan.konfirmasi', $pemesanan))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertEquals(
            StatusPemesanan::Dikonfirmasi->value,
            $pemesanan->fresh()->status,
        );

        $this->assertEquals(
            StatusMobil::Disewa->value,
            $this->mobil->fresh()->status,
        );

        Bus::assertDispatched(KirimEmailPemesanan::class);
    }

    public function test_admin_tidak_dapat_mengkonfirmasi_pemesanan_berstatus_pending(): void
    {
        $pemesanan = Pemesanan::factory()->create([
            'mobil_id' => $this->mobil->id,
            'status' => StatusPemesanan::Pending->value,
        ]);

        $this->actingAs($this->admin)
            ->patch(route('admin.pemesanan.konfirmasi', $pemesanan))
            ->assertSessionHas('error');
    }

    // ── Alur penolakan ────────────────────────────────────────────────────

    public function test_admin_dapat_menolak_pemesanan(): void
    {
        Bus::fake();

        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $this->user->id,
            'mobil_id' => $this->mobil->id,
            'status' => StatusPemesanan::MenungguKonfirmasiAdmin->value,
        ]);

        $this->actingAs($this->admin)
            ->patch(route('admin.pemesanan.tolak', $pemesanan))
            ->assertSessionHas('success');

        $this->assertEquals(
            StatusPemesanan::Dibatalkan->value,
            $pemesanan->fresh()->status,
        );
    }

    // ── Alur selesai ──────────────────────────────────────────────────────

    public function test_admin_dapat_menandai_pemesanan_selesai(): void
    {
        Bus::fake();

        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $this->user->id,
            'mobil_id' => $this->mobil->id,
            'status' => StatusPemesanan::Dikonfirmasi->value,
        ]);

        $this->mobil->update(['status' => StatusMobil::Disewa->value]);

        $this->actingAs($this->admin)
            ->patch(route('admin.pemesanan.selesai', $pemesanan))
            ->assertSessionHas('success');

        $this->assertEquals(StatusPemesanan::Selesai->value, $pemesanan->fresh()->status);
        $this->assertEquals(StatusMobil::Tersedia->value, $this->mobil->fresh()->status);
    }
}
