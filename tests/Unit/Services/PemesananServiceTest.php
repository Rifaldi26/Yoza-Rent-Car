<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\StatusPemesanan;
use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Services\NotifikasiService;
use App\Services\PemesananService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
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

    private PemesananService  $service;
    private NotifikasiService $notifikasiService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->notifikasiService = $this->createMock(NotifikasiService::class);
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
            'harga_per_hari'       => 200_000,
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
            'harga_per_hari'       => 200_000,
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
            'mobil_id'        => $mobil->id,
            'tanggal_mulai'   => '2026-07-10',
            'tanggal_selesai' => '2026-07-15',
            'status'          => StatusPemesanan::Dikonfirmasi->value,
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
            'mobil_id'        => $mobil->id,
            'tanggal_mulai'   => '2026-07-10',
            'tanggal_selesai' => '2026-07-15',
            'status'          => StatusPemesanan::Dikonfirmasi->value,
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
            'mobil_id'        => $mobil->id,
            'tanggal_mulai'   => '2026-07-10',
            'tanggal_selesai' => '2026-07-15',
            'status'          => StatusPemesanan::Kadaluarsa->value,
        ]);

        // Seharusnya tidak konflik karena status kadaluarsa
        $this->assertFalse(
            Pemesanan::adaKonflik($mobil->id, '2026-07-10', '2026-07-15'),
        );
    }

    public function test_mobil_tidak_tersedia_melempar_validation_exception(): void
    {
        $user  = \App\Models\User::factory()->create(['email_verified_at' => now()]);
        $mobil = Mobil::factory()->create(['status' => 'disewa']);

        $this->expectException(ValidationException::class);

        $this->service->buat([
            'mobil_id'        => $mobil->id,
            'tanggal_mulai'   => now()->addDay()->toDateString(),
            'tanggal_selesai' => now()->addDays(3)->toDateString(),
        ], $user->id);
    }

    // ── Transisi status ───────────────────────────────────────────────────

    public function test_pemesanan_tidak_bisa_dibatalkan_setelah_dikonfirmasi(): void
    {
        $this->expectException(\DomainException::class);

        $user      = \App\Models\User::factory()->create();
        $pemesanan = Pemesanan::factory()->create([
            'status' => StatusPemesanan::Dikonfirmasi->value,
        ]);

        $this->service->batalkan($pemesanan, $user->id);
    }
}
