<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'pemesanan_id',
        'amount',
        'metode',
        'status',
        'paid_at',
        'wa_sent_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'wa_sent_at' => 'datetime',
    ];

    // ── Helpers ────────────────────────────────────────────
    public function isDikonfirmasi(): bool
    {
        return $this->status === 'dikonfirmasi';
    }

    public function isPaid(): bool
    {
        return $this->isDikonfirmasi();
    }

    public function labelMetode(): string
    {
        return config("payment.metode.{$this->metode}.label", '-');
    }

    public function labelStatus(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'menunggu_konfirmasi' => 'Menunggu Konfirmasi Admin',
            'dikonfirmasi' => 'Dikonfirmasi',
            'dibatalkan' => 'Dibatalkan',
            default => $this->status,
        };
    }

    // ── Relasi ─────────────────────────────────────────────
    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class);
    }

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class);
    }
}
