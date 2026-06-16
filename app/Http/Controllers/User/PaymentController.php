<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Services\PaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    // Injeksi PaymentService melalui constructor
    public function __construct(
        private PaymentService $paymentService
    ) {}

    // ── Halaman pilih metode pembayaran ────────────────────
    public function checkout(Pemesanan $pemesanan)
    {
        abort_if($pemesanan->user_id !== Auth::id(), 403);

        if ($pemesanan->status !== 'pending') {
            return redirect()->route('pemesanan.show', $pemesanan)
                ->with('info', 'Pemesanan ini tidak memerlukan pembayaran.');
        }

        $pemesanan->load(['mobil', 'user']);
        $metode = config('payment.metode');

        return view('user.payment.checkout', compact('pemesanan', 'metode'));
    }

    // ── Proses pilihan metode → simpan Payment → redirect WA ─
    public function pilihMetode(Request $request, Pemesanan $pemesanan)
    {
        abort_if($pemesanan->user_id !== Auth::id(), 403);

        $request->validate([
            'metode' => 'required|in:cash,transfer,qris,edc',
        ]);

        if ($pemesanan->status !== 'pending') {
            return back()->with('error', 'Pemesanan sudah tidak dalam status pending.');
        }

        // Delegasikan seluruh logika bisnis ke PaymentService
        $this->paymentService->pilihMetode($pemesanan, $request->metode);

        // Build WhatsApp URL dari PaymentService
        $waUrl = $this->paymentService->bangunUrlWhatsApp($pemesanan, $request->metode);

        return redirect()->away($waUrl);
    }

    // ── Halaman konfirmasi setelah redirect balik dari WA ───
    public function setelahWa(Pemesanan $pemesanan)
    {
        abort_if($pemesanan->user_id !== Auth::id(), 403);
        $pemesanan->load(['mobil', 'payment']);

        return view('user.payment.setelah-wa', compact('pemesanan'));
    }

    // ── Download Invoice PDF ────────────────────────────────
    public function invoice(Pemesanan $pemesanan)
    {
        abort_if($pemesanan->user_id !== Auth::id(), 403);
        abort_unless(
            in_array($pemesanan->status, ['menunggu_konfirmasi_admin', 'dikonfirmasi', 'selesai']),
            403
        );

        $pemesanan->load(['mobil', 'user', 'payment']);

        $pdf = Pdf::loadView('pdf.invoice', compact('pemesanan'))
            ->setPaper('a4');

        return $pdf->download("invoice-yoza-rent-car-{$pemesanan->id}.pdf");
    }
}