<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * ProfilTest
 *
 * Menggantikan ProfileTest bawaan Laravel Breeze. Proyek ini memakai
 * App\Http\Controllers\User\ProfilController dengan route /profil
 * (Bahasa Indonesia), bukan ProfileController/profile bawaan Breeze
 * yang sudah dihapus karena tidak pernah dipakai di layout manapun.
 */
final class ProfilTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_profil_dapat_ditampilkan(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profil');

        $response->assertOk();
    }

    public function test_tamu_tidak_bisa_akses_halaman_profil(): void
    {
        $response = $this->get('/profil');

        $response->assertRedirect('/login');
    }

    public function test_informasi_profil_dapat_diperbarui(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profil', [
                'name'  => 'Nama Baru',
                'email' => 'baru@example.com',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);

        $user->refresh();

        $this->assertSame('Nama Baru', $user->name);
        $this->assertSame('baru@example.com', $user->email);
    }

    public function test_nomor_hp_opsional_dapat_diperbarui(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profil', [
                'name'  => $user->name,
                'email' => $user->email,
                'no_hp' => '081234567890',
            ]);

        $response->assertSessionHasNoErrors();

        $this->assertSame('081234567890', $user->refresh()->no_hp);
    }

    public function test_email_yang_sudah_dipakai_user_lain_ditolak(): void
    {
        $userLain = User::factory()->create(['email' => 'dipakai@example.com']);
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profil', [
                'name'  => $user->name,
                'email' => 'dipakai@example.com',
            ]);

        $response->assertSessionHasErrors('email');
        $this->assertNotSame('dipakai@example.com', $user->refresh()->email);
    }

    public function test_password_dapat_diganti_dengan_password_lama_yang_benar(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password-lama'),
        ]);

        $response = $this
            ->actingAs($user)
            ->patch('/profil', [
                'name'             => $user->name,
                'email'            => $user->email,
                'current_password' => 'password-lama',
                'password'         => 'password-baru-123',
                'password_confirmation' => 'password-baru-123',
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('password-baru-123', $user->refresh()->password));
    }

    public function test_ganti_password_ditolak_jika_password_lama_salah(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password-lama'),
        ]);

        $response = $this
            ->actingAs($user)
            ->patch('/profil', [
                'name'                  => $user->name,
                'email'                 => $user->email,
                'current_password'      => 'password-salah',
                'password'              => 'password-baru-123',
                'password_confirmation' => 'password-baru-123',
            ]);

        $response->assertSessionHasErrors('current_password');
    }

    public function test_pengguna_dapat_menghapus_akunnya(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this
            ->actingAs($user)
            ->delete('/profil', [
                'password' => 'password',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_password_yang_benar_wajib_diisi_untuk_menghapus_akun(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/profil')
            ->delete('/profil', [
                'password' => 'password-salah',
            ]);

        $response->assertSessionHasErrorsIn('userDeletion', 'password');
        $response->assertRedirect('/profil');

        $this->assertNotNull($user->fresh());
    }
}