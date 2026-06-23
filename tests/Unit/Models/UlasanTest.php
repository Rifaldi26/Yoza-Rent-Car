<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Mobil;
use App\Models\Ulasan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit test model Ulasan: scope moderasi (disetujui / menunggu).
 */
final class UlasanTest extends TestCase
{
    use RefreshDatabase;

    public function test_scope_disetujui_hanya_mengambil_ulasan_yang_disetujui(): void
    {
        Ulasan::factory()->count(2)->create(['disetujui' => true]);
        Ulasan::factory()->count(3)->create(['disetujui' => false]);

        $this->assertCount(2, Ulasan::disetujui()->get());
    }

    public function test_scope_menunggu_hanya_mengambil_ulasan_yang_belum_disetujui(): void
    {
        Ulasan::factory()->count(2)->create(['disetujui' => true]);
        Ulasan::factory()->count(3)->create(['disetujui' => false]);

        $this->assertCount(3, Ulasan::menunggu()->get());
    }

    public function test_relasi_mobil_termuat_dengan_benar(): void
    {
        $mobil = Mobil::factory()->create();
        $ulasan = Ulasan::factory()->create(['mobil_id' => $mobil->id]);

        $this->assertTrue($ulasan->mobil->is($mobil));
    }
}
