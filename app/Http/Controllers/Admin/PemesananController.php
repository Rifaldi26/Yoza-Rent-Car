<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\KirimEmailPemesanan;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Notifikasi;
use App\Models\Pemesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PemesananController extends Controller
{
    public function index(Request $request)
    {
        $pemesanans = Pemesanan::with(['user', 'mobil', 'payment'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->bulan, fn($q) => $q->whereMonth('created_at', $request->bulan))
            ->when($request->tahun, fn($q) => $q->whereYear('created_at', $request->tahun))
            ->when($request->search, function ($q) use ($request) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$request->search}%"))
                  ->orWhereHas('mobil', fn($m) => $m->where('nama', 'like', "%{$request->search}%"));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pemesanan.index', compact('pemesanans'));
    }

    public function show(Pemesanan $pemesanan)
    {
        $pemesanan->load(['user', 'mobil', 'payment', 'journalEntries.account']);
        return view('admin.pemesanan.show', compact('pemesanan'));
    }

    public function konfirmasi(Pemesanan $pemesanan)
    {
        if ($pemesanan->status !== 'menunggu_konfirmasi_admin') {
            return back()->with('error', 'Pemesanan tidak dalam status yang dapat dikonfirmasi.');
        }

        $pemesanan->update([
            'status'    => 'dikonfirmasi',
            'mobil_id'  => $pemesanan->mobil_id,
        ]);

        // Update status mobil
        $pemesanan->mobil->update(['status' => 'disewa']);

        // Notifikasi in-app
        Notifikasi::kirim(
            $pemesanan->user_id,
            'Pemesanan Dikonfirmasi',
            "Pemesanan #{$pemesanan->id} untuk {$pemesanan->mobil->nama} telah dikonfirmasi. Selamat menikmati perjalanan Anda!",
            'success',
            route('pemesanan.show', $pemesanan)
        );

        // Email async
        KirimEmailPemesanan::dispatch($pemesanan, 'dikonfirmasi');

        return back()->with('success', 'Pemesanan berhasil dikonfirmasi.');
    }

    public function tolak(Request $request, Pemesanan $pemesanan)
    {
        if (!in_array($pemesanan->status, ['pending', 'menunggu_konfirmasi_admin'])) {
            return back()->with('error', 'Pemesanan tidak dapat ditolak.');
        }

        $pemesanan->update(['status' => 'dibatalkan']);

        // Notifikasi in-app
        Notifikasi::kirim(
            $pemesanan->user_id,
            'Pemesanan Ditolak',
            "Maaf, pemesanan #{$pemesanan->id} untuk {$pemesanan->mobil->nama} tidak dapat kami proses. Hubungi kami via chat untuk informasi lebih lanjut.",
            'warning',
            route('chat.index')
        );

        // Email async
        KirimEmailPemesanan::dispatch($pemesanan, 'ditolak');

        return back()->with('success', 'Pemesanan berhasil ditolak.');
    }

    public function selesai(Pemesanan $pemesanan)
    {
        if ($pemesanan->status !== 'dikonfirmasi') {
            return back()->with('error', 'Hanya pemesanan yang dikonfirmasi yang dapat diselesaikan.');
        }

        $pemesanan->update(['status' => 'selesai']);

        // Kembalikan status mobil
        $pemesanan->mobil->update(['status' => 'tersedia']);

        // Buat jurnal pendapatan final
        $this->buatJurnalSelesai($pemesanan);

        // Notifikasi in-app
        Notifikasi::kirim(
            $pemesanan->user_id,
            'Pemesanan Selesai',
            "Terima kasih telah menggunakan Yoza Rent Car! Pemesanan #{$pemesanan->id} telah selesai. Sampai jumpa lagi!",
            'success',
            route('pemesanan.index')
        );

        

        // Email async
        KirimEmailPemesanan::dispatch($pemesanan, 'selesai');

        return back()->with('success', 'Pemesanan ditandai selesai.');
    }

    // Tambahkan setelah method selesai()

    public function konfirmasiPembayaran(Pemesanan $pemesanan)
    {
        if (!$pemesanan->payment) {
            return back()->with('error', 'Tidak ada data pembayaran untuk pemesanan ini.');
        }
    
        if ($pemesanan->payment->status === 'dikonfirmasi') {
            return back()->with('info', 'Pembayaran sudah dikonfirmasi sebelumnya.');
        }
    
        $pemesanan->payment->update([
            'status'  => 'dikonfirmasi',
            'paid_at' => now(),
        ]);
    
        // Lanjut konfirmasi pemesanan jika belum
        if ($pemesanan->status === 'menunggu_konfirmasi_admin') {
            $pemesanan->update(['status' => 'dikonfirmasi']);
            $pemesanan->mobil->update(['status' => 'disewa']);
    
            Notifikasi::kirim(
                $pemesanan->user_id,
                'Pemesanan Dikonfirmasi',
                "Pemesanan #{$pemesanan->id} untuk {$pemesanan->mobil->nama} telah dikonfirmasi.",
                'success',
                route('pemesanan.show', $pemesanan)
            );
    
            KirimEmailPemesanan::dispatch($pemesanan->fresh(['user', 'mobil', 'payment']), 'dikonfirmasi');
        }
    
        return back()->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }

    public function invoice(Pemesanan $pemesanan)
    {
        $pemesanan->load(['user', 'mobil', 'payment']);

        $pdf = Pdf::loadView('pdf.invoice', compact('pemesanan'))
            ->setPaper('a4');

        return $pdf->download("invoice-{$pemesanan->id}.pdf");
    }

    // ── Private: buat jurnal saat pemesanan selesai ────────────
    private function buatJurnalSelesai(Pemesanan $pemesanan): void
    {
        $kas            = Account::where('kode', '1-001')->first();
        $pendapatanSewa = Account::where('kode', '4-001')->first();
        $pendapatanSupir= Account::where('kode', '4-002')->first();

        if (!$kas || !$pendapatanSewa) return;

        $hargaSewa  = $pemesanan->total_harga - ($pemesanan->biaya_supir ?? 0);
        $biayaSupir = $pemesanan->biaya_supir ?? 0;
        $today      = now()->toDateString();
        $paymentId  = $pemesanan->payment?->id;

        // Debit Kas — total pembayaran masuk
        JournalEntry::create([
            'account_id'   => $kas->id,
            'pemesanan_id' => $pemesanan->id,
            'payment_id'   => $paymentId,
            'debit'        => $pemesanan->total_harga,
            'credit'       => 0,
            'description'  => "Kas masuk — Pemesanan #{$pemesanan->id}",
            'date'         => $today,
        ]);

        // Kredit Pendapatan Sewa
        JournalEntry::create([
            'account_id'   => $pendapatanSewa->id,
            'pemesanan_id' => $pemesanan->id,
            'payment_id'   => $paymentId,
            'debit'        => 0,
            'credit'       => $hargaSewa,
            'description'  => "Pendapatan sewa — Pemesanan #{$pemesanan->id}",
            'date'         => $today,
        ]);

        // Kredit Pendapatan Jasa Supir (jika ada)
        if ($biayaSupir > 0 && $pendapatanSupir) {
            JournalEntry::create([
                'account_id'   => $pendapatanSupir->id,
                'pemesanan_id' => $pemesanan->id,
                'payment_id'   => $paymentId,
                'debit'        => 0,
                'credit'       => $biayaSupir,
                'description'  => "Pendapatan jasa supir — Pemesanan #{$pemesanan->id}",
                'date'         => $today,
            ]);
        }

        // Update balance akun
        $kas->increment('balance', $pemesanan->total_harga);
        $pendapatanSewa->increment('balance', $hargaSewa);
        if ($biayaSupir > 0 && $pendapatanSupir) {
            $pendapatanSupir->increment('balance', $biayaSupir);
        }
    }
}