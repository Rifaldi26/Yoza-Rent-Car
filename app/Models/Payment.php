<?php

namespace App\Models;

use App\Enums\StatusPayment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;

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

    // ── Delegasi ke Enum (hilangkan duplikasi) ────────────

    public function statusEnum(): StatusPayment
    {
        return StatusPayment::from($this->status);
    }

    /** @deprecated Gunakan $payment->statusEnum()->label() */
    public function labelStatus(): string
    {
        return $this->statusEnum()->label();
    }

    // ── Relasi ─────────────────────────────────────────────
    public function pemesanan(): BelongsTo
    {
        return $this->belongsTo(Pemesanan::class);
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }
}