<?php

namespace App\Console\Commands;

use App\Jobs\SendRentalReminder;
use App\Models\Pemesanan;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * Mencari pemesanan yang perlu mendapat pengingat dan mendispatch
 * SendRentalReminder per pemesanan.
 *
 * Interval pengingat dikonfigurasi melalui config/rental.php sehingga
 * tidak perlu mengubah kode command ini saat interval berubah.
 *
 * Penggunaan:
 *   php artisan reminders:dispatch
 *   php artisan reminders:dispatch --dry-run
 */
class DispatchRentalReminders extends Command
{
    protected $signature = 'reminders:dispatch
                            {--dry-run : Tampilkan pemesanan yang akan dikirimi tanpa benar-benar mendispatch}';

    protected $description = 'Dispatch pengingat sewa untuk pemesanan yang mendekati tanggal mulai.';

    // ── Handle ────────────────────────────────────────────────────────────

    public function handle(): int
    {
        $intervals = $this->reminderIntervals();
        $isDryRun = $this->option('dry-run');
        $today = Carbon::today();
        $total = 0;

        if ($isDryRun) {
            $this->components->warn('Mode dry-run aktif — tidak ada job yang didispatch.');
        }

        foreach ($intervals as $hariSebelum) {
            $targetDate = $today->copy()->addDays($hariSebelum);
            $pemesanans = $this->queryPemesanan($targetDate);

            $this->components->info(sprintf(
                'H-%d (%s): %d pemesanan ditemukan.',
                $hariSebelum,
                $targetDate->toDateString(),
                $pemesanans->count(),
            ));

            foreach ($pemesanans as $pemesanan) {
                $this->line(sprintf(
                    '  <fg=gray>—</> #%d %s (%s)',
                    $pemesanan->id,
                    $pemesanan->mobil->nama,
                    $pemesanan->user->email,
                ));

                if (! $isDryRun) {
                    SendRentalReminder::dispatch($pemesanan, $hariSebelum);
                }

                $total++;
            }
        }

        $this->newLine();
        $this->components->info(
            $isDryRun
                ? "Dry-run selesai. Total {$total} pengingat akan dikirim."
                : "Selesai. {$total} job pengingat telah didispatch ke queue."
        );

        return self::SUCCESS;
    }

    // ── Private ───────────────────────────────────────────────────────────

    /**
     * Ambil interval pengingat dari konfigurasi.
     * Default: [3, 1] (H-3 dan H-1).
     *
     * @return int[]
     */
    private function reminderIntervals(): array
    {
        return config('rental.reminder_intervals', [3, 1]);
    }

    /**
     * Query pemesanan dikonfirmasi yang tanggal mulainya tepat di $targetDate.
     */
    private function queryPemesanan(Carbon $targetDate): Collection
    {
        return Pemesanan::with(['user', 'mobil'])
            ->where('status', 'dikonfirmasi')
            ->whereDate('tanggal_mulai', $targetDate->toDateString())
            ->get();
    }
}
