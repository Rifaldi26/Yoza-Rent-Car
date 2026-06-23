<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Favorit;
use App\Models\Mobil;
use App\Models\Notifikasi;
use App\Models\Pesan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit test model User: helper role, kode pelanggan, dan relasi.
 */
final class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_admin_true_untuk_role_admin(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->assertTrue($user->isAdmin());
    }

    public function test_is_admin_false_untuk_role_user(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->assertFalse($user->isAdmin());
    }

    // ── Kode pelanggan ───────────────────────────────────────────────────

    public function test_kode_pelanggan_menggunakan_inisial_dua_kata_pertama(): void
    {
        $user = User::factory()->create(['name' => 'Budi Santoso']);

        $this->assertEquals(
            sprintf('BS-%03d', $user->id),
            $user->kode_pelanggan,
        );
    }

    public function test_kode_pelanggan_untuk_nama_satu_kata_memakai_dua_huruf_pertama(): void
    {
        $user = User::factory()->create(['name' => 'Budi']);

        $this->assertEquals(
            sprintf('BU-%03d', $user->id),
            $user->kode_pelanggan,
        );
    }

    public function test_kode_pelanggan_id_diformat_tiga_digit(): void
    {
        $user = User::factory()->create(['name' => 'Budi Santoso']);

        $this->assertMatchesRegularExpression('/^[A-Z]{2}-\d{3,}$/', $user->kode_pelanggan);
    }

    // ── Notifikasi & pesan belum dibaca ─────────────────────────────────

    public function test_unread_notifikasi_menghitung_yang_belum_dibaca_saja(): void
    {
        $user = User::factory()->create();

        Notifikasi::factory()->count(3)->create(['user_id' => $user->id, 'dibaca' => false]);
        Notifikasi::factory()->count(2)->create(['user_id' => $user->id, 'dibaca' => true]);

        $this->assertEquals(3, $user->unreadNotifikasi());
    }

    public function test_unread_pesan_menghitung_pesan_diterima_yang_belum_dibaca(): void
    {
        $user = User::factory()->create();
        $pengirim = User::factory()->create();

        Pesan::factory()->count(2)->create([
            'pengirim_id' => $pengirim->id,
            'penerima_id' => $user->id,
            'dibaca' => false,
        ]);

        Pesan::factory()->create([
            'pengirim_id' => $pengirim->id,
            'penerima_id' => $user->id,
            'dibaca' => true,
        ]);

        $this->assertEquals(2, $user->unreadPesan());
    }

    // ── Relasi favorit ───────────────────────────────────────────────────

    public function test_mobil_favorit_mengembalikan_mobil_yang_difavoritkan(): void
    {
        $user = User::factory()->create();
        $mobil = Mobil::factory()->create();

        Favorit::create(['user_id' => $user->id, 'mobil_id' => $mobil->id]);

        $this->assertTrue($user->mobilFavorit->contains($mobil));
    }
}
