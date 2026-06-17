@extends('layouts.admin')
@section('title', __('Jurnal Harian'))

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.pembukuan.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors">
        <x-icon name="arrow-left" class="w-4 h-4" />
        {{ __('Kembali ke Pembukuan') }}
    </a>
</div>

<x-page-header title="{{ __('Jurnal Harian') }}"
    description="{{ __('Catatan transaksi harian.') }}" />

{{-- Filter Tanggal --}}
<form method="GET" class="mb-4 flex flex-wrap items-end gap-3">
    <x-input name="date_from" label="{{ __('Dari Tanggal') }}" type="date"
        :value="request('date_from', now()->startOfMonth()->format('Y-m-d'))" />
    <x-input name="date_to" label="{{ __('Sampai Tanggal') }}" type="date"
        :value="request('date_to', now()->format('Y-m-d'))" />
    <button type="submit"
            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white
                   hover:bg-blue-700 transition-colors">
        {{ __('Filter') }}
    </button>
</form>

{{-- Tabel --}}
<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                    <th class="px-4 py-3">{{ __('Tanggal') }}</th>
                    <th class="px-4 py-3">{{ __('Akun') }}</th>
                    <th class="px-4 py-3">{{ __('Keterangan') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Debit') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Kredit') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $j)
                <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 whitespace-nowrap text-gray-500">
                        {{ $j->date->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $j->account->nama }}</td>
                    <td class="px-4 py-3 text-gray-500 max-w-xs truncate">{{ $j->description }}</td>
                    <td class="px-4 py-3 text-right tabular-nums text-gray-900">
                        @if($j->debit > 0)
                            Rp {{ number_format($j->debit, 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right tabular-nums text-gray-900">
                        @if($j->credit > 0)
                            Rp {{ number_format($j->credit, 0, ',', '.') }}
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <x-empty-state icon="book-open" title="{{ __('Belum ada jurnal') }}"
                            description="{{ __('Belum ada transaksi pada periode ini.') }}" />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($entries->hasPages())
        <div class="border-t border-gray-100 px-4 py-3">
            {{ $entries->links() }}
        </div>
    @endif
</div>

@endsection