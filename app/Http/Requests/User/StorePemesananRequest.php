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

            // ── Data tambahan (wajib) ──────────────────────────────
            'alamat'        => ['required', 'string', 'max:500'],
            'tujuan_sewa'   => ['required', 'string', 'max:255'],
            'kota_tujuan'   => ['required', 'string', 'max:255'],

            // Minimal salah satu media sosial wajib diisi. Screenshot-nya
            // TIDAK diupload di web — dikirim manual lewat WhatsApp saat
            // user klik "Konfirmasi via WA".
            'instagram'     => ['nullable', 'required_without:tiktok', 'string', 'max:255'],
            'tiktok'        => ['nullable', 'required_without:instagram', 'string', 'max:255'],

            // Saling eksklusif: pilih salah satu, lalu isi field turunannya.
            // Lampiran (foto ID card / KTM/KRS) juga dikirim via WhatsApp,
            // bukan upload di web.
            'status_pekerjaan' => ['required', 'in:bekerja,mahasiswa'],
            'tempat_kerja'  => ['required_if:status_pekerjaan,bekerja', 'nullable', 'string', 'max:255'],
            'kampus'        => ['required_if:status_pekerjaan,mahasiswa', 'nullable', 'string', 'max:255'],

            'sumber_info'   => ['required', 'string', 'max:255'],
            'kontak_darurat' => ['required', 'string', 'max:30'],

            // Link share lokasi Google Maps (mis. https://maps.app.goo.gl/...)
            'share_lokasi'  => ['required', 'string', 'max:500', 'starts_with:http://,https://'],
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

            'alamat.required'             => 'Alamat wajib diisi.',
            'tujuan_sewa.required'        => 'Tujuan sewa wajib diisi.',
            'kota_tujuan.required'        => 'Kota tujuan wajib diisi.',
            'instagram.required_without'  => 'Isi salah satu: Instagram atau Tiktok.',
            'tiktok.required_without'     => 'Isi salah satu: Instagram atau Tiktok.',
            'status_pekerjaan.required'   => 'Pilih status: sudah bekerja atau mahasiswa.',
            'status_pekerjaan.in'         => 'Status tidak valid.',
            'tempat_kerja.required_if'    => 'Tempat kerja wajib diisi.',
            'kampus.required_if'          => 'Nama kampus wajib diisi.',
            'sumber_info.required'        => 'Mohon isi tahu Yoza Rent Car dari mana.',
            'kontak_darurat.required'     => 'Nomor WA kontak darurat wajib diisi.',
            'share_lokasi.required'       => 'Share lokasi alamat rumah wajib diisi.',
            'share_lokasi.starts_with'    => 'Mohon kirim link share lokasi Google Maps yang valid.',
        ];
    }

    public function dataValid(): array
    {
        return array_merge($this->validated(), [
            'tipe_sewa'   => $this->input('tipe_sewa', 'harian'),
            'waktu_mulai' => $this->input('waktu_mulai'),
            'opsi_supir'  => $this->boolean('opsi_supir'),

            // Pastikan hanya field yang relevan dengan status terpilih yang
            // tersimpan — mencegah data "bocor" antar status kalau user
            // sempat ganti pilihan sebelum submit.
            'tempat_kerja' => $this->input('status_pekerjaan') === 'bekerja'
                ? $this->input('tempat_kerja')
                : null,
            'kampus' => $this->input('status_pekerjaan') === 'mahasiswa'
                ? $this->input('kampus')
                : null,
        ]);
    }
}