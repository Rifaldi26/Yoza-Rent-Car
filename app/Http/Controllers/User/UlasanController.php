<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use App\Models\Mobil;
use App\Models\Ulasan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UlasanController extends Controller
{
    /**
     * Simpan ulasan baru dari user.
     *
     * Syarat:
     * - User sudah login.
     * - User memiliki pemesanan dengan status 'selesai' untuk mobil ini.
     * - Pemesanan tersebut belum memiliki ulasan.
     */
    public function store(Request $request, Mobil $mobil): RedirectResponse
    {
        $request->validate([
            'pemesanan_id' => ['required', 'integer'],
            'rating'       => ['required', 'integer', 'min:1', 'max:5'],
            'komentar'     => ['nullable', 'string', 'max:1000'],
        ]);

        // Pastikan pemesanan milik user yang login, terkait mobil ini, dan sudah selesai
        $pemesanan = auth()->user()
            ->pemesanans()
            ->where('id', $request->pemesanan_id)
            ->where('mobil_id', $mobil->id)
            ->where('status', 'selesai')
            ->firstOrFail();

        // Pastikan belum ada ulasan untuk pemesanan ini
        if ($pemesanan->ulasan()->exists()) {
            return back()->with('error', __('Anda sudah memberikan ulasan untuk pesanan ini.'));
        }

        Ulasan::create([
            'user_id'      => auth()->id(),
            'mobil_id'     => $mobil->id,
            'pemesanan_id' => $pemesanan->id,
            'rating'       => $request->rating,
            'komentar'     => $request->komentar,
            'disetujui'    => false, // menunggu moderasi admin
        ]);

        return back()->with('success', __('Ulasan Anda telah dikirim dan menunggu persetujuan.'));
    }
}
