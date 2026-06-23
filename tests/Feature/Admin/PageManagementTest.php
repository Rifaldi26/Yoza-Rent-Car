<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature test manajemen halaman CMS (Admin\PageController).
 */
final class PageManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->buatAdmin();
    }

    public function test_admin_dapat_melihat_daftar_halaman(): void
    {
        Page::factory()->create(['slug' => 'terms']);

        $response = $this->actingAs($this->admin)->get(route('admin.pages.index'));

        $response->assertOk();
        $response->assertViewHas('pages');
    }

    public function test_admin_dapat_membuka_form_edit_halaman(): void
    {
        $page = Page::factory()->create(['slug' => 'privacy']);

        $response = $this->actingAs($this->admin)->get(route('admin.pages.edit', $page->slug));

        $response->assertOk();
        $response->assertViewHas('page');
    }

    public function test_edit_halaman_yang_tidak_ada_mengembalikan_404(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.pages.edit', 'halaman-tidak-ada'))
            ->assertNotFound();
    }

    public function test_admin_dapat_memperbarui_konten_halaman(): void
    {
        $page = Page::factory()->create(['slug' => 'terms', 'title' => 'Judul Lama']);

        $response = $this->actingAs($this->admin)->put(route('admin.pages.update', $page->slug), [
            'title' => 'Judul Baru',
            'sections' => [
                ['title' => 'Bagian 1', 'intro' => 'Penjelasan bagian 1', 'items' => []],
            ],
        ]);

        $response->assertRedirect(route('admin.pages.index'));
        $this->assertDatabaseHas('pages', ['slug' => 'terms', 'title' => 'Judul Baru']);
    }

    public function test_perubahan_halaman_terlihat_di_halaman_publik(): void
    {
        $page = Page::factory()->create(['slug' => 'terms', 'title' => 'Judul Lama']);

        $this->actingAs($this->admin)->put(route('admin.pages.update', $page->slug), [
            'title' => 'Syarat Terbaru Yoza',
            'sections' => [],
        ]);

        $this->get(route('terms'))->assertSee('Syarat Terbaru Yoza');
    }

    public function test_user_biasa_tidak_dapat_mengakses_manajemen_halaman(): void
    {
        $user = $this->buatUser();

        $this->actingAs($user)
            ->get(route('admin.pages.index'))
            ->assertForbidden();
    }
}
