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
        'password'          => 'hashed',
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
}