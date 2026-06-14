<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StorePemesananRequest
 *
 * Memvalidasi input pembuatan pemesanan baru.
 * Menggantikan $request->validate() inline di UserPemesananController.
 */
final class StorePemesananRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->hasVerifiedEmail();
    }

    public function rules(): array
    {
        return [
            'mobil_id' => ['required', 'integer', 'exists:mobils,id'],
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['required', 'date', 'after:tanggal_mulai'],
            'opsi_supir' => ['nullable', 'boolean'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'mobil_id.required' => 'Mobil harus dipilih.',
            'mobil_id.exists' => 'Mobil yang dipilih tidak valid.',
            'tanggal_mulai.required' => 'Tanggal mulai harus diisi.',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai tidak boleh di masa lalu.',
            'tanggal_selesai.required' => 'Tanggal selesai harus diisi.',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'catatan.max' => 'Catatan maksimal 500 karakter.',
        ];
    }

    /**
     * Kembalikan data yang sudah dibersihkan untuk diteruskan ke Service.
     */
    public function dataValid(): array
    {
        return array_merge($this->validated(), [
            'opsi_supir' => $this->boolean('opsi_supir'),
        ]);
    }
}
