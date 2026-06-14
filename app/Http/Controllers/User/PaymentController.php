<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Jobs\KirimEmailPemesanan;
use App\Models\Notifikasi;
use App\Models\Payment;
use App\Models\Pemesanan;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
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

        // Buat atau update payment record
        $payment = Payment::updateOrCreate(
            ['pemesanan_id' => $pemesanan->id],
            [
                'amount' => $pemesanan->total_harga,
                'metode' => $request->metode,
                'status' => 'menunggu_konfirmasi',
                'wa_sent_at' => now(),
            ]
        );

        // Update status pemesanan
        $pemesanan->update(['status' => 'menunggu_konfirmasi_admin']);

        // Notifikasi in-app ke pelanggan
        Notifikasi::kirim(
            Auth::id(),
            'Menunggu Konfirmasi',
            "Pemesanan #{$pemesanan->id} sedang menunggu konfirmasi admin setelah kamu mengirim pesan WhatsApp.",
            'info',
            route('pemesanan.show', $pemesanan)
        );

        // Notifikasi in-app ke semua admin
        User::where('role', 'admin')->each(function ($admin) use ($pemesanan) {
            Notifikasi::kirim(
                $admin->id,
                'Pesanan Baru via WhatsApp',
                "Pemesanan #{$pemesanan->id} dari {$pemesanan->user->name} memilih metode {$pemesanan->payment->labelMetode()}. Cek WhatsApp.",
                'info',
                route('admin.pemesanan.show', $pemesanan)
            );
        });

        // Email async
        KirimEmailPemesanan::dispatch($pemesanan->fresh(['user', 'mobil', 'payment']), 'menunggu_konfirmasi');

        // Build WhatsApp URL
        $waUrl = $this->buildWhatsAppUrl($pemesanan, $request->metode);

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

    // ── Private: Build URL WhatsApp ─────────────────────────
    private function buildWhatsAppUrl(Pemesanan $pemesanan, string $metode): string
    {
        $template = config("payment.wa_template.{$metode}", '');
        $config = config("payment.metode.{$metode}", []);

        // Info waktu untuk sewa 12 jam
        $waktuInfo = $pemesanan->adalah12Jam() && $pemesanan->waktu_mulai
            ? ' pukul '.substr($pemesanan->waktu_mulai, 0, 5)
            : '';

        // Label durasi yang mudah dibaca
        $labelDurasi = $pemesanan->adalah12Jam()
            ? 'Sewa 12 Jam'
            : 'Sewa '.$pemesanan->durasi().' Hari';

        $pesan = strtr($template, [
            '{id}' => $pemesanan->id,
            '{nama}' => $pemesanan->user->name,
            '{mobil}' => $pemesanan->mobil->nama,
            '{durasi}' => $labelDurasi,
            '{tanggal_mulai}' => $pemesanan->tanggal_mulai->format('d M Y'),
            '{tanggal_selesai}' => $pemesanan->tanggal_selesai->format('d M Y'),
            '{waktu_info}' => $waktuInfo,
            '{total}' => number_format($pemesanan->total_harga, 0, ',', '.'),
            '{bank}' => $config['bank'] ?? '',
            '{rekening}' => $config['rekening'] ?? '',
            '{atas_nama}' => $config['atas_nama'] ?? '',
        ]);

        return 'https://wa.me/'.config('payment.wa_number')
            .'?text='.rawurlencode($pesan);
    }
}
