<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\NotifikasiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit test NotifikasiService.
 *
 * Implementasi konkret dari NotifikasiServiceInterface yang
 * dipakai langsung oleh container (lihat AppServiceProvider).
 */
final class NotifikasiServiceTest extends TestCase
{
    use RefreshDatabase;

    private NotifikasiService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new NotifikasiService();
    }

    public function test_kirim_ke_pengguna_membuat_record_notifikasi(): void
    {
        $user = User::factory()->create();

        $notifikasi = $this->service->kirimKePengguna(
            userId: $user->id,
            judul: 'Judul Uji',
            pesan: 'Pesan uji coba',
            tipe: 'info',
            link: '/dashboard',
        );

        $this->assertDatabaseHas('notifikasis', [
            'id' => $notifikasi->id,
            'user_id' => $user->id,
            'judul' => 'Judul Uji',
            'pesan' => 'Pesan uji coba',
            'tipe' => 'info',
            'link' => '/dashboard',
            'dibaca' => false,
        ]);
    }

    public function test_kirim_ke_pengguna_default_tipe_info_dan_link_null(): void
    {
        $user = User::factory()->create();

        $notifikasi = $this->service->kirimKePengguna($user->id, 'Judul', 'Pesan');

        $this->assertEquals('info', $notifikasi->tipe);
        $this->assertNull($notifikasi->link);
    }

    public function test_kirim_ke_banyak_membuat_notifikasi_untuk_setiap_user(): void
    {
        $users = User::factory()->count(3)->create();

        $this->service->kirimKeBanyak(
            userIds: $users->pluck('id')->all(),
            judul: 'Pengumuman',
            pesan: 'Pesan untuk semua',
        );

        $this->assertDatabaseCount('notifikasis', 3);

        foreach ($users as $user) {
            $this->assertDatabaseHas('notifikasis', [
                'user_id' => $user->id,
                'judul' => 'Pengumuman',
            ]);
        }
    }

    public function test_kirim_ke_banyak_dengan_daftar_kosong_tidak_membuat_apapun(): void
    {
        $this->service->kirimKeBanyak([], 'Judul', 'Pesan');

        $this->assertDatabaseCount('notifikasis', 0);
    }
}
