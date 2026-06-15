@extends('layouts.app')
@section('title', __('Favorit'))

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#18213a]">{{ __('Favorit Saya') }}</h1>
        <p class="mt-1 text-sm text-[#7a8499]">{{ $mobils->count() }} {{ __('kendaraan tersimpan') }}.</p>
    </div>

    @if($mobils->isEmpty())
        <x-empty-state icon="heart" title="{{ __('Belum ada favorit') }}"
            description="{{ __('Simpan mobil favorit Anda agar mudah ditemukan lagi.') }}">
            <x-slot:action>
                <a href="{{ route('home') }}"
                   class="inline-flex items-center gap-1.5 rounded-lg bg-[#3b6fd4] px-4 py-2
                          text-sm font-medium text-white hover:bg-[#2e5bb8] transition-colors">
                    {{ __('Jelajahi Katalog') }}
                </a>
            </x-slot:action>
        </x-empty-state>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($mobils as $mobil)
            <div class="overflow-hidden rounded-2xl border border-[#e5e9f2] bg-white shadow-sm
                        hover:shadow-md transition-shadow">
                @if($mobil->foto)
                    <img src="{{ Storage::url($mobil->foto) }}"
                         class="h-40 w-full object-cover" alt="{{ $mobil->nama }}" loading="lazy">
                @else
                    <div class="grid h-40 place-items-center bg-[#eef2fb]">
                        <x-icon name="car" class="w-12 h-12 text-[#3b6fd4]/40" />
                    </div>
                @endif
                <div class="p-4">
                    <h3 class="font-semibold text-[#18213a]">{{ $mobil->nama }}</h3>
                    <p class="text-xs text-[#7a8499]">{{ $mobil->merek }} &middot; {{ $mobil->tahun }}</p>
                    <div class="mt-3 flex items-center justify-between border-t border-[#e5e9f2] pt-3">
                        <p class="text-sm font-bold text-[#3b6fd4]">
                            Rp {{ number_format($mobil->harga_per_hari, 0, ',', '.') }}<span class="text-xs font-normal text-[#7a8499]">{{ __('/hari') }}</span>
                        </p>
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('mobil.show', $mobil) }}"
                               class="rounded-lg bg-[#3b6fd4] px-3 py-1.5 text-xs font-medium
                                      text-white hover:bg-[#2e5bb8] transition-colors">
                                {{ __('Detail') }}
                            </a>
                            <form method="POST" action="{{ route('favorit.toggle', $mobil) }}">
                                @csrf
                                <button type="submit"
                                        class="grid h-7 w-7 place-items-center rounded-lg border
                                               border-red-200 text-red-400 hover:bg-red-50 transition-colors">
                                    <x-icon name="x" class="w-3.5 h-3.5" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection