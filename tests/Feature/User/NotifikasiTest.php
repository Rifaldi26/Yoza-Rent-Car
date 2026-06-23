<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature test fitur notifikasi (User\NotifikasiController).
 */
final class NotifikasiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->buatUser();
    }

    public function test_user_dapat_melihat_daftar_notifikasi_miliknya(): void
    {
        Notifikasi::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('notifikasi.index'));

        $response->assertOk();
        $response->assertViewHas('notifikasis');
    }

    public function test_unread_count_mengembalikan_jumlah_yang_benar(): void
    {
        Notifikasi::factory()->count(2)->create(['user_id' => $this->user->id, 'dibaca' => false]);
        Notifikasi::factory()->create(['user_id' => $this->user->id, 'dibaca' => true]);

        $response = $this->actingAs($this->user)->getJson(route('notifikasi.unread-count'));

        $response->assertOk();
        $response->assertJson(['count' => 2]);
    }

    public function test_user_dapat_menandai_notifikasi_sebagai_dibaca(): void
    {
        $notifikasi = Notifikasi::factory()->create([
            'user_id' => $this->user->id,
            'dibaca' => false,
            'link' => null,
        ]);

        $this->actingAs($this->user)
            ->patch(route('notifikasi.baca', $notifikasi))
            ->assertRedirect();

        $this->assertTrue($notifikasi->fresh()->dibaca);
    }

    public function test_baca_notifikasi_redirect_ke_link_jika_ada(): void
    {
        $notifikasi = Notifikasi::factory()->create([
            'user_id' => $this->user->id,
            'link' => '/dashboard',
        ]);

        $this->actingAs($this->user)
            ->patch(route('notifikasi.baca', $notifikasi))
            ->assertRedirect('/dashboard');
    }

    public function test_user_tidak_dapat_menandai_notifikasi_milik_orang_lain(): void
    {
        $userLain = $this->buatUser();
        $notifikasi = Notifikasi::factory()->create(['user_id' => $userLain->id]);

        $this->actingAs($this->user)
            ->patch(route('notifikasi.baca', $notifikasi))
            ->assertForbidden();
    }

    public function test_user_dapat_menghapus_notifikasi_miliknya(): void
    {
        $notifikasi = Notifikasi::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->delete(route('notifikasi.destroy', $notifikasi))
            ->assertRedirect();

        $this->assertDatabaseMissing('notifikasis', ['id' => $notifikasi->id]);
    }

    public function test_user_tidak_dapat_menghapus_notifikasi_milik_orang_lain(): void
    {
        $userLain = $this->buatUser();
        $notifikasi = Notifikasi::factory()->create(['user_id' => $userLain->id]);

        $this->actingAs($this->user)
            ->delete(route('notifikasi.destroy', $notifikasi))
            ->assertForbidden();

        $this->assertDatabaseHas('notifikasis', ['id' => $notifikasi->id]);
    }

    public function test_user_dapat_menghapus_semua_notifikasi_miliknya(): void
    {
        Notifikasi::factory()->count(4)->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->delete(route('notifikasi.hapus-semua'))
            ->assertRedirect();

        $this->assertDatabaseCount('notifikasis', 0);
    }

    public function test_hapus_semua_tidak_menghapus_notifikasi_milik_user_lain(): void
    {
        $userLain = $this->buatUser();
        Notifikasi::factory()->count(2)->create(['user_id' => $this->user->id]);
        Notifikasi::factory()->count(2)->create(['user_id' => $userLain->id]);

        $this->actingAs($this->user)->delete(route('notifikasi.hapus-semua'));

        $this->assertDatabaseCount('notifikasis', 2);
    }
}
