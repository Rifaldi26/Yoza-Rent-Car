<?php

namespace App\Jobs;

use App\Mail\PengingatSewa;
use App\Models\Notifikasi;
use App\Models\Pemesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Mengirim pengingat sewa untuk satu pemesanan.
 *
 * Job ini dirancang untuk satu pemesanan (Single Responsibility).
 * Dispatching massal dilakukan oleh command DispatchRentalReminders.
 */
class SendRentalReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jumlah hari sebelum tanggal mulai yang dikirim pengingat ini.
     * Digunakan untuk label log dan subject email.
     */
    public function __construct(
        public readonly Pemesanan $pemesanan,
        public readonly int $hariSebelum,
    ) {}

    public int $tries = 3;

    public int $backoff = 120; // detik antar retry

    // ── Handle ────────────────────────────────────────────────────────────

    public function handle(): void
    {
        // Guard: lewati jika status sudah berubah saat job diproses
        if (! $this->pemesanan->isDikonfirmasi()) {
            Log::info(sprintf(
                '[RentalReminder] Dilewati — pemesanan #%d sudah berstatus "%s".',
                $this->pemesanan->id,
                $this->pemesanan->status,
            ));

            return;
        }

        $this->kirimEmail();
        $this->kirimNotifikasiInApp();

        Log::info(sprintf(
            '[RentalReminder] H-%d terkirim ke %s (pemesanan #%d).',
            $this->hariSebelum,
            $this->pemesanan->user->email,
            $this->pemesanan->id,
        ));
    }

    public function failed(\Throwable $exception): void
    {
        Log::error(sprintf(
            '[RentalReminder] Gagal permanen — pemesanan #%d H-%d: %s',
            $this->pemesanan->id,
            $this->hariSebelum,
            $exception->getMessage(),
        ));
    }

    // ── Private ───────────────────────────────────────────────────────────

    private function kirimEmail(): void
    {
        Mail::to($this->pemesanan->user->email)
            ->send(new PengingatSewa($this->pemesanan, $this->hariSebelum));
    }

    private function kirimNotifikasiInApp(): void
    {
        $label = $this->hariSebelum === 1 ? 'besok' : "{$this->hariSebelum} hari lagi";

        Notifikasi::kirim(
            userId : $this->pemesanan->user_id,
            judul  : "Pengingat Sewa — {$this->pemesanan->mobil->nama}",
            pesan  : "Sewa kendaraan Anda untuk {$this->pemesanan->mobil->nama} dimulai {$label} "
                     ."({$this->pemesanan->tanggal_mulai->isoFormat('D MMM Y')}).",
            tipe   : 'info',
            link   : route('pemesanan.show', $this->pemesanan),
        );
    }
}
