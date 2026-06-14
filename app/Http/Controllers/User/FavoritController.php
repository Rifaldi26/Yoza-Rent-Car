<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Favorit;
use App\Models\Mobil;
use Illuminate\Support\Facades\Auth;

class FavoritController extends Controller
{
    public function index()
    {
        $mobils = Auth::user()->mobilFavorit()->latest('favorits.created_at')->get();

        return view('user.favorit.index', compact('mobils'));
    }

    public function toggle(Mobil $mobil)
    {
        $user = Auth::user();

        $favorit = Favorit::where('user_id', $user->id)
            ->where('mobil_id', $mobil->id)
            ->first();

        if ($favorit) {
            $favorit->delete();
            $status = false;
        } else {
            Favorit::create([
                'user_id' => $user->id,
                'mobil_id' => $mobil->id,
            ]);
            $status = true;
        }

        if (request()->wantsJson()) {
            return response()->json(['favorit' => $status]);
        }

        return back()->with('success', $status ? 'Ditambahkan ke favorit.' : 'Dihapus dari favorit.');
    }
}
