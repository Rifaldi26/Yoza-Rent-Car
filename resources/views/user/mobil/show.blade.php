@extends('layouts.app')
@section('title', $mobil->nama)

@section('content')
<div class="mx-auto max-w-5xl px-4 py-8">

    <div class="mb-4">
        <a href="{{ route('home') }}"
           class="inline-flex items-center gap-1.5 text-sm text-[#7a8499] hover:text-[#18213a] transition-colors">
            <x-icon name="arrow-left" class="w-4 h-4" />
            {{ __('Kembali ke Katalog') }}
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-5">

        {{-- Foto + Info --}}
        <div class="lg:col-span-3 space-y-4">

            {{-- Foto --}}
            <div class="overflow-hidden rounded-2xl border border-[#e5e9f2] bg-white">
                @if($mobil->foto)
                    <img src="{{ Storage::url($mobil->foto) }}"
                         alt="{{ $mobil->nama }}"
                         class="h-72 w-full object-cover md:h-96">
                @else
                    <div class="grid h-72 place-items-center bg-gradient-to-br from-[#eef2fb] to-[#f4f6fb]">
                        <x-icon name="car" class="w-24 h-24 text-[#3b6fd4]/30" />
                    </div>
                @endif
            </div>

            {{-- Deskripsi --}}
            @if($mobil->deskripsi)
            <div class="rounded-2xl border border-[#e5e9f2] bg-white p-5">
                <h3 class="text-sm font-semibold text-[#18213a] mb-2">{{ __('Tentang Kendaraan') }}</h3>
                <p class="text-sm text-[#7a8499] leading-relaxed">{{ $mobil->deskripsi }}</p>
            </div>
            @endif
        </div>

        {{-- Booking Card --}}
        <div class="lg:col-span-2">
            <div class="sticky top-24 space-y-4">

                {{-- Info Utama --}}
                <div class="rounded-2xl border border-[#e5e9f2] bg-white p-5 shadow-sm">

                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h1 class="text-xl font-bold text-[#18213a]">{{ $mobil->nama }}</h1>
                            <p class="text-sm text-[#7a8499]">{{ $mobil->merek }} &middot; {{ $mobil->tahun }}</p>
                        </div>
                        <x-status-badge :status="$mobil->status">
                            {{ ucfirst($mobil->status) }}
                        </x-status-badge>
                    </div>

                    <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-xl bg-[#f4f6fb] p-3">
                            <p class="text-xs text-[#7a8499]">{{ __('Plat Nomor') }}</p>
                            <p class="mt-0.5 font-semibold text-[#18213a]">{{ $mobil->plat_nomor }}</p>
                        </div>
                        <div class="rounded-xl bg-[#f4f6fb] p-3">
                            <p class="text-xs text-[#7a8499]">{{ __('Harga / Hari') }}</p>
                            <p class="mt-0.5 font-bold text-[#3b6fd4]">
                                Rp {{ number_format($mobil->harga_per_hari, 0, ',', '.') }}
                            </p>
                        </div>
                        @if($mobil->adaSupir())
                        <div class="col-span-2 rounded-xl bg-[#eef2fb] p-3">
                            <p class="text-xs text-[#3b6fd4] font-medium">{{ __('Tersedia Opsi Supir') }}</p>
                            <p class="mt-0.5 font-semibold text-[#18213a]">
                                + Rp {{ number_format($mobil->biaya_supir_per_hari, 0, ',', '.') }} / hari
                            </p>
                        </div>
                        @endif
                    </div>

                    {{-- CTA --}}
                    <div class="mt-4 space-y-2">
                        @if($mobil->tersedia())
                            @auth
                                <a href="{{ route('pemesanan.create', ['mobil_id' => $mobil->id]) }}"
                                   class="flex w-full items-center justify-center gap-2 rounded-xl
                                          bg-[#3b6fd4] py-3 text-sm font-semibold text-white
                                          hover:bg-[#2e5bb8] transition-colors">
                                    <x-icon name="calendar" class="w-4 h-4" />
                                    {{ __('Sewa Sekarang') }}
                                </a>
                            @else
                                <button @click="$dispatch('open-login')"
                                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#3b6fd4] py-3 text-sm font-semibold text-white hover:bg-[#2e5bb8] transition-colors">
                                    {{ __('Masuk untuk Memesan') }}
                                </button>
                            @endauth
                        @else
                            <button disabled
                                    class="flex w-full items-center justify-center rounded-xl bg-gray-100 py-3 text-sm font-medium text-gray-400 cursor-not-allowed">
                                {{ __('Tidak Tersedia') }}
                            </button>
                        @endif

                        {{-- Favorit --}}
                        @auth
                        <form method="POST" action="{{ route('favorit.toggle', $mobil) }}">
                            @csrf
                            <button type="submit"
                                    class="flex w-full items-center justify-center gap-2 rounded-xl
                                           border border-[#e5e9f2] py-2.5 text-sm font-medium
                                           transition-colors
                                           {{ $isFavorit
                                               ? 'border-red-200 bg-red-50 text-red-600 hover:bg-red-100'
                                               : 'text-[#7a8499] hover:bg-[#f1f4fa]' }}">
                                <x-icon name="heart" class="w-4 h-4" />
                                {{ $isFavorit ? __('Hapus dari Favorit') : __('Simpan ke Favorit') }}
                            </button>
                        </form>
                        @endauth
                    </div>
                </div>

                {{-- Info Tambahan --}}
                <div class="rounded-2xl border border-[#e5e9f2] bg-white p-4 text-xs text-[#7a8499]">
                    <div class="flex items-center gap-2 mb-1.5">
                        <x-icon name="shield" class="w-4 h-4 text-[#3b6fd4]" />
                        <span class="font-medium text-[#18213a]">{{ __('Pembayaran Aman') }}</span>
                    </div>
                    <p>{{ __('Transaksi mendukung cash, transfer bank, QRIS, dan EDC.') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection