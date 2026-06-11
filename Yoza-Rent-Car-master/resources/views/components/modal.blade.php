@props([
    'id'    => 'modal',
    'title' => '',
    'size'  => 'md',
])

@php
$maxW = match($size) {
    'sm' => 'max-w-sm',
    'lg' => 'max-w-2xl',
    'xl' => 'max-w-4xl',
    default => 'max-w-lg',
};
@endphp

<div
    x-data="{ open: false }"
    x-on:open-modal-{{ $id }}.window="open = true"
    x-on:close-modal-{{ $id }}.window="open = false"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    x-cloak
>
    {{-- Backdrop --}}
    <div
        class="absolute inset-0 bg-black/50"
        @click="open = false"
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    ></div>

    {{-- Dialog --}}
    <div
        class="relative w-full {{ $maxW }} rounded-2xl bg-white shadow-xl"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.stop
    >
        {{-- Header --}}
        @if($title)
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
            <h3 class="text-base font-semibold text-gray-900">{{ $title }}</h3>
            <button
                @click="open = false"
                class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors"
            >
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>
        @endif

        {{-- Body --}}
        <div class="px-6 py-5">{{ $slot }}</div>

        {{-- Footer --}}
        @isset($footer)
            <div class="flex items-center justify-end gap-3 border-t border-gray-100 px-6 py-4">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>