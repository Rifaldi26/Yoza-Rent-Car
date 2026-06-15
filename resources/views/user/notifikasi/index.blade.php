@extends('layouts.app')
@section('title', __('Notifikasi'))

@section('content')
<div class="mx-auto max-w-2xl px-4 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#18213a]">{{ __('Notifikasi') }}</h1>
            <p class="mt-1 text-sm text-[#7a8499]">{{ __('Aktivitas dan pembaruan pesanan Anda.') }}</p>
        </div>
        @if($notifikasis->total() > 0)
        <form method="POST" action="{{ route('notifikasi.hapus-semua') }}">
            @csrf @method('DELETE')
            <button type="submit"
                    class="rounded-lg border border-[#e5e9f2] bg-white px-3 py-1.5 text-xs
                           font-medium text-[#7a8499] hover:bg-[#f1f4fa] transition-colors">
                {{ __('Hapus Semua') }}
            </button>
        </form>
        @endif
    </div>

    <div class="overflow-hidden rounded-2xl border border-[#e5e9f2] bg-white shadow-sm">
        @forelse($notifikasis as $notif)
        @php
        $style = match($notif->tipe) {
            'success' => ['cls' => 'bg-green-100 text-green-600', 'icon' => 'check-circle'],
            'warning' => ['cls' => 'bg-yellow-100 text-yellow-600', 'icon' => 'warning'],
            default   => ['cls' => 'bg-[#eef2fb] text-[#3b6fd4]', 'icon' => 'info'],
        };
        @endphp
        <div class="flex items-start gap-3 border-b border-[#e5e9f2] p-4 last:border-0
                    {{ !$notif->dibaca ? 'bg-[#f4f6fb]' : '' }} hover:bg-[#f4f6fb] transition-colors">
            <div class="grid h-10 w-10 flex-shrink-0 place-items-center rounded-full {{ $style['cls'] }}">
                <x-icon :name="$style['icon']" class="w-5 h-5" />
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2">
                    <p class="text-sm font-semibold text-[#18213a]">{{ $notif->judul }}</p>
                    @if(!$notif->dibaca)
                        <span class="h-1.5 w-1.5 flex-shrink-0 rounded-full bg-[#3b6fd4]"></span>
                    @endif
                </div>
                <p class="mt-0.5 text-sm text-[#7a8499]">{{ $notif->pesan }}</p>
                <p class="mt-1 text-xs text-[#aab0bf]">{{ $notif->created_at->diffForHumans() }}</p>
            </div>
            @if(!$notif->dibaca)
            <form method="POST" action="{{ route('notifikasi.baca', $notif) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="text-xs text-[#3b6fd4] hover:underline whitespace-nowrap flex-shrink-0">
                    {{ __('Baca') }}
                </button>
            </form>
            @endif
        </div>
        @empty
            <x-empty-state icon="bell" title="{{ __('Tidak ada notifikasi') }}"
                description="{{ __('Semua notifikasi akan muncul di sini.') }}" />
        @endforelse

        @if($notifikasis->hasPages())
            <div class="border-t border-[#e5e9f2] px-4 py-3">
                {{ $notifikasis->links() }}
            </div>
        @endif
    </div>
</div>
@endsection