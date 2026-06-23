<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\StatusPemesanan;
use App\Exceptions\PemesananException;
use App\Contracts\NotifikasiServiceInterface;
use App\Jobs\KirimEmailPemesanan;
use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Models\User;
use App\Services\PemesananService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * Unit test PemesananService.
 *
 * Fokus pada logika bisnis: perhitungan harga,
 * pengecekan konflik, transisi status.
 */
final class PemesananServiceTest extends TestCase
{
    use RefreshDatabase;

    private PemesananService $service;

    private NotifikasiServiceInterface $notifikasiService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->notifikasiService = $this->createMock(NotifikasiServiceInterface::class);
        $this->service = new PemesananService($this->notifikasiService);
    }

    // ── Perhitungan harga ─────────────────────────────────────────────────

    public function test_hitung_harga_tanpa_supir(): void
    {
        $mobil = Mobil::factory()->create(['harga_per_hari' => 200_000]);

        $hasil = $this->service->hitungHarga(
            mobilId        : $mobil->id,
            tanggalMulai   : now()->addDay()->toDateString(),
            tanggalSelesai : now()->addDays(4)->toDateString(), // 3 hari
            opsiSupir      : false,
        );

        $this->assertEquals(3, $hasil['durasi']);
        $this->assertEquals(600_000, $hasil['harga_sewa']);
        $this->assertEquals(0, $hasil['biaya_supir']);
        $this->assertEquals(600_000, $hasil['total_harga']);
    }

    public function test_hitung_harga_dengan_supir(): void
    {
        $mobil = Mobil::factory()->create([
            'harga_per_hari' => 200_000,
            'biaya_supir_per_hari' => 100_000,
        ]);

        $hasil = $this->service->hitungHarga(
            mobilId        : $mobil->id,
            tanggalMulai   : now()->addDay()->toDateString(),
            tanggalSelesai : now()->addDays(4)->toDateString(), // 3 hari
            opsiSupir      : true,
        );

        $this->assertEquals(300_000, $hasil['biaya_supir']);
        $this->assertEquals(900_000, $hasil['total_harga']);
    }

    public function test_opsi_supir_tidak_diterapkan_jika_mobil_tidak_punya_supir(): void
    {
        $mobil = Mobil::factory()->create([
            'harga_per_hari' => 200_000,
            'biaya_supir_per_hari' => null, // tidak ada supir
        ]);

        $hasil = $this->service->hitungHarga(
            mobilId        : $mobil->id,
            tanggalMulai   : now()->addDay()->toDateString(),
            tanggalSelesai : now()->addDays(3)->toDateString(),
            opsiSupir      : true,
        );

        $this->assertEquals(0, $hasil['biaya_supir']);
    }

    // ── Pengecekan konflik tanggal ────────────────────────────────────────

    public function test_ada_konflik_mendeteksi_tumpang_tindih(): void
    {
        $mobil = Mobil::factory()->create();

        Pemesanan::factory()->create([
            'mobil_id' => $mobil->id,
            'tanggal_mulai' => '2026-07-10',
            'tanggal_selesai' => '2026-07-15',
            'status' => StatusPemesanan::Dikonfirmasi->value,
        ]);

        // Tumpang tindih di tengah
        $this->assertTrue(
            Pemesanan::adaKonflik($mobil->id, '2026-07-12', '2026-07-17'),
        );
    }

    public function test_tidak_ada_konflik_untuk_tanggal_berbeda(): void
    {
        $mobil = Mobil::factory()->create();

        Pemesanan::factory()->create([
            'mobil_id' => $mobil->id,
            'tanggal_mulai' => '2026-07-10',
            'tanggal_selesai' => '2026-07-15',
            'status' => StatusPemesanan::Dikonfirmasi->value,
        ]);

        // Tanggal setelah pemesanan selesai
        $this->assertFalse(
            Pemesanan::adaKonflik($mobil->id, '2026-07-16', '2026-07-20'),
        );
    }

    public function test_pemesanan_kadaluarsa_tidak_dihitung_sebagai_konflik(): void
    {
        $mobil = Mobil::factory()->create();

        Pemesanan::factory()->create([
            'mobil_id' => $mobil->id,
            'tanggal_mulai' => '2026-07-10',
            'tanggal_selesai' => '2026-07-15',
            'status' => StatusPemesanan::Kadaluarsa->value,
        ]);

        // Seharusnya tidak konflik karena status kadaluarsa
        $this->assertFalse(
            Pemesanan::adaKonflik($mobil->id, '2026-07-10', '2026-07-15'),
        );
    }

    public function test_mobil_tidak_tersedia_melempar_validation_exception(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $mobil = Mobil::factory()->create(['status' => 'disewa']);

        $this->expectException(ValidationException::class);

        $this->service->buat([
            'mobil_id' => $mobil->id,
            'tanggal_mulai' => now()->addDay()->toDateString(),
            'tanggal_selesai' => now()->addDays(3)->toDateString(),
        ], $user->id);
    }

    // ── Transisi status ───────────────────────────────────────────────────

    public function test_pemesanan_tidak_bisa_dibatalkan_setelah_dikonfirmasi(): void
    {
        $this->expectException(PemesananException::class);

        $user = User::factory()->create();
        $pemesanan = Pemesanan::factory()->create([
            'status' => StatusPemesanan::Dikonfirmasi->value,
        ]);

        $this->service->batalkan($pemesanan, $user->id);
    }

    // ── Sewa 12 jam ───────────────────────────────────────────────────────────────

    public function test_hitung_harga_12_jam_adalah_50_persen_harga_harian(): void
    {
        $mobil = Mobil::factory()->create(['harga_per_hari' => 300_000]);

        $hasil = $this->service->hitungHarga(
            mobilId        : $mobil->id,
            tanggalMulai   : now()->addDay()->toDateString(),
            tanggalSelesai : now()->addDay()->toDateString(),
            opsiSupir      : false,
            tipe           : '12_jam',
        );

        $this->assertEquals('12_jam', $hasil['tipe']);
        $this->assertEquals(0, $hasil['durasi']);
        $this->assertEquals(150_000, $hasil['harga_sewa']);
        $this->assertEquals(0, $hasil['biaya_supir']);
        $this->assertEquals(150_000, $hasil['total_harga']);
    }

    public function test_hitung_harga_12_jam_dengan_supir_dihitung_per_1_hari(): void
    {
        $mobil = Mobil::factory()->create([
            'harga_per_hari'       => 300_000,
            'biaya_supir_per_hari' => 100_000,
        ]);

        $hasil = $this->service->hitungHarga(
            mobilId        : $mobil->id,
            tanggalMulai   : now()->addDay()->toDateString(),
            tanggalSelesai : now()->addDay()->toDateString(),
            opsiSupir      : true,
            tipe           : '12_jam',
        );

        // durasi = 0 → max(0, 1) = 1 hari untuk biaya supir
        $this->assertEquals(150_000, $hasil['harga_sewa']);
        $this->assertEquals(100_000, $hasil['biaya_supir']);
        $this->assertEquals(250_000, $hasil['total_harga']);
    }

    public function test_buat_pemesanan_12_jam_berhasil_disimpan(): void
    {
        Bus::fake();

        $user  = User::factory()->create(['email_verified_at' => now()]);
        $mobil = Mobil::factory()->create([
            'status'        => 'tersedia',
            'harga_per_hari' => 300_000,
        ]);

        $tanggal = now()->addDay()->toDateString();

        $pemesanan = $this->service->buat([
            'mobil_id'        => $mobil->id,
            'tipe_sewa'       => '12_jam',
            'tanggal_mulai'   => $tanggal,
            'tanggal_selesai' => $tanggal,
            'waktu_mulai'     => '08:00',
            'opsi_supir'      => false,
        ], $user->id);

        $this->assertDatabaseHas('pemesanans', [
            'id'              => $pemesanan->id,
            'tipe_sewa'       => '12_jam',
            'waktu_mulai'     => '08:00:00',
            'tanggal_mulai'   => $tanggal,
            'tanggal_selesai' => $tanggal,
            'total_harga'     => 150_000,
        ]);

        Bus::assertDispatched(KirimEmailPemesanan::class);
    }

    public function test_dua_pemesanan_12_jam_di_hari_yang_sama_konflik(): void
    {
        Bus::fake();

        $user  = User::factory()->create(['email_verified_at' => now()]);
        $mobil = Mobil::factory()->create([
            'status'        => 'tersedia',
            'harga_per_hari' => 300_000,
        ]);

        $tanggal = now()->addDay()->toDateString();

        // Pemesanan pertama berhasil
        $this->service->buat([
            'mobil_id'        => $mobil->id,
            'tipe_sewa'       => '12_jam',
            'tanggal_mulai'   => $tanggal,
            'tanggal_selesai' => $tanggal,
            'waktu_mulai'     => '08:00',
            'opsi_supir'      => false,
        ], $user->id);

        // Pemesanan kedua di hari yang sama harus konflik
        $this->expectException(ValidationException::class);

        $this->service->buat([
            'mobil_id'        => $mobil->id,
            'tipe_sewa'       => '12_jam',
            'tanggal_mulai'   => $tanggal,
            'tanggal_selesai' => $tanggal,
            'waktu_mulai'     => '14:00',
            'opsi_supir'      => false,
        ], $user->id);
    }

    // ── Konflik per-user (anti pesan ganda) ─────────────────────────────────

    public function test_ada_konflik_user_mendeteksi_pemesanan_lain_milik_user_yang_sama(): void
    {
        $user = User::factory()->create();
        $mobil = Mobil::factory()->create();

        Pemesanan::factory()->create([
            'user_id' => $user->id,
            'mobil_id' => $mobil->id,
            'tanggal_mulai' => '2026-07-10',
            'tanggal_selesai' => '2026-07-15',
            'status' => StatusPemesanan::Dikonfirmasi->value,
        ]);

        $this->assertTrue(
            Pemesanan::adaKonflikUser($user->id, '2026-07-12', '2026-07-17'),
        );
    }

    public function test_ada_konflik_user_mengabaikan_pemesanan_milik_user_lain(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $mobil = Mobil::factory()->create();

        Pemesanan::factory()->create([
            'user_id' => $userA->id,
            'mobil_id' => $mobil->id,
            'tanggal_mulai' => '2026-07-10',
            'tanggal_selesai' => '2026-07-15',
            'status' => StatusPemesanan::Dikonfirmasi->value,
        ]);

        // userB belum punya pemesanan apapun → tidak konflik
        $this->assertFalse(
            Pemesanan::adaKonflikUser($userB->id, '2026-07-12', '2026-07-17'),
        );
    }

    public function test_user_tidak_bisa_memesan_mobil_lain_di_tanggal_yang_tumpang_tindih(): void
    {
        Bus::fake();

        $user  = User::factory()->create(['email_verified_at' => now()]);
        $mobilA = Mobil::factory()->create(['status' => 'tersedia']);
        $mobilB = Mobil::factory()->create(['status' => 'tersedia']);

        // Pemesanan pertama: user memesan mobil A
        $this->service->buat([
            'mobil_id'        => $mobilA->id,
            'tanggal_mulai'   => now()->addDay()->toDateString(),
            'tanggal_selesai' => now()->addDays(4)->toDateString(),
        ], $user->id);

        // User yang sama coba pesan mobil B di tanggal yang tumpang tindih
        $this->expectException(ValidationException::class);

        $this->service->buat([
            'mobil_id'        => $mobilB->id,
            'tanggal_mulai'   => now()->addDays(2)->toDateString(),
            'tanggal_selesai' => now()->addDays(5)->toDateString(),
        ], $user->id);
    }
}