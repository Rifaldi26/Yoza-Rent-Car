<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUlasanRequest;
use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Models\Ulasan;
use Illuminate\Http\RedirectResponse;

/**
 * UlasanController (User)
 *
 * Menangani pembuatan ulasan oleh pelanggan untuk mobil yang
 * telah selesai disewa. Validasi input ditangani StoreUlasanRequest,
 * otorisasi kepemilikan & status ditangani PemesananPolicy::ulasan().
 */
final class UlasanController extends Controller
{
    public function store(StoreUlasanRequest $request, Mobil $mobil): RedirectResponse
    {
        $pemesanan = Pemesanan::where('id', $request->validated('pemesanan_id'))
            ->where('mobil_id', $mobil->id)
            ->firstOrFail();

        $this->authorize('ulasan', $pemesanan);

        Ulasan::create([
            'user_id'      => $request->user()->id,
            'mobil_id'     => $mobil->id,
            'pemesanan_id' => $pemesanan->id,
            'rating'       => $request->validated('rating'),
            'komentar'     => $request->validated('komentar'),
            'disetujui'    => true, // menunggu moderasi admin
        ]);

        return back()->with('success', __('Terima kasih! Ulasan Anda telah dipublikasikan.'));
    }
}