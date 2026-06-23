<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'tipe',
        'balance',
        'is_system',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'is_system' => 'boolean',
    ];

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class);
    }

    // Helper: total debit dari jurnal
    public function totalDebit(): float
    {
        return $this->journalEntries()->sum('debit');
    }

    // Helper: total kredit dari jurnal
    public function totalCredit(): float
    {
        return $this->journalEntries()->sum('credit');
    }
}