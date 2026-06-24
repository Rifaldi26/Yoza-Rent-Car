@extends('layouts.admin')
@section('title', __('Laporan Laba Rugi'))

@section('content')

<x-page-header title="{{ __('Laporan Laba Rugi') }}" description="{{ __('Ringkasan pendapatan, pengeluaran, dan arus kas per tahun.') }}">
    <x-slot:actions>
        <a href="{{ route('admin.pembukuan.index') }}"
           class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white
                  px-3 py-1.5 text-sm font-medium hover:bg-gray-50 transition-colors">
            <x-icon name="arrow-left" class="w-4 h-4" />
            {{ __('Kembali') }}
        </a>
        <a href="{{ route('admin.pembukuan.export', ['tahun' => $tahun]) }}"
           class="inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-3 py-1.5
                  text-sm font-medium text-white hover:bg-primary-700 transition-colors">
            <x-icon name="download" class="w-4 h-4" />
            {{ __('Unduh PDF') }}
        </a>
    </x-slot:actions>
</x-page-header>

{{-- Filter tahun --}}
<form method="GET" action="{{ route('admin.pembukuan.laporan') }}"
      class="mb-6 flex flex-wrap items-end gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
    <div class="w-32">
        <x-select name="tahun" label="{{ __('Tahun') }}" :selected="$tahun" :placeholder="null"
            :options="collect(range(now()->year, now()->year - 4))->mapWithKeys(fn ($y) => [$y => $y])->toArray()" />
    </div>
    <button type="submit"
            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white
                   px-3 py-2.5 text-sm font-medium hover:bg-gray-50 transition-colors">
        <x-icon name="search" class="w-4 h-4" />
        {{ __('Terapkan') }}
    </button>
</form>

{{-- Ringkasan --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-6">
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="mb-3 grid h-10 w-10 place-items-center rounded-lg bg-green-100 text-green-600">
            <x-icon name="trending-up" class="w-5 h-5" />
        </div>
        <p class="text-xs font-medium text-gray-500">{{ __('Total Pendapatan') }}</p>
        <p class="mt-1 text-2xl font-bold tabular-nums text-gray-900">
            Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
        </p>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="mb-3 grid h-10 w-10 place-items-center rounded-lg bg-yellow-100 text-yellow-600">
            <x-icon name="trending-down" class="w-5 h-5" />
        </div>
        <p class="text-xs font-medium text-gray-500">{{ __('Total Pengeluaran') }}</p>
        <p class="mt-1 text-2xl font-bold tabular-nums text-gray-900">
            Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
        </p>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm
                {{ $labaRugi >= 0 ? 'border-green-200' : 'border-red-200' }}">
        <div class="mb-3 grid h-10 w-10 place-items-center rounded-lg
                    {{ $labaRugi >= 0 ? 'bg-blue-100 text-blue-600' : 'bg-red-100 text-red-600' }}">
            <x-icon name="chart-bar" class="w-5 h-5" />
        </div>
        <p class="text-xs font-medium text-gray-500">{{ __('Laba / Rugi') }}</p>
        <p class="mt-1 text-2xl font-bold tabular-nums
                  {{ $labaRugi >= 0 ? 'text-green-600' : 'text-red-600' }}">
            Rp {{ number_format(abs($labaRugi), 0, ',', '.') }}
        </p>
    </div>
</div>

{{-- Rincian Pendapatan & Pengeluaran --}}
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-6">
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-4 py-3">
            <h2 class="text-sm font-semibold text-gray-900">{{ __('Rincian Pendapatan') }}</h2>
        </div>
        <table class="w-full text-sm">
            <tbody>
                @forelse($pendapatan as $a)
                <tr class="border-t border-gray-100">
                    <td class="px-4 py-2.5 text-gray-500">{{ __($a['nama']) }}</td>
                    <td class="px-4 py-2.5 text-right tabular-nums text-gray-900">
                        Rp {{ number_format($a['total'], 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="2" class="px-4 py-6 text-center text-sm text-gray-400">{{ __('Belum ada data.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-4 py-3">
            <h2 class="text-sm font-semibold text-gray-900">{{ __('Rincian Pengeluaran') }}</h2>
        </div>
        <table class="w-full text-sm">
            <tbody>
                @forelse($pengeluaran as $a)
                <tr class="border-t border-gray-100">
                    <td class="px-4 py-2.5 text-gray-500">{{ __($a['nama']) }}</td>
                    <td class="px-4 py-2.5 text-right tabular-nums text-gray-900">
                        Rp {{ number_format($a['total'], 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="2" class="px-4 py-6 text-center text-sm text-gray-400">{{ __('Belum ada data.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Arus Kas per Bulan --}}
<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="border-b border-gray-100 px-4 py-3">
        <h2 class="text-sm font-semibold text-gray-900">{{ __('Arus Kas Bulanan') }} {{ $tahun }}</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                    <th class="px-4 py-3">{{ __('Bulan') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Kas Masuk') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Kas Keluar') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Neto') }}</th>
                </tr>
            </thead>
            <tbody>
                @php
                $namaBulan = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
                              7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                @endphp
                @foreach($arusKas as $row)
                <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-2.5 text-gray-900">{{ $namaBulan[$row['bulan']] }}</td>
                    <td class="px-4 py-2.5 text-right tabular-nums text-green-600">
                        Rp {{ number_format($row['masuk'], 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-2.5 text-right tabular-nums text-red-600">
                        Rp {{ number_format($row['keluar'], 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-2.5 text-right tabular-nums font-medium
                               {{ $row['neto'] >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                        Rp {{ number_format($row['neto'], 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection