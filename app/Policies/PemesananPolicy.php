<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Pemesanan;
use App\Models\User;

/**
 * PemesananPolicy
 *
 * Mendefinisikan siapa yang boleh melakukan aksi apa
 * terhadap resource Pemesanan. Diregistrasi otomatis
 * oleh Laravel melalui model discovery.
 */
final class PemesananPolicy
{
    /**
     * Admin bisa melihat semua pemesanan.
     * User hanya bisa melihat miliknya sendiri.
     */
    public function view(User $user, Pemesanan $pemesanan): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $pemesanan->user_id === $user->id;
    }

    /**
     * User yang sudah terverifikasi boleh membuat pemesanan.
     */
    public function create(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    /**
     * Hanya pemilik pemesanan yang boleh membatalkan.
     */
    public function batalkan(User $user, Pemesanan $pemesanan): bool
    {
        return $pemesanan->user_id === $user->id;
    }

    /**
     * Hanya pemilik yang bisa melanjutkan ke halaman bayar.
     */
    public function bayar(User $user, Pemesanan $pemesanan): bool
    {
        return $pemesanan->user_id === $user->id;
    }

    /**
     * Hanya pemilik yang bisa mengunduh invoice-nya.
     */
    public function unduhInvoice(User $user, Pemesanan $pemesanan): bool
    {
        return $pemesanan->user_id === $user->id;
    }

    /**
     * User boleh memberi ulasan jika:
     * - Pemesanan tersebut miliknya.
     * - Status pemesanan sudah 'selesai'.
     * - Belum pernah memberi ulasan untuk pemesanan ini.
     */
    public function ulasan(User $user, Pemesanan $pemesanan): bool
    {
        return $pemesanan->user_id === $user->id
            && $pemesanan->isSelesai()
            && ! $pemesanan->ulasan()->exists();
    }

    /**
     * Hanya admin yang boleh mengkonfirmasi.
     */
    public function konfirmasi(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Hanya admin yang boleh menolak.
     */
    public function tolak(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Hanya admin yang boleh menandai selesai.
     */
    public function selesai(User $user): bool
    {
        return $user->isAdmin();
    }
}