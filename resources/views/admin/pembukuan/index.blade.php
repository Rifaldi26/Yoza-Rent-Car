@extends('layouts.admin')
@section('title', __('Pembukuan'))

@section('content')

{{-- Alpine.js state wrapper untuk modal transaksi --}}
<div x-data="transaksiForm()">

<x-page-header title="{{ __('Pembukuan') }}" description="{{ __('Chart of Accounts dan ringkasan penjualan.') }}">
    <x-slot:actions>
        <a href="{{ route('admin.pembukuan.jurnal') }}"
           class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white
                  px-3 py-1.5 text-sm font-medium hover:bg-gray-50 transition-colors">
            <x-icon name="book-open" class="w-4 h-4" />
            {{ __('Jurnal Harian') }}
        </a>
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
                    <th class="px-4 py-3 text-right">{{ __('Debit') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Kredit') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Saldo') }}</th>
                    <th class="px-4 py-3 text-center">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($accounts as $account)
                <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">
                        {{ $account->kode }}
                    </td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $account->nama_translated }}</td>
                    <td class="px-4 py-3 text-right tabular-nums text-gray-900">
                        Rp {{ number_format($account->total_debit ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-right tabular-nums text-gray-900">
                        Rp {{ number_format($account->total_credit ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-right font-medium tabular-nums text-gray-900">
                        Rp {{ number_format($account->balance, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        {{-- Hanya tampilkan tombol untuk akun manual (bukan Kas, Pendapatan Sewa, Pendapatan Jasa Supir) --}}
                        @if(!in_array($account->kode, ['1-001', '4-001', '4-002']))
                            <button @click="$dispatch('set-data-transaksi', { account_id: {{ $account->id }}, account_name: '{{ $account->nama_translated }}', tipe: '{{ $account->tipe }}' })"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 
                                           bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700
                                           hover:bg-gray-50 transition-colors">
                                <x-icon name="plus" class="w-3.5 h-3.5" />
                                {{ __('Input') }}
                            </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                @php
                    $totalDebit = $accounts->sum('total_debit');
                    $totalCredit = $accounts->sum('total_credit');
                    $selisih = $totalDebit - $totalCredit;
                    $seimbang = abs($selisih) < 0.01;
                @endphp
                <tr class="border-t-2 border-gray-200 bg-gray-50 font-semibold text-gray-900">
                    <td class="px-4 py-3" colspan="2">{{ __('Total') }}</td>
                    <td class="px-4 py-3 text-right tabular-nums">
                        Rp {{ number_format($totalDebit, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-right tabular-nums">
                        Rp {{ number_format($totalCredit, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <span class="inline-flex items-center rounded-full border px-2 py-0.5
                                     text-[11px] font-medium
                                     {{ $seimbang
                                         ? 'border-green-200 bg-green-50 text-green-700'
                                         : 'border-red-200 bg-red-50 text-red-700' }}">
                            {{ $seimbang
                                ? __('Seimbang')
                                : __('Selisih Rp') . ' ' . number_format(abs($selisih), 0, ',', '.') }}
                        </span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- Modal Input Transaksi (Debit/Kredit) --}}
<x-modal id="transaksi" size="md">
    <x-slot:title>
        {{ __('Input Transaksi') }} - <span x-text="account_name"></span>
    </x-slot:title>

    <form method="POST" action="{{ route('admin.pembukuan.input-transaksi') }}"
          class="space-y-4" id="form-transaksi">
        @csrf
        
        {{-- Hidden account_id --}}
        <input type="hidden" name="account_id" x-model="account_id">
        <input type="hidden" name="tipe" x-model="tipe">

        {{-- Pilihan Tipe Transaksi (Debit/Kredit) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('Tipe Transaksi') }} <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-3">
                <label class="flex items-center cursor-pointer">
                    <input type="radio" name="tipe_transaksi" value="debit" x-model="tipe_transaksi" class="w-4 h-4">
                    <span class="ml-2 text-sm text-gray-700">{{ __('Debit') }}</span>
                </label>
                <label class="flex items-center cursor-pointer">
                    <input type="radio" name="tipe_transaksi" value="credit" x-model="tipe_transaksi" class="w-4 h-4">
                    <span class="ml-2 text-sm text-gray-700">{{ __('Kredit') }}</span>
                </label>
            </div>
        </div>

        {{-- Jumlah --}}
        <x-input name="amount" label="{{ __('Jumlah (Rp)') }}" type="number" prefix="Rp" required />

        {{-- Tanggal --}}
        <x-input name="date" label="{{ __('Tanggal') }}" type="date"
            :value="now()->format('Y-m-d')" required />

        {{-- Keterangan --}}
        <x-textarea name="description" label="{{ __('Keterangan') }}" rows="2" required />
    </form>

    <x-slot:footer>
        <button @click="$dispatch('close-modal-transaksi')"
                class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium
                       text-gray-700 hover:bg-gray-50 transition-colors">
            {{ __('Batal') }}
        </button>
        <button form="form-transaksi" type="submit"
                class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white
                       hover:bg-primary-700 transition-colors">
            {{ __('Simpan') }}
        </button>
    </x-slot:footer>
</x-modal>

</div> {{-- closing div x-data --}}

{{-- Alpine.js function untuk form transaksi --}}
<script>
function transaksiForm() {
    return {
        account_id: null,
        account_name: '',
        tipe: '',
        tipe_transaksi: 'debit',
        
        init() {
            // Listen untuk event dari tombol Input
            document.addEventListener('set-data-transaksi', (e) => {
                this.account_id = e.detail.account_id;
                this.account_name = e.detail.account_name;
                this.tipe = e.detail.tipe;
                this.tipe_transaksi = 'debit'; // reset ke default

                // Open modal
                this.$dispatch('open-modal-transaksi');
            });
        }
    }
}
</script>

@endsection