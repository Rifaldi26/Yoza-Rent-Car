@extends('layouts.app')
@section('title', 'Menunggu Konfirmasi')

@section('content')
<div class="mx-auto max-w-md px-4 py-12">
    <div class="overflow-hidden rounded-2xl border border-[#e5e9f2] bg-white shadow-sm text-center">

        {{-- Icon --}}
        <div class="bg-[#eef2fb] px-6 py-10">
            <div class="mx-auto mb-4 grid h-16 w-16 place-items-center rounded-2xl bg-[#3b6fd4] text-white shadow-lg">
                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-[#18213a]">Pesan WhatsApp Terkirim</h1>
            <p class="mt-2 text-sm text-[#7a8499]">
                Pesanan Anda sedang menunggu konfirmasi dari Admin Yoza Rent Car.
            </p>
        </div>

        <div class="p-6 space-y-4">

            {{-- Status Pemesanan --}}
            <div class="rounded-xl border border-[#e5e9f2] bg-[#f4f6fb] p-4 text-sm">
                <div class="flex justify-between mb-2">
                    <span class="text-[#7a8499]">ID Pemesanan</span>
                    <span class="font-mono font-semibold text-[#18213a]">#{{ $pemesanan->id }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-[#7a8499]">Kendaraan</span>
                    <span class="font-medium text-[#18213a]">{{ $pemesanan->mobil->nama }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-[#7a8499]">Metode Bayar</span>
                    <span class="font-medium text-[#18213a]">
                        {{ $pemesanan->payment?->labelMetode() ?? '-' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[#7a8499]">Status</span>
                    <x-status-badge status="menunggu_konfirmasi_admin">
                        Menunggu Konfirmasi
                    </x-status-badge>
                </div>
            </div>

            {{-- Langkah selanjutnya --}}
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-left">
                <p class="text-xs font-semibold text-[#3b6fd4] mb-2">Langkah selanjutnya:</p>
                <ol class="space-y-1.5 text-xs text-[#18213a]">
                    @if($pemesanan->payment?->metode === 'transfer')
                    <li class="flex items-start gap-2">
                        <span class="flex-shrink-0 font-bold text-[#3b6fd4]">1.</span>
                        Transfer ke rekening
                        <strong>{{ config('payment.metode.transfer.bank') }}
                        {{ config('payment.metode.transfer.rekening') }}</strong>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="flex-shrink-0 font-bold text-[#3b6fd4]">2.</span>
                        Kirim bukti transfer via WhatsApp ke Admin
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="flex-shrink-0 font-bold text-[#3b6fd4]">3.</span>
                        Tunggu konfirmasi dari Admin (biasanya &lt; 1 jam)
                    </li>
                    @elseif($pemesanan->payment?->metode === 'qris')
                    <li class="flex items-start gap-2">
                        <span class="flex-shrink-0 font-bold text-[#3b6fd4]">1.</span>
                        Scan QRIS dan selesaikan pembayaran
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="flex-shrink-0 font-bold text-[#3b6fd4]">2.</span>
                        Kirim bukti bayar via WhatsApp ke Admin
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="flex-shrink-0 font-bold text-[#3b6fd4]">3.</span>
                        Tunggu konfirmasi dari Admin
                    </li>
                    @else
                    <li class="flex items-start gap-2">
                        <span class="flex-shrink-0 font-bold text-[#3b6fd4]">1.</span>
                        Admin akan mengkonfirmasi pemesanan Anda
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="flex-shrink-0 font-bold text-[#3b6fd4]">2.</span>
                        Bayar saat pengambilan kendaraan
                    </li>
                    @endif
                </ol>
            </div>

            {{-- CTA --}}
            <div class="flex flex-col gap-2">
                <a href="{{ route('pemesanan.show', $pemesanan) }}"
                   class="flex items-center justify-center gap-2 rounded-xl bg-[#3b6fd4] py-2.5
                          text-sm font-semibold text-white hover:bg-[#2e5bb8] transition-colors">
                    <x-icon name="eye" class="w-4 h-4" />
                    Pantau Status Pemesanan
                </a>
                <a href="{{ 'https://wa.me/' . config('payment.wa_number') }}"
                   target="_blank"
                   class="flex items-center justify-center gap-2 rounded-xl border border-[#e5e9f2]
                          py-2.5 text-sm font-medium text-[#18213a] hover:bg-[#f4f6fb] transition-colors">
                    <svg class="h-4 w-4 text-[#25D366]" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                    </svg>
                    Chat Admin WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>
@endsection