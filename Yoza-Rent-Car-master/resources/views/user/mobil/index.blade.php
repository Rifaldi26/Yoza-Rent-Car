@extends('layouts.app')
@section('title', 'Katalog Mobil')

@section('content')

{{-- ── Hero Section ────────────────────────────────────────── --}}
<section class="bg-[#1E3A5F] text-white">
    <div class="mx-auto max-w-7xl px-4 py-14 md:py-20">
        <div class="max-w-2xl">
            <h1 class="text-3xl font-bold leading-tight md:text-4xl">
                Sewa Mobil Mudah,<br>Perjalanan Nyaman
            </h1>
            <p class="mt-3 text-base text-blue-200">
                Tersedia opsi self-drive dan dengan supir.
            </p>

            {{-- Search Bar --}}
            <form method="GET" action="{{ route('home') }}"
                  class="mt-6 flex flex-col gap-2 sm:flex-row">
                <div class="relative flex-1">
                    <x-icon name="search"
                        class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#7a8499]" />
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari merek atau nama mobil..."
                           class="h-11 w-full rounded-xl border-0 pl-9 pr-4 text-sm text-[#18213a]
                                  outline-none focus:ring-2 focus:ring-white/50 bg-white">
                </div>
                <button type="submit"
                        class="h-11 rounded-xl bg-[#3b6fd4] px-6 text-sm font-semibold text-white
                               hover:bg-[#2e5bb8] transition-colors whitespace-nowrap">
                    Cari Mobil
                </button>
            </form>

            {{-- Quick Stats --}}
            <div class="mt-8 flex flex-wrap gap-x-8 gap-y-2 text-sm text-blue-200">
                <span class="flex items-center gap-1.5">
                    <x-icon name="check-circle" class="w-4 h-4 text-green-400" />
                    Armada terpercaya
                </span>
                <span class="flex items-center gap-1.5">
                    <x-icon name="shield" class="w-4 h-4 text-blue-300" />
                    Pembayaran fleksibel
                </span>
                <span class="flex items-center gap-1.5">
                    <x-icon name="clock" class="w-4 h-4 text-blue-300" />
                    Konfirmasi cepat
                </span>
            </div>
        </div>
    </div>
</section>

{{-- ── Filter Bar ──────────────────────────────────────────── --}}
<div class="sticky top-16 z-30 border-b border-[#e5e9f2] bg-white shadow-sm">
    <div class="mx-auto max-w-7xl px-4">
        <form method="GET" action="{{ route('home') }}"
              class="flex items-center gap-2 overflow-x-auto py-3">

            {{-- Status --}}
            <div class="flex gap-1.5 flex-shrink-0">
                @foreach(['' => 'Semua', 'tersedia' => 'Tersedia', 'disewa' => 'Disewa'] as $val => $label)
                    <button type="submit" name="status" value="{{ $val }}"
                            class="whitespace-nowrap rounded-full border px-3 py-1 text-xs font-medium
                                   transition-colors
                                   {{ request('status', '') === $val
                                       ? 'border-[#3b6fd4] bg-[#3b6fd4] text-white'
                                       : 'border-[#e5e9f2] bg-white text-[#7a8499] hover:bg-[#f1f4fa]' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <div class="h-4 w-px bg-[#e5e9f2] flex-shrink-0"></div>

            {{-- Opsi Supir --}}
            <button type="submit" name="supir" value="{{ request('supir') ? '' : '1' }}"
                    class="flex-shrink-0 whitespace-nowrap rounded-full border px-3 py-1 text-xs
                           font-medium transition-colors
                           {{ request('supir')
                               ? 'border-[#3b6fd4] bg-[#3b6fd4] text-white'
                               : 'border-[#e5e9f2] bg-white text-[#7a8499] hover:bg-[#f1f4fa]' }}">
                <x-icon name="user" class="inline w-3 h-3 mr-1" />
                Ada Supir
            </button>

            {{-- Urutkan Harga --}}
            <select name="sort_harga" onchange="this.form.submit()"
                    class="flex-shrink-0 h-7 rounded-full border border-[#e5e9f2] bg-white
                           px-3 text-xs text-[#7a8499] outline-none focus:border-[#3b6fd4]">
                <option value="">Urutkan Harga</option>
                <option value="asc" @selected(request('sort_harga') == 'asc')>
                    Harga Terendah
                </option>
                <option value="desc" @selected(request('sort_harga') == 'desc')>
                    Harga Tertinggi
                </option>
            </select>

            @if(request()->hasAny(['search','status','supir','sort_harga']))
                <a href="{{ route('home') }}"
                   class="flex-shrink-0 text-xs text-[#7a8499] hover:text-[#18213a] underline underline-offset-2">
                    Reset
                </a>
            @endif
        </form>
    </div>
</div>

{{-- ── Katalog Grid ─────────────────────────────────────────── --}}
<div class="mx-auto max-w-7xl px-4 py-8">

    {{-- Result info --}}
    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-[#7a8499]">
            Menampilkan <span class="font-semibold text-[#18213a]">{{ $mobils->total() }}</span> kendaraan
        </p>
    </div>

    @if($mobils->isEmpty())
        <x-empty-state icon="car" title="Tidak ada kendaraan ditemukan"
            description="Coba ubah filter atau kata kunci pencarian Anda." />
    @else
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($mobils as $mobil)
            <div class="group overflow-hidden rounded-2xl border border-[#e5e9f2] bg-white
                        shadow-sm transition-all duration-200 hover:shadow-md hover:-translate-y-0.5">

                {{-- Foto --}}
                <div class="relative overflow-hidden">
                    @if($mobil->foto)
                        <img src="{{ Storage::url($mobil->foto) }}"
                             alt="{{ $mobil->nama }}"
                             class="h-44 w-full object-cover transition-transform duration-300
                                    group-hover:scale-105"
                             loading="lazy">
                    @else
                        <div class="grid h-44 place-items-center bg-gradient-to-br
                                    from-[#eef2fb] to-[#f4f6fb]">
                            <x-icon name="car" class="w-16 h-16 text-[#3b6fd4]/30" />
                        </div>
                    @endif

                    {{-- Status Badge --}}
                    <div class="absolute top-3 left-3">
                        @if($mobil->status === 'tersedia')
                            <span class="inline-flex items-center gap-1 rounded-full border
                                         border-green-200 bg-white/90 px-2 py-0.5
                                         text-[11px] font-medium text-green-700 backdrop-blur-sm">
                                <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                Tersedia
                            </span>
                        @elseif($mobil->status === 'disewa')
                            <span class="inline-flex items-center gap-1 rounded-full border
                                         border-blue-200 bg-white/90 px-2 py-0.5
                                         text-[11px] font-medium text-blue-700 backdrop-blur-sm">
                                Sedang Disewa
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full border
                                         border-yellow-200 bg-white/90 px-2 py-0.5
                                         text-[11px] font-medium text-yellow-700 backdrop-blur-sm">
                                Perawatan
                            </span>
                        @endif
                    </div>

                    {{-- Favorit Toggle --}}
                    @auth
                    <form method="POST"
                          action="{{ route('favorit.toggle', $mobil) }}"
                          class="absolute top-3 right-3">
                        @csrf
                        <button type="submit"
                                class="grid h-8 w-8 place-items-center rounded-full bg-white/90
                                       shadow-sm backdrop-blur-sm hover:bg-white transition-colors">
                            <x-icon name="heart"
                                class="w-4 h-4 {{ $mobil->difavoritOleh(auth()->id())
                                    ? 'text-red-500' : 'text-[#7a8499]' }}" />
                        </button>
                    </form>
                    @endauth
                </div>

                {{-- Info --}}
                <div class="p-4">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <h3 class="font-semibold text-[#18213a] truncate">{{ $mobil->nama }}</h3>
                            <p class="text-xs text-[#7a8499]">
                                {{ $mobil->merek }} &middot; {{ $mobil->tahun }}
                            </p>
                        </div>
                    </div>

                    {{-- Tags --}}
                    <div class="mt-2.5 flex flex-wrap gap-1.5">
                        @if($mobil->adaSupir())
                            <span class="inline-flex items-center gap-1 rounded-full bg-[#eef2fb]
                                         px-2 py-0.5 text-[11px] font-medium text-[#3b6fd4]">
                                <x-icon name="user" class="w-3 h-3" />
                                Ada Supir
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1 rounded-full bg-[#f1f4fa]
                                     px-2 py-0.5 text-[11px] font-medium text-[#7a8499]">
                            <x-icon name="calendar" class="w-3 h-3" />
                            {{ $mobil->tahun }}
                        </span>
                    </div>

                    {{-- Harga + CTA --}}
                    <div class="mt-3 flex items-end justify-between border-t border-[#e5e9f2] pt-3">
                        <div>
                            <p class="text-[10px] text-[#7a8499]">Mulai dari</p>
                            <p class="text-base font-bold text-[#3b6fd4]">
                                Rp {{ number_format($mobil->harga_per_hari, 0, ',', '.') }}
                            </p>
                            <p class="text-[10px] text-[#7a8499]">/ hari</p>
                        </div>
                        <a href="{{ route('mobil.show', $mobil) }}"
                           class="rounded-lg bg-[#3b6fd4] px-3 py-1.5 text-xs font-semibold
                                  text-white hover:bg-[#2e5bb8] transition-colors">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($mobils->hasPages())
            <div class="mt-8">{{ $mobils->links() }}</div>
        @endif
    @endif
</div>

{{-- ── CTA Section (publik, ajakan daftar) ────────────────── --}}
@guest
<section class="border-t border-[#e5e9f2] bg-white py-14">
    <div class="mx-auto max-w-2xl px-4 text-center">
        <div class="mx-auto mb-4 grid h-14 w-14 place-items-center rounded-2xl bg-[#eef2fb]">
            <x-icon name="shield" class="w-7 h-7 text-[#3b6fd4]" />
        </div>
        <h2 class="text-xl font-bold text-[#18213a]">Siap untuk perjalanan?</h2>
        <p class="mt-2 text-sm text-[#7a8499]">
            Daftar gratis untuk memesan, memantau status, dan menyimpan favorit Anda.
        </p>
        <div class="mt-5 flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
            {{-- Tombol Daftar --}}
            <button @click="$dispatch('open-register')"
                    class="w-full sm:w-auto rounded-xl bg-[#3b6fd4] px-6 py-2.5 text-sm
                           font-semibold text-white hover:bg-[#2e5bb8] transition-colors">
                Daftar Sekarang — Gratis
            </button>
            {{-- Tombol Masuk --}}
            <button @click="$dispatch('open-login')"
                    class="w-full sm:w-auto rounded-xl border border-[#e5e9f2] px-6 py-2.5 text-sm
                           font-medium text-[#18213a] hover:bg-[#f1f4fa] transition-colors">
                Sudah punya akun? Masuk
            </button>
        </div>
    </div>
</section>
@endguest

@if(request('modal'))
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        window.dispatchEvent(new CustomEvent('open-{{ request('modal') === 'register' ? 'register' : 'login' }}'));
    });
</script>
@endpush
@endif

@endsection