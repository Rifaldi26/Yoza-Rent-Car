<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Notifikasi;

/**
 * NotifikasiService
 *
 * Wrapper injectable untuk pengiriman notifikasi in-app.
 * Menggantikan panggilan Notifikasi::kirim() statis agar
 * dapat di-mock dengan mudah dalam pengujian.
 */
final class NotifikasiService
{
    /**
     * Kirim notifikasi ke satu pengguna.
     */
    public function kirimKePengguna(
        int $userId,
        string $judul,
        string $pesan,
        string $tipe = 'info',
        ?string $link = null,
    ): Notifikasi {
        return Notifikasi::create([
            'user_id' => $userId,
            'judul' => $judul,
            'pesan' => $pesan,
            'tipe' => $tipe,
            'link' => $link,
            'dibaca' => false,
        ]);
    }

    /**
     * Kirim notifikasi yang sama ke banyak pengguna sekaligus.
     */
    public function kirimKeBanyak(
        array $userIds,
        string $judul,
        string $pesan,
        string $tipe = 'info',
        ?string $link = null,
    ): void {
        foreach ($userIds as $userId) {
            $this->kirimKePengguna($userId, $judul, $pesan, $tipe, $link);
        }
    }
}
