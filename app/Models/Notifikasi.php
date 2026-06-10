<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'tipe',
        'link',
        'dibaca',
    ];

    protected $casts = [
        'dibaca' => 'boolean',
    ];

    // ── Relasi ────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Static helper ─────────────────────────────────────
    public static function kirim(int $userId, string $judul, string $pesan, string $tipe = 'info', ?string $link = null): self
    {
        return static::create([
            'user_id' => $userId,
            'judul'   => $judul,
            'pesan'   => $pesan,
            'tipe'    => $tipe,
            'link'    => $link,
            'dibaca'  => false,
        ]);
    }
}