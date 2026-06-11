<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasis = Notifikasi::where('user_id', Auth::id())
            ->latest()->paginate(20);

        return view('admin.notifikasi.index', compact('notifikasis'));
    }

    public function unreadCount()
    {
        return response()->json([
            'count' => Auth::user()->unreadNotifikasi(),
        ]);
    }

    public function baca(Notifikasi $notifikasi)
    {
        abort_if($notifikasi->user_id !== Auth::id(), 403);
        $notifikasi->update(['dibaca' => true]);
        return response()->json(['success' => true]);
    }

    public function hapusSemua()
    {
        Notifikasi::where('user_id', Auth::id())->delete();
        return back()->with('success', 'Semua notifikasi dihapus.');
    }
}