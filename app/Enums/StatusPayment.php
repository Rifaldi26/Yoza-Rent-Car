<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusPayment: string
{
    case Pending = 'pending';
    case MenungguKonfirmasi = 'menunggu_konfirmasi';
    case Dikonfirmasi = 'dikonfirmasi';
    case Dibatalkan = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Pembayaran',
            self::MenungguKonfirmasi => 'Menunggu Konfirmasi Admin',
            self::Dikonfirmasi => 'Dikonfirmasi',
            self::Dibatalkan => 'Dibatalkan',
        };
    }

    public function sudahDibayar(): bool
    {
        return $this === self::Dikonfirmasi;
    }
}
