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
    <x-input name="tanggal_dari" label="{{ __('Dari Tanggal') }}" type="date"
        :value="request('tanggal_dari', now()->startOfMonth()->format('Y-m-d'))" />
    <x-input name="tanggal_sampai" label="{{ __('Sampai Tanggal') }}" type="date"
        :value="request('tanggal_sampai', now()->format('Y-m-d'))" />
    <button type="submit"
            class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white
                   hover:bg-primary-700 transition-colors">
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
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $j->account->nama_translated }}</td>
                    <td class="px-4 py-3 text-gray-500 max-w-xs truncate">
                        @if($j->pemesanan && $j->pemesanan->mobil)
                            {{ $j->pemesanan->mobil->nama }} - {{ $j->pemesanan->mobil->plat_nomor }}
                        @else
                            {{ $j->description }}
                        @endif
                    </td>
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
            @if($entries->isNotEmpty())
            <tfoot>
                @php
                    $selisihJurnal = $totalDebit - $totalCredit;
                    $seimbangJurnal = abs($selisihJurnal) < 0.01;
                @endphp
                <tr class="border-t-2 border-gray-200 bg-gray-50 font-semibold text-gray-900">
                    <td class="px-4 py-3" colspan="3">
                        {{ __('Total') }}
                    </td>
                    <td class="px-4 py-3 text-right tabular-nums">
                        Rp {{ number_format($totalDebit, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-right tabular-nums">
                        Rp {{ number_format($totalCredit, 0, ',', '.') }}
                    </td>
                </tr>
                <tr class="bg-gray-50">
                    <td class="px-4 py-3 text-right" colspan="5">
                        <span class="inline-flex items-center rounded-full border px-2 py-0.5
                                     text-[11px] font-medium
                                     {{ $seimbangJurnal
                                         ? 'border-green-200 bg-green-50 text-green-700'
                                         : 'border-red-200 bg-red-50 text-red-700' }}">
                            {{ $seimbangJurnal
                                ? __('Seimbang')
                                : __('Selisih Rp') . ' ' . number_format(abs($selisihJurnal), 0, ',', '.') }}
                        </span>
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
 
    @if($entries->hasPages())
        <div class="border-t border-gray-100 px-4 py-3">
            {{ $entries->links() }}
        </div>
    @endif
</div>
 
@endsection