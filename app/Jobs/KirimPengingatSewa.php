<?php

namespace App\Jobs;

use App\Mail\PengingatSewa;
use App\Models\Pemesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class KirimPengingatSewa implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function handle(): void
    {
        // Ambil semua pemesanan dikonfirmasi yang mulai besok
        $pemesanans = Pemesanan::with(['user', 'mobil'])
            ->where('status', 'dikonfirmasi')
            ->whereDate('tanggal_mulai', now()->addDay()->toDateString())
            ->get();

        foreach ($pemesanans as $pemesanan) {
            try {
                Mail::to($pemesanan->user->email)
                    ->send(new PengingatSewa($pemesanan));

                Log::info("Pengingat H-1 terkirim ke {$pemesanan->user->email} untuk pemesanan #{$pemesanan->id}");
            } catch (\Exception $e) {
                Log::error("Gagal kirim pengingat H-1 pemesanan #{$pemesanan->id}: " . $e->getMessage());
            }
        }
    }
}