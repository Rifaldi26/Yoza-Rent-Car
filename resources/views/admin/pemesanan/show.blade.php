@extends('layouts.admin')
@section('title', __('Detail Pemesanan') . ' #' . $pemesanan->id)

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.pemesanan.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors">
        <x-icon name="arrow-left" class="w-4 h-4" />
        {{ __('Kembali ke Pemesanan') }}
    </a>
</div>

<div class="grid gap-4 lg:grid-cols-3">

    {{-- Detail Utama --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Info Pemesanan --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">
                        {{ __('Pemesanan') }} #{{ $pemesanan->id }}
                    </h2>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ __('Dibuat') }} {{ $pemesanan->created_at->diffForHumans() }}
                    </p>
                </div>
                <x-status-badge :status="$pemesanan->status">
                    {{ $pemesanan->labelStatus() }}
                </x-status-badge>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Pelanggan') }}</p>
                    <p class="mt-1 font-medium text-gray-900">{{ $pemesanan->user->name }}</p>
                    <p class="text-xs text-gray-500">{{ $pemesanan->user->email }}</p>
                    <p class="text-xs text-gray-500">{{ $pemesanan->user->no_hp ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Kendaraan') }}</p>
                    <p class="mt-1 font-medium text-gray-900">{{ $pemesanan->mobil->nama }}</p>
                    <p class="text-xs text-gray-500">{{ $pemesanan->mobil->plat_nomor }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Periode Sewa') }}</p>
                    <p class="mt-1 font-medium text-gray-900">
                        {{ $pemesanan->tanggal_mulai->format('d M Y') }}
                    </p>
                    <p class="text-xs text-gray-500">
                        {{ __('s/d') }} {{ $pemesanan->tanggal_selesai->format('d M Y') }}
                        ({{ $pemesanan->durasi() }} {{ __('hari') }})
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ __('Opsi') }}</p>
                    <p class="mt-1 font-medium text-gray-900">
                        {{ $pemesanan->opsi_supir ? __('Dengan Supir') : __('Self-Drive') }}
                    </p>
                </div>
            </div>

            @if($pemesanan->catatan)
                <div class="mt-4 rounded-lg bg-gray-50 p-3">
                    <p class="text-xs font-medium text-gray-500">{{ __('Catatan pelanggan') }}</p>
                    <p class="mt-1 text-sm text-gray-700">{{ $pemesanan->catatan }}</p>
                </div>
            @endif
        </div>

        {{-- Rincian Harga --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ __('Rincian Harga') }}</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>{{ __('Sewa mobil') }} ({{ $pemesanan->durasi() }} {{ __('hari') }} × Rp
                        {{ number_format($pemesanan->mobil->harga_per_hari, 0, ',', '.') }})
                    </span>
                    <span class="tabular-nums">
                        Rp {{ number_format($pemesanan->durasi() * $pemesanan->mobil->harga_per_hari, 0, ',', '.') }}
                    </span>
                </div>
                @if($pemesanan->opsi_supir && $pemesanan->biaya_supir)
                    <div class="flex justify-between text-gray-600">
                        <span>{{ __('Jasa supir') }} ({{ $pemesanan->durasi() }} {{ __('hari') }} × Rp
                            {{ number_format($pemesanan->mobil->biaya_supir_per_hari, 0, ',', '.') }})
                        </span>
                        <span class="tabular-nums">
                            Rp {{ number_format($pemesanan->biaya_supir, 0, ',', '.') }}
                        </span>
                    </div>
                @endif
                <div class="flex justify-between border-t border-gray-100 pt-2 font-semibold text-gray-900">
                    <span>{{ __('Total') }}</span>
                    <span class="tabular-nums">
                        Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar Aksi --}}
    <div class="space-y-4">

        {{-- Aksi Admin --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ __('Tindakan Admin') }}</h3>
        
            <div class="space-y-2">
        
                {{-- Konfirmasi Pembayaran (jika sudah WA tapi belum dikonfirmasi) --}}
                @if($pemesanan->payment && $pemesanan->payment->status === 'menunggu_konfirmasi')
                    <div class="mb-3 rounded-xl border border-blue-200 bg-blue-50 p-3">
                        <p class="text-xs font-semibold text-blue-700 mb-1">{{ __('Info Pembayaran') }}</p>
                        <div class="text-xs text-gray-700 space-y-1">
                            <div class="flex justify-between">
                                <span class="text-gray-500">{{ __('Metode') }}</span>
                                <span class="font-semibold">
                                    {{ $pemesanan->payment->labelMetode() }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">{{ __('WA Dikirim') }}</span>
                                <span>{{ $pemesanan->payment->wa_sent_at?->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                    </div>
        
                    <form method="POST"
                          action="{{ route('admin.pemesanan.konfirmasi-bayar', $pemesanan) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="flex w-full items-center justify-center gap-2 rounded-lg
                                       bg-primary-600 px-4 py-2.5 text-sm font-medium text-white
                                       hover:bg-primary-700 transition-colors">
                            <x-icon name="check-circle" class="w-4 h-4" />
                            {{ __('Konfirmasi Pembayaran Diterima') }}
                        </button>
                    </form>
                @endif
        
                {{-- Konfirmasi Pemesanan --}}
                @if($pemesanan->status === 'menunggu_konfirmasi_admin')
                    <form method="POST"
                          action="{{ route('admin.pemesanan.konfirmasi', $pemesanan) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="flex w-full items-center justify-center gap-2 rounded-lg
                                       bg-green-600 px-4 py-2.5 text-sm font-medium text-white
                                       hover:bg-green-700 transition-colors">
                            <x-icon name="check-circle" class="w-4 h-4" />
                            {{ __('Konfirmasi Pemesanan') }}
                        </button>
                    </form>
        
                    <button @click="$dispatch('open-modal-tolak')"
                            class="flex w-full items-center justify-center gap-2 rounded-lg border
                                   border-red-200 px-4 py-2.5 text-sm font-medium text-red-600
                                   hover:bg-red-50 transition-colors">
                        <x-icon name="x-circle" class="w-4 h-4" />
                        {{ __('Tolak Pemesanan') }}
                    </button>
        
                @elseif($pemesanan->status === 'dikonfirmasi')
                    <form method="POST"
                          action="{{ route('admin.pemesanan.selesai', $pemesanan) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="flex w-full items-center justify-center gap-2 rounded-lg
                                       bg-primary-600 px-4 py-2.5 text-sm font-medium text-white
                                       hover:bg-primary-700 transition-colors">
                            <x-icon name="check-circle" class="w-4 h-4" />
                            {{ __('Tandai Selesai') }}
                        </button>
                    </form>
        
                @else
                    <p class="text-xs text-center py-2 text-gray-400">
                        {{ __('Tidak ada tindakan tersedia untuk status ini.') }}
                    </p>
                @endif
        
                {{-- Chat WA langsung ke pelanggan --}}
                @php
                    $noPelanggan = preg_replace('/[^0-9]/', '', $pemesanan->user->no_hp ?? '');
                    $noPelanggan = $noPelanggan ? '62' . ltrim($noPelanggan, '0') : null;
                @endphp
                @if($noPelanggan)
                    <a href="https://wa.me/{{ $noPelanggan }}"
                       target="_blank"
                       class="flex w-full items-center justify-center gap-2 rounded-lg border
                              border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700
                              hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4 text-[#25D366]" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                        </svg>
                        {{ __('Chat Pelanggan di WhatsApp') }}
                    </a>
                @endif
        
                {{-- Download Invoice --}}
                @if(in_array($pemesanan->status, ['menunggu_konfirmasi_admin','dikonfirmasi','selesai']))
                    <a href="{{ route('admin.pemesanan.invoice', $pemesanan) }}"
                       class="flex w-full items-center justify-center gap-2 rounded-lg border
                              border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700
                              hover:bg-gray-50 transition-colors">
                        <x-icon name="download" class="w-4 h-4" />
                        {{ __('Download Invoice') }}
                    </a>
                @endif
            </div>
        </div>

        {{-- Timeline --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ __('Timeline') }}</h3>
            <ol class="space-y-3">
                <li class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="h-2 w-2 rounded-full bg-primary-600 mt-1.5 flex-shrink-0"></div>
                        <div class="w-px flex-1 bg-gray-200 mt-1"></div>
                    </div>
                    <div class="pb-3">
                        <p class="text-xs font-medium text-gray-900">{{ __('Pemesanan dibuat') }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $pemesanan->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                </li>
                @if($pemesanan->payment?->paid_at)
                <li class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="h-2 w-2 rounded-full bg-green-500 mt-1.5 flex-shrink-0"></div>
                        <div class="w-px flex-1 bg-gray-200 mt-1"></div>
                    </div>
                    <div class="pb-3">
                        <p class="text-xs font-medium text-gray-900">{{ __('Pembayaran diterima') }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $pemesanan->payment->paid_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                </li>
                @endif
                <li class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="h-2 w-2 rounded-full
                            {{ in_array($pemesanan->status, ['dikonfirmasi','selesai'])
                                ? 'bg-green-500' : 'bg-gray-200' }}
                            mt-1.5 flex-shrink-0"></div>
                    </div>
                    <div>
                        <p class="text-xs font-medium
                            {{ in_array($pemesanan->status, ['dikonfirmasi','selesai'])
                                ? 'text-gray-900' : 'text-gray-300' }}">
                            {{ __('Dikonfirmasi admin') }}
                        </p>
                    </div>
                </li>
            </ol>
        </div>

    </div>
</div>

{{-- Modal Tolak --}}
<x-modal id="tolak" title="{{ __('Tolak Pemesanan') }}" size="sm">
    <p class="text-sm text-gray-600">
        {!! __('Yakin ingin menolak pemesanan <strong>#:id</strong>?', ['id' => $pemesanan->id]) !!}
        {{ __('Pelanggan akan diberitahu melalui notifikasi dan email.') }}
    </p>
    <x-slot:footer>
        <button @click="$dispatch('close-modal-tolak')"
                class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium
                       text-gray-700 hover:bg-gray-50 transition-colors">
            {{ __('Batal') }}
        </button>
        <form method="POST" action="{{ route('admin.pemesanan.tolak', $pemesanan) }}">
            @csrf @method('PATCH')
            <button type="submit"
                    class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white
                           hover:bg-red-700 transition-colors">
                {{ __('Ya, Tolak') }}
            </button>
        </form>
    </x-slot:footer>
</x-modal>

@endsection