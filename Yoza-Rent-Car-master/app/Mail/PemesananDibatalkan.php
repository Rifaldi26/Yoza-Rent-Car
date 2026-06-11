<?php

namespace App\Mail;

use App\Models\Pemesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PemesananDibatalkan extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Pemesanan $pemesanan) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Pemesanan #{$this->pemesanan->id} Dibatalkan — Yoza Rent Car",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.pemesanan-dibatalkan');
    }
}