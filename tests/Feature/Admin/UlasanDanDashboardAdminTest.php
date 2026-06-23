<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Mobil;
use App\Models\Payment;
use App\Models\Pemesanan;
use App\Models\Ulasan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature test moderasi ulasan (Admin\UlasanController)
 * dan dashboard ringkasan (Admin\DashboardController).
 */
final class UlasanDanDashboardAdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->buatAdmin();
    }

    // ── Moderasi ulasan ──────────────────────────────────────────────────

    public function test_admin_dapat_melihat_ulasan_yang_menunggu_moderasi_secara_default(): void
    {
        Ulasan::factory()->create(['disetujui' => false, 'komentar' => 'Menunggu moderasi']);
        Ulasan::factory()->create(['disetujui' => true, 'komentar' => 'Sudah disetujui']);

        $response = $this->actingAs($this->admin)->get(route('admin.ulasan.index'));

        $response->assertOk();
        $response->assertSee('Menunggu moderasi');
        $response->assertDontSee('Sudah disetujui');
    }

    public function test_tab_semua_menampilkan_seluruh_ulasan(): void
    {
        Ulasan::factory()->create(['disetujui' => false, 'komentar' => 'Belum disetujui']);
        Ulasan::factory()->create(['disetujui' => true, 'komentar' => 'Sudah tayang']);

        $response = $this->actingAs($this->admin)->get(route('admin.ulasan.index', ['tab' => 'semua']));

        $response->assertOk();
        $response->assertSee('Belum disetujui');
        $response->assertSee('Sudah tayang');
    }

    public function test_admin_dapat_menyetujui_ulasan(): void
    {
        $ulasan = Ulasan::factory()->create(['disetujui' => false]);

        $this->actingAs($this->admin)
            ->patch(route('admin.ulasan.setujui', $ulasan))
            ->assertRedirect();

        $this->assertTrue($ulasan->fresh()->disetujui);
    }

    public function test_admin_dapat_menghapus_ulasan(): void
    {
        $ulasan = Ulasan::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.ulasan.destroy', $ulasan))
            ->assertRedirect();

        $this->assertDatabaseMissing('ulasans', ['id' => $ulasan->id]);
    }

    public function test_user_biasa_tidak_dapat_memoderasi_ulasan(): void
    {
        $user = $this->buatUser();

        $this->actingAs($user)
            ->get(route('admin.ulasan.index'))
            ->assertForbidden();
    }

    // ── Dashboard ─────────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_dashboard_dengan_statistik_lengkap(): void
    {
        Mobil::factory()->create(['status' => 'tersedia']);
        Mobil::factory()->create(['status' => 'disewa']);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('stats');
        $response->assertViewHas('pemesanan_terbaru');
    }

    public function test_dashboard_menghitung_pendapatan_bulan_ini_dengan_benar(): void
    {
        $pemesanan = Pemesanan::factory()->create();
        Payment::factory()->create([
            'pemesanan_id' => $pemesanan->id,
            'status' => 'dikonfirmasi',
            'amount' => 750_000,
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $stats = $response->viewData('stats');
        $this->assertEquals(750_000, (float) $stats['pendapatan_bulan']);
    }

    public function test_dashboard_hanya_menampilkan_pemesanan_pending_atau_menunggu_konfirmasi(): void
    {
        Pemesanan::factory()->create(['status' => 'pending']);
        Pemesanan::factory()->create(['status' => 'selesai']);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $terbaru = $response->viewData('pemesanan_terbaru');
        $this->assertTrue($terbaru->every(fn ($p) => in_array($p->status, ['pending', 'menunggu_konfirmasi_admin'], true)));
    }

    public function test_user_biasa_tidak_dapat_mengakses_dashboard_admin(): void
    {
        $user = $this->buatUser();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
