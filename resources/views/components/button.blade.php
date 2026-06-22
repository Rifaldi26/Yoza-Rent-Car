@props([
    'variant'  => 'primary',
    'size'     => 'md',
    'icon'     => null,
    'iconPos'  => 'left',
    'href'     => null,
    'loading'  => false,
])

@php
$base = 'inline-flex items-center justify-center gap-2 font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

$variants = match($variant) {
    'secondary' => 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 focus:ring-gray-300',
    'danger'    => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    'success'   => 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
    'ghost'     => 'text-gray-600 hover:bg-gray-100 focus:ring-gray-300',
    'link'      => 'text-primary-600 hover:underline focus:ring-primary-500 p-0',
    default     => 'bg-primary-600 text-white hover:bg-primary-700 focus:ring-primary-500',
};

$sizes = match($size) {
    'xs' => 'px-2.5 py-1.5 text-xs',
    'sm' => 'px-3 py-2 text-sm',
    'lg' => 'px-5 py-3 text-base',
    'xl' => 'px-6 py-3.5 text-base',
    default => 'px-4 py-2.5 text-sm',
};

$classes = "$base $variants $sizes";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon && $iconPos === 'left')
            <x-icon :name="$icon" class="w-4 h-4" />
        @endif
        {{ $slot }}
        @if($icon && $iconPos === 'right')
            <x-icon :name="$icon" class="w-4 h-4" />
        @endif
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes]) }}>
        @if($loading)
            <x-icon name="spinner" class="w-4 h-4 animate-spin" />
        @elseif($icon && $iconPos === 'left')
            <x-icon :name="$icon" class="w-4 h-4" />
        @endif
        {{ $slot }}
        @if(!$loading && $icon && $iconPos === 'right')
            <x-icon :name="$icon" class="w-4 h-4" />
        @endif
    </button>
@endif