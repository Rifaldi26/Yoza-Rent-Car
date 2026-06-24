@extends('layouts.admin')
@section('title', __('Ulasan'))

@section('content')

<x-page-header
    title="{{ __('Ulasan') }}"
    description="{{ __('Kelola semua ulasan dari penyewa. Ulasan dipublikasikan secara otomatis.') }}"
/>

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
                                   class="text-xs text-primary-600 hover:underline">
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

                    {{-- Aksi: Hanya tombol Hapus --}}
                    <td class="px-4 py-3 text-right">
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
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="5">
                        <x-empty-state
                            icon="star"
                            title="{{ __('Tidak ada ulasan') }}"
                            description="{{ __('Belum ada ulasan masuk.') }}"
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