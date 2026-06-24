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
     * Tampilkan daftar semua ulasan.
     * Ulasan sudah langsung public, tidak perlu moderasi.
     */
    public function index(Request $request): View
    {
        $ulasans = Ulasan::with(['user', 'mobil', 'pemesanan'])
            ->latest()
            ->paginate(15);

        return view('admin.ulasan.index', compact('ulasans'));
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