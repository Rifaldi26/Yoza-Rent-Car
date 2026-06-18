<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ulasan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mobil_id',
        'pemesanan_id',
        'rating',
        'komentar',
        'disetujui',
    ];

    protected $casts = [
        'disetujui' => 'boolean',
        'rating'    => 'integer',
    ];

    // ── Relasi ────────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mobil(): BelongsTo
    {
        return $this->belongsTo(Mobil::class);
    }

    public function pemesanan(): BelongsTo
    {
        return $this->belongsTo(Pemesanan::class);
    }

    // ── Scope ─────────────────────────────────────────────────────────────────

    public function scopeDisetujui($query)
    {
        return $query->where('disetujui', true);
    }

    public function scopeMenunggu($query)
    {
        return $query->where('disetujui', false);
    }
}