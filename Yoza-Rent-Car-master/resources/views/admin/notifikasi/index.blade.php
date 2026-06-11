@extends('layouts.admin')
@section('title', __('Notifikasi'))

@section('content')

<x-page-header :title="__('Notifikasi')" :description="__('Aktivitas dan pengingat sistem.')">
    <x-slot:actions>
        @if($notifikasis->total() > 0)
            <form method="POST" action="{{ route('admin.notifikasi.hapus-semua') }}">
                @csrf @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white
                               px-3 py-1.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                    <x-icon name="trash" class="w-4 h-4" />
                    {{ __('Hapus Semua') }}
                </button>
            </form>
        @endif
    </x-slot:actions>
</x-page-header>

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    @forelse($notifikasis as $notif)
    @php
    $iconMap = [
        'success' => ['icon' => 'check-circle', 'cls' => 'bg-green-100 text-green-600'],
        'warning' => ['icon' => 'warning',       'cls' => 'bg-yellow-100 text-yellow-600'],
        'info'    => ['icon' => 'info',           'cls' => 'bg-blue-100 text-blue-600'],
    ];
    $style = $iconMap[$notif->tipe] ?? $iconMap['info'];
    @endphp
    <div class="flex items-start gap-3 border-b border-gray-100 p-4 last:border-0
                transition-colors hover:bg-gray-50
                {{ !$notif->dibaca ? 'bg-blue-50/30' : '' }}">

        <div class="grid h-10 w-10 flex-shrink-0 place-items-center rounded-full {{ $style['cls'] }}">
            <x-icon :name="$style['icon']" class="w-5 h-5" />
        </div>

        <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2">
                <p class="text-sm font-semibold text-gray-900">{{ $notif->judul }}</p>
                @if(!$notif->dibaca)
                    <span class="h-1.5 w-1.5 rounded-full bg-blue-500 flex-shrink-0"></span>
                @endif
            </div>
            <p class="mt-0.5 text-sm text-gray-500">{{ $notif->pesan }}</p>
            <p class="mt-1 text-xs text-gray-400">{{ $notif->created_at->diffForHumans() }}</p>
        </div>

        @if(!$notif->dibaca)
        <form method="POST" action="{{ route('admin.notifikasi.baca', $notif) }}">
            @csrf @method('PATCH')
            <button type="submit"
                    class="text-xs text-blue-600 hover:underline whitespace-nowrap flex-shrink-0 mt-1">
                {{ __('Tandai dibaca') }}
            </button>
        </form>
        @endif
    </div>
    @empty
        <x-empty-state icon="bell" :title="__('Tidak ada notifikasi')"
            :description="__('Notifikasi akan muncul saat ada aktivitas baru.')" />
    @endforelse

    @if($notifikasis->hasPages())
        <div class="border-t border-gray-100 px-4 py-3">
            {{ $notifikasis->links() }}
        </div>
    @endif
</div>

@endsection
