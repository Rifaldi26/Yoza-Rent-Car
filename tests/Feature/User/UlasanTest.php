<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Models\Ulasan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature test fitur ulasan (User\UlasanController + PemesananPolicy::ulasan).
 */
final class UlasanTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Mobil $mobil;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->buatUser();
        $this->mobil = Mobil::factory()->create();
    }

    public function test_user_dapat_memberi_ulasan_untuk_pemesanan_yang_sudah_selesai(): void
    {
        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $this->user->id,
            'mobil_id' => $this->mobil->id,
            'status' => 'selesai',
        ]);

        $response = $this->actingAs($this->user)->post(
            route('ulasan.store', $this->mobil),
            [
                'pemesanan_id' => $pemesanan->id,
                'rating' => 5,
                'komentar' => 'Mobil bersih dan nyaman.',
            ],
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('ulasans', [
            'user_id' => $this->user->id,
            'mobil_id' => $this->mobil->id,
            'pemesanan_id' => $pemesanan->id,
            'rating' => 5,
            'disetujui' => false,
        ]);
    }

    public function test_ulasan_baru_belum_disetujui_secara_default(): void
    {
        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $this->user->id,
            'mobil_id' => $this->mobil->id,
            'status' => 'selesai',
        ]);

        $this->actingAs($this->user)->post(route('ulasan.store', $this->mobil), [
            'pemesanan_id' => $pemesanan->id,
            'rating' => 4,
        ]);

        $ulasan = Ulasan::latest()->first();
        $this->assertFalse($ulasan->disetujui);
    }

    public function test_rating_wajib_antara_1_sampai_5(): void
    {
        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $this->user->id,
            'mobil_id' => $this->mobil->id,
            'status' => 'selesai',
        ]);

        $response = $this->actingAs($this->user)->post(route('ulasan.store', $this->mobil), [
            'pemesanan_id' => $pemesanan->id,
            'rating' => 6,
        ]);

        $response->assertSessionHasErrors('rating');
    }

    public function test_user_tidak_dapat_memberi_ulasan_untuk_pemesanan_yang_belum_selesai(): void
    {
        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $this->user->id,
            'mobil_id' => $this->mobil->id,
            'status' => 'dikonfirmasi',
        ]);

        $this->actingAs($this->user)->post(route('ulasan.store', $this->mobil), [
            'pemesanan_id' => $pemesanan->id,
            'rating' => 5,
        ])->assertForbidden();
    }

    public function test_user_tidak_dapat_memberi_ulasan_dua_kali_untuk_pemesanan_yang_sama(): void
    {
        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $this->user->id,
            'mobil_id' => $this->mobil->id,
            'status' => 'selesai',
        ]);

        Ulasan::factory()->create([
            'user_id' => $this->user->id,
            'mobil_id' => $this->mobil->id,
            'pemesanan_id' => $pemesanan->id,
        ]);

        $this->actingAs($this->user)->post(route('ulasan.store', $this->mobil), [
            'pemesanan_id' => $pemesanan->id,
            'rating' => 5,
        ])->assertForbidden();
    }

    public function test_user_tidak_dapat_memberi_ulasan_untuk_pemesanan_milik_orang_lain(): void
    {
        $userLain = $this->buatUser();
        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $userLain->id,
            'mobil_id' => $this->mobil->id,
            'status' => 'selesai',
        ]);

        $this->actingAs($this->user)->post(route('ulasan.store', $this->mobil), [
            'pemesanan_id' => $pemesanan->id,
            'rating' => 5,
        ])->assertForbidden();
    }
}
