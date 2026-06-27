@extends('layouts.admin')
@section('title', __('Armada Mobil'))

@section('content')

<x-page-header title="{{ __('Armada Mobil') }}" description="{{ __('Daftar armada dan ketersediaan.') }}">
    <x-slot:actions>
        <a href="{{ route('admin.mobil.create') }}"
           class="inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-3 py-1.5
                  text-sm font-medium text-white hover:bg-primary-700 transition-colors">
            <x-icon name="plus" class="w-4 h-4" />
            {{ __('Tambah') }}
        </a>
    </x-slot:actions>
</x-page-header>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @forelse($mobils as $mobil)
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm
                transition-shadow hover:shadow-md">

        {{-- Foto / Placeholder --}}
        @if($mobil->foto)
            <img src="{{ Storage::url($mobil->foto) }}"
                 alt="{{ $mobil->nama }}"
                 class="h-40 w-full object-cover">
        @else
            <div class="grid h-40 place-items-center bg-gradient-to-br from-primary-50 to-gray-100">
                <x-icon name="car" class="w-16 h-16 text-primary-300" />
            </div>
        @endif

        <div class="p-4">
            {{-- Nama + Status --}}
            <div class="flex items-start justify-between gap-2">
                <div>
                    <h3 class="font-semibold text-gray-900">{{ $mobil->nama }}</h3>
                    <p class="font-mono text-xs text-gray-400">
                        {{ $mobil->plat_nomor }} &middot; {{ $mobil->tahun }}
                    </p>
                </div>
                <x-status-badge :status="$mobil->status">
                    {{ $mobil->labelStatus() }}
                </x-status-badge>
            </div>

            {{-- Deskripsi singkat --}}
            <div class="mt-3 flex items-center gap-3 text-xs text-gray-400">
                <span class="inline-flex items-center gap-1">
                    <x-icon name="user" class="w-3.5 h-3.5" />
                    {{ $mobil->merek }}
                </span>
                @if($mobil->adaSupir())
                    <span class="inline-flex items-center gap-1">
                        <x-icon name="check-circle" class="w-3.5 h-3.5 text-green-500" />
                        {{ __('Ada supir') }}
                    </span>
                @endif
            </div>

            {{-- Harga + Aksi --}}
            <div class="mt-3 flex items-end justify-between border-t border-gray-100 pt-3">
                <div>
                    <p class="text-xs text-gray-400">{{ __('/ hari') }}</p>
                    <p class="text-base font-bold text-primary-600">
                        Rp {{ number_format($mobil->harga_per_hari, 0, ',', '.') }}
                    </p>
                </div>
                <div class="flex items-center gap-1">
                    <a href="{{ route('admin.mobil.edit', $mobil) }}"
                       class="inline-flex h-8 w-8 items-center justify-center rounded-lg border
                              border-gray-200 text-gray-500 hover:bg-gray-100 transition-colors">
                        <x-icon name="pencil" class="w-3.5 h-3.5" />
                    </a>
                    <form method="POST"
                          action="{{ route('admin.mobil.toggle-status', $mobil) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border
                                       border-gray-200 text-gray-500 hover:bg-gray-100 transition-colors"
                                title="{{ __('Toggle status') }}">
                            <x-icon name="clock" class="w-3.5 h-3.5" />
                        </button>
                    </form>
                    <button
                        @click="$dispatch('open-modal-hapus-{{ $mobil->id }}')"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border
                               border-gray-200 text-red-400 hover:bg-red-50 transition-colors">
                        <x-icon name="trash" class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Hapus per mobil --}}
    <x-modal id="hapus-{{ $mobil->id }}" title="{{ __('Hapus Mobil') }}" size="sm">
        <p class="text-sm text-gray-600">
            {!! __('Yakin ingin menghapus <strong>:nama</strong>?', ['nama' => $mobil->nama]) !!}
            {{ __('Tindakan ini tidak dapat dibatalkan.') }}
        </p>
        <x-slot:footer>
            <button @click="$dispatch('close-modal-hapus-{{ $mobil->id }}')"
                    class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium
                           text-gray-700 hover:bg-gray-50 transition-colors">
                {{ __('Batal') }}
            </button>
            <form method="POST" action="{{ route('admin.mobil.destroy', $mobil) }}">
                @csrf @method('DELETE')
                <button type="submit"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white
                               hover:bg-red-700 transition-colors">
                    {{ __('Hapus') }}
                </button>
            </form>
        </x-slot:footer>
    </x-modal>

    @empty
    <div class="sm:col-span-2 lg:col-span-3">
        <x-empty-state icon="car" title="{{ __('Belum ada kendaraan') }}"
            description="{{ __('Tambah armada pertama Anda.') }}">
            <x-slot:action>
                <a href="{{ route('admin.mobil.create') }}"
                   class="inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-4 py-2
                          text-sm font-medium text-white hover:bg-primary-700 transition-colors">
                    <x-icon name="plus" class="w-4 h-4" />
                    {{ __('Tambah Mobil') }}
                </a>
            </x-slot:action>
        </x-empty-state>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($mobils->hasPages())
    <div class="mt-4">{{ $mobils->links() }}</div>
@endif

@endsection