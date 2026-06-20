<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StatusPayment;
use App\Enums\StatusPemesanan;
use App\Exceptions\PaymentException;
use App\Contracts\NotifikasiServiceInterface;
use App\Jobs\KirimEmailPemesanan;
use App\Models\Payment;
use App\Models\Pemesanan;
use App\Models\User;

/**
 * PaymentService
 *
 * Mengelola logika pembayaran: pemilihan metode, pembuatan
 * record Payment, konfirmasi oleh admin, dan pembangunan
 * URL WhatsApp untuk konfirmasi manual.
 */
final class PaymentService
{
    public function __construct(
        private readonly NotifikasiServiceInterface $notifikasiService,
    ) {}

    // ── Pilih metode dan buat record payment ──────────────────────────────

    /**
     * Menyimpan pilihan metode pembayaran dan mengubah status pemesanan.
     * Mengirim notifikasi ke user dan admin.
     */
    public function pilihMetode(Pemesanan $pemesanan, string $metode): Payment
    {
        $payment = Payment::updateOrCreate(
            ['pemesanan_id' => $pemesanan->id],
            [
                'amount' => $pemesanan->total_harga,
                'metode' => $metode,
                'status' => StatusPayment::MenungguKonfirmasi->value,
                'wa_sent_at' => now(),
            ],
        );

        $pemesanan->update(['status' => StatusPemesanan::MenungguKonfirmasiAdmin->value]);

        $this->notifikasiService->kirimKePengguna(
            userId : $pemesanan->user_id,
            judul  : 'Menunggu Konfirmasi',
            pesan  : "Pemesanan #{$pemesanan->id} sedang menunggu konfirmasi admin setelah Anda mengirim pesan WhatsApp.",
            tipe   : 'info',
            link   : route('pemesanan.show', $pemesanan),
        );

        User::where('role', 'admin')->each(function (User $admin) use ($pemesanan) {
            $this->notifikasiService->kirimKePengguna(
                userId : $admin->id,
                judul  : 'Pesanan Baru via WhatsApp',
                pesan  : "Pemesanan #{$pemesanan->id} dari {$pemesanan->user->name}. Cek WhatsApp.",
                tipe   : 'info',
                link   : route('admin.pemesanan.show', $pemesanan),
            );
        });

        KirimEmailPemesanan::dispatch(
            $pemesanan->fresh(['user', 'mobil', 'payment']),
            'menunggu_konfirmasi',
        );

        return $payment;
    }

    // ── Konfirmasi pembayaran oleh admin ──────────────────────────────────

    /**
     * @throws PaymentException bila payment tidak ditemukan atau sudah dikonfirmasi
     */
    public function konfirmasiPembayaran(Pemesanan $pemesanan): void
    {
        $payment = $pemesanan->payment;

        if (! $payment) {
            throw PaymentException::tidakDitemukan();
        }

        if (StatusPayment::from($payment->status)->sudahDibayar()) {
            throw PaymentException::sudahDikonfirmasi();
        }

        $payment->update([
            'status' => StatusPayment::Dikonfirmasi->value,
            'paid_at' => now(),
        ]);

        if ($pemesanan->status === StatusPemesanan::MenungguKonfirmasiAdmin->value) {
            $pemesanan->update(['status' => StatusPemesanan::Dikonfirmasi->value]);
            $pemesanan->mobil->update(['status' => 'disewa']);

            $this->notifikasiService->kirimKePengguna(
                userId : $pemesanan->user_id,
                judul  : 'Pemesanan Dikonfirmasi',
                pesan  : "Pemesanan #{$pemesanan->id} untuk {$pemesanan->mobil->nama} telah dikonfirmasi.",
                tipe   : 'success',
                link   : route('pemesanan.show', $pemesanan),
            );

            KirimEmailPemesanan::dispatch(
                $pemesanan->fresh(['user', 'mobil', 'payment']),
                'dikonfirmasi',
            );
        }
    }

    // ── Bangun URL WhatsApp ───────────────────────────────────────────────

    public function bangunUrlWhatsApp(Pemesanan $pemesanan, string $metode): string
    {
        $template = config("payment.wa_template.{$metode}", '');
        $config = config("payment.metode.{$metode}", []);

        $labelDurasi = $pemesanan->adalah12Jam()
            ? 'Sewa 12 Jam'
            : 'Sewa '.$pemesanan->durasi().' Hari';

        $waktuInfo = ($pemesanan->adalah12Jam() && $pemesanan->waktu_mulai)
            ? ' pukul '.substr($pemesanan->waktu_mulai, 0, 5)
            : '';

        $pesan = strtr($template, [
            '{id}' => $pemesanan->id,
            '{nama}' => $pemesanan->user->name,
            '{mobil}' => $pemesanan->mobil->nama,
            '{durasi}' => $labelDurasi,
            '{tanggal_mulai}' => $pemesanan->tanggal_mulai->format('d M Y'),
            '{tanggal_selesai}' => $pemesanan->tanggal_selesai->format('d M Y'),
            '{waktu_info}' => $waktuInfo,
            '{total}' => number_format((float) $pemesanan->total_harga, 0, ',', '.'),
            '{bank}' => $config['bank'] ?? '',
            '{rekening}' => $config['rekening'] ?? '',
            '{atas_nama}' => $config['atas_nama'] ?? '',
        ]);

        return 'https://wa.me/'.config('payment.wa_number')
            .'?text='.rawurlencode($pesan);
    }
}