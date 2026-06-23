<?php

declare(strict_types=1);

namespace Tests\Feature\Public;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature test halaman statis publik: Syarat & Ketentuan dan
 * Kebijakan Privasi, beserta switch bahasa (locale).
 */
final class HalamanStatisTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_syarat_dan_ketentuan_dapat_diakses(): void
    {
        $response = $this->get(route('terms'));

        $response->assertOk();
    }

    public function test_halaman_kebijakan_privasi_dapat_diakses(): void
    {
        $response = $this->get(route('privacy'));

        $response->assertOk();
    }

    public function test_halaman_terms_menampilkan_default_jika_belum_dibuat_admin(): void
    {
        $response = $this->get(route('terms'));

        $response->assertOk();
        $response->assertViewHas('page');
    }

    public function test_halaman_terms_menampilkan_konten_yang_sudah_diisi_admin(): void
    {
        Page::factory()->create([
            'slug' => 'terms',
            'title' => 'Syarat Khusus Yoza',
        ]);

        $response = $this->get(route('terms'));

        $response->assertOk();
        $response->assertSee('Syarat Khusus Yoza');
    }

    public function test_locale_dapat_diganti_ke_bahasa_yang_didukung(): void
    {
        $response = $this->get(route('locale.switch', 'en'));

        $response->assertRedirect();
        $this->assertEquals('en', session('locale'));
    }

    public function test_locale_tidak_didukung_dibatasi_oleh_route_constraint(): void
    {
        $response = $this->get('/locale/fr');

        $response->assertNotFound();
    }
}
