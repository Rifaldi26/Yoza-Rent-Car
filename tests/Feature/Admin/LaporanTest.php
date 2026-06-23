<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Mobil;
use App\Models\Payment;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature test modul laporan analitik (Admin\LaporanController).
 */
final class LaporanTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->buatAdmin();
    }

    public function test_admin_dapat_melihat_halaman_laporan(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.index'));

        $response->assertOk();
        $response->assertViewHas('ringkasan');
        $response->assertViewHas('topMobil');
    }

    public function test_ringkasan_menghitung_total_pendapatan_dari_payment_dikonfirmasi(): void
    {
        $pemesanan = Pemesanan::factory()->create();
        Payment::factory()->create([
            'pemesanan_id' => $pemesanan->id,
            'status' => 'dikonfirmasi',
            'amount' => 500_000,
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.laporan.index'));

        $ringkasan = $response->viewData('ringkasan');
        $this->assertEquals(500_000, (float) $ringkasan['total_pendapatan']);
    }

    public function test_top_mobil_diurutkan_berdasarkan_jumlah_sewa_selesai(): void
    {
        $mobilPopuler = Mobil::factory()->create();
        Pemesanan::factory()->count(3)->create([
            'mobil_id' => $mobilPopuler->id,
            'status' => 'selesai',
        ]);

        $mobilSepi = Mobil::factory()->create();
        Pemesanan::factory()->create([
            'mobil_id' => $mobilSepi->id,
            'status' => 'selesai',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.laporan.index'));

        $topMobil = $response->viewData('topMobil');
        $this->assertEquals($mobilPopuler->id, $topMobil->first()->id);
    }

    public function test_chart_data_mengembalikan_json_pendapatan_dan_distribusi_status(): void
    {
        $response = $this->actingAs($this->admin)->getJson(route('admin.laporan.chart-data'));

        $response->assertOk();
        $response->assertJsonStructure(['pendapatan_per_bulan', 'status_distribusi']);
    }

    public function test_admin_dapat_mengunduh_laporan_pdf(): void
    {
        Pemesanan::factory()->count(2)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.laporan.export-pdf'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_export_pdf_dapat_difilter_berdasarkan_status(): void
    {
        Pemesanan::factory()->create(['status' => 'selesai']);
        Pemesanan::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.laporan.export-pdf', ['status' => 'selesai']));

        $response->assertOk();
    }

    public function test_user_biasa_tidak_dapat_mengakses_laporan(): void
    {
        $user = $this->buatUser();

        $this->actingAs($user)
            ->get(route('admin.laporan.index'))
            ->assertForbidden();
    }
}
