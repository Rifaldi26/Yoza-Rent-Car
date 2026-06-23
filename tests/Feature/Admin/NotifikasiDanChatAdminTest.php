<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Events\PesanTerkirim;
use App\Models\Notifikasi;
use App\Models\Pesan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Feature test notifikasi & chat dari sisi admin
 * (Admin\NotifikasiController & Admin\ChatController).
 */
final class NotifikasiDanChatAdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->buatAdmin();
    }

    // ── Notifikasi ────────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_daftar_notifikasi_miliknya(): void
    {
        Notifikasi::factory()->count(2)->create(['user_id' => $this->admin->id]);

        $this->actingAs($this->admin)
            ->get(route('admin.notifikasi.index'))
            ->assertOk();
    }

    public function test_admin_dapat_menandai_notifikasi_dibaca(): void
    {
        $notifikasi = Notifikasi::factory()->create([
            'user_id' => $this->admin->id,
            'dibaca' => false,
        ]);

        $this->actingAs($this->admin)
            ->patch(route('admin.notifikasi.baca', $notifikasi))
            ->assertRedirect();

        $this->assertTrue($notifikasi->fresh()->dibaca);
    }

    public function test_admin_tidak_dapat_menandai_notifikasi_milik_admin_lain(): void
    {
        $adminLain = $this->buatAdmin();
        $notifikasi = Notifikasi::factory()->create(['user_id' => $adminLain->id]);

        $this->actingAs($this->admin)
            ->patch(route('admin.notifikasi.baca', $notifikasi))
            ->assertForbidden();
    }

    public function test_admin_dapat_menghapus_semua_notifikasinya(): void
    {
        Notifikasi::factory()->count(3)->create(['user_id' => $this->admin->id]);

        $this->actingAs($this->admin)
            ->delete(route('admin.notifikasi.hapus-semua'))
            ->assertRedirect();

        $this->assertDatabaseCount('notifikasis', 0);
    }

    // ── Chat ─────────────────────────────────────────────────────────────

    public function test_admin_dapat_melihat_daftar_kontak_chat(): void
    {
        $user = $this->buatUser();
        Pesan::factory()->create([
            'pengirim_id' => $user->id,
            'penerima_id' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.chat.index'));

        $response->assertOk();
        $response->assertViewHas('users');
    }

    public function test_admin_dapat_mengirim_pesan_ke_user(): void
    {
        Event::fake();

        $user = $this->buatUser();

        $response = $this->actingAs($this->admin)->postJson(
            route('admin.chat.kirim', $user),
            ['isi' => 'Selamat siang, ada yang bisa kami bantu?'],
        );

        $response->assertCreated();
        $this->assertDatabaseHas('pesans', [
            'pengirim_id' => $this->admin->id,
            'penerima_id' => $user->id,
        ]);

        Event::assertDispatched(PesanTerkirim::class);
    }

    public function test_admin_dapat_melihat_riwayat_chat_dengan_user(): void
    {
        $user = $this->buatUser();
        Pesan::factory()->create([
            'pengirim_id' => $user->id,
            'penerima_id' => $this->admin->id,
            'isi' => 'Halo admin',
        ]);

        $response = $this->actingAs($this->admin)->getJson(route('admin.chat.riwayat', $user));

        $response->assertOk();
        $response->assertJsonCount(1);
    }

    public function test_user_biasa_tidak_dapat_mengakses_chat_panel_admin(): void
    {
        $user = $this->buatUser();

        $this->actingAs($user)
            ->get(route('admin.chat.index'))
            ->assertForbidden();
    }
}
