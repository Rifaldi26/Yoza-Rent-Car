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
 * Feature test alur pemesanan dari sisi pengguna.
 *
 * Mencakup: pembuatan pemesanan (payload lengkap sesuai
 * StorePemesananRequest), validasi konflik, pembatalan, dan
 * otorisasi (PemesananPolicy).
 */
final class PemesananFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Mobil $mobil;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->buatUser();
        $this->mobil = $this->buatMobilTersedia(['harga_per_hari' => 300_000]);
    }

    // ── Pembuatan pemesanan ───────────────────────────────────────────────

    public function test_user_dapat_membuat_pemesanan_baru(): void
    {
        Bus::fake();

        $response = $this->actingAs($this->user)->post(
            route('pemesanan.store'),
            $this->payloadPemesananValid($this->mobil, [
                'tanggal_mulai' => now()->addDay()->toDateString(),
                'tanggal_selesai' => now()->addDays(3)->toDateString(), // 2 hari
            ]),
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pemesanans', [
            'user_id' => $this->user->id,
            'mobil_id' => $this->mobil->id,
            'status' => StatusPemesanan::Pending->value,
            'total_harga' => 600_000, // 2 hari x 300_000
        ]);

        Bus::assertDispatched(KirimEmailPemesanan::class);
    }

    public function test_total_harga_dihitung_otomatis_berdasarkan_durasi(): void
    {
        Bus::fake();

        $this->actingAs($this->user)->post(
            route('pemesanan.store'),
            $this->payloadPemesananValid($this->mobil, [
                'tanggal_mulai' => now()->addDay()->toDateString(),
                'tanggal_selesai' => now()->addDays(4)->toDateString(), // 3 hari
            ]),
        );

        $pemesanan = Pemesanan::latest()->first();
        $this->assertEquals(900_000, $pemesanan->total_harga);
    }

    public function test_total_harga_mencakup_biaya_supir_jika_dipilih(): void
    {
        Bus::fake();

        $mobil = $this->buatMobilTersedia([
            'harga_per_hari' => 200_000,
            'biaya_supir_per_hari' => 100_000,
        ]);

        $this->actingAs($this->user)->post(
            route('pemesanan.store'),
            $this->payloadPemesananValid($mobil, [
                'tanggal_mulai' => now()->addDay()->toDateString(),
                'tanggal_selesai' => now()->addDays(4)->toDateString(), // 3 hari
                'opsi_supir' => true,
            ]),
        );

        $pemesanan = Pemesanan::latest()->first();
        // 3 hari x 200_000 + 3 hari x 100_000
        $this->assertEquals(900_000, $pemesanan->total_harga);
    }

    public function test_tidak_dapat_memesan_mobil_yang_tidak_tersedia(): void
    {
        $this->mobil->update(['status' => 'disewa']);

        $response = $this->actingAs($this->user)->post(
            route('pemesanan.store'),
            $this->payloadPemesananValid($this->mobil),
        );

        $response->assertRedirect();
        $response->assertSessionHasErrors();
        $this->assertDatabaseCount('pemesanans', 0);
    }

    public function test_tidak_dapat_memesan_tanggal_yang_sudah_dipesan(): void
    {
        Bus::fake();

        Pemesanan::factory()->create([
            'mobil_id' => $this->mobil->id,
            'tanggal_mulai' => now()->addDays(2)->toDateString(),
            'tanggal_selesai' => now()->addDays(5)->toDateString(),
            'status' => StatusPemesanan::Dikonfirmasi->value,
        ]);

        $response = $this->actingAs($this->user)->post(
            route('pemesanan.store'),
            $this->payloadPemesananValid($this->mobil, [
                'tanggal_mulai' => now()->addDays(3)->toDateString(),
                'tanggal_selesai' => now()->addDays(6)->toDateString(),
            ]),
        );

        $response->assertSessionHasErrors();
    }

    public function test_tanggal_mulai_tidak_boleh_di_masa_lalu(): void
    {
        $response = $this->actingAs($this->user)->post(
            route('pemesanan.store'),
            $this->payloadPemesananValid($this->mobil, [
                'tanggal_mulai' => now()->subDay()->toDateString(),
                'tanggal_selesai' => now()->addDay()->toDateString(),
            ]),
        );

        $response->assertSessionHasErrors('tanggal_mulai');
    }

    public function test_mobil_id_wajib_diisi(): void
    {
        $payload = $this->payloadPemesananValid($this->mobil);
        unset($payload['mobil_id']);

        $response = $this->actingAs($this->user)->post(route('pemesanan.store'), $payload);

        $response->assertSessionHasErrors('mobil_id');
    }

    public function test_minimal_satu_media_sosial_wajib_diisi(): void
    {
        $payload = $this->payloadPemesananValid($this->mobil, [
            'instagram' => null,
            'tiktok' => null,
        ]);

        $response = $this->actingAs($this->user)->post(route('pemesanan.store'), $payload);

        $response->assertSessionHasErrors(['instagram', 'tiktok']);
    }

    public function test_tempat_kerja_wajib_diisi_jika_status_pekerjaan_bekerja(): void
    {
        $payload = $this->payloadPemesananValid($this->mobil, [
            'status_pekerjaan' => 'bekerja',
            'tempat_kerja' => null,
        ]);

        $response = $this->actingAs($this->user)->post(route('pemesanan.store'), $payload);

        $response->assertSessionHasErrors('tempat_kerja');
    }

    public function test_kampus_wajib_diisi_jika_status_pekerjaan_mahasiswa(): void
    {
        $payload = $this->payloadPemesananValid($this->mobil, [
            'status_pekerjaan' => 'mahasiswa',
            'tempat_kerja' => null,
            'kampus' => null,
        ]);

        $response = $this->actingAs($this->user)->post(route('pemesanan.store'), $payload);

        $response->assertSessionHasErrors('kampus');
    }

    public function test_share_lokasi_harus_berupa_url(): void
    {
        $payload = $this->payloadPemesananValid($this->mobil, [
            'share_lokasi' => 'bukan url',
        ]);

        $response = $this->actingAs($this->user)->post(route('pemesanan.store'), $payload);

        $response->assertSessionHasErrors('share_lokasi');
    }

    // ── Sewa 12 jam (HTTP layer) ─────────────────────────────────────────

    public function test_user_dapat_membuat_pemesanan_12_jam(): void
    {
        Bus::fake();

        $tanggal = now()->addDay()->toDateString();

        $response = $this->actingAs($this->user)->post(
            route('pemesanan.store'),
            $this->payloadPemesananValid($this->mobil, [
                'tipe_sewa' => '12_jam',
                'tanggal_mulai' => $tanggal,
                'tanggal_selesai' => $tanggal,
                'waktu_mulai' => '08:00',
                'waktu_selesai' => '20:00',
            ]),
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('pemesanans', [
            'user_id' => $this->user->id,
            'tipe_sewa' => '12_jam',
            'total_harga' => 150_000, // 50% dari 300_000
        ]);
    }

    public function test_waktu_selesai_12_jam_harus_setelah_waktu_mulai(): void
    {
        $tanggal = now()->addDay()->toDateString();

        $response = $this->actingAs($this->user)->post(
            route('pemesanan.store'),
            $this->payloadPemesananValid($this->mobil, [
                'tipe_sewa' => '12_jam',
                'tanggal_mulai' => $tanggal,
                'tanggal_selesai' => $tanggal,
                'waktu_mulai' => '20:00',
                'waktu_selesai' => '08:00',
            ]),
        );

        $response->assertSessionHasErrors('waktu_selesai');
    }

    // ── Pembatalan ────────────────────────────────────────────────────────

    public function test_user_dapat_membatalkan_pemesanan_pending(): void
    {
        Bus::fake();

        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $this->user->id,
            'mobil_id' => $this->mobil->id,
            'status' => StatusPemesanan::Pending->value,
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
            'user_id' => $this->user->id,
            'mobil_id' => $this->mobil->id,
            'status' => StatusPemesanan::Dikonfirmasi->value,
        ]);

        $this->actingAs($this->user)
            ->patch(route('pemesanan.cancel', $pemesanan))
            ->assertSessionHas('error');

        $this->assertEquals(
            StatusPemesanan::Dikonfirmasi->value,
            $pemesanan->fresh()->status,
        );
    }

    public function test_user_tidak_dapat_membatalkan_pemesanan_milik_orang_lain(): void
    {
        $userLain = $this->buatUser();
        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $userLain->id,
            'mobil_id' => $this->mobil->id,
            'status' => StatusPemesanan::Pending->value,
        ]);

        $this->actingAs($this->user)
            ->patch(route('pemesanan.cancel', $pemesanan))
            ->assertForbidden();
    }

    // ── Otorisasi & daftar ────────────────────────────────────────────────

    public function test_user_dapat_melihat_daftar_pemesanan_miliknya(): void
    {
        Pemesanan::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('pemesanan.index'));

        $response->assertOk();
        $response->assertViewHas('pemesanans');
    }

    public function test_user_dapat_melihat_detail_pemesanan_miliknya(): void
    {
        $pemesanan = Pemesanan::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->get(route('pemesanan.show', $pemesanan))
            ->assertOk();
    }

    public function test_user_tidak_dapat_mengakses_pemesanan_milik_orang_lain(): void
    {
        $userLain = $this->buatUser();
        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $userLain->id,
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
        ])->assertRedirect(route('login'));
    }

    public function test_halaman_buat_pemesanan_redirect_jika_mobil_tidak_tersedia(): void
    {
        $this->mobil->update(['status' => 'disewa']);

        $response = $this->actingAs($this->user)
            ->get(route('pemesanan.create', ['mobil_id' => $this->mobil->id]));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error');
    }

    public function test_halaman_buat_pemesanan_tampil_untuk_mobil_tersedia(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('pemesanan.create', ['mobil_id' => $this->mobil->id]));

        $response->assertOk();
    }
}
