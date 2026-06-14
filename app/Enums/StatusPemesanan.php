<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusPemesanan: string
{
    case Pending                  = 'pending';
    case MenungguKonfirmasiAdmin  = 'menunggu_konfirmasi_admin';
    case Dikonfirmasi             = 'dikonfirmasi';
    case Selesai                  = 'selesai';
    case Dibatalkan               = 'dibatalkan';
    case Kadaluarsa               = 'kadaluarsa';

    // ── Label tampilan ────────────────────────────────────────────────────

    public function label(): string
    {
        return match($this) {
            self::Pending                 => 'Menunggu Pembayaran',
            self::MenungguKonfirmasiAdmin => 'Menunggu Konfirmasi',
            self::Dikonfirmasi            => 'Dikonfirmasi',
            self::Selesai                 => 'Selesai',
            self::Dibatalkan              => 'Dibatalkan',
            self::Kadaluarsa              => 'Kadaluarsa',
        };
    }

    // ── Kelas Tailwind untuk badge ────────────────────────────────────────

    public function warnaBadge(): string
    {
        return match($this) {
            self::Pending                 => 'bg-yellow-100 text-yellow-800',
            self::MenungguKonfirmasiAdmin => 'bg-blue-100 text-blue-800',
            self::Dikonfirmasi            => 'bg-green-100 text-green-800',
            self::Selesai                 => 'bg-gray-100 text-gray-800',
            self::Dibatalkan              => 'bg-red-100 text-red-800',
            self::Kadaluarsa              => 'bg-orange-100 text-orange-800',
        };
    }

    // ── State helpers ─────────────────────────────────────────────────────

    /** Status yang berarti mobil sedang aktif digunakan / diproses. */
    public static function aktif(): array
    {
        return [
            self::Pending->value,
            self::MenungguKonfirmasiAdmin->value,
            self::Dikonfirmasi->value,
        ];
    }

    public function bisaDibatalkan(): bool
    {
        return $this === self::Pending;
    }

    public function bisaDikonfirmasiAdmin(): bool
    {
        return $this === self::MenungguKonfirmasiAdmin;
    }

    public function bisaSelesai(): bool
    {
        return $this === self::Dikonfirmasi;
    }
}
