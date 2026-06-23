<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StorePemesananRequest;
use App\Exceptions\PemesananException;
use App\Models\Mobil;
use App\Models\Pemesanan;
use App\Services\PemesananService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * PemesananController (User)
 *
 * Bertanggung jawab hanya untuk:
 * 1. Menerima request HTTP
 * 2. Mendelegasikan ke PemesananService
 * 3. Mengembalikan View atau Redirect
 *
 * Tidak ada logika bisnis, kalkulasi, atau query kompleks di sini.
 */
final class PemesananController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        private readonly PemesananService $pemesananService,
    ) {}

    public function index(): View
    {
        $pemesanans = Pemesanan::with(['mobil', 'payment'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('user.pemesanan.index', compact('pemesanans'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        $mobil = Mobil::findOrFail($request->mobil_id);

        if (! $mobil->isTersedia()) {
            return redirect()->route('home')
                ->with('error', 'Mobil ini sedang tidak tersedia.');
        }

        return view('user.pemesanan.create', compact('mobil'));
    }

    public function store(StorePemesananRequest $request): RedirectResponse
    {
        try {
            $pemesanan = $this->pemesananService->buat(
                data   : $request->dataValid(),
                userId : Auth::id(),
            );

            return redirect()
                ->route('payment.checkout', $pemesanan)
                ->with('success', 'Pemesanan berhasil dibuat. Selesaikan pembayaran Anda.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }

    public function show(Pemesanan $pemesanan): View
    {
        $this->authorize('view', $pemesanan);

        // Load semua relasi yang dibutuhkan view show:
        // - mobil        : foto, harga, spesifikasi
        // - payment      : status, metode, wa_sent_at, paid_at
        // (journalEntries tidak ditampilkan ke user — hanya untuk admin)
        $pemesanan->load(['mobil', 'payment']);

        return view('user.pemesanan.show', compact('pemesanan'));
    }

    public function cancel(Pemesanan $pemesanan): RedirectResponse
    {
        $this->authorize('batalkan', $pemesanan);

        try {
            $this->pemesananService->batalkan($pemesanan, Auth::id());

            return redirect()
                ->route('pemesanan.index')
                ->with('success', 'Pemesanan berhasil dibatalkan.');
        } catch (PemesananException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}