<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Favorit;
use App\Models\Mobil;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit test model Mobil: helper status, scope, dan relasi favorit.
 */
final class MobilTest extends TestCase
{
    use RefreshDatabase;

    public function test_tersedia_mengembalikan_true_jika_status_tersedia(): void
    {
        $mobil = Mobil::factory()->create(['status' => 'tersedia']);

        $this->assertTrue($mobil->isTersedia());
    }

    public function test_tersedia_mengembalikan_false_jika_status_bukan_tersedia(): void
    {
        $mobil = Mobil::factory()->create(['status' => 'disewa']);

        $this->assertFalse($mobil->isTersedia());
    }

    public function test_ada_supir_true_jika_biaya_supir_diisi(): void
    {
        $mobil = Mobil::factory()->create(['biaya_supir_per_hari' => 100_000]);

        $this->assertTrue($mobil->adaSupir());
    }

    public function test_ada_supir_false_jika_biaya_supir_null(): void
    {
        $mobil = Mobil::factory()->create(['biaya_supir_per_hari' => null]);

        $this->assertFalse($mobil->adaSupir());
    }

    public function test_foto_url_mengembalikan_default_jika_foto_kosong(): void
    {
        $mobil = Mobil::factory()->create(['foto' => null]);

        $this->assertStringContainsString('mobil-default.png', $mobil->fotoUrl());
    }

    public function test_foto_url_mengembalikan_path_storage_jika_foto_ada(): void
    {
        $mobil = Mobil::factory()->create(['foto' => 'mobil/contoh.jpg']);

        $this->assertStringContainsString('storage/mobil/contoh.jpg', $mobil->fotoUrl());
    }

    public function test_scope_tersedia_hanya_mengambil_mobil_status_tersedia(): void
    {
        Mobil::factory()->create(['status' => 'tersedia']);
        Mobil::factory()->create(['status' => 'disewa']);
        Mobil::factory()->create(['status' => 'perawatan']);

        $this->assertCount(1, Mobil::tersedia()->get());
    }

    public function test_scope_disewa_hanya_mengambil_mobil_status_disewa(): void
    {
        Mobil::factory()->create(['status' => 'tersedia']);
        Mobil::factory()->count(2)->create(['status' => 'disewa']);

        $this->assertCount(2, Mobil::disewa()->get());
    }

    public function test_scope_perawatan_hanya_mengambil_mobil_status_perawatan(): void
    {
        Mobil::factory()->create(['status' => 'tersedia']);
        Mobil::factory()->create(['status' => 'perawatan']);

        $this->assertCount(1, Mobil::perawatan()->get());
    }

    public function test_difavorit_oleh_mendeteksi_user_yang_sudah_favorit(): void
    {
        $user = User::factory()->create();
        $mobil = Mobil::factory()->create();

        Favorit::create(['user_id' => $user->id, 'mobil_id' => $mobil->id]);

        $this->assertTrue($mobil->difavoritOleh($user->id));
    }

    public function test_difavorit_oleh_mengembalikan_false_jika_belum_difavoritkan(): void
    {
        $user = User::factory()->create();
        $mobil = Mobil::factory()->create();

        $this->assertFalse($mobil->difavoritOleh($user->id));
    }
}
