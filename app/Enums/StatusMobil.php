<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusMobil: string
{
    case Tersedia = 'tersedia';
    case Disewa = 'disewa';
    case Perawatan = 'perawatan';

    public function label(): string
    {
        return match ($this) {
            self::Tersedia => 'Tersedia',
            self::Disewa => 'Sedang Disewa',
            self::Perawatan => 'Dalam Perawatan',
        };
    }

    public function warnaBadge(): string
    {
        return match ($this) {
            self::Tersedia => 'bg-green-100 text-green-800',
            self::Disewa => 'bg-blue-100 text-blue-800',
            self::Perawatan => 'bg-yellow-100 text-yellow-800',
        };
    }
}
