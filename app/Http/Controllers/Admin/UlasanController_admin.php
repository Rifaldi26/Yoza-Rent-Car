<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ulasan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UlasanController extends Controller
{
    /**
     * Tampilkan daftar ulasan dengan tab "Menunggu" dan "Semua".
     */
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'menunggu');

        $query = Ulasan::with(['user', 'mobil', 'pemesanan'])
            ->latest();

        if ($tab === 'semua') {
            $ulasans = $query->paginate(15)->withQueryString();
        } else {
            $ulasans = $query->menunggu()->paginate(15)->withQueryString();
        }

        $jumlahMenunggu = Ulasan::menunggu()->count();

        return view('admin.ulasan.index', compact('ulasans', 'jumlahMenunggu'));
    }

    /**
     * Setujui ulasan agar tampil ke publik.
     */
    public function setujui(Ulasan $ulasan): RedirectResponse
    {
        $ulasan->update(['disetujui' => true]);

        return back()->with('success', __('Ulasan berhasil disetujui.'));
    }

    /**
     * Hapus ulasan.
     */
    public function destroy(Ulasan $ulasan): RedirectResponse
    {
        $ulasan->delete();

        return back()->with('success', __('Ulasan berhasil dihapus.'));
    }
}
