<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'pemesanan_id',
        'payment_id',
        'debit',
        'credit',
        'description',
        'date',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'date' => 'date',
    ];

    // ── Relasi ────────────────────────────────────────────
    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}