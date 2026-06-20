<?php

namespace App\Providers;

use App\Contracts\NotifikasiServiceInterface;
use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Policies\MobilPolicy;
use App\Policies\PemesananPolicy;
use App\Services\NotifikasiService;
use App\Services\PaymentService;
use App\Services\PemesananService;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    // ── Registrasi binding ke DI container ───────────────────────────────
    //
    // Mendaftarkan Service sebagai singleton agar hanya dibuat sekali
    // per request dan bisa di-inject otomatis oleh Laravel.

    public function register(): void
    {
        $this->app->bind(
            NotifikasiServiceInterface::class, 
            NotifikasiService::class
        );

        $this->app->singleton(NotifikasiService::class);

        $this->app->singleton(PemesananService::class, function ($app) {
            return new PemesananService(
                $app->make(NotifikasiServiceInterface::class),
            );
        });

        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService(
                $app->make(NotifikasiServiceInterface::class),
            );
        });
    }

    public function boot(): void
    {
        // ── Registrasi Policy ─────────────────────────────────────────────
        //
        // Menghubungkan setiap Model ke Policy-nya.
        // Setelah ini, $this->authorize('batalkan', $pemesanan) di Controller
        // akan otomatis memeriksa PemesananPolicy::batalkan().

        Gate::policy(Pemesanan::class, PemesananPolicy::class);
        Gate::policy(Mobil::class, MobilPolicy::class);

        // ── Paksa HTTPS hanya di production ────────────────────────────────
        //
        // Sebelumnya kondisi ini bernilai true di environment APA PUN selain
        // 'local' — termasuk 'testing' — sehingga forceScheme('https') ikut
        // aktif saat test berjalan dan bisa mengacaukan assertion redirect/URL.
        // Diperbaiki agar hanya aktif di production, dengan tetap menghormati
        // header proxy (mis. di belakang load balancer yang terminate SSL).
        if (config('app.env') === 'production'
            || request()->server('HTTP_X_FORWARDED_PROTO') === 'https') {
            URL::forceScheme('https');
        }

        // ── Custom email verifikasi ───────────────────────────────────────
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Verifikasi Email — Yoza Rent Car')
                ->greeting('Halo, '.$notifiable->name.'!')
                ->line('Klik tombol di bawah untuk memverifikasi alamat email Anda.')
                ->action('Verifikasi Email', $url)
                ->line('Jika Anda tidak mendaftar di Yoza Rent Car, abaikan email ini.')
                ->salutation('Salam, Tim Yoza Rent Car');
        });
    }
}