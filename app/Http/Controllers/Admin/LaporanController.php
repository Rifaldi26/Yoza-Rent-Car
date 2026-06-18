<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mobil;
use App\Models\Payment;
use App\Models\Pemesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);

        $ringkasan = [
            'total_pendapatan' => Payment::where('status', 'dikonfirmasi')
                ->whereYear('paid_at', $tahun)->sum('amount'),
            'total_selesai' => Pemesanan::where('status', 'selesai')
                ->whereYear('updated_at', $tahun)->count(),
            'total_pending' => Pemesanan::where('status', 'pending')->count(),
            'total_dibatalkan' => Pemesanan::where('status', 'dibatalkan')
                ->whereYear('updated_at', $tahun)->count(),
        ];

        $topMobil = Mobil::withCount([
            'pemesanans as total_sewa' => fn ($q) => $q->where('status', 'selesai'),
        ])
            ->withSum([
                'pemesanans as total_pendapatan' => fn ($q) => $q->where('status', 'selesai'),
            ], 'total_harga')
            ->orderByDesc('total_sewa')
            ->take(5)
            ->get();

        return view('admin.laporan.index', compact('ringkasan', 'topMobil', 'tahun'));
    }

    public function chartData(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);

        // Pendapatan per bulan
        $pendapatan = collect(range(1, 12))->map(fn ($bulan) => Payment::where('status', 'dikonfirmasi')
            ->whereYear('paid_at', $tahun)
            ->whereMonth('paid_at', $bulan)
            ->sum('amount')
        );

        // Distribusi status pemesanan
        $statusCounts = Pemesanan::selectRaw('status, COUNT(*) as total')
            ->whereYear('created_at', $tahun)
            ->groupBy('status')
            ->pluck('total', 'status');

        return response()->json([
            'pendapatan_per_bulan' => $pendapatan,
            'status_distribusi' => $statusCounts,
        ]);
    }

    public function exportPdf(Request $request)
    {
        $tahun = $request->get('tahun', now()->year);
        $status = $request->get('status');

        $pemesanans = Pemesanan::with(['user', 'mobil', 'payment'])
            ->whereYear('created_at', $tahun)
            ->when($status, fn ($q) => $q->where('status', $status))
            ->get();

        $ringkasan = [
            'total_pendapatan' => Payment::where('status', 'dikonfirmasi')
                ->whereYear('paid_at', $tahun)->sum('amount'),
            'total_selesai' => Pemesanan::where('status', 'selesai')
                ->whereYear('updated_at', $tahun)->count(),
            'total_pending' => Pemesanan::where('status', 'pending')->count(),
            'total_dibatalkan' => Pemesanan::where('status', 'dibatalkan')
                ->whereYear('updated_at', $tahun)->count(),
        ];

        $pendapatan = collect(range(1, 12))->map(fn ($bulan) =>
            Payment::where('status', 'dikonfirmasi')
                ->whereYear('paid_at', $tahun)
                ->whereMonth('paid_at', $bulan)
                ->sum('amount')
        );

        $statuses = ['selesai', 'dikonfirmasi', 'menunggu_konfirmasi_admin', 'dibatalkan', 'kadaluarsa', 'pending'];
        $statusLabels = ['Selesai', 'Dikonfirmasi', 'Menunggu Konfirmasi', 'Dibatalkan', 'Kadaluarsa', 'Pending'];
        $statusCounts = collect($statuses)->map(fn ($s) =>
            Pemesanan::whereYear('created_at', $tahun)
                ->where('status', $s)
                ->count()
        );

        $pdf = Pdf::loadView('admin.laporan.pdf', compact(
            'tahun', 'pemesanans', 'ringkasan', 'pendapatan', 'statusLabels', 'statusCounts'
        ));

        return $pdf->download("laporan-yoza-rent-car-{$tahun}.pdf");
    }
}