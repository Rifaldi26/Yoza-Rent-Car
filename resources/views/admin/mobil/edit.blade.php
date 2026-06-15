@extends('layouts.admin')
@section('title', __('Edit') . ' ' . $mobil->nama)

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.mobil.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors">
        <x-icon name="arrow-left" class="w-4 h-4" />
        {{ __('Kembali ke Armada') }}
    </a>
</div>

<x-page-header title="{{ __('Edit') }} {{ $mobil->nama }}" />

<form method="POST" action="{{ route('admin.mobil.update', $mobil) }}" enctype="multipart/form-data">
@csrf @method('PUT')
<div class="grid gap-4 lg:grid-cols-3">

    <div class="lg:col-span-2 space-y-4">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">{{ __('Informasi Kendaraan') }}</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <x-input name="nama" label="{{ __('Nama / Model') }}"
                    :value="old('nama', $mobil->nama)" required />
                <x-input name="merek" label="{{ __('Merek') }}"
                    :value="old('merek', $mobil->merek)" required />
                <x-input name="tahun" label="{{ __('Tahun') }}" type="number"
                    :value="old('tahun', $mobil->tahun)" required />
                <x-input name="plat_nomor" label="{{ __('Plat Nomor') }}"
                    :value="old('plat_nomor', $mobil->plat_nomor)" required />
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">{{ __('Harga & Opsi') }}</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <x-input name="harga_per_hari" label="{{ __('Harga per Hari (Rp)') }}"
                    type="number" prefix="Rp"
                    :value="old('harga_per_hari', $mobil->harga_per_hari)" required />
                <x-input name="biaya_supir_per_hari" label="{{ __('Biaya Supir per Hari (Rp)') }}"
                    type="number" prefix="Rp"
                    :value="old('biaya_supir_per_hari', $mobil->biaya_supir_per_hari)"
                    helper="{{ __('Kosongkan jika tidak ada opsi supir') }}" />
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <x-textarea name="deskripsi" label="{{ __('Deskripsi Kendaraan') }}" rows="4">
                {{ old('deskripsi', $mobil->deskripsi) }}
            </x-textarea>
        </div>
    </div>

    <div class="space-y-4">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">{{ __('Foto Kendaraan') }}</h3>
            <div x-data="{ preview: '{{ $mobil->foto ? Storage::url($mobil->foto) : '' }}' }">
                <div class="mb-3">
                    <template x-if="preview">
                        <img :src="preview" class="h-40 w-full rounded-lg object-cover">
                    </template>
                    <template x-if="!preview">
                        <div class="grid h-40 place-items-center rounded-lg bg-gray-100 text-gray-400">
                            <x-icon name="car" class="w-12 h-12" />
                        </div>
                    </template>
                </div>
                <input type="file" name="foto" id="foto-edit" accept="image/*"
                       class="hidden"
                       @change="preview = URL.createObjectURL($event.target.files[0])">
                <label for="foto-edit"
                       class="block w-full cursor-pointer rounded-lg border border-gray-200
                              px-3 py-2 text-center text-sm font-medium text-gray-600
                              hover:bg-gray-50 transition-colors">
                    {{ __('Ganti Foto') }}
                </label>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <button type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600
                           py-2.5 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                <x-icon name="check-circle" class="w-4 h-4" />
                {{ __('Simpan Perubahan') }}
            </button>
        </div>
    </div>

</div>
</form>

@endsection