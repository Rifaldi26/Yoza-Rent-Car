<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Notifikasi;

interface NotifikasiServiceInterface
{
    public function kirimKePengguna(
        int $userId,
        string $judul,
        string $pesan,
        string $tipe = 'info',
        ?string $link = null,
    ): Notifikasi;

    public function kirimKeBanyak(
        array $userIds,
        string $judul,
        string $pesan,
        string $tipe = 'info',
        ?string $link = null,
    ): void;
}