<?php

namespace App\Models;

use App\Enums\StatusPemesanan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pemesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mobil_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'waktu_mulai',    // jam mulai sewa (HH:MM) — diisi untuk semua tipe sewa
        'waktu_selesai',  // jam selesai sewa (HH:MM) — diisi untuk semua tipe sewa
        'tipe_sewa',      // ← BARU: 'harian' | '12_jam'
        'opsi_supir',
        'biaya_supir',
        'total_harga',
        'status',
        'catatan',
        'no_hp',
        'alamat',
        'tujuan_sewa',
        'kota_tujuan',
        'instagram',
        'tiktok',
        'status_pekerjaan',
        'tempat_kerja',
        'kampus',
        'sumber_info',
        'kontak_darurat',
        'share_lokasi',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date:Y-m-d',
        'tanggal_selesai' => 'date:Y-m-d',
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

    /**
     * True bila user mengisi statusnya sebagai "sudah bekerja" saat
     * memesan (lihat juga isMahasiswa() — keduanya saling eksklusif).
     */
    public function isBekerja(): bool
    {
        return $this->status_pekerjaan === 'bekerja';
    }

    /** True bila user mengisi statusnya sebagai "mahasiswa" saat memesan. */
    public function isMahasiswa(): bool
    {
        return $this->status_pekerjaan === 'mahasiswa';
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mobil(): BelongsTo
    {
        return $this->belongsTo(Mobil::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function pesans(): HasMany
    {
        return $this->hasMany(Pesan::class);
    }

    // ── Scopes ────────────────────────────────────────────

    public function scopeAktif(Builder $query): Builder
    {
        return $query->whereIn('status', StatusPemesanan::aktif());
    }

    public function scopeBulan(Builder $query, int $bulan, int $tahun): Builder
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

    /**
     * Cek apakah user tertentu sudah memiliki pemesanan AKTIF lain yang
     * tumpang tindih dengan rentang tanggal yang diberikan — tanpa
     * memandang mobil mana yang dipesan.
     *
     * Mencegah satu user memesan lebih dari satu mobil pada periode
     * yang sama (mis. user A sudah punya pemesanan aktif 10–15 Juli,
     * maka user A tidak bisa membuat pemesanan baru—mobil apapun—yang
     * tumpang tindih dengan rentang tersebut).
     */
    public static function adaKonflikUser(int $userId, string $mulai, string $selesai, ?int $excludeId = null): bool
    {
        return static::where('user_id', $userId)
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

    public function ulasan(): HasOne
    {
        return $this->hasOne(Ulasan::class);
    }
}