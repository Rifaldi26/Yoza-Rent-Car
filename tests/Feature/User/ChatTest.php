<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use App\Events\PesanTerkirim;
use App\Models\Pemesanan;
use App\Models\Pesan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Feature test fitur chat (User\ChatController).
 *
 * Event PesanTerkirim di-fake agar test tidak bergantung pada
 * koneksi Reverb/WebSocket sungguhan.
 */
final class ChatTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->buatUser();
        $this->admin = $this->buatAdmin();
    }

    public function test_user_dapat_melihat_halaman_chat(): void
    {
        $response = $this->actingAs($this->user)->get(route('chat.index'));

        $response->assertOk();
        $response->assertViewHas('admin');
    }

    public function test_user_dapat_mengirim_pesan_ke_admin(): void
    {
        Event::fake();

        $response = $this->actingAs($this->user)->postJson(
            route('chat.kirim', $this->admin),
            ['isi' => 'Halo, saya ingin bertanya soal mobil.'],
        );

        $response->assertCreated();

        $this->assertDatabaseHas('pesans', [
            'pengirim_id' => $this->user->id,
            'penerima_id' => $this->admin->id,
            'isi' => 'Halo, saya ingin bertanya soal mobil.',
        ]);

        Event::assertDispatched(PesanTerkirim::class);
    }

    public function test_isi_pesan_wajib_diisi(): void
    {
        $response = $this->actingAs($this->user)->postJson(
            route('chat.kirim', $this->admin),
            ['isi' => ''],
        );

        $response->assertStatus(422);
    }

    public function test_pesan_dengan_pemesanan_milik_orang_lain_ditolak(): void
    {
        $userLain = $this->buatUser();
        $pemesanan = Pemesanan::factory()->create(['user_id' => $userLain->id]);

        $response = $this->actingAs($this->user)->postJson(
            route('chat.kirim', $this->admin),
            ['isi' => 'Tanya soal pemesanan', 'pemesanan_id' => $pemesanan->id],
        );

        $response->assertStatus(422);
    }

    public function test_user_dapat_melihat_riwayat_percakapan(): void
    {
        Pesan::factory()->create([
            'pengirim_id' => $this->user->id,
            'penerima_id' => $this->admin->id,
            'isi' => 'Halo admin',
        ]);
        Pesan::factory()->create([
            'pengirim_id' => $this->admin->id,
            'penerima_id' => $this->user->id,
            'isi' => 'Halo, ada yang bisa dibantu?',
        ]);

        $response = $this->actingAs($this->user)->getJson(route('chat.riwayat', $this->admin));

        $response->assertOk();
        $response->assertJsonCount(2);
    }

    public function test_melihat_riwayat_menandai_pesan_masuk_sebagai_dibaca(): void
    {
        Pesan::factory()->create([
            'pengirim_id' => $this->admin->id,
            'penerima_id' => $this->user->id,
            'dibaca' => false,
        ]);

        $this->actingAs($this->user)->getJson(route('chat.riwayat', $this->admin));

        $this->assertEquals(0, $this->user->fresh()->unreadPesan());
    }

    public function test_unread_count_chat_mengembalikan_jumlah_yang_benar(): void
    {
        Pesan::factory()->count(3)->create([
            'pengirim_id' => $this->admin->id,
            'penerima_id' => $this->user->id,
            'dibaca' => false,
        ]);

        $response = $this->actingAs($this->user)->getJson(route('chat.unread-count'));

        $response->assertOk();
        $response->assertJson(['count' => 3]);
    }

    public function test_tamu_tidak_dapat_mengakses_chat(): void
    {
        $this->get(route('chat.index'))->assertRedirect(route('login'));
    }
}
