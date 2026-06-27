<?php

namespace App\Models;

use App\Enums\StatusMobil;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    public function isTersedia(): bool
    {
        return $this->status === 'tersedia';
    }

    public function statusEnum(): StatusMobil
    {
        return StatusMobil::from($this->status);
    }

    public function labelStatus(): string
    {
        return $this->statusEnum()->label();
    }

    public function warnaBadgeStatus(): string
    {
        return $this->statusEnum()->warnaBadge();
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
    public function pemesanans(): HasMany
    {
        return $this->hasMany(Pemesanan::class);
    }

    public function favorits(): HasMany
    {
        return $this->hasMany(Favorit::class);
    }

    public function difavoritOleh(int $userId): bool
    {
        return $this->favorits()->where('user_id', $userId)->exists();
    }

    // ── Scope ─────────────────────────────────────────────
    public function scopeTersedia(Builder $query): Builder
    {
        return $query->where('status', 'tersedia');
    }

    public function scopeDisewa(Builder $query): Builder
    {
        return $query->where('status', 'disewa');
    }

    public function scopePerawatan(Builder $query): Builder
    {
        return $query->where('status', 'perawatan');
    }

    public function ulasans(): HasMany
    {
        return $this->hasMany(Ulasan::class);
    }
}