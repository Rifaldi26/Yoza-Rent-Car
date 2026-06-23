<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Feature test manajemen mobil oleh admin (Admin\MobilController).
 */
final class MobilManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->buatAdmin();
        Storage::fake('public');
    }

    public function test_admin_dapat_melihat_daftar_mobil(): void
    {
        Mobil::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.mobil.index'))
            ->assertOk()
            ->assertViewIs('admin.mobil.index');
    }

    public function test_admin_dapat_menambahkan_mobil_baru(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.mobil.store'), [
            'nama' => 'Toyota Avanza',
            'merek' => 'Toyota',
            'tahun' => 2023,
            'plat_nomor' => 'B 1234 ABC',
            'harga_per_hari' => 250_000,
            'biaya_supir_per_hari' => 100_000,
            'deskripsi' => 'Mobil keluarga nyaman.',
        ]);

        $response->assertRedirect(route('admin.mobil.index'));
        $this->assertDatabaseHas('mobils', [
            'nama' => 'Toyota Avanza',
            'plat_nomor' => 'B 1234 ABC',
        ]);
    }

    public function test_admin_dapat_mengunggah_foto_mobil(): void
    {
        $foto = UploadedFile::fake()->image('avanza.jpg');

        $this->actingAs($this->admin)->post(route('admin.mobil.store'), [
            'nama' => 'Toyota Avanza',
            'merek' => 'Toyota',
            'tahun' => 2023,
            'plat_nomor' => 'B 1234 ABC',
            'harga_per_hari' => 250_000,
            'foto' => $foto,
        ]);

        $mobil = Mobil::latest()->first();
        Storage::disk('public')->assertExists($mobil->foto);
    }

    public function test_plat_nomor_harus_unik(): void
    {
        Mobil::factory()->create(['plat_nomor' => 'B 9999 ZZZ']);

        $response = $this->actingAs($this->admin)->post(route('admin.mobil.store'), [
            'nama' => 'Mobil Baru',
            'merek' => 'Honda',
            'tahun' => 2022,
            'plat_nomor' => 'B 9999 ZZZ',
            'harga_per_hari' => 200_000,
        ]);

        $response->assertSessionHasErrors('plat_nomor');
    }

    public function test_admin_dapat_memperbarui_data_mobil(): void
    {
        $mobil = Mobil::factory()->create(['nama' => 'Nama Lama']);

        $response = $this->actingAs($this->admin)->put(route('admin.mobil.update', $mobil), [
            'nama' => 'Nama Baru',
            'merek' => $mobil->merek,
            'tahun' => $mobil->tahun,
            'plat_nomor' => $mobil->plat_nomor,
            'harga_per_hari' => $mobil->harga_per_hari,
        ]);

        $response->assertRedirect(route('admin.mobil.index'));
        $this->assertDatabaseHas('mobils', ['id' => $mobil->id, 'nama' => 'Nama Baru']);
    }

    public function test_plat_nomor_unik_mengabaikan_mobil_yang_sedang_diedit(): void
    {
        $mobil = Mobil::factory()->create(['plat_nomor' => 'B 1111 AAA']);

        $response = $this->actingAs($this->admin)->put(route('admin.mobil.update', $mobil), [
            'nama' => $mobil->nama,
            'merek' => $mobil->merek,
            'tahun' => $mobil->tahun,
            'plat_nomor' => 'B 1111 AAA', // sama dengan miliknya sendiri
            'harga_per_hari' => $mobil->harga_per_hari,
        ]);

        $response->assertSessionDoesntHaveErrors('plat_nomor');
    }

    public function test_admin_dapat_menghapus_mobil_tanpa_pemesanan_aktif(): void
    {
        $mobil = Mobil::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.mobil.destroy', $mobil));

        $response->assertRedirect(route('admin.mobil.index'));
        $this->assertDatabaseMissing('mobils', ['id' => $mobil->id]);
    }

    public function test_admin_tidak_dapat_menghapus_mobil_dengan_pemesanan_aktif(): void
    {
        $mobil = Mobil::factory()->create();
        Pemesanan::factory()->create([
            'mobil_id' => $mobil->id,
            'status' => 'dikonfirmasi',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.mobil.destroy', $mobil));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('mobils', ['id' => $mobil->id]);
    }

    public function test_admin_dapat_toggle_status_tersedia_ke_perawatan(): void
    {
        $mobil = Mobil::factory()->create(['status' => 'tersedia']);

        $this->actingAs($this->admin)->patch(route('admin.mobil.toggle-status', $mobil));

        $this->assertEquals('perawatan', $mobil->fresh()->status);
    }

    public function test_status_disewa_tidak_dapat_ditoggle_manual(): void
    {
        $mobil = Mobil::factory()->create(['status' => 'disewa']);

        $response = $this->actingAs($this->admin)->patch(route('admin.mobil.toggle-status', $mobil));

        $response->assertSessionHas('error');
        $this->assertEquals('disewa', $mobil->fresh()->status);
    }

    public function test_user_biasa_tidak_dapat_mengakses_manajemen_mobil(): void
    {
        $user = $this->buatUser();

        $this->actingAs($user)
            ->get(route('admin.mobil.index'))
            ->assertForbidden();
    }

    public function test_tamu_diarahkan_ke_login_saat_mengakses_panel_admin(): void
    {
        $this->get(route('admin.mobil.index'))->assertRedirect(route('login'));
    }
}
