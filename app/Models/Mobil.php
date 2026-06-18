<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mobil extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'merek',
        'tahun',
        'plat_nomor',
        'harga_per_hari',
        'biaya_supir_per_hari',
        'status',
        'foto',
        'deskripsi',
    ];

    protected $casts = [
        'harga_per_hari' => 'decimal:2',
        'biaya_supir_per_hari' => 'decimal:2',
    ];

    // ── Helpers ───────────────────────────────────────────
    public function tersedia(): bool
    {
        return $this->status === 'tersedia';
    }

    public function adaSupir(): bool
    {
        return ! is_null($this->biaya_supir_per_hari);
    }

    public function fotoUrl(): string
    {
        return $this->foto
            ? asset('storage/'.$this->foto)
            : asset('images/mobil-default.png');
    }

    // ── Relasi ────────────────────────────────────────────
    public function pemesanans()
    {
        return $this->hasMany(Pemesanan::class);
    }

    public function favorits()
    {
        return $this->hasMany(Favorit::class);
    }

    public function difavoritOleh(int $userId): bool
    {
        return $this->favorits()->where('user_id', $userId)->exists();
    }

    // ── Scope ─────────────────────────────────────────────
    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia');
    }

    public function scopeDisewa($query)
    {
        return $query->where('status', 'disewa');
    }

    public function scopePerawatan($query)
    {
        return $query->where('status', 'perawatan');
    }

    public function ulasans()
    {
        return $this->hasMany(Ulasan::class);
    }
}