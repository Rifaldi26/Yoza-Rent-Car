@extends('layouts.admin')
@section('title', 'Pemesanan')

@section('content')

<x-page-header title="Pemesanan" description="Kelola semua pemesanan dari pelanggan.">
    <x-slot:actions>
    </x-slot:actions>
</x-page-header>

{{-- Status Tabs --}}
@php
$tabs = [
    ''                          => 'Semua',
    'pending'                   => 'Menunggu Bayar',
    'menunggu_konfirmasi_admin' => 'Menunggu Konfirmasi',
    'dikonfirmasi'              => 'Dikonfirmasi',
    'selesai'                   => 'Selesai',
    'dibatalkan'                => 'Dibatalkan',
];
$active = request('status', '');
@endphp

<div class="mb-4 flex gap-1.5 overflow-x-auto pb-1">
    @foreach($tabs as $value => $label)
        <a href="{{ route('admin.pemesanan.index', array_filter(['status' => $value])) }}"
           class="whitespace-nowrap rounded-full border px-3 py-1 text-xs font-medium transition-colors
                  {{ $active === $value
                      ? 'border-blue-600 bg-blue-600 text-white'
                      : 'border-gray-200 bg-white text-gray-500 hover:bg-gray-50' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

{{-- Table --}}
<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Pelanggan</th>
                    <th class="px-4 py-3">Mobil</th>
                    <th class="px-4 py-3 hidden lg:table-cell">Tanggal</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-right">Total</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pemesanans as $p)
                <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 font-mono text-xs text-gray-400">#{{ $p->id }}</td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $p->user->name }}</td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">{{ $p->mobil->nama }}</div>
                        <div class="text-xs text-gray-400">
                            {{ $p->opsi_supir ? '+ Supir' : 'Self-Drive' }}
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-500 hidden lg:table-cell whitespace-nowrap">
                        {{ $p->tanggal_mulai->format('d M') }} &ndash;
                        {{ $p->tanggal_selesai->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3">
                        <x-status-badge :status="$p->status">
                            {{ $p->labelStatus() }}
                        </x-status-badge>
                    </td>
                    <td class="px-4 py-3 text-right font-medium tabular-nums text-gray-900">
                        Rp {{ number_format($p->total_harga, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.pemesanan.show', $p) }}"
                           class="inline-flex h-8 w-8 items-center justify-center rounded-lg
                                  border border-gray-200 text-gray-500 hover:bg-gray-100
                                  hover:text-gray-700 transition-colors">
                            <x-icon name="eye" class="w-4 h-4" />
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <x-empty-state icon="calendar" title="Tidak ada pemesanan"
                            description="Belum ada pemesanan dengan status ini." />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pemesanans->hasPages())
        <div class="border-t border-gray-100 px-4 py-3">
            {{ $pemesanans->links() }}
        </div>
    @endif
</div>

@endsection