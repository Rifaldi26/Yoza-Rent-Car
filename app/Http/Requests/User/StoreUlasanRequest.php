<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreUlasanRequest
 *
 * Memvalidasi input pembuatan ulasan baru oleh pelanggan.
 * Menggantikan $request->validate() inline di User\UlasanController.
 */
final class StoreUlasanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->hasVerifiedEmail();
    }

    public function rules(): array
    {
        return [
            'pemesanan_id' => ['required', 'integer', 'exists:pemesanans,id'],
            'rating'       => ['required', 'integer', 'min:1', 'max:5'],
            'komentar'     => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'pemesanan_id.required' => 'Pemesanan harus dipilih.',
            'pemesanan_id.exists'   => 'Pemesanan yang dipilih tidak valid.',
            'rating.required'       => 'Rating harus diisi.',
            'rating.min'            => 'Rating minimal 1.',
            'rating.max'            => 'Rating maksimal 5.',
            'komentar.max'          => 'Komentar maksimal 1000 karakter.',
        ];
    }
}
