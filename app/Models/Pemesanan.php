<?php

namespace App\Models;

use App\Enums\StatusPemesanan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mobil_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'waktu_mulai',    // ← BARU: waktu HH:MM untuk sewa 12 jam
        'tipe_sewa',      // ← BARU: 'harian' | '12_jam'
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

    /**
     * Jumlah hari sewa. Untuk sewa 12 jam mengembalikan 0,
     * gunakan adalah12Jam() untuk membedakannya.
     */
    public function durasi(): int
    {
        return $this->tanggal_mulai->diffInDays($this->tanggal_selesai);
    }

    /**
     * Kembalikan true bila ini adalah sewa 12 jam (half-day).
     * Prioritaskan kolom tipe_sewa; fallback ke pengecekan durasi
     * untuk kompatibilitas data lama.
     */
    public function adalah12Jam(): bool
    {
        // Jika kolom tipe_sewa sudah diisi, gunakan itu sebagai sumber kebenaran
        if (isset($this->attributes['tipe_sewa'])) {
            return $this->tipe_sewa === '12_jam';
        }

        // Fallback: durasi 0 berarti tanggal mulai == tanggal selesai
        return $this->durasi() === 0;
    }

    // ── Delegasi ke Enum (hilangkan duplikasi) ────────────

    public function statusEnum(): StatusPemesanan
    {
        return StatusPemesanan::from($this->status);
    }

    /** @deprecated Gunakan $pemesanan->statusEnum()->label() */
    public function labelStatus(): string
    {
        return $this->statusEnum()->label();
    }

    /** @deprecated Gunakan $pemesanan->statusEnum()->warnaBadge() */
    public function warnaBadgeStatus(): string
    {
        return $this->statusEnum()->warnaBadge();
    }

    // ── State shortcuts ───────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === StatusPemesanan::Pending->value;
    }

    public function isDikonfirmasi(): bool
    {
        return $this->status === StatusPemesanan::Dikonfirmasi->value;
    }

    public function isSelesai(): bool
    {
        return $this->status === StatusPemesanan::Selesai->value;
    }

    public function isBisaDibatalkan(): bool
    {
        return $this->statusEnum()->bisaDibatalkan();
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

    // ── Scopes ────────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->whereIn('status', StatusPemesanan::aktif());
    }

    public function scopeBulan($query, int $bulan, int $tahun)
    {
        return $query->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun);
    }

    // ── Cek konflik ketersediaan ──────────────────────────

    public static function adaKonflik(int $mobilId, string $mulai, string $selesai, ?int $excludeId = null): bool
    {
        return static::where('mobil_id', $mobilId)
            ->whereIn('status', StatusPemesanan::aktif())
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

    public function ulasan()
    {
        return $this->hasOne(Ulasan::class);
    }
}