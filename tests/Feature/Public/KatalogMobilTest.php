<?php

declare(strict_types=1);

namespace Tests\Feature\Public;

use App\Models\Favorit;
use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Models\Ulasan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature test katalog mobil publik (home & detail mobil).
 *
 * Halaman ini bisa diakses tanpa login (lihat routes/web.php:
 * Route::get('/', [UserMobil::class, 'index'])).
 */
final class KatalogMobilTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_utama_menampilkan_katalog_mobil(): void
    {
        Mobil::factory()->count(3)->create();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertViewIs('user.mobil.index');
        $response->assertViewHas('mobils');
    }

    public function test_katalog_dapat_difilter_berdasarkan_pencarian(): void
    {
        Mobil::factory()->create(['nama' => 'Avanza Spesial']);
        Mobil::factory()->create(['nama' => 'Brio Satya']);

        $response = $this->get('/?search=Avanza');

        $response->assertOk();
        $response->assertSee('Avanza Spesial');
    }

    public function test_katalog_dapat_difilter_berdasarkan_status(): void
    {
        Mobil::factory()->create(['status' => 'tersedia']);
        Mobil::factory()->create(['status' => 'perawatan']);

        $response = $this->get('/?status=perawatan');

        $response->assertOk();
        $mobils = $response->viewData('mobils');

        $this->assertTrue($mobils->every(fn ($m) => $m->status === 'perawatan'));
    }

    public function test_halaman_detail_mobil_dapat_diakses_tanpa_login(): void
    {
        $mobil = Mobil::factory()->create();

        $response = $this->get(route('mobil.show', $mobil));

        $response->assertOk();
        $response->assertViewIs('user.mobil.show');
    }

    public function test_halaman_detail_menampilkan_hanya_ulasan_yang_disetujui(): void
    {
        $mobil = Mobil::factory()->create();

        $disetujui = Ulasan::factory()->disetujui()->create([
            'mobil_id' => $mobil->id,
            'komentar' => 'Ulasan yang sudah tayang',
        ]);

        Ulasan::factory()->create([
            'mobil_id' => $mobil->id,
            'disetujui' => false,
            'komentar' => 'Ulasan yang masih menunggu moderasi',
        ]);

        $response = $this->get(route('mobil.show', $mobil));

        $response->assertOk();
        $response->assertSee('Ulasan yang sudah tayang');
        $response->assertDontSee('Ulasan yang masih menunggu moderasi');
    }

    public function test_halaman_detail_menampilkan_status_favorit_untuk_user_login(): void
    {
        $user = $this->buatUser();
        $mobil = Mobil::factory()->create();

        Favorit::create(['user_id' => $user->id, 'mobil_id' => $mobil->id]);

        $response = $this->actingAs($user)->get(route('mobil.show', $mobil));

        $response->assertOk();
        $this->assertTrue($response->viewData('isFavorit'));
    }

    public function test_user_dengan_pemesanan_selesai_diperbolehkan_memberi_ulasan(): void
    {
        $user = $this->buatUser();
        $mobil = Mobil::factory()->create();

        Pemesanan::factory()->create([
            'user_id' => $user->id,
            'mobil_id' => $mobil->id,
            'status' => 'selesai',
        ]);

        $response = $this->actingAs($user)->get(route('mobil.show', $mobil));

        $response->assertOk();
        $this->assertNotNull($response->viewData('pemesananSelesai'));
    }

    public function test_user_yang_sudah_memberi_ulasan_tidak_ditawari_ulasan_lagi(): void
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

        $response = $this->actingAs($user)->get(route('mobil.show', $mobil));

        $this->assertNotNull($response->viewData('ulasanSaya'));
        $this->assertNull($response->viewData('pemesananSelesai'));
    }
}
