<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total_pemesanan' => Pemesanan::where('user_id', $user->id)->count(),
            'aktif' => Pemesanan::where('user_id', $user->id)
                ->whereIn('status', ['dikonfirmasi', 'menunggu_konfirmasi_admin'])
                ->count(),
            'selesai' => Pemesanan::where('user_id', $user->id)
                ->where('status', 'selesai')->count(),
            'pending' => Pemesanan::where('user_id', $user->id)
                ->where('status', 'pending')->count(),
        ];

        $pemesanan_terbaru = Pemesanan::with(['mobil', 'payment'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get();

        return view('user.dashboard', compact('stats', 'pemesanan_terbaru'));
    }
}
