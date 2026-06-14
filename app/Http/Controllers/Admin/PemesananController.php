<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Services\PaymentService;
use App\Services\PemesananService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * PemesananController (Admin)
 *
 * Manajemen pemesanan dari sisi admin: lihat daftar,
 * konfirmasi, tolak, tandai selesai, unduh invoice.
 *
 * Semua logika bisnis ada di PemesananService & PaymentService.
 */
final class PemesananController extends Controller
{
    public function __construct(
        private readonly PemesananService $pemesananService,
        private readonly PaymentService $paymentService,
    ) {}

    public function index(Request $request): View
    {
        $pemesanans = Pemesanan::with(['user', 'mobil', 'payment'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->bulan, fn ($q) => $q->whereMonth('created_at', $request->bulan))
            ->when($request->tahun, fn ($q) => $q->whereYear('created_at', $request->tahun))
            ->when($request->search, function ($q) use ($request) {
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$request->search}%"))
                    ->orWhereHas('mobil', fn ($m) => $m->where('nama', 'like', "%{$request->search}%"));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pemesanan.index', compact('pemesanans'));
    }

    public function show(Pemesanan $pemesanan): View
    {
        $pemesanan->load(['user', 'mobil', 'payment', 'journalEntries.account']);

        return view('admin.pemesanan.show', compact('pemesanan'));
    }

    public function konfirmasi(Pemesanan $pemesanan): RedirectResponse
    {
        try {
            $this->pemesananService->konfirmasi($pemesanan);

            return back()->with('success', 'Pemesanan berhasil dikonfirmasi.');
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function tolak(Pemesanan $pemesanan): RedirectResponse
    {
        try {
            $this->pemesananService->tolak($pemesanan);

            return back()->with('success', 'Pemesanan berhasil ditolak.');
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function selesai(Pemesanan $pemesanan): RedirectResponse
    {
        try {
            $this->pemesananService->selesai($pemesanan);

            return back()->with('success', 'Pemesanan ditandai selesai.');
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function konfirmasiPembayaran(Pemesanan $pemesanan): RedirectResponse
    {
        try {
            $this->paymentService->konfirmasiPembayaran($pemesanan);

            return back()->with('success', 'Pembayaran berhasil dikonfirmasi.');
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function invoice(Pemesanan $pemesanan): mixed
    {
        $pemesanan->load(['user', 'mobil', 'payment']);

        $pdf = Pdf::loadView('pdf.invoice', compact('pemesanan'))->setPaper('a4');

        return $pdf->download("invoice-{$pemesanan->id}.pdf");
    }
}
