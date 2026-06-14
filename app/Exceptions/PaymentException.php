<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

/**
 * PaymentException
 *
 * Dilempar saat terjadi pelanggaran aturan bisnis pembayaran.
 */
final class PaymentException extends Exception
{
    public static function tidakDitemukan(): self
    {
        return new self('Tidak ada data pembayaran untuk pemesanan ini.');
    }

    public static function sudahDikonfirmasi(): self
    {
        return new self('Pembayaran ini sudah dikonfirmasi sebelumnya.');
    }

    public static function metodeTidakValid(string $metode): self
    {
        return new self("Metode pembayaran '{$metode}' tidak didukung.");
    }
}
