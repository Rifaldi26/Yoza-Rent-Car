@extends('layouts.app')
@section('title', 'Pemesanan Saya')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#18213a]">{{ __('Pemesanan Saya') }}</h1>
        <p class="mt-1 text-sm text-[#7a8499]">{{ __('Riwayat dan status semua pemesanan Anda.') }}</p>
    </div>

    {{-- Status Filter --}}
    <div class="mb-4 flex gap-1.5 overflow-x-auto pb-1">
        @php
        $tabs = ['' => __('Semua'),'pending' => __('Menunggu Bayar'),'menunggu_konfirmasi_admin' => __('Menunggu Konfirmasi'),
                 'dikonfirmasi' => __('Dikonfirmasi'),'selesai' => __('Selesai'),'dibatalkan' => __('Dibatalkan')];
        @endphp
        @foreach($tabs as $val => $label)
            <a href="{{ route('pemesanan.index', array_filter(['status' => $val])) }}"
               class="whitespace-nowrap rounded-full border px-3 py-1 text-xs font-medium transition-colors
                      {{ request('status', '') === $val
                          ? 'border-primary-600 bg-primary-600 text-white'
                          : 'border-[#e5e9f2] bg-white text-[#7a8499] hover:bg-[#f1f4fa]' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @forelse($pemesanans as $p)
    <div class="mb-3 overflow-hidden rounded-2xl border border-[#e5e9f2] bg-white shadow-sm">

        {{-- Header Card --}}
        <div class="flex items-center justify-between border-b border-[#e5e9f2] px-4 py-3">
            <div class="flex items-center gap-2">
                <span class="font-mono text-xs text-[#7a8499]">#{{ $p->id }}</span>
                <x-status-badge :status="$p->status">{{ $p->labelStatus() }}</x-status-badge>
            </div>
            <span class="text-xs text-[#7a8499]">{{ $p->created_at->format('d M Y') }}</span>
        </div>

        {{-- Body --}}
        <div class="flex items-start gap-4 px-4 py-3">
            <div class="grid h-12 w-12 flex-shrink-0 place-items-center rounded-xl bg-primary-50">
                <x-icon name="car" class="w-6 h-6 text-primary-600" />
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-[#18213a]">{{ $p->mobil->nama }}</p>
                <p class="text-sm text-[#7a8499]">
                    {{ $p->tanggal_mulai->format('d M Y') }}
                    @if(!$p->adalah12Jam())
                        &ndash; {{ $p->tanggal_selesai->format('d M Y') }}
                        &middot; {{ $p->durasi() }} {{ __('hari') }}
                    @else
                        &middot; {{ __('Sewa 12 Jam') }}
                    @endif
                </p>
                <p class="text-xs text-[#7a8499]">{{ $p->opsi_supir ? __('Dengan Supir') : __('Self-Drive') }}</p>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="text-base font-bold text-[#18213a]">
                    Rp {{ number_format($p->total_harga, 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Footer Aksi --}}
        <div class="flex items-center justify-between border-t border-[#e5e9f2] px-4 py-2.5">
            <div class="flex gap-2">
                @if($p->status === 'pending')
                    <a href="{{ route('payment.checkout', $p) }}"
                       class="rounded-lg bg-primary-600 px-3 py-1.5 text-xs font-medium text-white
                              hover:bg-primary-700 transition-colors">
                        {{ __('Bayar Sekarang') }}
                    </a>
                    <form method="POST" action="{{ route('pemesanan.cancel', $p) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="rounded-lg border border-[#e5e9f2] px-3 py-1.5 text-xs
                                       font-medium text-[#7a8499] hover:bg-[#f1f4fa] transition-colors">
                            {{ __('Batalkan') }}
                        </button>
                    </form>
                @endif
                @if($p->payment?->isPaid())
                    <a href="{{ route('payment.invoice', $p) }}"
                       class="rounded-lg border border-[#e5e9f2] px-3 py-1.5 text-xs font-medium
                              text-[#7a8499] hover:bg-[#f1f4fa] transition-colors inline-flex items-center gap-1">
                        <x-icon name="download" class="w-3.5 h-3.5" />
                        {{ __('Invoice') }}
                    </a>
                @endif
            </div>
            <a href="{{ route('pemesanan.show', $p) }}"
               class="text-xs font-medium text-primary-600 hover:underline">
                {{ __('Detail') }}
            </a>
        </div>
    </div>
    @empty
        <x-empty-state icon="calendar" title="{{ __('Belum ada pemesanan') }}"
            description="{{ __('Belum ada pemesanan dengan status ini.') }}">
            <x-slot:action>
                <a href="{{ route('home') }}"
                   class="inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-4 py-2
                          text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                    {{ __('Lihat Katalog Mobil') }}
                </a>
            </x-slot:action>
        </x-empty-state>
    @endforelse

    @if($pemesanans->hasPages())
        <div class="mt-4">{{ $pemesanans->links() }}</div>
    @endif
</div>
@endsection