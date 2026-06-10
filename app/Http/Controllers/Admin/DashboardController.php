<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Models\Payment;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_mobil'        => Mobil::count(),
            'mobil_tersedia'     => Mobil::where('status', 'tersedia')->count(),
            'mobil_disewa'       => Mobil::where('status', 'disewa')->count(),
            'mobil_perawatan'    => Mobil::where('status', 'perawatan')->count(),
            'total_pemesanan'    => Pemesanan::count(),
            'pending'            => Pemesanan::where('status', 'pending')->count(),
            'menunggu_konfirmasi'=> Pemesanan::where('status', 'menunggu_konfirmasi_admin')->count(),
            'total_user'         => User::where('role', 'user')->count(),
            'pendapatan_bulan'   => Payment::where('status', 'dikonfirmasi')
                                        ->whereMonth('paid_at', now()->month)
                                        ->whereYear('paid_at', now()->year)
                                        ->sum('amount'),
        ];

        $pemesanan_terbaru = Pemesanan::with(['user', 'mobil'])
            ->whereIn('status', ['menunggu_konfirmasi_admin', 'pending'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'pemesanan_terbaru'));
    }
}