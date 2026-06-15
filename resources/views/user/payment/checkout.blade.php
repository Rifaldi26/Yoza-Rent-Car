@extends('layouts.app')
@section('title', __('Pilih Metode Pembayaran'))

@section('content')
<div class="mx-auto max-w-2xl px-4 py-8">

    {{-- Back --}}
    <div class="mb-4">
        <a href="{{ route('pemesanan.show', $pemesanan) }}"
           class="inline-flex items-center gap-1.5 text-sm text-[#7a8499]
                  hover:text-[#18213a] transition-colors">
            <x-icon name="arrow-left" class="w-4 h-4" />
            {{ __('Kembali ke Detail Pemesanan') }}
        </a>
    </div>

    {{-- Page Title --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#18213a]">{{ __('Pilih Metode Pembayaran') }}</h1>
        <p class="mt-1 text-sm text-[#7a8499]">
            {{ __('Setelah memilih, Anda akan diarahkan ke WhatsApp Admin untuk konfirmasi.') }}
        </p>
    </div>

    {{-- Ringkasan Pesanan --}}
    <div class="mb-6 overflow-hidden rounded-2xl border border-[#e5e9f2] bg-white shadow-sm">
        <div class="flex items-center gap-4 p-4">
            @if($pemesanan->mobil->foto)
                <img src="{{ Storage::url($pemesanan->mobil->foto) }}"
                     class="h-16 w-24 flex-shrink-0 rounded-xl object-cover"
                     alt="{{ $pemesanan->mobil->nama }}">
            @else
                <div class="grid h-16 w-24 flex-shrink-0 place-items-center
                            rounded-xl bg-[#eef2fb]">
                    <x-icon name="car" class="w-8 h-8 text-[#3b6fd4]" />
                </div>
            @endif
            <div class="min-w-0 flex-1">
                <h3 class="font-semibold text-[#18213a]">{{ $pemesanan->mobil->nama }}</h3>
                <p class="text-xs text-[#7a8499]">
                    {{ $pemesanan->tanggal_mulai->format('d M Y') }}
                    &ndash; {{ $pemesanan->tanggal_selesai->format('d M Y') }}
                    &middot; {{ $pemesanan->durasi() }} {{ __('hari') }}
                </p>
                <p class="mt-1 text-lg font-bold text-[#3b6fd4]">
                    Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Form Pilih Metode --}}
    <form method="POST"
          action="{{ route('payment.pilih-metode', $pemesanan) }}"
          x-data="{ selected: null, submitting: false }"
          @submit="submitting = true">
        @csrf

        <div class="mb-4 space-y-3">
            @foreach($metode as $key => $info)
            <label class="block cursor-pointer">
                <input type="radio" name="metode" value="{{ $key }}"
                       x-model="selected"
                       class="sr-only">
                <div :class="selected === '{{ $key }}'
                        ? 'border-[#3b6fd4] bg-[#eef2fb] ring-2 ring-[#3b6fd4]/20'
                        : 'border-[#e5e9f2] bg-white hover:border-[#3b6fd4]/40 hover:bg-[#f9fbff]'"
                     class="flex items-start gap-4 rounded-2xl border p-4 transition-all duration-150">

                    {{-- Ikon Metode --}}
                    <div :class="selected === '{{ $key }}'
                            ? 'bg-[#3b6fd4] text-white'
                            : 'bg-[#f1f4fa] text-[#7a8499]'"
                         class="grid h-11 w-11 flex-shrink-0 place-items-center
                                rounded-xl transition-colors">
                        @if($key === 'cash')
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h1.5m-1.5 0h-1.5m-9 0H6m-1.5 0H3"/>
                            </svg>
                        @elseif($key === 'transfer')
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/>
                            </svg>
                        @elseif($key === 'qris')
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z"/>
                            </svg>
                        @else
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/>
                            </svg>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-2">
                            <p class="font-semibold text-[#18213a]">{{ $info['label'] }}</p>
                            {{-- Checkmark --}}
                            <div x-show="selected === '{{ $key }}'"
                                 class="grid h-5 w-5 flex-shrink-0 place-items-center
                                        rounded-full bg-[#3b6fd4]" x-cloak>
                                <svg class="h-3 w-3 text-white" fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                                </svg>
                            </div>
                        </div>
                        <p class="mt-0.5 text-xs text-[#7a8499]">{{ $info['deskripsi'] }}</p>

                        {{-- Detail Transfer --}}
                        @if($key === 'transfer')
                        <div x-show="selected === 'transfer'" x-cloak
                             class="mt-2 rounded-xl bg-white border border-[#e5e9f2] p-3">
                            <div class="grid grid-cols-2 gap-2 text-xs">
                                <div>
                                    <p class="text-[#7a8499]">{{ __('Bank') }}</p>
                                    <p class="font-semibold text-[#18213a]">{{ $info['bank'] }}</p>
                                </div>
                                <div>
                                    <p class="text-[#7a8499]">{{ __('No. Rekening') }}</p>
                                    <p class="font-semibold font-mono text-[#18213a]">
                                        {{ $info['rekening'] }}
                                    </p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-[#7a8499]">{{ __('Atas Nama') }}</p>
                                    <p class="font-semibold text-[#18213a]">{{ $info['atas_nama'] }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- QRIS Image --}}
                        @if($key === 'qris' && Storage::disk('public')->exists($info['qris_image'] ?? ''))
                        <div x-show="selected === 'qris'" x-cloak class="mt-2">
                            <img src="{{ Storage::url($info['qris_image']) }}"
                                 alt="QRIS Yoza Rent Car"
                                 class="h-40 w-40 rounded-xl border border-[#e5e9f2] object-contain bg-white p-2">
                        </div>
                        @endif
                    </div>
                </div>
            </label>
            @endforeach
        </div>

        {{-- Instruksi dinamis --}}
        @foreach($metode as $key => $info)
        <div x-show="selected === '{{ $key }}'" x-cloak
             class="mb-4 flex items-start gap-2.5 rounded-xl border border-blue-200
                    bg-blue-50 p-3">
            <x-icon name="info" class="w-4 h-4 flex-shrink-0 mt-0.5 text-[#3b6fd4]" />
            <p class="text-xs text-[#18213a]">{{ $info['instruksi'] }}</p>
        </div>
        @endforeach

        {{-- Submit --}}
        <button type="submit"
                :disabled="!selected || submitting"
                class="flex w-full items-center justify-center gap-2 rounded-2xl bg-[#25D366]
                       py-3.5 text-sm font-bold text-white shadow-sm
                       hover:bg-[#1ebe5d] disabled:opacity-40 disabled:cursor-not-allowed
                       transition-colors">

            {{-- Loading state --}}
            <template x-if="submitting">
                <span class="flex items-center gap-2">
                    <x-icon name="spinner" class="w-4 h-4 animate-spin" />
                    {{ __('Membuka WhatsApp...') }}
                </span>
            </template>

            {{-- Default state --}}
            <template x-if="!submitting">
                <span class="flex items-center gap-2">
                    {{-- WhatsApp SVG --}}
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                    </svg>
                    <span x-text="selected ? '{{ __("Konfirmasi via WhatsApp") }}' : '{{ __("Pilih metode pembayaran dulu") }}'">
                    </span>
                </span>
            </template>
        </button>

        <p class="mt-3 text-center text-xs text-[#7a8499]">
            {{ __('Anda akan diarahkan ke WhatsApp Admin dengan pesan yang sudah terisi otomatis.') }}
        </p>
    </form>
</div>
@endsection