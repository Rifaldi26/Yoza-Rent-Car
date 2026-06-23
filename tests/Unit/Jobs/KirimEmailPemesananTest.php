<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\KirimEmailPemesanan;
use App\Models\Pemesanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

final class KirimEmailPemesananTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        Log::spy();
    }

    public function test_handle_event_dibuat(): void
    {
        $pemesanan = Pemesanan::factory()->create();
        $job = new KirimEmailPemesanan($pemesanan, 'dibuat');
        $job->handle();

        Mail::assertSent(\App\Mail\PemesananDibuat::class);
    }

    public function test_handle_event_menunggu_konfirmasi(): void
    {
        $pemesanan = Pemesanan::factory()->create();
        $job = new KirimEmailPemesanan($pemesanan, 'menunggu_konfirmasi');
        $job->handle();

        Mail::assertSent(\App\Mail\PemesananDibayar::class);
    }

    public function test_handle_event_dikonfirmasi(): void
    {
        $pemesanan = Pemesanan::factory()->create();
        $job = new KirimEmailPemesanan($pemesanan, 'dikonfirmasi');
        $job->handle();

        Mail::assertSent(\App\Mail\PemesananDikonfirmasi::class);
    }

    public function test_handle_event_ditolak(): void
    {
        $pemesanan = Pemesanan::factory()->create();
        $job = new KirimEmailPemesanan($pemesanan, 'ditolak');
        $job->handle();

        Mail::assertSent(\App\Mail\PemesananDitolak::class);
    }

    public function test_handle_event_selesai(): void
    {
        $pemesanan = Pemesanan::factory()->create();
        $job = new KirimEmailPemesanan($pemesanan, 'selesai');
        $job->handle();

        Mail::assertSent(\App\Mail\PemesananSelesai::class);
    }

    public function test_handle_event_dibatalkan(): void
    {
        $pemesanan = Pemesanan::factory()->create();
        $job = new KirimEmailPemesanan($pemesanan, 'dibatalkan');
        $job->handle();

        Mail::assertSent(\App\Mail\PemesananDibatalkan::class);
    }

    public function test_handle_event_tidak_dikenal_log_warning(): void
    {
        $pemesanan = Pemesanan::factory()->create();
        $job = new KirimEmailPemesanan($pemesanan, 'unknown_event');
        $job->handle();

        Mail::assertNothingOutgoing();
    }

    public function test_tries_and_backoff(): void
    {
        $pemesanan = Pemesanan::factory()->create();
        $job = new KirimEmailPemesanan($pemesanan, 'dibuat');

        $this->assertEquals(3, $job->tries);
        $this->assertEquals(60, $job->backoff);
    }
}