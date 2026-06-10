<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Jobs\KirimEmailPemesanan;
use App\Models\Mobil;
use App\Models\Notifikasi;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemesananController extends Controller
{
    public function index()
    {
        $pemesanans = Pemesanan::with(['mobil', 'payment'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('user.pemesanan.index', compact('pemesanans'));
    }

    public function create(Request $request)
    {
        $mobil = Mobil::findOrFail($request->mobil_id);

        if (!$mobil->tersedia()) {
            return redirect()->route('home')
                ->with('error', 'Mobil ini sedang tidak tersedia.');
        }

        return view('user.pemesanan.create', compact('mobil'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mobil_id'      => 'required|exists:mobils,id',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai'=> 'required|date|after:tanggal_mulai',
            'opsi_supir'    => 'boolean',
            'catatan'       => 'nullable|string|max:500',
        ]);

        $mobil = Mobil::findOrFail($validated['mobil_id']);

        // Cek ketersediaan mobil
        if (!$mobil->tersedia()) {
            return back()->with('error', 'Mobil ini sudah tidak tersedia.');
        }

        // Cek konflik tanggal
        if (Pemesanan::adaKonflik($mobil->id, $validated['tanggal_mulai'], $validated['tanggal_selesai'])) {
            return back()->with('error', 'Mobil sudah dipesan pada rentang tanggal tersebut.');
        }

        // Hitung harga
        $mulai    = \Carbon\Carbon::parse($validated['tanggal_mulai']);
        $selesai  = \Carbon\Carbon::parse($validated['tanggal_selesai']);
        $durasi   = $mulai->diffInDays($selesai);
        $opsiSupir= $request->boolean('opsi_supir');

        $biayaSupir = 0;
        if ($opsiSupir && $mobil->adaSupir()) {
            $biayaSupir = $durasi * $mobil->biaya_supir_per_hari;
        }

        $totalHarga = ($durasi * $mobil->harga_per_hari) + $biayaSupir;

        $pemesanan = Pemesanan::create([
            'user_id'        => Auth::id(),
            'mobil_id'       => $mobil->id,
            'tanggal_mulai'  => $validated['tanggal_mulai'],
            'tanggal_selesai'=> $validated['tanggal_selesai'],
            'opsi_supir'     => $opsiSupir,
            'biaya_supir'    => $biayaSupir > 0 ? $biayaSupir : null,
            'total_harga'    => $totalHarga,
            'status'         => 'pending',
            'catatan'        => $validated['catatan'] ?? null,
        ]);

        // Notifikasi in-app
        Notifikasi::kirim(
            Auth::id(),
            'Pemesanan Dibuat',
            "Pemesanan #{$pemesanan->id} untuk {$mobil->nama} berhasil dibuat. Silakan selesaikan pembayaran.",
            'info',
            route('payment.checkout', $pemesanan)
        );

        // Email async
        KirimEmailPemesanan::dispatch($pemesanan, 'dibuat');

        return redirect()->route('payment.checkout', $pemesanan)
            ->with('success', 'Pemesanan berhasil dibuat. Selesaikan pembayaran Anda.');
    }

    public function show(Pemesanan $pemesanan)
    {
        abort_if($pemesanan->user_id !== Auth::id(), 403);
        $pemesanan->load(['mobil', 'payment', 'journalEntries']);
        return view('user.payment.checkout', compact('pemesanan'));
    }

    public function cancel(Pemesanan $pemesanan)
    {
        abort_if($pemesanan->user_id !== Auth::id(), 403);

        if (!$pemesanan->isBisaDibatalkan()) {
            return back()->with('error', 'Pemesanan ini tidak dapat dibatalkan.');
        }

        $pemesanan->update(['status' => 'dibatalkan']);

        Notifikasi::kirim(
            Auth::id(),
            'Pemesanan Dibatalkan',
            "Pemesanan #{$pemesanan->id} untuk {$pemesanan->mobil->nama} telah dibatalkan.",
            'warning',
            route('pemesanan.index')
        );

        KirimEmailPemesanan::dispatch($pemesanan, 'dibatalkan');

        return redirect()->route('pemesanan.index')
            ->with('success', 'Pemesanan berhasil dibatalkan.');
    }
}