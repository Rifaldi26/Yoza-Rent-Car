@extends('layouts.admin')
@section('title', __('Notifikasi'))

@section('content')

<x-page-header title="{{ __('Notifikasi') }}" description="{{ __('Daftar pemberitahuan sistem.') }}">

</x-page-header>

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <ul class="divide-y divide-gray-100">
        @forelse($notifikasis as $notif)
        <li id="notif-{{ $notif->id }}"
            class="flex items-start gap-3 p-4 transition-colors hover:bg-gray-50
                {{ $notif->dibaca ? '' : 'bg-blue-50/50' }}">

            {{-- Icon --}}
            <div class="grid h-9 w-9 flex-shrink-0 place-items-center rounded-lg
                        {{ $notif->dibaca ? 'bg-gray-100 text-gray-400' : 'bg-blue-100 text-blue-600' }}">
                <x-icon name="{{ $notif->icon ?? 'bell' }}" class="w-4 h-4" />
            </div>

            {{-- Konten --}}
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-gray-900">
                    {{ $notif->judul }}
                </p>
                <p class="mt-0.5 text-xs text-gray-500">
                    {{ $notif->pesan }}
                </p>
                <p class="mt-1 text-[11px] text-gray-400">
                    {{ $notif->created_at->diffForHumans() }}
                </p>
            </div>

            {{-- Aksi --}}
            <div class="flex flex-col items-end gap-1">
                @if(!$notif->dibaca)
                    <form method="POST" action="{{ route('admin.notifikasi.baca', $notif) }}" class="mark-read-form" data-id="{{ $notif->id }}">
                        @csrf @method('PATCH')
                        <button type="submit" 
                            id="btn-{{ $notif->id }}"
                            class="text-xs text-blue-500 hover:text-blue-700 transition-colors">
                                {{ __('Tandai dibaca') }}
                        </button>
                    </form>
                @endif
                @if($notif->url)
                    <a href="{{ $notif->url }}"
                       class="text-xs text-gray-400 hover:text-gray-600 transition-colors">
                        {{ __('Lihat') }} &rarr;
                    </a>
                @endif
            </div>
        </li>
        @empty
        <li>
            <x-empty-state icon="bell" title="{{ __('Tidak ada notifikasi') }}"
                description="{{ __('Notifikasi akan muncul disini saat ada aktivitas.') }}" />
        </li>
        @endforelse
    </ul>
</div>

{{-- Pagination --}}
@if($notifikasis->hasPages())
    <div class="mt-4">{{ $notifikasis->links() }}</div>
@endif

@endsection

<script>
document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.mark-read-form').forEach(form => {

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const notifId = this.dataset.id;

            const response = await fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    'Accept': 'application/json'
                },
                body: new URLSearchParams({
                    _method: 'PATCH'
                })
            });

            const data = await response.json();

            if (data.success) {

                // Hilangkan tombol
                this.remove();

                // Ubah warna item menjadi sudah dibaca
                const item = document.getElementById(
                    `notif-${notifId}`
                );

                item.classList.remove('bg-blue-50/50');
            }
        });

    });

});
</script>