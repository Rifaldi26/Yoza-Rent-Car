<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Mobil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ulasan;

class MobilController extends Controller
{
    public function index(Request $request)
    {
        $mobils = Mobil::query()
            ->when($request->search, fn ($q) => $q->where('nama', 'like', "%{$request->search}%")
                ->orWhere('merek', 'like', "%{$request->search}%")
            )
            ->when($request->status, fn ($q) => $q->where('status', $request->status)
            )
            ->when($request->supir, fn ($q) => $q->whereNotNull('biaya_supir_per_hari')
            )
            ->when($request->sort_harga, function ($q) use ($request) {
                $q->orderBy(
                    'harga_per_hari',
                    $request->sort_harga === 'desc' ? 'desc' : 'asc'
                );
            })
            ->when(! $request->sort_harga, fn ($q) => $q->latest()
            )
            ->paginate(12)
            ->withQueryString();

        return view('user.mobil.index', compact('mobils'));
    }

    public function show(Mobil $mobil)
    {
        $ulasans      = $mobil->ulasans()->disetujui()->with('user')->latest()->get();
        $rataRating   = $ulasans->avg('rating') ?? 0;
        $jumlahUlasan = $ulasans->count();
    
        $ulasanSaya        = null;
        $pemesananSelesai  = null;
    
        if (auth()->check()) {
            $ulasanSaya = Ulasan::where('user_id', auth()->id())
                ->where('mobil_id', $mobil->id)->first();
    
            if (!$ulasanSaya) {
                $pemesananSelesai = auth()->user()->pemesanans()
                    ->where('mobil_id', $mobil->id)
                    ->where('status', 'selesai')
                    ->whereDoesntHave('ulasan')
                    ->latest()->first();
            }
        }
    
        $isFavorit = Auth::check()
            ? $mobil->difavoritOleh(Auth::id())
            : false;

        return view('user.mobil.show', compact(
            'mobil', 'isFavorit', 'ulasans', 'rataRating',
            'jumlahUlasan', 'ulasanSaya', 'pemesananSelesai'
        ));
    }
}
