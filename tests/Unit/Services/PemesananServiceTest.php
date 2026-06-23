<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Contracts\NotifikasiServiceInterface;
use App\Enums\StatusMobil;
use App\Enums\StatusPemesanan;
use App\Exceptions\PemesananException;
use App\Jobs\KirimEmailPemesanan;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Models\User;
use App\Services\PemesananService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Unit test PemesananService.
 *
 * Fokus pada logika bisnis murni: perhitungan harga, pengecekan
 * konflik tanggal, dan transisi status — tanpa menyentuh HTTP layer.
 * NotifikasiServiceInterface di-mock agar test tidak bergantung pada
 * implementasi konkretnya (lihat App\Contracts\NotifikasiServiceInterface).
 */
final class PemesananServiceTest extends TestCase
{
    use RefreshDatabase;

    private PemesananService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $notifikasiService = $this->createMock(NotifikasiServiceInterface::class);
        $this->service = new PemesananService($notifikasiService);
    }

    /**
     * Payload minimal yang dibutuhkan PemesananService::buat() —
     * mengikuti field wajib pada StorePemesananRequest.
     *
     * @return array<string, mixed>
     */
    private function payloadBuat(Mobil $mobil, array $override = []): array
    {
        return array_merge([
            'mobil_id' => $mobil->id,
            'tanggal_mulai' => now()->addDay()->toDateString(),
            'tanggal_selesai' => now()->addDays(4)->toDateString(),
            'alamat' => 'Jl. Merdeka No. 1',
            'tujuan_sewa' => 'Liburan',
            'kota_tujuan' => 'Bandung',
            'status_pekerjaan' => 'bekerja',
            'sumber_info' => 'Instagram',
            'kontak_darurat' => '081234567890',
            'share_lokasi' => 'https://maps.app.goo.gl/contoh',
        ], $override);
    }

    // ── Perhitungan harga (sewa harian) ────────────────────────────────────

    public function test_hitung_harga_tanpa_supir(): void
    {
        $mobil = Mobil::factory()->create(['harga_per_hari' => 200_000]);

        $hasil = $this->service->hitungHarga(
            mobilId: $mobil->id,
            tanggalMulai: now()->addDay()->toDateString(),
            tanggalSelesai: now()->addDays(4)->toDateString(), // 3 hari
            opsiSupir: false,
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
            mobilId: $mobil->id,
            tanggalMulai: now()->addDay()->toDateString(),
            tanggalSelesai: now()->addDays(4)->toDateString(), // 3 hari
            opsiSupir: true,
        );

        $this->assertEquals(300_000, $hasil['biaya_supir']);
        $this->assertEquals(900_000, $hasil['total_harga']);
    }

    public function test_opsi_supir_tidak_diterapkan_jika_mobil_tidak_punya_supir(): void
    {
        $mobil = Mobil::factory()->create([
            'harga_per_hari' => 200_000,
            'biaya_supir_per_hari' => null,
        ]);

        $hasil = $this->service->hitungHarga(
            mobilId: $mobil->id,
            tanggalMulai: now()->addDay()->toDateString(),
            tanggalSelesai: now()->addDays(3)->toDateString(),
            opsiSupir: true,
        );

        $this->assertEquals(0, $hasil['biaya_supir']);
    }

    // ── Perhitungan harga (sewa 12 jam) ─────────────────────────────────────

    public function test_hitung_harga_12_jam_adalah_50_persen_harga_harian(): void
    {
        $mobil = Mobil::factory()->create(['harga_per_hari' => 300_000]);

        $hasil = $this->service->hitungHarga(
            mobilId: $mobil->id,
            tanggalMulai: now()->addDay()->toDateString(),
            tanggalSelesai: now()->addDay()->toDateString(),
            opsiSupir: false,
            tipe: '12_jam',
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
            'harga_per_hari' => 300_000,
            'biaya_supir_per_hari' => 100_000,
        ]);

        $hasil = $this->service->hitungHarga(
            mobilId: $mobil->id,
            tanggalMulai: now()->addDay()->toDateString(),
            tanggalSelesai: now()->addDay()->toDateString(),
            opsiSupir: true,
            tipe: '12_jam',
        );

        $this->assertEquals(150_000, $hasil['harga_sewa']);
        $this->assertEquals(100_000, $hasil['biaya_supir']);
        $this->assertEquals(250_000, $hasil['total_harga']);
    }

    // ── Pengecekan konflik tanggal (delegasi ke model) ──────────────────────

    public function test_ada_konflik_mendeteksi_tumpang_tindih(): void
    {
        $mobil = Mobil::factory()->create();

        Pemesanan::factory()->create([
            'mobil_id' => $mobil->id,
            'tanggal_mulai' => '2026-07-10',
            'tanggal_selesai' => '2026-07-15',
            'status' => StatusPemesanan::Dikonfirmasi->value,
        ]);

        $this->assertTrue(Pemesanan::adaKonflik($mobil->id, '2026-07-12', '2026-07-17'));
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

        $this->assertFalse(Pemesanan::adaKonflik($mobil->id, '2026-07-16', '2026-07-20'));
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

        $this->assertFalse(Pemesanan::adaKonflik($mobil->id, '2026-07-10', '2026-07-15'));
    }

    // ── buat() — pembuatan pemesanan penuh ──────────────────────────────────

    public function test_buat_pemesanan_berhasil_dengan_payload_lengkap(): void
    {
        Bus::fake();

        $user = User::factory()->create(['email_verified_at' => now()]);
        $mobil = Mobil::factory()->create(['status' => 'tersedia', 'harga_per_hari' => 200_000]);

        $pemesanan = $this->service->buat($this->payloadBuat($mobil), $user->id);

        $this->assertDatabaseHas('pemesanans', [
            'id' => $pemesanan->id,
            'user_id' => $user->id,
            'mobil_id' => $mobil->id,
            'status' => StatusPemesanan::Pending->value,
            'total_harga' => 600_000, // 3 hari x 200_000
        ]);

        Bus::assertDispatched(KirimEmailPemesanan::class);
    }

    public function test_mobil_tidak_tersedia_melempar_validation_exception(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $mobil = Mobil::factory()->create(['status' => 'disewa']);

        $this->expectException(ValidationException::class);

        $this->service->buat($this->payloadBuat($mobil), $user->id);
    }

    public function test_tanggal_konflik_dengan_mobil_lain_melempar_validation_exception(): void
    {
        Bus::fake();

        $mobil = Mobil::factory()->create(['status' => 'tersedia']);
        $user = User::factory()->create(['email_verified_at' => now()]);

        Pemesanan::factory()->create([
            'mobil_id' => $mobil->id,
            'tanggal_mulai' => now()->addDays(2)->toDateString(),
            'tanggal_selesai' => now()->addDays(5)->toDateString(),
            'status' => StatusPemesanan::Dikonfirmasi->value,
        ]);

        $this->expectException(ValidationException::class);

        $this->service->buat($this->payloadBuat($mobil, [
            'tanggal_mulai' => now()->addDays(3)->toDateString(),
            'tanggal_selesai' => now()->addDays(6)->toDateString(),
        ]), $user->id);
    }

    public function test_user_dengan_pemesanan_aktif_lain_tidak_bisa_pesan_mobil_berbeda_di_rentang_sama(): void
    {
        Bus::fake();

        $mobilA = Mobil::factory()->create(['status' => 'tersedia']);
        $mobilB = Mobil::factory()->create(['status' => 'tersedia']);
        $user = User::factory()->create(['email_verified_at' => now()]);

        Pemesanan::factory()->create([
            'user_id' => $user->id,
            'mobil_id' => $mobilA->id,
            'tanggal_mulai' => now()->addDays(2)->toDateString(),
            'tanggal_selesai' => now()->addDays(5)->toDateString(),
            'status' => StatusPemesanan::Pending->value,
        ]);

        $this->expectException(ValidationException::class);

        $this->service->buat($this->payloadBuat($mobilB, [
            'tanggal_mulai' => now()->addDays(3)->toDateString(),
            'tanggal_selesai' => now()->addDays(4)->toDateString(),
        ]), $user->id);
    }

    // ── Sewa 12 jam (end-to-end via buat()) ─────────────────────────────────

    public function test_buat_pemesanan_12_jam_berhasil_disimpan(): void
    {
        Bus::fake();

        $user = User::factory()->create(['email_verified_at' => now()]);
        $mobil = Mobil::factory()->create(['status' => 'tersedia', 'harga_per_hari' => 300_000]);

        $tanggal = now()->addDay()->toDateString();

        $pemesanan = $this->service->buat($this->payloadBuat($mobil, [
            'tipe_sewa' => '12_jam',
            'tanggal_mulai' => $tanggal,
            'tanggal_selesai' => $tanggal,
            'waktu_mulai' => '08:00',
        ]), $user->id);

        $this->assertDatabaseHas('pemesanans', [
            'id' => $pemesanan->id,
            'tipe_sewa' => '12_jam',
            'waktu_mulai' => '08:00:00',
            'tanggal_mulai' => $tanggal,
            'tanggal_selesai' => $tanggal,
            'total_harga' => 150_000,
        ]);

        Bus::assertDispatched(KirimEmailPemesanan::class);
    }

    public function test_dua_pemesanan_12_jam_di_hari_yang_sama_konflik(): void
    {
        Bus::fake();

        $user = User::factory()->create(['email_verified_at' => now()]);
        $mobil = Mobil::factory()->create(['status' => 'tersedia', 'harga_per_hari' => 300_000]);

        $tanggal = now()->addDay()->toDateString();

        $this->service->buat($this->payloadBuat($mobil, [
            'tipe_sewa' => '12_jam',
            'tanggal_mulai' => $tanggal,
            'tanggal_selesai' => $tanggal,
            'waktu_mulai' => '08:00',
        ]), $user->id);

        $this->expectException(ValidationException::class);

        $this->service->buat($this->payloadBuat($mobil, [
            'tipe_sewa' => '12_jam',
            'tanggal_mulai' => $tanggal,
            'tanggal_selesai' => $tanggal,
            'waktu_mulai' => '14:00',
        ]), $user->id);
    }

    // ── Transisi status ──────────────────────────────────────────────────

    public function test_batalkan_mengubah_status_menjadi_dibatalkan(): void
    {
        Bus::fake();

        $user = User::factory()->create();
        $pemesanan = Pemesanan::factory()->create(['status' => StatusPemesanan::Pending->value]);

        $this->service->batalkan($pemesanan, $user->id);

        $this->assertEquals(StatusPemesanan::Dibatalkan->value, $pemesanan->fresh()->status);
        Bus::assertDispatched(KirimEmailPemesanan::class);
    }

    public function test_pemesanan_tidak_bisa_dibatalkan_setelah_dikonfirmasi(): void
    {
        $this->expectException(PemesananException::class);

        $user = User::factory()->create();
        $pemesanan = Pemesanan::factory()->create([
            'status' => StatusPemesanan::Dikonfirmasi->value,
        ]);

        $this->service->batalkan($pemesanan, $user->id);
    }

    public function test_konfirmasi_mengubah_status_pemesanan_dan_mobil(): void
    {
        Bus::fake();

        $mobil = Mobil::factory()->create(['status' => StatusMobil::Tersedia->value]);
        $pemesanan = Pemesanan::factory()->create([
            'mobil_id' => $mobil->id,
            'status' => StatusPemesanan::MenungguKonfirmasiAdmin->value,
        ]);

        $this->service->konfirmasi($pemesanan);

        $this->assertEquals(StatusPemesanan::Dikonfirmasi->value, $pemesanan->fresh()->status);
        $this->assertEquals(StatusMobil::Disewa->value, $mobil->fresh()->status);
        Bus::assertDispatched(KirimEmailPemesanan::class);
    }

    public function test_konfirmasi_gagal_jika_status_bukan_menunggu_konfirmasi_admin(): void
    {
        $this->expectException(PemesananException::class);

        $pemesanan = Pemesanan::factory()->create(['status' => StatusPemesanan::Pending->value]);

        $this->service->konfirmasi($pemesanan);
    }

    public function test_tolak_mengubah_status_menjadi_dibatalkan(): void
    {
        Bus::fake();

        $pemesanan = Pemesanan::factory()->create([
            'status' => StatusPemesanan::MenungguKonfirmasiAdmin->value,
        ]);

        $this->service->tolak($pemesanan);

        $this->assertEquals(StatusPemesanan::Dibatalkan->value, $pemesanan->fresh()->status);
        Bus::assertDispatched(KirimEmailPemesanan::class);
    }

    public function test_tolak_gagal_untuk_status_yang_sudah_final(): void
    {
        $this->expectException(PemesananException::class);

        $pemesanan = Pemesanan::factory()->create(['status' => StatusPemesanan::Selesai->value]);

        $this->service->tolak($pemesanan);
    }

    public function test_selesai_mengubah_status_pemesanan_dan_mobil_serta_mencatat_jurnal(): void
    {
        Bus::fake();

        $mobil = Mobil::factory()->create(['status' => StatusMobil::Disewa->value]);
        $pemesanan = Pemesanan::factory()->create([
            'mobil_id' => $mobil->id,
            'status' => StatusPemesanan::Dikonfirmasi->value,
            'total_harga' => 600_000,
            'biaya_supir' => null,
        ]);

        $this->service->selesai($pemesanan);

        $this->assertEquals(StatusPemesanan::Selesai->value, $pemesanan->fresh()->status);
        $this->assertEquals(StatusMobil::Tersedia->value, $mobil->fresh()->status);
        Bus::assertDispatched(KirimEmailPemesanan::class);
    }

    public function test_selesai_gagal_jika_status_belum_dikonfirmasi(): void
    {
        $this->expectException(PemesananException::class);

        $pemesanan = Pemesanan::factory()->create(['status' => StatusPemesanan::Pending->value]);

        $this->service->selesai($pemesanan);
    }

    public function test_selesai_mencatat_jurnal_double_entry_saat_akun_tersedia(): void
    {
        Bus::fake();

        $kas = Account::factory()->kas()->create(['balance' => 0]);
        $pendapatanSewa = Account::factory()->pendapatanSewa()->create(['balance' => 0]);

        $mobil = Mobil::factory()->create(['status' => StatusMobil::Disewa->value]);
        $pemesanan = Pemesanan::factory()->create([
            'mobil_id' => $mobil->id,
            'status' => StatusPemesanan::Dikonfirmasi->value,
            'total_harga' => 500_000,
            'biaya_supir' => null,
        ]);

        $this->service->selesai($pemesanan);

        $this->assertDatabaseHas('journal_entries', [
            'account_id' => $kas->id,
            'pemesanan_id' => $pemesanan->id,
            'debit' => '500000.00',
            'credit' => '0.00',
        ]);

        $this->assertDatabaseHas('journal_entries', [
            'account_id' => $pendapatanSewa->id,
            'pemesanan_id' => $pemesanan->id,
            'debit' => '0.00',
            'credit' => '500000.00',
        ]);

        $this->assertEquals(500_000, (float) $kas->fresh()->balance);
        $this->assertEquals(500_000, (float) $pendapatanSewa->fresh()->balance);
    }

    public function test_selesai_tidak_mencatat_jurnal_jika_akun_belum_disiapkan(): void
    {
        Bus::fake();

        // Tidak ada Account sama sekali — catatJurnalSelesai() harus
        // diam-diam tidak melakukan apa pun, bukan melempar error.
        $mobil = Mobil::factory()->create(['status' => StatusMobil::Disewa->value]);
        $pemesanan = Pemesanan::factory()->create([
            'mobil_id' => $mobil->id,
            'status' => StatusPemesanan::Dikonfirmasi->value,
        ]);

        $this->service->selesai($pemesanan);

        $this->assertEquals(StatusPemesanan::Selesai->value, $pemesanan->fresh()->status);
        $this->assertDatabaseCount('journal_entries', 0);
    }
}