<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreMobilRequest
 *
 * Validasi penambahan data mobil baru oleh admin.
 */
final class StoreMobilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'merek' => ['required', 'string', 'max:50'],
            'tahun' => ['required', 'integer', 'min:1990', 'max:'.(date('Y') + 1)],
            'plat_nomor' => ['required', 'string', 'max:20', 'unique:mobils,plat_nomor'],
            'harga_per_hari' => ['required', 'numeric', 'min:10000'],
            'biaya_supir_per_hari' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:tersedia,disewa,perawatan'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama mobil harus diisi.',
            'plat_nomor.unique' => 'Plat nomor ini sudah terdaftar.',
            'harga_per_hari.min' => 'Harga sewa minimal Rp 10.000 per hari.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format gambar harus JPG, PNG, atau WebP.',
            'foto.max' => 'Ukuran gambar maksimal 2 MB.',
        ];
    }
}
