@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8">

    {{-- Welcome --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-[#18213a]">
            {{ __('Halo') }}, {{ auth()->user()->name }}
        </h1>
        <p class="mt-1 text-sm text-[#7a8499]">{{ __('Pantau semua aktivitas sewa Anda di sini.') }}</p>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4 mb-6">
        @php
        $cards = [
            ['label' => __('Total Pemesanan'), 'value' => $stats['total_pemesanan'], 'icon' => 'calendar', 'color' => 'blue'],
            ['label' => __('Aktif'),           'value' => $stats['aktif'],           'icon' => 'check-circle','color' => 'green'],
            ['label' => __('Selesai'),         'value' => $stats['selesai'],         'icon' => 'star',      'color' => 'purple'],
            ['label' => __('Menunggu Bayar'),  'value' => $stats['pending'],         'icon' => 'clock',     'color' => 'orange'],
        ];
        $colorMap = [
            'blue'   => 'bg-[#eef2fb] text-[#3b6fd4]',
            'green'  => 'bg-green-50 text-green-600',
            'purple' => 'bg-purple-50 text-purple-600',
            'orange' => 'bg-orange-50 text-orange-600',
        ];
        @endphp
        @foreach($cards as $c)
        <div class="rounded-xl border border-[#e5e9f2] bg-white p-4 shadow-sm">
            <div class="flex items-start justify-between">
                <p class="text-xs font-medium text-[#7a8499]">{{ $c['label'] }}</p>
                <div class="grid h-8 w-8 place-items-center rounded-lg {{ $colorMap[$c['color']] }}">
                    <x-icon :name="$c['icon']" class="w-4 h-4" />
                </div>
            </div>
            <p class="mt-3 text-2xl font-bold text-[#18213a]">{{ $c['value'] }}</p>
        </div>
        @endforeach
    </div>

    <div class="grid gap-4 lg:grid-cols-3">

        {{-- Pemesanan Terbaru --}}
        <div class="lg:col-span-2 rounded-xl border border-[#e5e9f2] bg-white p-4 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-[#18213a]">{{ __('Pemesanan Terbaru') }}</h2>
                <a href="{{ route('pemesanan.index') }}"
                   class="text-xs font-medium text-[#3b6fd4] hover:underline">{{ __('Lihat semua') }}</a>
            </div>

            @if($pemesanan_terbaru->isEmpty())
                <x-empty-state icon="calendar" title="{{ __('Belum ada pemesanan') }}"
                    description="{{ __('Mulai sewa mobil pertama Anda sekarang.') }}">
                    <x-slot:action>
                        <a href="{{ route('home') }}"
                           class="inline-flex items-center gap-1.5 rounded-lg bg-[#3b6fd4] px-4 py-2
                                  text-sm font-medium text-white hover:bg-[#2e5bb8] transition-colors">
                            <x-icon name="car" class="w-4 h-4" />
                            {{ __('Lihat Katalog') }}
                        </a>
                    </x-slot:action>
                </x-empty-state>
            @else
                <div class="space-y-3">
                    @foreach($pemesanan_terbaru as $p)
                    <a href="{{ route('pemesanan.show', $p) }}"
                       class="flex items-start gap-3 rounded-xl border border-[#e5e9f2] p-3
                              hover:bg-[#f4f6fb] transition-colors group">
                        <div class="grid h-10 w-10 flex-shrink-0 place-items-center rounded-xl bg-[#eef2fb]">
                            <x-icon name="car" class="w-5 h-5 text-[#3b6fd4]" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <p class="text-sm font-semibold text-[#18213a]">
                                        {{ $p->mobil->nama }}
                                    </p>
                                    <p class="text-xs text-[#7a8499]">
                                        {{ $p->tanggal_mulai->format('d M') }}
                                        &ndash; {{ $p->tanggal_selesai->format('d M Y') }}
                                    </p>
                                </div>
                                <x-status-badge :status="$p->status">
                                    {{ $p->labelStatus() }}
                                </x-status-badge>
                            </div>
                            <div class="mt-1 flex items-center justify-between">
                                <p class="text-xs text-[#7a8499]">
                                {{ $p->opsi_supir ? __('+ Supir') : __('Self-Drive') }}
                                &middot; {{ $p->durasi() }} {{ __('hari') }}
                                </p>
                                <p class="text-sm font-bold text-[#18213a]">
                                    Rp {{ number_format($p->total_harga, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        <x-icon name="chevron-right"
                            class="w-4 h-4 text-[#7a8499] flex-shrink-0 mt-3 opacity-0 group-hover:opacity-100 transition-opacity" />
                    </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Aksi Cepat --}}
        <div class="space-y-4">
            <div class="rounded-xl border border-[#e5e9f2] bg-white p-4 shadow-sm">
                <h3 class="text-sm font-semibold text-[#18213a] mb-3">{{ __('Aksi Cepat') }}</h3>
                <div class="grid grid-cols-2 gap-2">
                    @php
                    $quickActions = [
                        ['href' => route('home'),              'icon' => 'car',      'label' => __('Sewa Mobil')],
                        ['href' => route('pemesanan.index'),   'icon' => 'calendar', 'label' => __('Riwayat')],
                        ['href' => route('favorit.index'),     'icon' => 'heart',    'label' => __('Favorit')],
                        ['href' => route('chat.index'),        'icon' => 'chat',     'label' => __('Chat Admin')],
                    ];
                    @endphp
                    @foreach($quickActions as $qa)
                    <a href="{{ $qa['href'] }}"
                       class="group flex flex-col items-center gap-1.5 rounded-xl border border-[#e5e9f2]
                              bg-[#f4f6fb] p-3 text-center hover:bg-[#eef2fb] hover:border-[#3b6fd4]/30
                              transition-colors">
                        <x-icon :name="$qa['icon']"
                            class="w-5 h-5 text-[#7a8499] group-hover:text-[#3b6fd4]" />
                        <span class="text-xs font-medium text-[#7a8499] group-hover:text-[#3b6fd4]">
                            {{ $qa['label'] }}
                        </span>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Notifikasi Terbaru --}}
            <div class="rounded-xl border border-[#e5e9f2] bg-white p-4 shadow-sm">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-[#18213a]">{{ __('Notifikasi') }}</h3>
                    <a href="{{ route('notifikasi.index') }}"
                       class="text-xs text-[#3b6fd4] hover:underline">{{ __('Semua') }}</a>
                </div>
                @php
                    $notifs = auth()->user()->notifikasis()
                        ->latest()->take(3)->get();
                @endphp
                @forelse($notifs as $notif)
                <div class="flex gap-2.5 py-2 border-b border-[#e5e9f2] last:border-0">
                    <div class="grid h-7 w-7 flex-shrink-0 place-items-center rounded-full
                        {{ $notif->tipe === 'success' ? 'bg-green-100 text-green-600'
                           : ($notif->tipe === 'warning' ? 'bg-yellow-100 text-yellow-600'
                           : 'bg-[#eef2fb] text-[#3b6fd4]') }}">
                        <x-icon name="{{ $notif->tipe === 'success' ? 'check-circle'
                            : ($notif->tipe === 'warning' ? 'warning' : 'info') }}"
                            class="w-3.5 h-3.5" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-[#18213a] truncate">{{ $notif->judul }}</p>
                        <p class="text-[10px] text-[#7a8499]">{{ $notif->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                    <p class="py-4 text-center text-xs text-[#7a8499]">{{ __('Tidak ada notifikasi') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection