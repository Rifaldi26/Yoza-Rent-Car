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
            self::Pending => __('Menunggu Pembayaran'),
            self::MenungguKonfirmasi => __('Menunggu Konfirmasi Admin'),
            self::Dikonfirmasi => __('Dikonfirmasi'),
            self::Dibatalkan => __('Dibatalkan'),
        };
    }

    public function sudahDibayar(): bool
    {
        return $this === self::Dikonfirmasi;
    }
}