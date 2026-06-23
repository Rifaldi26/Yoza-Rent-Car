<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use App\Models\Favorit;
use App\Models\Mobil;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature test fitur favorit (FavoritController).
 */
final class FavoritTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->buatUser();
    }

    public function test_user_dapat_melihat_daftar_mobil_favorit(): void
    {
        $mobil = Mobil::factory()->create();
        Favorit::create(['user_id' => $this->user->id, 'mobil_id' => $mobil->id]);

        $response = $this->actingAs($this->user)->get(route('favorit.index'));

        $response->assertOk();
        $response->assertViewHas('mobils');
    }

    public function test_user_dapat_menambahkan_mobil_ke_favorit(): void
    {
        $mobil = Mobil::factory()->create();

        $response = $this->actingAs($this->user)
            ->post(route('favorit.toggle', $mobil));

        $response->assertRedirect();
        $this->assertDatabaseHas('favorits', [
            'user_id' => $this->user->id,
            'mobil_id' => $mobil->id,
        ]);
    }

    public function test_toggle_favorit_menghapus_jika_sudah_ada(): void
    {
        $mobil = Mobil::factory()->create();
        Favorit::create(['user_id' => $this->user->id, 'mobil_id' => $mobil->id]);

        $this->actingAs($this->user)->post(route('favorit.toggle', $mobil));

        $this->assertDatabaseMissing('favorits', [
            'user_id' => $this->user->id,
            'mobil_id' => $mobil->id,
        ]);
    }

    public function test_toggle_favorit_via_ajax_mengembalikan_json(): void
    {
        $mobil = Mobil::factory()->create();

        $response = $this->actingAs($this->user)
            ->postJson(route('favorit.toggle', $mobil));

        $response->assertOk();
        $response->assertJson(['favorit' => true]);
    }

    public function test_tamu_tidak_dapat_mengakses_favorit(): void
    {
        $mobil = Mobil::factory()->create();

        $this->post(route('favorit.toggle', $mobil))
            ->assertRedirect(route('login'));
    }
}
