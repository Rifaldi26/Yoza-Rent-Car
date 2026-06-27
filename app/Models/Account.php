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

    /**
     * Apakah saldo normal akun ini berada di sisi Debit?
     *
     * Aturan akuntansi standar:
     * - Aset & Pengeluaran  → saldo normal di sisi Debit (debit menambah saldo)
     * - Liabilitas, Modal, & Pendapatan → saldo normal di sisi Kredit (kredit menambah saldo)
     *
     * Dipakai untuk menentukan arah balance (increment/decrement) saat
     * mencatat transaksi manual lewat inputTransaksi(), supaya konsisten
     * untuk SEMUA tipe akun, bukan hanya akun pengeluaran.
     */
    public function saldoNormalDebit(): bool
    {
        return in_array($this->tipe, ['aset', 'pengeluaran'], true);
    }

    // Accessor: Nama terjemahan (auto-translate nama account)
    public function getNamaTranslatedAttribute(): string
    {
        return __($this->nama);
    }
}