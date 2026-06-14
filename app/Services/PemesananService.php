<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StatusMobil;
use App\Enums\StatusPemesanan;
use App\Jobs\KirimEmailPemesanan;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Mobil;
use App\Models\Notifikasi;
use App\Models\Pemesanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * PemesananService
 *
 * Memusatkan seluruh logika bisnis pemesanan sewa mobil:
 * perhitungan harga, validasi ketersediaan, perubahan status,
 * pencatatan jurnal, dan pengiriman notifikasi.
 *
 * Controller hanya bertanggung jawab menerima input, memanggil
 * service, dan mengembalikan respons.
 */
final class PemesananService
{
    public function __construct(
        private readonly NotifikasiService $notifikasiService,
    ) {}

    // ── Buat pemesanan baru ───────────────────────────────────────────────

    /**
     * Membuat pemesanan baru setelah memvalidasi ketersediaan mobil
     * dan menghitung total harga secara otomatis.
     *
     * @throws ValidationException bila mobil tidak tersedia atau ada konflik tanggal
     */
    public function buat(array $data, int $userId): Pemesanan
    {
        $mobil = Mobil::findOrFail($data['mobil_id']);

        if (! $mobil->tersedia()) {
            throw ValidationException::withMessages([
                'mobil_id' => 'Mobil ini sedang tidak tersedia untuk dipesan.',
            ]);
        }

        if (Pemesanan::adaKonflik($mobil->id, $data['tanggal_mulai'], $data['tanggal_selesai'])) {
            throw ValidationException::withMessages([
                'tanggal_mulai' => 'Mobil sudah dipesan pada rentang tanggal tersebut.',
            ]);
        }

        $mulai = Carbon::parse($data['tanggal_mulai']);
        $selesai = Carbon::parse($data['tanggal_selesai']);
        $durasi = $mulai->diffInDays($selesai);
        $opsiSupir = (bool) ($data['opsi_supir'] ?? false);

        $biayaSupir = ($opsiSupir && $mobil->adaSupir())
            ? $durasi * $mobil->biaya_supir_per_hari
            : 0;

        $totalHarga = ($durasi * $mobil->harga_per_hari) + $biayaSupir;

        $pemesanan = Pemesanan::create([
            'user_id' => $userId,
            'mobil_id' => $mobil->id,
            'tanggal_mulai' => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'],
            'opsi_supir' => $opsiSupir,
            'biaya_supir' => $biayaSupir > 0 ? $biayaSupir : null,
            'total_harga' => $totalHarga,
            'status' => StatusPemesanan::Pending->value,
            'catatan' => $data['catatan'] ?? null,
        ]);

        $this->notifikasiService->kirimKePengguna(
            userId : $userId,
            judul  : 'Pemesanan Dibuat',
            pesan  : "Pemesanan #{$pemesanan->id} untuk {$mobil->nama} berhasil dibuat. Selesaikan pembayaran Anda.",
            tipe   : 'info',
            link   : route('payment.checkout', $pemesanan),
        );

        KirimEmailPemesanan::dispatch($pemesanan->fresh(['user', 'mobil']), 'dibuat');

        return $pemesanan;
    }

    // ── Batalkan pemesanan ────────────────────────────────────────────────

    /**
     * @throws \DomainException bila pemesanan tidak bisa dibatalkan
     */
    public function batalkan(Pemesanan $pemesanan, int $aktorId): void
    {
        if (! StatusPemesanan::from($pemesanan->status)->bisaDibatalkan()) {
            throw new \DomainException('Pemesanan ini tidak dapat dibatalkan.');
        }

        $pemesanan->update(['status' => StatusPemesanan::Dibatalkan->value]);

        $this->notifikasiService->kirimKePengguna(
            userId : $aktorId,
            judul  : 'Pemesanan Dibatalkan',
            pesan  : "Pemesanan #{$pemesanan->id} untuk {$pemesanan->mobil->nama} telah dibatalkan.",
            tipe   : 'warning',
            link   : route('pemesanan.index'),
        );

        KirimEmailPemesanan::dispatch($pemesanan->fresh(['user', 'mobil']), 'dibatalkan');
    }

    // ── Konfirmasi oleh admin ─────────────────────────────────────────────

    /**
     * @throws \DomainException bila status tidak sesuai
     */
    public function konfirmasi(Pemesanan $pemesanan): void
    {
        if (! StatusPemesanan::from($pemesanan->status)->bisaDikonfirmasiAdmin()) {
            throw new \DomainException('Pemesanan tidak dalam status yang dapat dikonfirmasi.');
        }

        DB::transaction(function () use ($pemesanan) {
            $pemesanan->update(['status' => StatusPemesanan::Dikonfirmasi->value]);
            $pemesanan->mobil->update(['status' => StatusMobil::Disewa->value]);

            $this->notifikasiService->kirimKePengguna(
                userId : $pemesanan->user_id,
                judul  : 'Pemesanan Dikonfirmasi',
                pesan  : "Pemesanan #{$pemesanan->id} untuk {$pemesanan->mobil->nama} telah dikonfirmasi.",
                tipe   : 'success',
                link   : route('pemesanan.show', $pemesanan),
            );
        });

        KirimEmailPemesanan::dispatch($pemesanan->fresh(['user', 'mobil', 'payment']), 'dikonfirmasi');
    }

    // ── Tolak oleh admin ──────────────────────────────────────────────────

    /**
     * @throws \DomainException bila status tidak sesuai
     */
    public function tolak(Pemesanan $pemesanan): void
    {
        $status = StatusPemesanan::from($pemesanan->status);

        if (! in_array($status, [StatusPemesanan::Pending, StatusPemesanan::MenungguKonfirmasiAdmin])) {
            throw new \DomainException('Pemesanan tidak dapat ditolak pada status saat ini.');
        }

        $pemesanan->update(['status' => StatusPemesanan::Dibatalkan->value]);

        $this->notifikasiService->kirimKePengguna(
            userId : $pemesanan->user_id,
            judul  : 'Pemesanan Ditolak',
            pesan  : "Maaf, pemesanan #{$pemesanan->id} untuk {$pemesanan->mobil->nama} tidak dapat kami proses. Hubungi kami via chat untuk informasi lebih lanjut.",
            tipe   : 'warning',
            link   : route('chat.index'),
        );

        KirimEmailPemesanan::dispatch($pemesanan->fresh(['user', 'mobil']), 'ditolak');
    }

    // ── Tandai selesai ────────────────────────────────────────────────────

    /**
     * @throws \DomainException bila status tidak sesuai
     */
    public function selesai(Pemesanan $pemesanan): void
    {
        if (! StatusPemesanan::from($pemesanan->status)->bisaSelesai()) {
            throw new \DomainException('Hanya pemesanan yang dikonfirmasi yang dapat diselesaikan.');
        }

        DB::transaction(function () use ($pemesanan) {
            $pemesanan->update(['status' => StatusPemesanan::Selesai->value]);
            $pemesanan->mobil->update(['status' => StatusMobil::Tersedia->value]);

            $this->catatJurnalSelesai($pemesanan);

            $this->notifikasiService->kirimKePengguna(
                userId : $pemesanan->user_id,
                judul  : 'Pemesanan Selesai',
                pesan  : "Terima kasih telah menggunakan Yoza Rent Car! Pemesanan #{$pemesanan->id} telah selesai.",
                tipe   : 'success',
                link   : route('pemesanan.index'),
            );
        });

        KirimEmailPemesanan::dispatch($pemesanan->fresh(['user', 'mobil', 'payment']), 'selesai');
    }

    // ── Pencatatan jurnal double-entry ────────────────────────────────────

    /**
     * Mencatat jurnal akuntansi saat pemesanan diselesaikan.
     * Menggunakan double-entry: Debit Kas, Kredit Pendapatan.
     */
    private function catatJurnalSelesai(Pemesanan $pemesanan): void
    {
        $kas = Account::where('kode', '1-001')->first();
        $pendapatanSewa = Account::where('kode', '4-001')->first();
        $pendapatanSupir = Account::where('kode', '4-002')->first();

        if (! $kas || ! $pendapatanSewa) {
            return;
        }

        $hargaSewa = $pemesanan->total_harga - ($pemesanan->biaya_supir ?? 0);
        $biayaSupir = $pemesanan->biaya_supir ?? 0;
        $tanggal = now()->toDateString();
        $paymentId = $pemesanan->payment?->id;

        JournalEntry::create([
            'account_id' => $kas->id,
            'pemesanan_id' => $pemesanan->id,
            'payment_id' => $paymentId,
            'debit' => $pemesanan->total_harga,
            'credit' => 0,
            'description' => "Kas masuk — Pemesanan #{$pemesanan->id}",
            'date' => $tanggal,
        ]);

        JournalEntry::create([
            'account_id' => $pendapatanSewa->id,
            'pemesanan_id' => $pemesanan->id,
            'payment_id' => $paymentId,
            'debit' => 0,
            'credit' => $hargaSewa,
            'description' => "Pendapatan sewa — Pemesanan #{$pemesanan->id}",
            'date' => $tanggal,
        ]);

        if ($biayaSupir > 0 && $pendapatanSupir) {
            JournalEntry::create([
                'account_id' => $pendapatanSupir->id,
                'pemesanan_id' => $pemesanan->id,
                'payment_id' => $paymentId,
                'debit' => 0,
                'credit' => $biayaSupir,
                'description' => "Pendapatan jasa supir — Pemesanan #{$pemesanan->id}",
                'date' => $tanggal,
            ]);
        }

        $kas->increment('balance', $pemesanan->total_harga);
        $pendapatanSewa->increment('balance', $hargaSewa);

        if ($biayaSupir > 0 && $pendapatanSupir) {
            $pendapatanSupir->increment('balance', $biayaSupir);
        }
    }

    // ── Hitung harga (untuk preview sebelum simpan) ───────────────────────

    public function hitungHarga(
        int $mobilId,
        string $tanggalMulai,
        string $tanggalSelesai,
        bool $opsiSupir = false,
    ): array {
        $mobil = Mobil::findOrFail($mobilId);
        $mulai = Carbon::parse($tanggalMulai);
        $selesai = Carbon::parse($tanggalSelesai);
        $durasi = $mulai->diffInDays($selesai);
        $biayaSupir = ($opsiSupir && $mobil->adaSupir())
            ? $durasi * $mobil->biaya_supir_per_hari
            : 0;

        return [
            'durasi' => $durasi,
            'harga_sewa' => $durasi * $mobil->harga_per_hari,
            'biaya_supir' => $biayaSupir,
            'total_harga' => ($durasi * $mobil->harga_per_hari) + $biayaSupir,
        ];
    }
}
