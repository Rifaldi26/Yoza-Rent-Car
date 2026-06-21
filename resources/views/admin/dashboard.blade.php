@extends('layouts.admin')

@section('title', __('Beranda'))

@section('content')

{{-- Page Header --}}
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-gray-900">
            {{ __('Selamat datang kembali') }}
        </h1>
        <p class="mt-1 text-sm text-gray-500">
            {{ now()->translatedFormat('l, d F Y') }} &mdash; {{ __('Berikut ringkasan operasional hari ini.') }}
        </p>
    </div>
    <span class="inline-flex w-fit items-center gap-1.5 rounded-full border border-green-200
                 bg-green-50 px-3 py-1 text-xs font-medium text-green-700">
        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-green-500"></span>
        {{ __('Sistem aktif') }}
    </span>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">

    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex items-start justify-between">
            <p class="text-xs font-medium text-gray-500">{{ __('Pendapatan Bulan Ini') }}</p>
            <div class="grid h-8 w-8 place-items-center rounded-lg bg-blue-50 text-blue-600">
                <x-icon name="trending-up" class="w-4 h-4" />
            </div>
        </div>
        <p class="mt-3 text-xl font-bold text-gray-900 sm:text-2xl">
            Rp {{ number_format($stats['pendapatan_bulan'], 0, ',', '.') }}
        </p>
        <p class="mt-1 inline-flex items-center gap-0.5 text-xs font-medium text-green-600">
            <x-icon name="trending-up" class="w-3 h-3" />
            {{ __('Bulan berjalan') }}
        </p>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex items-start justify-between">
            <p class="text-xs font-medium text-gray-500">{{ __('Menunggu Konfirmasi') }}</p>
            <div class="grid h-8 w-8 place-items-center rounded-lg bg-orange-50 text-orange-600">
                <x-icon name="clock" class="w-4 h-4" />
            </div>
        </div>
        <p class="mt-3 text-xl font-bold text-gray-900 sm:text-2xl">
            {{ $stats['menunggu_konfirmasi'] }}
        </p>
        @if($stats['menunggu_konfirmasi'] > 0)
            <p class="mt-1 text-xs font-medium text-orange-600">{{ __('Perlu tindakan segera') }}</p>
        @else
            <p class="mt-1 text-xs font-medium text-gray-400">{{ __('Tidak ada antrian') }}</p>
        @endif
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex items-start justify-between">
            <p class="text-xs font-medium text-gray-500">{{ __('Armada Tersedia') }}</p>
            <div class="grid h-8 w-8 place-items-center rounded-lg bg-green-50 text-green-600">
                <x-icon name="car" class="w-4 h-4" />
            </div>
        </div>
        <p class="mt-3 text-xl font-bold text-gray-900 sm:text-2xl">
            {{ $stats['mobil_tersedia'] }}
            <span class="text-base font-medium text-gray-400">/ {{ $stats['total_mobil'] }}</span>
        </p>
        <p class="mt-1 inline-flex items-center gap-1.5 text-xs font-medium text-gray-500">
            <span class="h-2 w-2 rounded-full bg-yellow-400"></span>
            {{ $stats['mobil_perawatan'] }} {{ __('dalam perawatan') }}
        </p>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex items-start justify-between">
            <p class="text-xs font-medium text-gray-500">{{ __('Total Pelanggan') }}</p>
            <div class="grid h-8 w-8 place-items-center rounded-lg bg-purple-50 text-purple-600">
                <x-icon name="users" class="w-4 h-4" />
            </div>
        </div>
        <p class="mt-3 text-xl font-bold text-gray-900 sm:text-2xl">{{ $stats['total_user'] }}</p>
        <p class="mt-1 text-xs font-medium text-gray-400">{{ __('Pengguna terdaftar') }}</p>
    </div>

</div>

{{-- Main Grid --}}
<div class="mt-6 grid gap-4 lg:grid-cols-3">

    {{-- Tabel Pemesanan --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm lg:col-span-2">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-900">{{ __('Pemesanan Terbaru') }}</h2>
            <a href="{{ route('admin.pemesanan.index') }}"
               class="text-xs font-medium text-blue-600 hover:underline">
                {{ __('Lihat semua') }}
            </a>
        </div>

        @if($pemesanan_terbaru->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <div class="grid h-12 w-12 place-items-center rounded-full bg-gray-100 mb-3">
                    <x-icon name="check-circle" class="w-6 h-6 text-gray-400" />
                </div>
                <p class="text-sm font-medium text-gray-900">{{ __('Tidak ada antrian') }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ __('Semua pemesanan sudah diproses.') }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-left text-xs font-medium
                                   uppercase tracking-wider text-gray-500">
                            <th class="py-2 pr-3">{{ __('ID') }}</th>
                            <th class="py-2 pr-3">{{ __('Pelanggan') }}</th>
                            <th class="py-2 pr-3 hidden md:table-cell">{{ __('Mobil') }}</th>
                            <th class="py-2 pr-3">{{ __('Status') }}</th>
                            <th class="py-2 pr-3 text-right">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pemesanan_terbaru as $p)
                        <tr class="border-b border-gray-100 last:border-0
                                   hover:bg-gray-50 transition-colors">
                            <td class="py-3 pr-3 font-mono text-xs text-gray-400">
                                {{ $p->user->kode_pelanggan }}
                            </td>
                            <td class="py-3 pr-3 font-medium text-gray-900">
                                {{ $p->user->name }}
                            </td>
                            <td class="py-3 pr-3 text-gray-500 hidden md:table-cell">
                                {{ $p->mobil->nama }}
                            </td>
                            <td class="py-3 pr-3">
                                @php
                                $badgeClass = match($p->status) {
                                    'pending'                   => 'border-yellow-300 bg-yellow-50 text-yellow-700',
                                    'menunggu_konfirmasi_admin' => 'border-blue-200 bg-blue-50 text-blue-700',
                                    'dikonfirmasi'              => 'border-blue-300 bg-blue-100 text-blue-700',
                                    'selesai'                   => 'border-green-300 bg-green-50 text-green-700',
                                    'dibatalkan'                => 'border-red-300 bg-red-50 text-red-600',
                                    'kadaluarsa'                => 'border-orange-300 bg-orange-50 text-orange-600',
                                    default                     => 'border-gray-200 bg-gray-50 text-gray-500',
                                };
                                @endphp
                                <span class="inline-flex items-center rounded-full border px-2 py-0.5
                                             text-[11px] font-medium {{ $badgeClass }}">
                                    {{ $p->labelStatus() }}
                                </span>
                            </td>
                            <td class="py-3 pr-3 text-right font-medium tabular-nums text-gray-900">
                                Rp {{ number_format($p->total_harga, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Status Armada --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <h2 class="mb-4 text-sm font-semibold text-gray-900">{{ __('Status Armada') }}</h2>

        @php
        $total        = max($stats['total_mobil'], 1);
        $fleet = [
            ['label'=> __('Tersedia'),  'count'=>$stats['mobil_tersedia'],  'color'=>'bg-green-500',  'pct'=> round($stats['mobil_tersedia']/$total*100)],
            ['label'=> __('Disewa'),    'count'=>$stats['mobil_disewa'],    'color'=>'bg-blue-600',   'pct'=> round($stats['mobil_disewa']/$total*100)],
            ['label'=> __('Perawatan'), 'count'=>$stats['mobil_perawatan'], 'color'=>'bg-yellow-400', 'pct'=> round($stats['mobil_perawatan']/$total*100)],
        ];
        @endphp

        <div class="space-y-4">
            @foreach($fleet as $row)
            <div>
                <div class="mb-1.5 flex items-center justify-between text-xs">
                    <span class="font-medium text-gray-700">{{ $row['label'] }}</span>
                    <span class="tabular-nums text-gray-400">
                        {{ $row['count'] }} / {{ $stats['total_mobil'] }}
                    </span>
                </div>
                <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                    <div class="h-full rounded-full {{ $row['color'] }} transition-all duration-700"
                         style="width: {{ $row['pct'] }}%"></div>
                </div>
            </div>
            @endforeach
        </div>

        <a href="{{ route('admin.mobil.index') }}"
           class="mt-5 flex items-center gap-1 text-xs font-medium text-blue-600 hover:underline">
            {{ __('Kelola armada') }}
            <x-icon name="chevron-right" class="w-3 h-3" />
        </a>
    </div>

</div>

@endsection
