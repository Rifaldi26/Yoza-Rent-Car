<?php

namespace App\Console\Commands;

use App\Models\Notifikasi;
use App\Models\Pemesanan;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Mengubah status pemesanan 'pending' yang melewati batas waktu pembayaran
 * menjadi 'kadaluarsa'.
 *
 * Batas waktu dikonfigurasi melalui config/rental.php (payment_expiry_hours).
 *
 * Penggunaan:
 *   php artisan bookings:expire
 *   php artisan bookings:expire --dry-run
 */
class ExpireStaleBookings extends Command
{
    protected $signature = 'bookings:expire
                            {--dry-run : Tampilkan pemesanan yang akan dikadaluarsakan tanpa mengubah data}';

    protected $description = 'Tandai pemesanan pending yang melewati batas waktu pembayaran sebagai kadaluarsa.';

    // ── Handle ────────────────────────────────────────────────────────────

    public function handle(): int
    {
        $isDryRun       = $this->option('dry-run');
        $expiryHours    = (int) config('rental.payment_expiry_hours', 24);
        $cutoff         = Carbon::now()->subHours($expiryHours);

        $pemesanans = Pemesanan::with(['user', 'mobil'])
            ->where('status', 'pending')
            ->where('created_at', '<=', $cutoff)
            ->get();

        if ($pemesanans->isEmpty()) {
            $this->components->info('Tidak ada pemesanan yang perlu dikadaluarsakan.');
            return self::SUCCESS;
        }

        if ($isDryRun) {
            $this->components->warn('Mode dry-run aktif — tidak ada data yang diubah.');
        }

        $this->components->info(sprintf(
            '%d pemesanan ditemukan (dibuat sebelum %s).',
            $pemesanans->count(),
            $cutoff->toDateTimeString(),
        ));

        $berhasil = 0;

        foreach ($pemesanans as $pemesanan) {
            $this->line(sprintf(
                '  <fg=gray>—</> #%d %s oleh %s (dibuat: %s)',
                $pemesanan->id,
                $pemesanan->mobil->nama,
                $pemesanan->user->email,
                $pemesanan->created_at->toDateTimeString(),
            ));

            if ($isDryRun) {
                continue;
            }

            try {
                DB::transaction(function () use ($pemesanan) {
                    $pemesanan->update(['status' => 'kadaluarsa']);

                    Notifikasi::kirim(
                        userId : $pemesanan->user_id,
                        judul  : 'Pemesanan Kadaluarsa',
                        pesan  : "Pemesanan #{$pemesanan->id} untuk {$pemesanan->mobil->nama} "
                                 . 'telah kadaluarsa karena pembayaran tidak diselesaikan tepat waktu.',
                        tipe   : 'warning',
                        link   : route('pemesanan.index'),
                    );
                });

                $berhasil++;
            } catch (\Throwable $e) {
                Log::error(sprintf(
                    '[ExpireBookings] Gagal mengkadaluarsakan pemesanan #%d: %s',
                    $pemesanan->id,
                    $e->getMessage(),
                ));

                $this->components->error("Gagal mengkadaluarsakan pemesanan #{$pemesanan->id}.");
            }
        }

        $this->newLine();
        $this->components->info("Selesai. {$berhasil} pemesanan berhasil dikadaluarsakan.");

        return self::SUCCESS;
    }
}
