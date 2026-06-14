<?php

namespace App\Mail;

use App\Models\Pemesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email pengingat sewa yang dikirim H-N sebelum tanggal mulai.
 *
 * Subject email menyesuaikan nilai hariSebelum sehingga template ini
 * dapat digunakan untuk semua interval pengingat (H-1, H-3, dst.)
 * tanpa perlu membuat Mailable terpisah.
 */
class PengingatSewa extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Pemesanan $pemesanan,
        public readonly int $hariSebelum = 1,
    ) {}

    public function envelope(): Envelope
    {
        $label = $this->hariSebelum === 1
            ? 'Besok'
            : "{$this->hariSebelum} Hari Lagi";

        return new Envelope(
            subject: "Pengingat: Sewa Mobil Anda Dimulai {$label} — Yoza Rent Car",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.pengingat-sewa');
    }
}
