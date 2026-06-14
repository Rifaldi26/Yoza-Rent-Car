<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use App\Enums\StatusPemesanan;
use App\Jobs\KirimEmailPemesanan;
use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * Test alur pemesanan penuh dari sisi pengguna.
 *
 * Mencakup: membuat pemesanan, validasi konflik,
 * pembatalan, dan akses otorisasi.
 */
final class PemesananFlowTest extends TestCase
{
    use RefreshDatabase;

    private User  $user;
    private Mobil $mobil;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email_verified_at' => now(),
            'role'              => 'user',
        ]);

        $this->mobil = Mobil::factory()->create([
            'status'         => 'tersedia',
            'harga_per_hari' => 300_000,
        ]);
    }

    // ── Pembuatan pemesanan ───────────────────────────────────────────────

    public function test_user_dapat_membuat_pemesanan_baru(): void
    {
        Bus::fake();

        $response = $this->actingAs($this->user)->post(route('pemesanan.store'), [
            'mobil_id'        => $this->mobil->id,
            'tanggal_mulai'   => now()->addDay()->toDateString(),
            'tanggal_selesai' => now()->addDays(3)->toDateString(),
            'opsi_supir'      => false,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pemesanans', [
            'user_id'    => $this->user->id,
            'mobil_id'   => $this->mobil->id,
            'status'     => StatusPemesanan::Pending->value,
            'total_harga'=> 600_000,
        ]);

        Bus::assertDispatched(KirimEmailPemesanan::class);
    }

    public function test_total_harga_dihitung_otomatis_berdasarkan_durasi(): void
    {
        Bus::fake();

        $this->actingAs($this->user)->post(route('pemesanan.store'), [
            'mobil_id'        => $this->mobil->id,
            'tanggal_mulai'   => now()->addDay()->toDateString(),
            'tanggal_selesai' => now()->addDays(4)->toDateString(), // 3 hari
            'opsi_supir'      => false,
        ]);

        $pemesanan = Pemesanan::latest()->first();
        $this->assertEquals(900_000, $pemesanan->total_harga);
    }

    public function test_tidak_dapat_memesan_mobil_yang_tidak_tersedia(): void
    {
        $this->mobil->update(['status' => 'disewa']);

        $response = $this->actingAs($this->user)->post(route('pemesanan.store'), [
            'mobil_id'        => $this->mobil->id,
            'tanggal_mulai'   => now()->addDay()->toDateString(),
            'tanggal_selesai' => now()->addDays(3)->toDateString(),
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
        $this->assertDatabaseCount('pemesanans', 0);
    }

    public function test_tidak_dapat_memesan_tanggal_yang_sudah_dipesan(): void
    {
        Bus::fake();

        // Pemesanan pertama
        Pemesanan::factory()->create([
            'mobil_id'        => $this->mobil->id,
            'tanggal_mulai'   => now()->addDays(2)->toDateString(),
            'tanggal_selesai' => now()->addDays(5)->toDateString(),
            'status'          => StatusPemesanan::Dikonfirmasi->value,
        ]);

        // Coba pesan tanggal yang sama/tumpang tindih
        $response = $this->actingAs($this->user)->post(route('pemesanan.store'), [
            'mobil_id'        => $this->mobil->id,
            'tanggal_mulai'   => now()->addDays(3)->toDateString(),
            'tanggal_selesai' => now()->addDays(6)->toDateString(),
        ]);

        $response->assertSessionHasErrors();
    }

    public function test_tanggal_mulai_tidak_boleh_di_masa_lalu(): void
    {
        $response = $this->actingAs($this->user)->post(route('pemesanan.store'), [
            'mobil_id'        => $this->mobil->id,
            'tanggal_mulai'   => now()->subDay()->toDateString(),
            'tanggal_selesai' => now()->addDay()->toDateString(),
        ]);

        $response->assertSessionHasErrors('tanggal_mulai');
    }

    // ── Pembatalan ────────────────────────────────────────────────────────

    public function test_user_dapat_membatalkan_pemesanan_pending(): void
    {
        Bus::fake();

        $pemesanan = Pemesanan::factory()->create([
            'user_id'  => $this->user->id,
            'mobil_id' => $this->mobil->id,
            'status'   => StatusPemesanan::Pending->value,
        ]);

        $this->actingAs($this->user)
            ->patch(route('pemesanan.cancel', $pemesanan))
            ->assertRedirect(route('pemesanan.index'));

        $this->assertEquals(
            StatusPemesanan::Dibatalkan->value,
            $pemesanan->fresh()->status,
        );
    }

    public function test_user_tidak_dapat_membatalkan_pemesanan_yang_sudah_dikonfirmasi(): void
    {
        $pemesanan = Pemesanan::factory()->create([
            'user_id'  => $this->user->id,
            'mobil_id' => $this->mobil->id,
            'status'   => StatusPemesanan::Dikonfirmasi->value,
        ]);

        $this->actingAs($this->user)
            ->patch(route('pemesanan.cancel', $pemesanan))
            ->assertSessionHas('error');

        $this->assertEquals(
            StatusPemesanan::Dikonfirmasi->value,
            $pemesanan->fresh()->status,
        );
    }

    // ── Otorisasi ─────────────────────────────────────────────────────────

    public function test_user_tidak_dapat_mengakses_pemesanan_milik_orang_lain(): void
    {
        $userLain  = User::factory()->create(['email_verified_at' => now()]);
        $pemesanan = Pemesanan::factory()->create([
            'user_id'  => $userLain->id,
            'mobil_id' => $this->mobil->id,
        ]);

        $this->actingAs($this->user)
            ->get(route('pemesanan.show', $pemesanan))
            ->assertForbidden();
    }

    public function test_tamu_tidak_dapat_membuat_pemesanan(): void
    {
        $this->post(route('pemesanan.store'), [
            'mobil_id' => $this->mobil->id,
        ])->assertRedirect(route('home'));
    }
}
