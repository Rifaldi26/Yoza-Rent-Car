<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature test modul pembukuan (Admin\PembukuanController).
 *
 * Mencakup: ringkasan akun, jurnal umum (dengan filter), laporan
 * laba-rugi, pencatatan pengeluaran (double-entry), dan proteksi
 * akun sistem dari pengeditan.
 */
final class PembukuanTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Account $kas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->buatAdmin();
        $this->kas = Account::factory()->kas()->create(['balance' => 1_000_000]);
    }

    public function test_admin_dapat_melihat_ringkasan_pembukuan(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pembukuan.index'));

        $response->assertOk();
        $response->assertViewHas('ringkasan');
        $response->assertViewHas('accounts');
    }

    public function test_admin_dapat_melihat_jurnal_umum(): void
    {
        JournalEntry::factory()->create(['account_id' => $this->kas->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.pembukuan.jurnal'));

        $response->assertOk();
        $response->assertViewHas('entries');
        $response->assertViewHas('totalDebit');
        $response->assertViewHas('totalCredit');
    }

    public function test_jurnal_dapat_difilter_berdasarkan_rentang_tanggal(): void
    {
        JournalEntry::factory()->create([
            'account_id' => $this->kas->id,
            'date' => '2026-01-10',
        ]);
        JournalEntry::factory()->create([
            'account_id' => $this->kas->id,
            'date' => '2026-06-10',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.pembukuan.jurnal', [
            'tanggal_dari' => '2026-06-01',
            'tanggal_sampai' => '2026-06-30',
        ]));

        $response->assertOk();
        $this->assertCount(1, $response->viewData('entries'));
    }

    public function test_admin_dapat_melihat_laporan_laba_rugi(): void
    {
        $pendapatan = Account::factory()->pendapatanSewa()->create();
        JournalEntry::factory()->create([
            'account_id' => $pendapatan->id,
            'credit' => 500_000,
            'debit' => 0,
            'date' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.pembukuan.laporan'));

        $response->assertOk();
        $response->assertViewHas('labaRugi');
        $response->assertViewHas('arusKas');
    }

    // ── Pencatatan pengeluaran (double-entry) ───────────────────────────────

    public function test_admin_dapat_mencatat_pengeluaran(): void
    {
        $akunPengeluaran = Account::factory()->create(['tipe' => 'pengeluaran', 'balance' => 0]);

        $response = $this->actingAs($this->admin)->post(route('admin.pembukuan.pengeluaran'), [
            'account_id' => $akunPengeluaran->id,
            'amount' => 150_000,
            'description' => 'Biaya servis rutin',
            'date' => now()->toDateString(),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Debit akun pengeluaran, kredit kas — double entry.
        $this->assertDatabaseHas('journal_entries', [
            'account_id' => $akunPengeluaran->id,
            'debit' => '150000.00',
            'credit' => '0.00',
        ]);
        $this->assertDatabaseHas('journal_entries', [
            'account_id' => $this->kas->id,
            'debit' => '0.00',
            'credit' => '150000.00',
        ]);

        $this->assertEquals(150_000, (float) $akunPengeluaran->fresh()->balance);
        $this->assertEquals(850_000, (float) $this->kas->fresh()->balance);
    }

    public function test_pengeluaran_ditolak_jika_akun_bukan_tipe_pengeluaran(): void
    {
        $akunPendapatan = Account::factory()->pendapatanSewa()->create();

        $response = $this->actingAs($this->admin)->post(route('admin.pembukuan.pengeluaran'), [
            'account_id' => $akunPendapatan->id,
            'amount' => 100_000,
            'description' => 'Salah akun',
            'date' => now()->toDateString(),
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('journal_entries', 0);
    }

    public function test_jumlah_pengeluaran_wajib_lebih_dari_nol(): void
    {
        $akunPengeluaran = Account::factory()->create(['tipe' => 'pengeluaran']);

        $response = $this->actingAs($this->admin)->post(route('admin.pembukuan.pengeluaran'), [
            'account_id' => $akunPengeluaran->id,
            'amount' => 0,
            'description' => 'Tidak valid',
            'date' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors('amount');
    }

    // ── Edit akun ────────────────────────────────────────────────────────

    public function test_admin_dapat_mengubah_nama_akun_biasa(): void
    {
        $akun = Account::factory()->create(['nama' => 'Nama Lama']);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.pembukuan.update', $akun), ['nama' => 'Nama Baru']);

        $response->assertRedirect(route('admin.pembukuan.index'));
        $this->assertDatabaseHas('accounts', ['id' => $akun->id, 'nama' => 'Nama Baru']);
    }

    public function test_akun_sistem_tidak_dapat_diubah(): void
    {
        $response = $this->actingAs($this->admin)
            ->put(route('admin.pembukuan.update', $this->kas), ['nama' => 'Coba Ganti']);

        $response->assertSessionHas('error');
        $this->assertEquals('Kas', $this->kas->fresh()->nama);
    }

    // ── Export PDF ───────────────────────────────────────────────────────

    public function test_admin_dapat_mengunduh_laporan_pembukuan_pdf(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pembukuan.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    // ── Otorisasi ─────────────────────────────────────────────────────────

    public function test_user_biasa_tidak_dapat_mengakses_pembukuan(): void
    {
        $user = $this->buatUser();

        $this->actingAs($user)
            ->get(route('admin.pembukuan.index'))
            ->assertForbidden();
    }
}
