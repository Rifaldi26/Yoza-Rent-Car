<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

/**
 * PemesananException
 *
 * Dilempar saat terjadi pelanggaran aturan bisnis pemesanan,
 * misalnya transisi status yang tidak valid atau konflik tanggal.
 *
 * Penggunaan di Controller:
 *
 *   try {
 *       $this->pemesananService->konfirmasi($pemesanan);
 *   } catch (PemesananException $e) {
 *       return back()->with('error', $e->getMessage());
 *   }
 */
final class PemesananException extends Exception
{
    public static function statusTidakValid(string $aksi, string $statusSaatIni): self
    {
        return new self(
            "Tidak dapat melakukan aksi '{$aksi}' pada pemesanan berstatus '{$statusSaatIni}'."
        );
    }

    public static function konflikTanggal(): self
    {
        return new self('Mobil sudah dipesan pada rentang tanggal tersebut.');
    }

    public static function mobilTidakTersedia(): self
    {
        return new self('Mobil ini sedang tidak tersedia untuk dipesan.');
    }
}
