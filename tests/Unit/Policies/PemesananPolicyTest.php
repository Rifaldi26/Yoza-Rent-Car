<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Models\Ulasan;
use App\Models\User;
use App\Policies\PemesananPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit test PemesananPolicy.
 *
 * Menguji setiap aturan otorisasi secara langsung (tanpa HTTP
 * layer) untuk memastikan setiap kombinasi role/ownership/status
 * tercover dengan presisi.
 */
final class PemesananPolicyTest extends TestCase
{
    use RefreshDatabase;

    private PemesananPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new PemesananPolicy();
    }

    // ── view ──────────────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_pemesanan_siapa_saja(): void
    {
        $admin = $this->buatAdmin();
        $pemesanan = Pemesanan::factory()->create();

        $this->assertTrue($this->policy->view($admin, $pemesanan));
    }

    public function test_pemilik_dapat_melihat_pemesanan_miliknya(): void
    {
        $user = $this->buatUser();
        $pemesanan = Pemesanan::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($this->policy->view($user, $pemesanan));
    }

    public function test_user_tidak_dapat_melihat_pemesanan_orang_lain(): void
    {
        $user = $this->buatUser();
        $pemesanan = Pemesanan::factory()->create();

        $this->assertFalse($this->policy->view($user, $pemesanan));
    }

    // ── create ────────────────────────────────────────────────────────────

    public function test_user_terverifikasi_dapat_membuat_pemesanan(): void
    {
        $user = $this->buatUser(['email_verified_at' => now()]);

        $this->assertTrue($this->policy->create($user));
    }

    public function test_user_belum_terverifikasi_tidak_dapat_membuat_pemesanan(): void
    {
        $user = $this->buatUser(['email_verified_at' => null]);

        $this->assertFalse($this->policy->create($user));
    }

    // ── batalkan, bayar, unduhInvoice (ownership only) ──────────────────────

    public function test_hanya_pemilik_yang_dapat_membatalkan_bayar_dan_unduh_invoice(): void
    {
        $pemilik = $this->buatUser();
        $bukanPemilik = $this->buatUser();
        $pemesanan = Pemesanan::factory()->create(['user_id' => $pemilik->id]);

        $this->assertTrue($this->policy->batalkan($pemilik, $pemesanan));
        $this->assertFalse($this->policy->batalkan($bukanPemilik, $pemesanan));

        $this->assertTrue($this->policy->bayar($pemilik, $pemesanan));
        $this->assertFalse($this->policy->bayar($bukanPemilik, $pemesanan));

        $this->assertTrue($this->policy->unduhInvoice($pemilik, $pemesanan));
        $this->assertFalse($this->policy->unduhInvoice($bukanPemilik, $pemesanan));
    }

    // ── ulasan ────────────────────────────────────────────────────────────

    public function test_ulasan_diizinkan_jika_pemilik_dan_status_selesai_dan_belum_pernah_ulasan(): void
    {
        $user = $this->buatUser();
        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $user->id,
            'status' => 'selesai',
        ]);

        $this->assertTrue($this->policy->ulasan($user, $pemesanan));
    }

    public function test_ulasan_ditolak_jika_bukan_pemilik(): void
    {
        $bukanPemilik = $this->buatUser();
        $pemesanan = Pemesanan::factory()->create(['status' => 'selesai']);

        $this->assertFalse($this->policy->ulasan($bukanPemilik, $pemesanan));
    }

    public function test_ulasan_ditolak_jika_status_belum_selesai(): void
    {
        $user = $this->buatUser();
        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $user->id,
            'status' => 'dikonfirmasi',
        ]);

        $this->assertFalse($this->policy->ulasan($user, $pemesanan));
    }

    public function test_ulasan_ditolak_jika_sudah_pernah_memberi_ulasan(): void
    {
        $user = $this->buatUser();
        $mobil = Mobil::factory()->create();
        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $user->id,
            'mobil_id' => $mobil->id,
            'status' => 'selesai',
        ]);

        Ulasan::factory()->create([
            'user_id' => $user->id,
            'mobil_id' => $mobil->id,
            'pemesanan_id' => $pemesanan->id,
        ]);

        $this->assertFalse($this->policy->ulasan($user, $pemesanan));
    }

    // ── konfirmasi, tolak, selesai (admin only) ─────────────────────────────

    public function test_hanya_admin_yang_dapat_konfirmasi_tolak_dan_selesai(): void
    {
        $admin = $this->buatAdmin();
        $user = $this->buatUser();

        $this->assertTrue($this->policy->konfirmasi($admin));
        $this->assertFalse($this->policy->konfirmasi($user));

        $this->assertTrue($this->policy->tolak($admin));
        $this->assertFalse($this->policy->tolak($user));

        $this->assertTrue($this->policy->selesai($admin));
        $this->assertFalse($this->policy->selesai($user));
    }
}
