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
        $is12Jam = $this->input('tipe_sewa') === '12_jam';
    
        return [
            'mobil_id'      => ['required', 'integer', 'exists:mobils,id'],
            'tipe_sewa'     => ['nullable', 'in:harian,12_jam'],
            'waktu_mulai'   => ['required_if:tipe_sewa,12_jam', 'nullable', 'date_format:H:i'],
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => [
                'required',
                'date',
                $is12Jam ? 'same:tanggal_mulai' : 'after:tanggal_mulai',
            ],
            'opsi_supir'    => ['nullable', 'boolean'],
            'catatan'       => ['nullable', 'string', 'max:500'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'mobil_id.required'           => 'Mobil harus dipilih.',
            'mobil_id.exists'             => 'Mobil yang dipilih tidak valid.',
            'tipe_sewa.in'                => 'Tipe sewa tidak valid.',
            'waktu_mulai.required_if'     => 'Waktu mulai wajib diisi untuk sewa 12 jam.',
            'waktu_mulai.date_format'     => 'Format waktu mulai tidak valid (HH:MM).',
            'tanggal_mulai.required'      => 'Tanggal mulai harus diisi.',
            'tanggal_mulai.after_or_equal'=> 'Tanggal mulai tidak boleh di masa lalu.',
            'tanggal_selesai.required'    => 'Tanggal selesai harus diisi.',
            'tanggal_selesai.after'       => 'Tanggal selesai harus setelah tanggal mulai.',
            'tanggal_selesai.same'        => 'Sewa 12 jam harus di tanggal yang sama.',
            'catatan.max'                 => 'Catatan maksimal 500 karakter.',
        ];
    }
    
    public function dataValid(): array
    {
        return array_merge($this->validated(), [
            'tipe_sewa'   => $this->input('tipe_sewa', 'harian'),
            'waktu_mulai' => $this->input('waktu_mulai'),
            'opsi_supir'  => $this->boolean('opsi_supir'),
        ]);
    }
}
