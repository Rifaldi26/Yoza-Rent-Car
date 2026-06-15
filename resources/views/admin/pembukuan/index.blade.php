@extends('layouts.admin')
@section('title', __('Pembukuan'))

@section('content')

<x-page-header title="{{ __('Pembukuan') }}" description="{{ __('Chart of Accounts dan ringkasan penjualan.') }}">
    <x-slot:actions>
        <a href="{{ route('admin.pembukuan.jurnal') }}"
           class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white
                  px-3 py-1.5 text-sm font-medium hover:bg-gray-50 transition-colors">
            <x-icon name="book-open" class="w-4 h-4" />
            {{ __('Jurnal Harian') }}
        </a>
        <button @click="$dispatch('open-modal-pengeluaran')"
                class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-1.5
                       text-sm font-medium text-white hover:bg-blue-700 transition-colors">
            <x-icon name="plus" class="w-4 h-4" />
            {{ __('Input Pengeluaran') }}
        </button>
    </x-slot:actions>
</x-page-header>

{{-- Ringkasan --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-6">
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="mb-3 grid h-10 w-10 place-items-center rounded-lg bg-green-100 text-green-600">
            <x-icon name="trending-up" class="w-5 h-5" />
        </div>
        <p class="text-xs font-medium text-gray-500">{{ __('Total Pendapatan') }}</p>
        <p class="mt-1 text-2xl font-bold tabular-nums text-gray-900">
            Rp {{ number_format($ringkasan['total_pendapatan'], 0, ',', '.') }}
        </p>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="mb-3 grid h-10 w-10 place-items-center rounded-lg bg-yellow-100 text-yellow-600">
            <x-icon name="trending-down" class="w-5 h-5" />
        </div>
        <p class="text-xs font-medium text-gray-500">{{ __('Total Pengeluaran') }}</p>
        <p class="mt-1 text-2xl font-bold tabular-nums text-gray-900">
            Rp {{ number_format($ringkasan['total_pengeluaran'], 0, ',', '.') }}
        </p>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm
                {{ $ringkasan['laba_rugi'] >= 0 ? 'border-green-200' : 'border-red-200' }}">
        <div class="mb-3 grid h-10 w-10 place-items-center rounded-lg
                    {{ $ringkasan['laba_rugi'] >= 0 ? 'bg-blue-100 text-blue-600' : 'bg-red-100 text-red-600' }}">
            <x-icon name="chart-bar" class="w-5 h-5" />
        </div>
        <p class="text-xs font-medium text-gray-500">{{ __('Laba / Rugi') }}</p>
        <p class="mt-1 text-2xl font-bold tabular-nums
                  {{ $ringkasan['laba_rugi'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
            Rp {{ number_format(abs($ringkasan['laba_rugi']), 0, ',', '.') }}
        </p>
    </div>
</div>

{{-- Tabel Chart of Accounts --}}
<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                    <th class="px-4 py-3">{{ __('Kode') }}</th>
                    <th class="px-4 py-3">{{ __('Nama Akun') }}</th>
                    <th class="px-4 py-3">{{ __('Tipe') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Saldo') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($accounts as $account)
                @php
                $typeCls = match($account->tipe) {
                    'pendapatan'  => 'border-green-200 bg-green-50 text-green-700',
                    'pengeluaran' => 'border-yellow-200 bg-yellow-50 text-yellow-700',
                    'aset'        => 'border-blue-200 bg-blue-50 text-blue-700',
                    default       => 'border-gray-200 bg-gray-50 text-gray-500',
                };
                @endphp
                <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">
                        {{ $account->kode }}
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $account->nama }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center rounded-full border px-2 py-0.5
                                     text-[11px] font-medium {{ $typeCls }}">
                            {{ ucfirst($account->tipe) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right font-medium tabular-nums text-gray-900">
                        Rp {{ number_format($account->balance, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Input Pengeluaran --}}
<x-modal id="pengeluaran" title="{{ __('Input Pengeluaran') }}" size="md">
    <form method="POST" action="{{ route('admin.pembukuan.pengeluaran') }}"
          class="space-y-4" id="form-pengeluaran">
        @csrf
        <x-select name="account_id" label="{{ __('Akun Pengeluaran') }}" required
            :options="$accounts->where('tipe','pengeluaran')->pluck('nama','id')->toArray()" />
        <x-input name="amount" label="{{ __('Jumlah (Rp)') }}" type="number" prefix="Rp" required />
        <x-input name="date" label="{{ __('Tanggal') }}" type="date"
            :value="now()->format('Y-m-d')" required />
        <x-textarea name="description" label="{{ __('Keterangan') }}" rows="2" required />
    </form>
    <x-slot:footer>
        <button @click="$dispatch('close-modal-pengeluaran')"
                class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium
                       text-gray-700 hover:bg-gray-50 transition-colors">
            {{ __('Batal') }}
        </button>
        <button form="form-pengeluaran" type="submit"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white
                       hover:bg-blue-700 transition-colors">
            {{ __('Simpan') }}
        </button>
    </x-slot:footer>
</x-modal>

@endsection