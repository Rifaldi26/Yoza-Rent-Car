<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateMobilRequest
 *
 * Validasi pembaruan data mobil. Plat nomor diabaikan
 * bila tidak berubah (unique ignore ID saat ini).
 */
final class UpdateMobilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $mobilId = $this->route('mobil')?->id;

        return [
            'nama'                 => ['required', 'string', 'max:100'],
            'merek'                => ['required', 'string', 'max:50'],
            'tahun'                => ['required', 'integer', 'min:1990', 'max:' . (date('Y') + 1)],
            'plat_nomor'           => ['required', 'string', 'max:20', "unique:mobils,plat_nomor,{$mobilId}"],
            'harga_per_hari'       => ['required', 'numeric', 'min:10000'],
            'biaya_supir_per_hari' => ['nullable', 'numeric', 'min:0'],
            'status'               => ['required', 'in:tersedia,disewa,perawatan'],
            'deskripsi'            => ['nullable', 'string', 'max:1000'],
            'foto'                 => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
