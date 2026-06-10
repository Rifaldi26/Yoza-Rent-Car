<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mobil;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MobilController extends Controller
{
    public function index()
    {
        $mobils = Mobil::latest()->paginate(10);
        return view('admin.mobil.index', compact('mobils'));
    }

    public function create()
    {
        return view('admin.mobil.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'                 => 'required|string|max:255',
            'merek'                => 'required|string|max:255',
            'tahun'                => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'plat_nomor'           => 'required|string|max:20|unique:mobils',
            'harga_per_hari'       => 'required|numeric|min:0',
            'biaya_supir_per_hari' => 'nullable|numeric|min:0',
            'deskripsi'            => 'nullable|string',
            'foto'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('mobil', 'public');
        }

        Mobil::create($validated);

        return redirect()->route('admin.mobil.index')
            ->with('success', 'Mobil berhasil ditambahkan.');
    }

    public function edit(Mobil $mobil)
    {
        return view('admin.mobil.edit', compact('mobil'));
    }

    public function update(Request $request, Mobil $mobil)
    {
        $validated = $request->validate([
            'nama'                 => 'required|string|max:255',
            'merek'                => 'required|string|max:255',
            'tahun'                => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'plat_nomor'           => 'required|string|max:20|unique:mobils,plat_nomor,' . $mobil->id,
            'harga_per_hari'       => 'required|numeric|min:0',
            'biaya_supir_per_hari' => 'nullable|numeric|min:0',
            'deskripsi'            => 'nullable|string',
            'foto'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            // Hapus foto lama
            if ($mobil->foto) {
                Storage::disk('public')->delete($mobil->foto);
            }
            $validated['foto'] = $request->file('foto')->store('mobil', 'public');
        }

        $mobil->update($validated);

        return redirect()->route('admin.mobil.index')
            ->with('success', 'Data mobil berhasil diperbarui.');
    }

    public function destroy(Mobil $mobil)
    {
        // Cegah hapus jika ada pemesanan aktif
        $aktif = Pemesanan::where('mobil_id', $mobil->id)
            ->whereIn('status', ['pending', 'menunggu_konfirmasi_admin', 'dikonfirmasi'])
            ->exists();

        if ($aktif) {
            return back()->with('error', 'Mobil tidak dapat dihapus karena masih ada pemesanan aktif.');
        }

        if ($mobil->foto) {
            Storage::disk('public')->delete($mobil->foto);
        }

        $mobil->delete();

        return redirect()->route('admin.mobil.index')
            ->with('success', 'Mobil berhasil dihapus.');
    }

    public function toggleStatus(Mobil $mobil)
    {
        $status = match($mobil->status) {
            'tersedia'  => 'perawatan',
            'perawatan' => 'tersedia',
            default     => $mobil->status, // 'disewa' tidak bisa di-toggle manual
        };

        if ($mobil->status === 'disewa') {
            return back()->with('error', 'Status mobil yang sedang disewa tidak dapat diubah manual.');
        }

        $mobil->update(['status' => $status]);

        return back()->with('success', "Status mobil diubah menjadi {$status}.");
    }
}