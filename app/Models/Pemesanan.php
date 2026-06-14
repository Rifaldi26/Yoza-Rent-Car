<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    protected $fillable = [
        'user_id',
        'mobil_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'opsi_supir',
        'biaya_supir',
        'total_harga',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'opsi_supir' => 'boolean',
        'biaya_supir' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    // ── Helpers ───────────────────────────────────────────
    public function durasi(): int
    {
        return $this->tanggal_mulai->diffInDays($this->tanggal_selesai);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isDikonfirmasi(): bool
    {
        return $this->status === 'dikonfirmasi';
    }

    public function isSelesai(): bool
    {
        return $this->status === 'selesai';
    }

    public function isBisaDibatalkan(): bool
    {
        return $this->status === 'pending';
    }

    public function labelStatus(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'menunggu_konfirmasi_admin' => 'Menunggu Konfirmasi',
            'dikonfirmasi' => 'Dikonfirmasi',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            'kadaluarsa' => 'Kadaluarsa',
            default => $this->status,
        };
    }

    public function warnaBadgeStatus(): string
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'menunggu_konfirmasi_admin' => 'bg-blue-100 text-blue-800',
            'dikonfirmasi' => 'bg-green-100 text-green-800',
            'selesai' => 'bg-gray-100 text-gray-800',
            'dibatalkan' => 'bg-red-100 text-red-800',
            'kadaluarsa' => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    // ── Relasi ────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mobil()
    {
        return $this->belongsTo(Mobil::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function pesans()
    {
        return $this->hasMany(Pesan::class);
    }

    // ── Scope ─────────────────────────────────────────────
    public function scopeAktif($query)
    {
        return $query->whereIn('status', ['pending', 'menunggu_konfirmasi_admin', 'dikonfirmasi']);
    }

    public function scopeBulan($query, int $bulan, int $tahun)
    {
        return $query->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun);
    }

    public function adalah12Jam(): bool
    {
        return $this->durasi() === 0 || $this->durasi() < 1;
    }

    // Cek apakah ada konflik tanggal untuk mobil tertentu
    public static function adaKonflik(int $mobilId, string $mulai, string $selesai, ?int $excludeId = null): bool
    {
        return static::where('mobil_id', $mobilId)
            ->whereIn('status', ['pending', 'menunggu_konfirmasi_admin', 'dikonfirmasi'])
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->where(function ($q) use ($mulai, $selesai) {
                $q->whereBetween('tanggal_mulai', [$mulai, $selesai])
                    ->orWhereBetween('tanggal_selesai', [$mulai, $selesai])
                    ->orWhere(function ($q2) use ($mulai, $selesai) {
                        $q2->where('tanggal_mulai', '<=', $mulai)
                            ->where('tanggal_selesai', '>=', $selesai);
                    });
            })
            ->exists();
    }
}
