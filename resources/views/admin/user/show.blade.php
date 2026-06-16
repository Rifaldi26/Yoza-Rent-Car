@extends('layouts.admin')
@section('title', $user->name)

@section('content')

{{-- Back link --}}
<div class="mb-6">
    <a href="{{ route('admin.user.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition-colors">
        <x-icon name="arrow-left" class="w-4 h-4" />
        {{ __('Kembali ke Pengguna') }}
    </a>
</div>

<div class="grid gap-4 lg:grid-cols-3">

    {{-- Kolom kiri: profil + statistik --}}
    <div class="space-y-4">

        {{-- Kartu profil --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col items-center text-center">
                <x-avatar :name="$user->name" size="xl" class="mb-3" />
                <h2 class="text-base font-semibold text-gray-900">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>

                @if($user->no_hp)
                    <p class="mt-1 text-sm text-gray-500">{{ $user->no_hp }}</p>
                @endif

                <div class="mt-3">
                    @if($user->email_verified_at)
                        <span class="inline-flex items-center gap-1 rounded-full border border-green-200
                                     bg-green-50 px-2.5 py-0.5 text-[11px] font-medium text-green-700">
                            <x-icon name="check-circle" class="w-3 h-3" />
                            {{ __('Terverifikasi') }}
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full border border-gray-200
                                     bg-gray-50 px-2.5 py-0.5 text-[11px] font-medium text-gray-500">
                            {{ __('Belum verifikasi') }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="mt-4 space-y-2 border-t border-gray-100 pt-4 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('Bergabung') }}</span>
                    <span class="font-medium text-gray-900">
                        {{ $user->created_at->format('d M Y') }}
                    </span>
                </div>
                @if($user->google_id)
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ __('Login via') }}</span>
                        <span class="font-medium text-gray-900">Google</span>
                    </div>
                @endif
            </div>

            {{-- Tombol chat WA --}}
            @php
                $noHp = preg_replace('/[^0-9]/', '', $user->no_hp ?? '');
                $noWa = $noHp ? '62' . ltrim($noHp, '0') : null;
            @endphp
            @if($noWa)
                <a href="https://wa.me/{{ $noWa }}" target="_blank"
                   class="mt-4 flex w-full items-center justify-center gap-2 rounded-lg border
                          border-gray-200 px-4 py-2 text-sm font-medium text-gray-700
                          hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 text-[#25D366]" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                    </svg>
                    {{ __('Chat di WhatsApp') }}
                </a>
            @endif
        </div>

        {{-- Statistik ringkas --}}
        @php
            $totalPemesanan  = $user->pemesanans->count();
            $totalSelesai    = $user->pemesanans->where('status', 'selesai')->count();
            $totalDibatalkan = $user->pemesanans->whereIn('status', ['dibatalkan', 'kadaluarsa'])->count();
            $totalNilai      = $user->pemesanans
                ->whereIn('status', ['dikonfirmasi', 'selesai'])
                ->sum('total_harga');
        @endphp

        <div class="grid grid-cols-2 gap-3">
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm text-center">
                <p class="text-2xl font-bold text-gray-900">{{ $totalPemesanan }}</p>
                <p class="mt-0.5 text-xs text-gray-500">{{ __('Total Pesanan') }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm text-center">
                <p class="text-2xl font-bold text-green-600">{{ $totalSelesai }}</p>
                <p class="mt-0.5 text-xs text-gray-500">{{ __('Selesai') }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm text-center">
                <p class="text-2xl font-bold text-red-500">{{ $totalDibatalkan }}</p>
                <p class="mt-0.5 text-xs text-gray-500">{{ __('Batal / Exp') }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm text-center">
                <p class="text-lg font-bold text-gray-900 tabular-nums">
                    {{ $totalNilai >= 1_000_000
                        ? 'Rp ' . number_format($totalNilai / 1_000_000, 1) . ' jt'
                        : 'Rp ' . number_format($totalNilai / 1_000, 0) . ' rb' }}
                </p>
                <p class="mt-0.5 text-xs text-gray-500">{{ __('Total Nilai') }}</p>
            </div>
        </div>

    </div>

    {{-- Kolom kanan: riwayat pemesanan --}}
    <div class="lg:col-span-2">
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">

            <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
                <h3 class="text-sm font-semibold text-gray-900">{{ __('Riwayat Pemesanan') }}</h3>
                <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">
                    {{ $totalPemesanan }}
                </span>
            </div>

            @forelse($user->pemesanans->sortByDesc('created_at') as $pemesanan)
                <div class="border-b border-gray-100 px-5 py-4 last:border-0
                            hover:bg-gray-50 transition-colors">
                    <div class="flex items-start justify-between gap-3">

                        {{-- Info pemesanan --}}
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('admin.pemesanan.show', $pemesanan) }}"
                                   class="text-sm font-semibold text-gray-900 hover:text-blue-600 transition-colors">
                                    #{{ $pemesanan->id }} — {{ $pemesanan->mobil->nama }}
                                </a>
                                <x-status-badge :status="$pemesanan->status">
                                    {{ $pemesanan->labelStatus() }}
                                </x-status-badge>
                            </div>

                            <div class="mt-1.5 flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500">
                                {{-- Periode --}}
                                <span class="flex items-center gap-1">
                                    <x-icon name="calendar" class="w-3 h-3" />
                                    @if($pemesanan->adalah12Jam())
                                        {{ $pemesanan->tanggal_mulai->format('d M Y') }}
                                        @if($pemesanan->waktu_mulai)
                                            · pukul {{ substr($pemesanan->waktu_mulai, 0, 5) }}
                                        @endif
                                        · <span class="font-medium text-gray-700">Sewa 12 Jam</span>
                                    @else
                                        {{ $pemesanan->tanggal_mulai->format('d M Y') }}
                                        &ndash;
                                        {{ $pemesanan->tanggal_selesai->format('d M Y') }}
                                        · <span class="font-medium text-gray-700">
                                            {{ $pemesanan->durasi() }} {{ __('hari') }}
                                        </span>
                                    @endif
                                </span>

                                {{-- Supir --}}
                                <span class="flex items-center gap-1">
                                    <x-icon name="user" class="w-3 h-3" />
                                    {{ $pemesanan->opsi_supir ? __('Dengan supir') : __('Self-drive') }}
                                </span>

                                {{-- Metode bayar --}}
                                @if($pemesanan->payment)
                                    <span class="flex items-center gap-1">
                                        <x-icon name="credit-card" class="w-3 h-3" />
                                        {{ strtoupper($pemesanan->payment->metode ?? '-') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Total harga --}}
                        <div class="flex-shrink-0 text-right">
                            <p class="text-sm font-semibold text-gray-900 tabular-nums">
                                Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}
                            </p>
                            <p class="mt-0.5 text-xs text-gray-400">
                                {{ $pemesanan->created_at->format('d M Y') }}
                            </p>
                        </div>

                    </div>
                </div>
            @empty
                <x-empty-state
                    icon="clipboard"
                    title="{{ __('Belum ada pemesanan') }}"
                    description="{{ __('Pengguna ini belum pernah melakukan pemesanan.') }}"
                    class="py-8"
                />
            @endforelse

        </div>
    </div>

</div>

@endsection
