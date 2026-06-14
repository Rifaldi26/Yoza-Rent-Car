<?php

namespace App\Mail;

use App\Models\Pemesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PemesananSelesai extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Pemesanan $pemesanan) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Terima Kasih! Pemesanan #{$this->pemesanan->id} Selesai — Yoza Rent Car",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.pemesanan-selesai');
    }
}
