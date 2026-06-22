@extends('layouts.admin')
@section('title', __('Tambah Mobil'))

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.mobil.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors">
        <x-icon name="arrow-left" class="w-4 h-4" />
        {{ __('Kembali ke Armada') }}
    </a>
</div>

<x-page-header title="{{ __('Tambah Mobil Baru') }}" />

<form method="POST" action="{{ route('admin.mobil.store') }}" enctype="multipart/form-data">
@csrf
<div class="grid gap-4 lg:grid-cols-3">

    {{-- Form Utama --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">{{ __('Informasi Kendaraan') }}</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <x-input name="nama" label="{{ __('Nama / Model') }}" placeholder="Toyota Avanza" required />
                <x-input name="merek" label="{{ __('Merek') }}" placeholder="Toyota" required />
                <x-input name="tahun" label="{{ __('Tahun') }}" type="number" placeholder="2023" required />
                <x-input name="plat_nomor" label="{{ __('Plat Nomor') }}" placeholder="B 1234 ABC" required />
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">{{ __('Harga & Opsi') }}</h3>
            <div class="grid gap-4 sm:grid-cols-2">
                <x-input name="harga_per_hari" label="{{ __('Harga per Hari (Rp)') }}"
                    type="number" placeholder="350000" prefix="Rp" required />
                <x-input name="biaya_supir_per_hari" label="{{ __('Biaya Supir per Hari (Rp)') }}"
                    type="number" placeholder="{{ __('Kosongkan jika tidak ada') }}"
                    prefix="Rp"
                    helper="{{ __('Isi jika kendaraan mendukung opsi dengan supir') }}" />
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">{{ __('Deskripsi') }}</h3>
            <x-textarea name="deskripsi" label="{{ __('Deskripsi Kendaraan') }}"
                placeholder="{{ __('Kondisi, fitur, kapasitas, dll.') }}"
                rows="4" />
        </div>
    </div>

    {{-- Upload Foto + Submit --}}
    <div class="space-y-4">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">{{ __('Foto Kendaraan') }}</h3>
            <div x-data="{ preview: null }"
                 class="flex flex-col items-center justify-center gap-3">
                <div class="relative w-full">
                    <div x-show="!preview"
                         class="grid h-40 w-full place-items-center rounded-lg border-2
                                border-dashed border-gray-200 bg-gray-50 text-gray-400">
                        <div class="text-center">
                            <x-icon name="upload" class="w-8 h-8 mx-auto mb-2" />
                            <p class="text-xs">{{ __('Klik untuk upload') }}</p>
                            <p class="text-[10px] mt-0.5">{{ __('JPG, PNG, WebP max 2MB') }}</p>
                        </div>
                    </div>
                    <img x-show="preview" :src="preview"
                         class="h-40 w-full rounded-lg object-cover" x-cloak>
                </div>
                <input type="file" name="foto" accept="image/*"
                       class="hidden" id="foto-input"
                       @change="preview = URL.createObjectURL($event.target.files[0])">
                <label for="foto-input"
                       class="w-full cursor-pointer rounded-lg border border-gray-200 px-3 py-2
                              text-center text-sm font-medium text-gray-600
                              hover:bg-gray-50 transition-colors">
                    {{ __('Pilih Foto') }}
                </label>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <button type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-primary-600
                           py-2.5 text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                <x-icon name="plus" class="w-4 h-4" />
                {{ __('Simpan Mobil') }}
            </button>
            <a href="{{ route('admin.mobil.index') }}"
               class="mt-2 flex w-full items-center justify-center rounded-lg border
                      border-gray-200 py-2.5 text-sm font-medium text-gray-600
                      hover:bg-gray-50 transition-colors">
                {{ __('Batal') }}
            </a>
        </div>
    </div>

</div>
</form>

@endsection