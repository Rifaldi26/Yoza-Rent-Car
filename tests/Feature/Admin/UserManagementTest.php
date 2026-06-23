<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature test manajemen user oleh admin (Admin\UserController).
 */
final class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->buatAdmin();
    }

    public function test_admin_dapat_melihat_daftar_pelanggan(): void
    {
        User::factory()->count(3)->create(['role' => 'user']);

        $response = $this->actingAs($this->admin)->get(route('admin.user.index'));

        $response->assertOk();
        $response->assertViewHas('users');
    }

    public function test_daftar_pelanggan_tidak_menyertakan_akun_admin(): void
    {
        User::factory()->create(['role' => 'admin', 'name' => 'Admin Lain']);
        User::factory()->create(['role' => 'user', 'name' => 'Pelanggan Satu']);

        $response = $this->actingAs($this->admin)->get(route('admin.user.index'));

        $response->assertDontSee('Admin Lain');
        $response->assertSee('Pelanggan Satu');
    }

    public function test_admin_dapat_melihat_detail_pelanggan_beserta_riwayat_pemesanan(): void
    {
        $user = $this->buatUser();
        $mobil = Mobil::factory()->create();
        Pemesanan::factory()->create(['user_id' => $user->id, 'mobil_id' => $mobil->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.user.show', $user));

        $response->assertOk();
        $response->assertViewHas('user');
    }

    public function test_user_biasa_tidak_dapat_mengakses_manajemen_user(): void
    {
        $user = $this->buatUser();

        $this->actingAs($user)
            ->get(route('admin.user.index'))
            ->assertForbidden();
    }
}
