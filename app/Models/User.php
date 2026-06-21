<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'no_hp',
        'password',
        'google_id',
        'role',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ── Helpers ───────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // ── Relasi ────────────────────────────────────────────
    public function pemesanans()
    {
        return $this->hasMany(Pemesanan::class);
    }

    public function notifikasis()
    {
        return $this->hasMany(Notifikasi::class);
    }

    public function favorits()
    {
        return $this->hasMany(Favorit::class);
    }

    public function mobilFavorit()
    {
        return $this->belongsToMany(Mobil::class, 'favorits');
    }

    // Pesan yang dikirim user ini
    public function pesanTerkirim()
    {
        return $this->hasMany(Pesan::class, 'pengirim_id');
    }

    // Pesan yang diterima user ini
    public function pesanDiterima()
    {
        return $this->hasMany(Pesan::class, 'penerima_id');
    }

    // Jumlah notifikasi belum dibaca
    public function unreadNotifikasi(): int
    {
        return $this->notifikasis()->where('dibaca', false)->count();
    }

    // Jumlah pesan belum dibaca
    public function unreadPesan(): int
    {
        return $this->pesanDiterima()->where('dibaca', false)->count();
        }
        
    // Generate Kode Pelanggan
    public function getKodePelangganAttribute(): string
    {
        return sprintf(
            '%s-%03d',
            self::buatInisial($this->name),
            $this->id
            );
    }
            
    // Generate Inisial Pelanggan
    private static function buatInisial(string $nama): string
    {
        $kata = array_values(array_filter(explode(' ', trim($nama))));

        if (count($kata) >= 2) {
            return strtoupper(
                substr($kata[0], 0, 1) .
                substr($kata[1], 0, 1)
            );
        }

        return strtoupper(substr($kata[0] ?? 'XX', 0, 2));
    }
}
