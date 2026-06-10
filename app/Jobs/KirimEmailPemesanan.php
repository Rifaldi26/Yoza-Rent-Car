<?php

namespace App\Jobs;

use App\Mail\PemesananDibatalkan;
use App\Mail\PemesananDibayar;
use App\Mail\PemesananDibuat;
use App\Mail\PemesananDikonfirmasi;
use App\Mail\PemesananDitolak;
use App\Mail\PemesananSelesai;
use App\Mail\PesananBaruAdmin;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class KirimEmailPemesanan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 60; // detik antar retry

    public function __construct(
        public Pemesanan $pemesanan,
        public string $event
    ) {}

    public function handle(): void
    {
        $this->pemesanan->load(['user', 'mobil', 'payment']);
        $email = $this->pemesanan->user->email;

        try {
            match($this->event) {
                'dibuat'              => Mail::to($email)->send(new PemesananDibuat($this->pemesanan)),
                'menunggu_konfirmasi' => $this->kirimMenungguKonfirmasi(), // GANTI dari 'dibayar'
                'dikonfirmasi'        => Mail::to($email)->send(new PemesananDikonfirmasi($this->pemesanan)),
                'ditolak'             => Mail::to($email)->send(new PemesananDitolak($this->pemesanan)),
                'selesai'             => Mail::to($email)->send(new PemesananSelesai($this->pemesanan)),
                'dibatalkan'          => Mail::to($email)->send(new PemesananDibatalkan($this->pemesanan)),
                default               => Log::warning("Event tidak dikenal: {$this->event}"),
            };

            Log::info("Email '{$this->event}' terkirim ke {$email} untuk pemesanan #{$this->pemesanan->id}");

        } catch (\Exception $e) {
            Log::error("Gagal kirim email '{$this->event}' ke {$email}: " . $e->getMessage());
            throw $e; // lempar ulang agar queue retry
        }
    }

    // Saat dibayar: kirim ke pelanggan + ke semua admin
    private function kirimDibayar(): void
    {
        Mail::to($this->pemesanan->user->email)
            ->send(new PemesananDibayar($this->pemesanan));

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Mail::to($admin->email)
                ->send(new PesananBaruAdmin($this->pemesanan));
        }
    }

    private function kirimMenungguKonfirmasi(): void
    {
        // Email ke pelanggan
        Mail::to($this->pemesanan->user->email)
            ->send(new PemesananDibayar($this->pemesanan));
    
        // Email ke semua admin
        User::where('role', 'admin')->each(function ($admin) {
            Mail::to($admin->email)
                ->send(new PesananBaruAdmin($this->pemesanan));
        });
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Job KirimEmailPemesanan gagal permanen untuk pemesanan #{$this->pemesanan->id}: "
            . $exception->getMessage());
    }
}