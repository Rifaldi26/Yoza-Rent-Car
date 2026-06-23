<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Pemesanan;
use App\Models\Pesan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PesanTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes(): void
    {
        $pengirim = User::factory()->create();
        $penerima = User::factory()->create();
        $pemesanan = Pemesanan::factory()->create();

        $pesan = Pesan::create([
            'pengirim_id'  => $pengirim->id,
            'penerima_id'  => $penerima->id,
            'isi'          => 'Halo',
            'pemesanan_id' => $pemesanan->id,
            'dibaca'       => false,
        ]);

        $this->assertEquals($pengirim->id, $pesan->pengirim_id);
        $this->assertEquals($penerima->id, $pesan->penerima_id);
        $this->assertEquals('Halo', $pesan->isi);
        $this->assertEquals($pemesanan->id, $pesan->pemesanan_id);
        $this->assertFalse($pesan->dibaca);
    }

    public function test_belongs_to_pengirim(): void
    {
        $pengirim = User::factory()->create();
        $pesan    = Pesan::factory()->create(['pengirim_id' => $pengirim->id]);

        $this->assertNotNull($pesan->pengirim);
        $this->assertEquals($pengirim->id, $pesan->pengirim->id);
    }

    public function test_belongs_to_penerima(): void
    {
        $penerima = User::factory()->create();
        $pesan    = Pesan::factory()->create(['penerima_id' => $penerima->id]);

        $this->assertNotNull($pesan->penerima);
        $this->assertEquals($penerima->id, $pesan->penerima->id);
    }

    public function test_belongs_to_pemesanan(): void
    {
        $pemesanan = Pemesanan::factory()->create();
        $pesan     = Pesan::factory()->create(['pemesanan_id' => $pemesanan->id]);

        $this->assertNotNull($pesan->pemesanan);
        $this->assertEquals($pemesanan->id, $pesan->pemesanan->id);
    }

    public function test_percakapan_mengembalikan_pesan_antara_dua_user(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $userC = User::factory()->create();
        $pem   = Pemesanan::factory()->create();

        Pesan::factory()->create(['pengirim_id' => $userA->id, 'penerima_id' => $userB->id, 'pemesanan_id' => $pem->id]);
        Pesan::factory()->create(['pengirim_id' => $userB->id, 'penerima_id' => $userA->id, 'pemesanan_id' => $pem->id]);
        Pesan::factory()->create(['pengirim_id' => $userA->id, 'penerima_id' => $userC->id, 'pemesanan_id' => $pem->id]);

        $percakapan = Pesan::percakapan($userA->id, $userB->id);

        $this->assertCount(2, $percakapan);
    }

    public function test_dibaca_cast_to_boolean(): void
    {
        $pesan = Pesan::factory()->create(['dibaca' => true]);

        $this->assertIsBool($pesan->dibaca);
        $this->assertTrue($pesan->dibaca);
    }
}