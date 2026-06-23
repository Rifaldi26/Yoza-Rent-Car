<?php

declare(strict_types=1);

namespace Tests\Feature\Public;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature test rute redirect Google OAuth.
 *
 * Hanya memverifikasi rute redirect tersedia dan benar-benar
 * mengarahkan ke domain Google — tanpa memanggil Google API
 * sungguhan (lihat App\Http\Controllers\Auth\GoogleController).
 */
final class GoogleOAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_google_redirect_mengarah_ke_google(): void
    {
        $response = $this->get(route('auth.google'));

        $response->assertRedirect();
        $this->assertStringContainsString(
            'accounts.google.com',
            $response->headers->get('Location'),
        );
    }
}
