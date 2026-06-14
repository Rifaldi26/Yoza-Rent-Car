<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasis = Notifikasi::where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('user.notifikasi.index', compact('notifikasis'));
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

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return $notifikasi->link
            ? redirect($notifikasi->link)
            : back();
    }

    public function destroy(Notifikasi $notifikasi)
    {
        abort_if($notifikasi->user_id !== Auth::id(), 403);
        $notifikasi->delete();

        return back()->with('success', 'Notifikasi dihapus.');
    }

    public function hapusSemua()
    {
        Notifikasi::where('user_id', Auth::id())->delete();

        return back()->with('success', 'Semua notifikasi dihapus.');
    }
}
