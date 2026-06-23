<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Enums\StatusPemesanan;
use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit test model Pemesanan: helper durasi, tipe sewa, status
 * shortcut, dan deteksi konflik tanggal (adaKonflik & adaKonflikUser).
 */
final class PemesananTest extends TestCase
{
    use RefreshDatabase;

    public function test_durasi_menghitung_selisih_hari(): void
    {
        $pemesanan = Pemesanan::factory()->create([
            'tanggal_mulai' => '2026-07-01',
            'tanggal_selesai' => '2026-07-04',
        ]);

        $this->assertEquals(3, $pemesanan->durasi());
    }

    public function test_adalah_12_jam_true_jika_tipe_sewa_12_jam(): void
    {
        $pemesanan = Pemesanan::factory()->create(['tipe_sewa' => '12_jam']);

        $this->assertTrue($pemesanan->adalah12Jam());
    }

    public function test_adalah_12_jam_false_jika_tipe_sewa_harian(): void
    {
        $pemesanan = Pemesanan::factory()->create(['tipe_sewa' => 'harian']);

        $this->assertFalse($pemesanan->adalah12Jam());
    }

    public function test_is_bekerja_dan_is_mahasiswa_saling_eksklusif(): void
    {
        $bekerja = Pemesanan::factory()->create(['status_pekerjaan' => 'bekerja']);
        $mahasiswa = Pemesanan::factory()->create(['status_pekerjaan' => 'mahasiswa']);

        $this->assertTrue($bekerja->isBekerja());
        $this->assertFalse($bekerja->isMahasiswa());

        $this->assertTrue($mahasiswa->isMahasiswa());
        $this->assertFalse($mahasiswa->isBekerja());
    }

    public function test_status_shortcut_sesuai_dengan_status_disimpan(): void
    {
        $pending = Pemesanan::factory()->create(['status' => StatusPemesanan::Pending->value]);
        $dikonfirmasi = Pemesanan::factory()->create(['status' => StatusPemesanan::Dikonfirmasi->value]);
        $selesai = Pemesanan::factory()->create(['status' => StatusPemesanan::Selesai->value]);

        $this->assertTrue($pending->isPending());
        $this->assertTrue($dikonfirmasi->isDikonfirmasi());
        $this->assertTrue($selesai->isSelesai());
    }

    public function test_is_bisa_dibatalkan_mendelegasikan_ke_enum(): void
    {
        $pending = Pemesanan::factory()->create(['status' => StatusPemesanan::Pending->value]);
        $dikonfirmasi = Pemesanan::factory()->create(['status' => StatusPemesanan::Dikonfirmasi->value]);

        $this->assertTrue($pending->isBisaDibatalkan());
        $this->assertFalse($dikonfirmasi->isBisaDibatalkan());
    }

    public function test_status_enum_dan_label_status_konsisten(): void
    {
        $pemesanan = Pemesanan::factory()->create(['status' => StatusPemesanan::Dikonfirmasi->value]);

        $this->assertSame(StatusPemesanan::Dikonfirmasi, $pemesanan->statusEnum());
        $this->assertEquals('Dikonfirmasi', $pemesanan->labelStatus());
    }

    // ── Scope aktif ────────────────────────────────────────────────────────

    public function test_scope_aktif_hanya_mengambil_status_aktif(): void
    {
        Pemesanan::factory()->create(['status' => StatusPemesanan::Pending->value]);
        Pemesanan::factory()->create(['status' => StatusPemesanan::Dikonfirmasi->value]);
        Pemesanan::factory()->create(['status' => StatusPemesanan::Selesai->value]);
        Pemesanan::factory()->create(['status' => StatusPemesanan::Dibatalkan->value]);

        $this->assertCount(2, Pemesanan::aktif()->get());
    }

    // ── adaKonflik (per mobil) ──────────────────────────────────────────────

    public function test_ada_konflik_true_untuk_rentang_tumpang_tindih_di_tengah(): void
    {
        $mobil = Mobil::factory()->create();

        Pemesanan::factory()->create([
            'mobil_id' => $mobil->id,
            'tanggal_mulai' => '2026-07-10',
            'tanggal_selesai' => '2026-07-15',
            'status' => StatusPemesanan::Dikonfirmasi->value,
        ]);

        $this->assertTrue(Pemesanan::adaKonflik($mobil->id, '2026-07-12', '2026-07-13'));
    }

    public function test_ada_konflik_true_jika_rentang_baru_membungkus_rentang_lama(): void
    {
        $mobil = Mobil::factory()->create();

        Pemesanan::factory()->create([
            'mobil_id' => $mobil->id,
            'tanggal_mulai' => '2026-07-10',
            'tanggal_selesai' => '2026-07-12',
            'status' => StatusPemesanan::Pending->value,
        ]);

        $this->assertTrue(Pemesanan::adaKonflik($mobil->id, '2026-07-05', '2026-07-20'));
    }

    public function test_ada_konflik_false_untuk_rentang_yang_tidak_bersinggungan(): void
    {
        $mobil = Mobil::factory()->create();

        Pemesanan::factory()->create([
            'mobil_id' => $mobil->id,
            'tanggal_mulai' => '2026-07-10',
            'tanggal_selesai' => '2026-07-15',
            'status' => StatusPemesanan::Dikonfirmasi->value,
        ]);

        $this->assertFalse(Pemesanan::adaKonflik($mobil->id, '2026-07-16', '2026-07-20'));
    }

    public function test_ada_konflik_mengabaikan_pemesanan_berstatus_tidak_aktif(): void
    {
        $mobil = Mobil::factory()->create();

        Pemesanan::factory()->create([
            'mobil_id' => $mobil->id,
            'tanggal_mulai' => '2026-07-10',
            'tanggal_selesai' => '2026-07-15',
            'status' => StatusPemesanan::Dibatalkan->value,
        ]);

        $this->assertFalse(Pemesanan::adaKonflik($mobil->id, '2026-07-10', '2026-07-15'));
    }

    public function test_ada_konflik_dapat_mengecualikan_id_tertentu(): void
    {
        $mobil = Mobil::factory()->create();

        $pemesanan = Pemesanan::factory()->create([
            'mobil_id' => $mobil->id,
            'tanggal_mulai' => '2026-07-10',
            'tanggal_selesai' => '2026-07-15',
            'status' => StatusPemesanan::Dikonfirmasi->value,
        ]);

        // Tanpa exclude → konflik dengan dirinya sendiri.
        $this->assertTrue(Pemesanan::adaKonflik($mobil->id, '2026-07-10', '2026-07-15'));

        // Dengan exclude id miliknya → tidak ada konflik lain.
        $this->assertFalse(Pemesanan::adaKonflik($mobil->id, '2026-07-10', '2026-07-15', $pemesanan->id));
    }

    // ── adaKonflikUser (lintas mobil, per user) ─────────────────────────────

    public function test_ada_konflik_user_true_walau_mobil_berbeda(): void
    {
        $user = User::factory()->create();
        $mobilA = Mobil::factory()->create();
        $mobilB = Mobil::factory()->create();

        Pemesanan::factory()->create([
            'user_id' => $user->id,
            'mobil_id' => $mobilA->id,
            'tanggal_mulai' => '2026-07-10',
            'tanggal_selesai' => '2026-07-15',
            'status' => StatusPemesanan::Pending->value,
        ]);

        $this->assertTrue(
            Pemesanan::adaKonflikUser($user->id, '2026-07-12', '2026-07-14'),
        );

        // Sebagai sanity check, mobil B tetap "available" untuk user lain.
        $this->assertFalse(Pemesanan::adaKonflik($mobilB->id, '2026-07-12', '2026-07-14'));
    }

    public function test_ada_konflik_user_false_untuk_user_lain(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $mobil = Mobil::factory()->create();

        Pemesanan::factory()->create([
            'user_id' => $userA->id,
            'mobil_id' => $mobil->id,
            'tanggal_mulai' => '2026-07-10',
            'tanggal_selesai' => '2026-07-15',
            'status' => StatusPemesanan::Pending->value,
        ]);

        $this->assertFalse(
            Pemesanan::adaKonflikUser($userB->id, '2026-07-12', '2026-07-14'),
        );
    }

    // ── Relasi ───────────────────────────────────────────────────────────

    public function test_relasi_user_dan_mobil_termuat_dengan_benar(): void
    {
        $user = User::factory()->create();
        $mobil = Mobil::factory()->create();
        $pemesanan = Pemesanan::factory()->create([
            'user_id' => $user->id,
            'mobil_id' => $mobil->id,
        ]);

        $this->assertTrue($pemesanan->user->is($user));
        $this->assertTrue($pemesanan->mobil->is($mobil));
    }
}
