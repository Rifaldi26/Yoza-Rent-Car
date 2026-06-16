@extends('layouts.admin')
@section('title', __('Ulasan'))

@section('content')

<x-page-header
    title="{{ __('Ulasan') }}"
    description="{{ __('Moderasi ulasan dari penyewa sebelum ditampilkan ke publik.') }}"
/>

{{-- Tab menunggu / semua --}}
<div class="mb-4 flex gap-1 border-b border-gray-200">
    <a href="{{ route('admin.ulasan.index') }}"
       class="px-4 py-2 text-sm font-medium transition-colors border-b-2
              {{ !request('tab') || request('tab') === 'menunggu'
                  ? 'border-[#3b6fd4] text-[#3b6fd4]'
                  : 'border-transparent text-gray-500 hover:text-gray-700' }}">
        {{ __('Menunggu') }}
        @if($jumlahMenunggu > 0)
            <span class="ml-1.5 rounded-full bg-amber-100 px-1.5 py-0.5 text-xs font-semibold text-amber-700">
                {{ $jumlahMenunggu }}
            </span>
        @endif
    </a>
    <a href="{{ route('admin.ulasan.index', ['tab' => 'semua']) }}"
       class="px-4 py-2 text-sm font-medium transition-colors border-b-2
              {{ request('tab') === 'semua'
                  ? 'border-[#3b6fd4] text-[#3b6fd4]'
                  : 'border-transparent text-gray-500 hover:text-gray-700' }}">
        {{ __('Semua') }}
    </a>
</div>

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr class="text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                <th class="px-4 py-3">{{ __('Penyewa') }}</th>
                <th class="px-4 py-3 hidden md:table-cell">{{ __('Kendaraan') }}</th>
                <th class="px-4 py-3">{{ __('Rating & Komentar') }}</th>
                <th class="px-4 py-3 hidden sm:table-cell">{{ __('Tanggal') }}</th>
                <th class="px-4 py-3 text-right">{{ __('Aksi') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ulasans as $ulasan)
                <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors">

                    {{-- Penyewa --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <x-avatar :name="$ulasan->user->name" size="sm" />
                            <div>
                                <p class="font-medium text-gray-900">{{ $ulasan->user->name }}</p>
                                <a href="{{ route('admin.pemesanan.show', $ulasan->pemesanan) }}"
                                   class="text-xs text-[#3b6fd4] hover:underline">
                                    Pesanan #{{ $ulasan->pemesanan_id }}
                                </a>
                            </div>
                        </div>
                    </td>

                    {{-- Kendaraan --}}
                    <td class="px-4 py-3 hidden md:table-cell">
                        <p class="font-medium text-gray-900">{{ $ulasan->mobil->nama }}</p>
                        <p class="text-xs text-gray-400">{{ $ulasan->mobil->plat_nomor }}</p>
                    </td>

                    {{-- Rating & komentar --}}
                    <td class="px-4 py-3 max-w-xs">
                        <div class="flex gap-0.5 mb-1">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-3.5 h-3.5 {{ $i <= $ulasan->rating ? 'text-amber-400' : 'text-gray-200' }}"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        @if($ulasan->komentar)
                            <p class="text-xs text-gray-500 line-clamp-2">{{ $ulasan->komentar }}</p>
                        @else
                            <p class="text-xs text-gray-300 italic">{{ __('Tanpa komentar') }}</p>
                        @endif
                    </td>

                    {{-- Tanggal --}}
                    <td class="px-4 py-3 hidden sm:table-cell">
                        <p class="text-xs text-gray-500">
                            {{ $ulasan->created_at->format('d M Y') }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $ulasan->created_at->format('H:i') }}
                        </p>
                    </td>

                    {{-- Aksi --}}
                    <td class="px-4 py-3 text-right">
                        <div class="inline-flex items-center gap-1">
                            @if(!$ulasan->disetujui)
                                <form method="POST"
                                      action="{{ route('admin.ulasan.setujui', $ulasan) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 rounded-lg
                                                   bg-green-50 border border-green-200 px-2.5 py-1.5
                                                   text-xs font-medium text-green-700
                                                   hover:bg-green-100 transition-colors">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5" />
                                        {{ __('Setujui') }}
                                    </button>
                                </form>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-lg
                                             bg-green-50 border border-green-200 px-2.5 py-1.5
                                             text-xs font-medium text-green-700">
                                    <x-icon name="check-circle" class="w-3.5 h-3.5" />
                                    {{ __('Disetujui') }}
                                </span>
                            @endif

                            <form method="POST"
                                  action="{{ route('admin.ulasan.destroy', $ulasan) }}"
                                  onsubmit="return confirm('{{ __('Hapus ulasan ini?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center rounded-lg border border-gray-200
                                               p-1.5 text-gray-400 hover:border-red-200 hover:bg-red-50
                                               hover:text-red-600 transition-colors">
                                    <x-icon name="trash" class="w-3.5 h-3.5" />
                                </button>
                            </form>
                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <x-empty-state
                            icon="star"
                            title="{{ __('Tidak ada ulasan') }}"
                            description="{{ request('tab') === 'semua'
                                ? __('Belum ada ulasan masuk.')
                                : __('Tidak ada ulasan yang menunggu persetujuan.') }}"
                        />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($ulasans->hasPages())
        <div class="border-t border-gray-100 px-4 py-3">
            {{ $ulasans->links() }}
        </div>
    @endif
</div>

@endsection
