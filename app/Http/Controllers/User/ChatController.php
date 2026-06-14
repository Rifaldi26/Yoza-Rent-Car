<?php

namespace App\Http\Controllers\User;

use App\Events\PesanTerkirim;
use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Models\Pesan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $admin = User::where('role', 'admin')->first();

        return view('user.chat.index', compact('admin'));
    }

    public function riwayat(User $lawan)
    {
        $pesans = Pesan::percakapan(Auth::id(), $lawan->id);

        // Tandai sudah dibaca
        Pesan::where('pengirim_id', $lawan->id)
            ->where('penerima_id', Auth::id())
            ->where('dibaca', false)
            ->update(['dibaca' => true]);

        return response()->json($pesans->map(fn ($p) => [
            'id' => $p->id,
            'pengirim_id' => $p->pengirim_id,
            'isi' => $p->isi,
            'waktu' => $p->created_at->format('H:i'),
            'pemesanan' => $p->pemesanan ? [
                'id' => $p->pemesanan->id,
                'nama_mobil' => $p->pemesanan->mobil->nama ?? '-',
                'tanggal_mulai' => $p->pemesanan->tanggal_mulai->format('d M Y'),
                'tanggal_selesai' => $p->pemesanan->tanggal_selesai->format('d M Y'),
                'status' => $p->pemesanan->labelStatus(),
                'total_harga' => number_format($p->pemesanan->total_harga, 0, ',', '.'),
                'url' => route('pemesanan.show', $p->pemesanan->id),
            ] : null,
        ]));
    }

    public function kirim(Request $request, User $lawan)
    {
        $request->validate([
            'isi' => 'required|string|max:2000',
            'pemesanan_id' => 'nullable|exists:pemesanans,id',
        ]);

        // Pastikan pemesanan milik user yang login
        if ($request->pemesanan_id) {
            $valid = Pemesanan::where('id', $request->pemesanan_id)
                ->where('user_id', Auth::id())
                ->exists();

            if (! $valid) {
                return response()->json(['error' => 'Pemesanan tidak valid.'], 422);
            }
        }

        $pesan = Pesan::create([
            'pengirim_id' => Auth::id(),
            'penerima_id' => $lawan->id,
            'isi' => $request->isi,
            'pemesanan_id' => $request->pemesanan_id,
        ]);

        broadcast(new PesanTerkirim($pesan))->toOthers();

        return response()->json([
            'id' => $pesan->id,
            'pengirim_id' => $pesan->pengirim_id,
            'isi' => $pesan->isi,
            'waktu' => $pesan->created_at->format('H:i'),
            'pemesanan' => null,
        ], 201);
    }

    public function unreadCount()
    {
        return response()->json([
            'count' => Auth::user()->unreadPesan(),
        ]);
    }
}
