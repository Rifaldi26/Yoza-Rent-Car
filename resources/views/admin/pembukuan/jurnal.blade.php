@extends('layouts.admin')
@section('title', 'Jurnal Harian')

@section('content')

<x-page-header title="Jurnal Harian" description="Catatan double-entry otomatis dari setiap transaksi." />

{{-- Filter --}}
<form method="GET"
      class="mb-4 flex flex-wrap gap-2 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
    <x-input name="tanggal_dari" type="date" label="Dari"
        :value="request('tanggal_dari')" class="flex-1 min-w-[140px]" />
    <x-input name="tanggal_sampai" type="date" label="Sampai"
        :value="request('tanggal_sampai')" class="flex-1 min-w-[140px]" />
    <x-select name="account_id" label="Akun"
        :options="$accounts->pluck('nama','id')->toArray()"
        :selected="request('account_id')"
        placeholder="Semua akun"
        class="flex-1 min-w-[160px]" />
    <div class="flex items-end">
        <button type="submit"
                class="h-10 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white
                       hover:bg-blue-700 transition-colors">
            Filter
        </button>
    </div>
</form>

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Ref</th>
                    <th class="px-4 py-3">Keterangan</th>
                    <th class="px-4 py-3 text-right">Debit</th>
                    <th class="px-4 py-3 text-right">Kredit</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $entry)
                <tr class="border-t border-gray-100 align-top hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-xs text-gray-400 whitespace-nowrap">
                        {{ $entry->date->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">
                        @if($entry->pemesanan_id)
                            #{{ $entry->pemesanan_id }}
                        @else
                            EXP
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-900">{{ $entry->account->nama }}</p>
                        @if($entry->description)
                            <p class="text-xs text-gray-400 mt-0.5">{{ $entry->description }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right font-medium tabular-nums text-gray-900">
                        @if($entry->debit > 0)
                            Rp {{ number_format($entry->debit, 0, ',', '.') }}
                        @else
                            <span class="text-gray-300">&mdash;</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right font-medium tabular-nums text-gray-900">
                        @if($entry->credit > 0)
                            Rp {{ number_format($entry->credit, 0, ',', '.') }}
                        @else
                            <span class="text-gray-300">&mdash;</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <x-empty-state icon="book-open" title="Tidak ada entri jurnal"
                            description="Jurnal akan dibuat otomatis saat ada transaksi." />
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