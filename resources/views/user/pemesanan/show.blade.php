@extends('layouts.app')
@section('title', 'Detail Pemesanan #' . $pemesanan->id)

@section('content')
<div class="mx-auto max-w-3xl px-4 py-8">

    {{-- Back + Header --}}
    <div class="mb-6">
        <a href="{{ route('pemesanan.index') }}"
           class="inline-flex items-center gap-1.5 text-sm text-[#7a8499] hover:text-[#18213a] transition-colors">
            <x-icon name="arrow-left" class="w-4 h-4" />
            Kembali ke Pemesanan Saya
        </a>
        <div class="mt-4 flex items-start justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-[#18213a]">Pemesanan #{{ $pemesanan->id }}</h1>
                <p class="mt-0.5 text-sm text-[#7a8499]">
                    Dibuat {{ $pemesanan->created_at->diffForHumans() }}
                    &middot; {{ $pemesanan->created_at->format('d M Y, H:i') }} WIB
                </p>
            </div>
            <x-status-badge :status="$pemesanan->status" class="mt-1 shrink-0">
                {{ $pemesanan->statusEnum()->label() }}
            </x-status-badge>
        </div>
    </div>

    {{-- ── Alert sesuai status ─────────────────────────────────────────── --}}

    @if($pemesanan->status === 'pending')
    <div class="mb-4 flex items-start gap-3 rounded-xl border border-yellow-200 bg-yellow-50 p-4">
        <x-icon name="clock" class="mt-0.5 h-5 w-5 shrink-0 text-yellow-500" />
        <div>
            <p class="text-sm font-semibold text-yellow-800">Menunggu Pembayaran</p>
            <p class="mt-0.5 text-sm text-yellow-700">
                Selesaikan pembayaran segera agar pemesanan Anda tidak otomatis kadaluarsa.
            </p>
            <a href="{{ route('payment.checkout', $pemesanan) }}"
               class="mt-2 inline-flex items-center gap-1.5 rounded-lg bg-yellow-500 px-4 py-2
                      text-sm font-semibold text-white hover:bg-yellow-600 transition-colors">
                <x-icon name="credit-card" class="h-4 w-4" />
                Bayar Sekarang
            </a>
        </div>
    </div>
    @endif

    @if($pemesanan->status === 'menunggu_konfirmasi_admin')
    <div class="mb-4 flex items-start gap-3 rounded-xl border border-blue-200 bg-blue-50 p-4">
        <x-icon name="clock" class="mt-0.5 h-5 w-5 shrink-0 text-blue-400" />
        <div>
            <p class="text-sm font-semibold text-blue-800">Menunggu Konfirmasi Admin</p>
            <p class="mt-0.5 text-sm text-blue-700">
                Pembayaran via WhatsApp sudah dikirim. Admin kami akan segera memverifikasi dan
                mengkonfirmasi pemesanan Anda. Biasanya dalam 1×24 jam.
            </p>
        </div>
    </div>
    @endif

    @if($pemesanan->status === 'dikonfirmasi')
    <div class="mb-4 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 p-4">
        <x-icon name="check-circle" class="mt-0.5 h-5 w-5 shrink-0 text-green-500" />
        <div>
            <p class="text-sm font-semibold text-green-800">Pemesanan Dikonfirmasi!</p>
            <p class="mt-0.5 text-sm text-green-700">
                Kendaraan siap diambil pada
                <strong>{{ $pemesanan->tanggal_mulai->format('d M Y') }}</strong>.
                Hubungi kami jika ada pertanyaan lebih lanjut.
            </p>
            <a href="{{ route('chat.index') }}"
               class="mt-2 inline-flex items-center gap-1.5 rounded-lg bg-green-600 px-4 py-2
                      text-sm font-semibold text-white hover:bg-green-700 transition-colors">
                <x-icon name="chat-bubble-left-ellipsis" class="h-4 w-4" />
                Hubungi Admin
            </a>
        </div>
    </div>
    @endif

    @if($pemesanan->status === 'selesai')
    <div class="mb-4 flex items-start gap-3 rounded-xl border border-gray-200 bg-gray-50 p-4">
        <x-icon name="check-circle" class="mt-0.5 h-5 w-5 shrink-0 text-gray-400" />
        <div>
            <p class="text-sm font-semibold text-gray-700">Pemesanan Selesai</p>
            <p class="mt-0.5 text-sm text-gray-500">
                Terima kasih telah mempercayai Yoza Rent Car. Sampai jumpa lagi!
            </p>
        </div>
    </div>
    @endif

    @if(in_array($pemesanan->status, ['dibatalkan', 'kadaluarsa']))
    <div class="mb-4 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4">
        <x-icon name="x-circle" class="mt-0.5 h-5 w-5 shrink-0 text-red-400" />
        <div>
            <p class="text-sm font-semibold text-red-800">
                Pemesanan {{ $pemesanan->status === 'kadaluarsa' ? 'Kadaluarsa' : 'Dibatalkan' }}
            </p>
            <p class="mt-0.5 text-sm text-red-700">
                @if($pemesanan->status === 'kadaluarsa')
                    Pemesanan ini otomatis kadaluarsa karena pembayaran tidak diselesaikan tepat waktu.
                @else
                    Pemesanan ini telah dibatalkan.
                @endif
            </p>
            <a href="{{ route('home') }}"
               class="mt-2 inline-flex items-center gap-1.5 rounded-lg border border-red-200
                      bg-white px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                Pesan Kembali
            </a>
        </div>
    </div>
    @endif

    {{-- ── Grid 2 kolom ────────────────────────────────────────────────── --}}
    <div class="space-y-4">

        {{-- Kartu: Kendaraan --}}
        <div class="overflow-hidden rounded-2xl border border-[#e5e9f2] bg-white shadow-sm">
            <div class="border-b border-[#e5e9f2] px-5 py-3.5">
                <h2 class="text-sm font-semibold text-[#18213a]">Kendaraan</h2>
            </div>
            <div class="flex items-center gap-4 p-5">
                <div class="h-20 w-28 shrink-0 overflow-hidden rounded-xl bg-[#f4f6fb]">
                    <img src="{{ $pemesanan->mobil->fotoUrl() }}"
                         alt="{{ $pemesanan->mobil->nama }}"
                         class="h-full w-full object-cover">
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-[#18213a]">{{ $pemesanan->mobil->nama }}</p>
                    <p class="text-sm text-[#7a8499]">{{ $pemesanan->mobil->plat_nomor }}</p>
                    <div class="mt-2 flex flex-wrap gap-1.5">
                        @if($pemesanan->mobil->tahun)
                        <span class="inline-flex items-center gap-1 rounded-full bg-[#f1f4fa] px-2.5 py-0.5
                                     text-xs text-[#7a8499]">
                            <x-icon name="calendar" class="h-3 w-3" />
                            {{ $pemesanan->mobil->tahun }}
                        </span>
                        @endif
                        @if($pemesanan->mobil->kapasitas)
                        <span class="inline-flex items-center gap-1 rounded-full bg-[#f1f4fa] px-2.5 py-0.5
                                     text-xs text-[#7a8499]">
                            <x-icon name="users" class="h-3 w-3" />
                            {{ $pemesanan->mobil->kapasitas }} Penumpang
                        </span>
                        @endif
                        @if($pemesanan->mobil->transmisi)
                        <span class="inline-flex items-center gap-1 rounded-full bg-[#f1f4fa] px-2.5 py-0.5
                                     text-xs text-[#7a8499]">
                            <x-icon name="cog-6-tooth" class="h-3 w-3" />
                            {{ ucfirst($pemesanan->mobil->transmisi) }}
                        </span>
                        @endif
                    </div>
                </div>
                <div class="shrink-0 text-right">
                    <p class="text-xs text-[#7a8499]">Harga/hari</p>
                    <p class="font-semibold text-[#18213a]">
                        Rp {{ number_format($pemesanan->mobil->harga_per_hari, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Kartu: Detail Pemesanan --}}
        <div class="overflow-hidden rounded-2xl border border-[#e5e9f2] bg-white shadow-sm">
            <div class="border-b border-[#e5e9f2] px-5 py-3.5">
                <h2 class="text-sm font-semibold text-[#18213a]">Detail Pemesanan</h2>
            </div>
            <div class="divide-y divide-[#f1f4fa]">

                {{-- Periode sewa --}}
                <div class="flex items-start gap-3 px-5 py-4">
                    <div class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-[#eef2fb]">
                        <x-icon name="calendar-days" class="h-4 w-4 text-[#3b6fd4]" />
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium uppercase tracking-wider text-[#7a8499]">Periode Sewa</p>
                        <p class="mt-1 font-medium text-[#18213a]">
                            {{ $pemesanan->tanggal_mulai->format('d M Y') }}
                            @if(!$pemesanan->adalah12Jam())
                                &ndash; {{ $pemesanan->tanggal_selesai->format('d M Y') }}
                            @endif
                        </p>
                        <p class="text-xs text-[#7a8499]">
                            @if($pemesanan->adalah12Jam())
                                Sewa 12 Jam
                                @if($pemesanan->waktu_mulai)
                                    · Mulai pukul {{ \Carbon\Carbon::parse($pemesanan->waktu_mulai)->format('H:i') }} WIB
                                @endif
                            @else
                                {{ $pemesanan->durasi() }} hari
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Opsi supir --}}
                <div class="flex items-start gap-3 px-5 py-4">
                    <div class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-[#eef2fb]">
                        <x-icon name="user" class="h-4 w-4 text-[#3b6fd4]" />
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium uppercase tracking-wider text-[#7a8499]">Opsi Mengemudi</p>
                        <p class="mt-1 font-medium text-[#18213a]">
                            {{ $pemesanan->opsi_supir ? 'Dengan Supir' : 'Self-Drive' }}
                        </p>
                        @if($pemesanan->opsi_supir && $pemesanan->biaya_supir)
                        <p class="text-xs text-[#7a8499]">
                            Biaya supir: Rp {{ number_format($pemesanan->biaya_supir, 0, ',', '.') }}
                        </p>
                        @endif
                    </div>
                </div>

                {{-- Catatan --}}
                @if($pemesanan->catatan)
                <div class="flex items-start gap-3 px-5 py-4">
                    <div class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-[#eef2fb]">
                        <x-icon name="chat-bubble-bottom-center-text" class="h-4 w-4 text-[#3b6fd4]" />
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-medium uppercase tracking-wider text-[#7a8499]">Catatan</p>
                        <p class="mt-1 text-sm text-[#18213a]">{{ $pemesanan->catatan }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Kartu: Status Pembayaran ──────────────────────────────────── --}}
        <div class="overflow-hidden rounded-2xl border border-[#e5e9f2] bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-[#e5e9f2] px-5 py-3.5">
                <h2 class="text-sm font-semibold text-[#18213a]">Pembayaran</h2>
                @if($pemesanan->payment)
                    @php
                        $payBadge = match($pemesanan->payment->status) {
                            'dikonfirmasi'       => ['bg-green-50 border-green-200 text-green-700', 'Terkonfirmasi'],
                            'menunggu_konfirmasi'=> ['bg-blue-50 border-blue-200 text-blue-700', 'Menunggu Konfirmasi'],
                            'dibatalkan'         => ['bg-red-50 border-red-200 text-red-600', 'Dibatalkan'],
                            default              => ['bg-yellow-50 border-yellow-200 text-yellow-700', 'Menunggu'],
                        };
                    @endphp
                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5
                                 text-[11px] font-medium {{ $payBadge[0] }}">
                        {{ $payBadge[1] }}
                    </span>
                @endif
            </div>

            @if($pemesanan->payment)
            <div class="divide-y divide-[#f1f4fa]">

                {{-- Metode --}}
                <div class="flex items-center justify-between px-5 py-3.5">
                    <p class="text-sm text-[#7a8499]">Metode Pembayaran</p>
                    <p class="text-sm font-semibold text-[#18213a]">
                        {{ $pemesanan->payment->labelMetode() }}
                    </p>
                </div>

                {{-- Waktu WA dikirim --}}
                @if($pemesanan->payment->wa_sent_at)
                <div class="flex items-center justify-between px-5 py-3.5">
                    <p class="text-sm text-[#7a8499]">Konfirmasi WA Dikirim</p>
                    <p class="text-sm text-[#18213a]">
                        {{ $pemesanan->payment->wa_sent_at->format('d M Y, H:i') }} WIB
                    </p>
                </div>
                @endif

                {{-- Waktu dikonfirmasi admin --}}
                @if($pemesanan->payment->paid_at)
                <div class="flex items-center justify-between px-5 py-3.5">
                    <p class="text-sm text-[#7a8499]">Dikonfirmasi Admin</p>
                    <p class="text-sm font-semibold text-green-600">
                        {{ $pemesanan->payment->paid_at->format('d M Y, H:i') }} WIB
                    </p>
                </div>
                @endif

                {{-- Progress steps --}}
                <div class="px-5 py-4">
                    <div class="flex items-center justify-between">
                        @php
                            $steps = [
                                ['label' => 'Pemesanan\nDibuat',   'done' => true],
                                ['label' => 'Konfirmasi\nWA',      'done' => !is_null($pemesanan->payment->wa_sent_at)],
                                ['label' => 'Verifikasi\nAdmin',   'done' => $pemesanan->payment->isDikonfirmasi()],
                                ['label' => 'Pemesanan\nAktif',    'done' => in_array($pemesanan->status, ['dikonfirmasi','selesai'])],
                            ];
                        @endphp
                        @foreach($steps as $i => $step)
                            <div class="flex flex-1 flex-col items-center gap-1 {{ $i < count($steps)-1 ? 'relative' : '' }}">
                                {{-- Connector line --}}
                                @if($i < count($steps) - 1)
                                <div class="absolute left-1/2 top-3.5 h-0.5 w-full
                                            {{ $steps[$i+1]['done'] ? 'bg-[#3b6fd4]' : 'bg-[#e5e9f2]' }}">
                                </div>
                                @endif
                                {{-- Dot --}}
                                <div class="relative z-10 grid h-7 w-7 place-items-center rounded-full border-2 text-xs font-bold
                                            {{ $step['done']
                                                ? 'border-[#3b6fd4] bg-[#3b6fd4] text-white'
                                                : 'border-[#e5e9f2] bg-white text-[#c0c8d8]' }}">
                                    @if($step['done'])
                                        <x-icon name="check" class="h-3.5 w-3.5" />
                                    @else
                                        {{ $i + 1 }}
                                    @endif
                                </div>
                                <p class="text-center text-[10px] leading-tight text-[#7a8499] whitespace-pre-line">
                                    {{ $step['label'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @else
            {{-- Belum ada payment record --}}
            <div class="flex flex-col items-center gap-2 px-5 py-8 text-center">
                <div class="grid h-12 w-12 place-items-center rounded-full bg-[#f1f4fa]">
                    <x-icon name="credit-card" class="h-6 w-6 text-[#c0c8d8]" />
                </div>
                <p class="text-sm font-medium text-[#18213a]">Belum Ada Pembayaran</p>
                <p class="text-xs text-[#7a8499]">Lanjutkan ke halaman pembayaran untuk memilih metode.</p>
                @if($pemesanan->status === 'pending')
                <a href="{{ route('payment.checkout', $pemesanan) }}"
                   class="mt-1 inline-flex items-center gap-1.5 rounded-lg bg-[#3b6fd4] px-4 py-2
                          text-sm font-semibold text-white hover:bg-[#2e5bb8] transition-colors">
                    Pilih Metode Pembayaran
                </a>
                @endif
            </div>
            @endif
        </div>

        {{-- Kartu: Rincian Harga --}}
        <div class="overflow-hidden rounded-2xl border border-[#e5e9f2] bg-white shadow-sm">
            <div class="border-b border-[#e5e9f2] px-5 py-3.5">
                <h2 class="text-sm font-semibold text-[#18213a]">Rincian Harga</h2>
            </div>
            <div class="space-y-3 px-5 py-4 text-sm">

                {{-- Sewa mobil --}}
                <div class="flex items-center justify-between text-[#7a8499]">
                    @if($pemesanan->adalah12Jam())
                        <span>Sewa 12 Jam (50% harga harian)</span>
                        <span>Rp {{ number_format(($pemesanan->total_harga - ($pemesanan->biaya_supir ?? 0)), 0, ',', '.') }}</span>
                    @else
                        <span>
                            Sewa {{ $pemesanan->durasi() }} hari
                            × Rp {{ number_format($pemesanan->mobil->harga_per_hari, 0, ',', '.') }}
                        </span>
                        <span>Rp {{ number_format($pemesanan->mobil->harga_per_hari * $pemesanan->durasi(), 0, ',', '.') }}</span>
                    @endif
                </div>

                {{-- Supir --}}
                @if($pemesanan->biaya_supir)
                <div class="flex items-center justify-between text-[#7a8499]">
                    <span>
                        Jasa supir
                        @if(!$pemesanan->adalah12Jam())
                            × {{ $pemesanan->durasi() }} hari
                        @endif
                    </span>
                    <span>Rp {{ number_format($pemesanan->biaya_supir, 0, ',', '.') }}</span>
                </div>
                @endif

                {{-- Total --}}
                <div class="flex items-center justify-between border-t border-[#f1f4fa] pt-3
                             text-base font-bold text-[#18213a]">
                    <span>Total</span>
                    <span class="text-[#3b6fd4]">
                        Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- ── Aksi bawah ────────────────────────────────────────────── --}}
        <div class="flex flex-wrap items-center justify-between gap-3 pb-2">
            <div class="flex flex-wrap gap-2">

                {{-- Invoice --}}
                @if(in_array($pemesanan->status, ['menunggu_konfirmasi_admin','dikonfirmasi','selesai'])
                    && $pemesanan->payment?->isDikonfirmasi())
                <a href="{{ route('payment.invoice', $pemesanan) }}"
                   class="inline-flex items-center gap-1.5 rounded-xl border border-[#e5e9f2] bg-white
                          px-4 py-2.5 text-sm font-medium text-[#18213a] shadow-sm
                          hover:bg-[#f1f4fa] transition-colors">
                    <x-icon name="arrow-down-tray" class="h-4 w-4" />
                    Unduh Invoice
                </a>
                @endif

                {{-- Chat admin --}}
                <a href="{{ route('chat.index') }}"
                   class="inline-flex items-center gap-1.5 rounded-xl border border-[#e5e9f2] bg-white
                          px-4 py-2.5 text-sm font-medium text-[#18213a] shadow-sm
                          hover:bg-[#f1f4fa] transition-colors">
                    <x-icon name="chat-bubble-left-ellipsis" class="h-4 w-4" />
                    Hubungi Admin
                </a>

                {{-- Batalkan --}}
                @if($pemesanan->isBisaDibatalkan())
                <form method="POST" action="{{ route('pemesanan.cancel', $pemesanan) }}"
                      x-data
                      @submit.prevent="if(confirm('Yakin ingin membatalkan pemesanan ini?')) $el.submit()">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-white
                                   px-4 py-2.5 text-sm font-medium text-red-500 shadow-sm
                                   hover:bg-red-50 transition-colors">
                        <x-icon name="x-circle" class="h-4 w-4" />
                        Batalkan Pemesanan
                    </button>
                </form>
                @endif
            </div>

            {{-- Pesan lagi --}}
            @if(in_array($pemesanan->status, ['selesai','dibatalkan','kadaluarsa']))
            <a href="{{ route('mobil.show', $pemesanan->mobil) }}"
               class="inline-flex items-center gap-1.5 rounded-xl bg-[#3b6fd4] px-4 py-2.5
                      text-sm font-semibold text-white shadow-sm hover:bg-[#2e5bb8] transition-colors">
                <x-icon name="arrow-path" class="h-4 w-4" />
                Pesan Lagi
            </a>
            @endif
        </div>

    </div>{{-- /space-y-4 --}}
</div>
@endsection
