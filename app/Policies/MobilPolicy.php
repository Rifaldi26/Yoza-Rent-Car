<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Mobil;
use App\Models\User;

/**
 * MobilPolicy
 *
 * CRUD manajemen armada hanya untuk admin.
 * Halaman katalog (read-only) bersifat publik — tidak memerlukan policy.
 */
final class MobilPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Mobil $mobil): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Mobil $mobil): bool
    {
        return $user->isAdmin();
    }

    public function toggleStatus(User $user, Mobil $mobil): bool
    {
        return $user->isAdmin();
    }
}
