@props([
    'src'  => null,
    'name' => 'U',
    'size' => 'md',
])

@php
$sizes = match($size) {
    'xs' => 'w-6 h-6 text-[10px]',
    'sm' => 'w-8 h-8 text-xs',
    'lg' => 'w-12 h-12 text-base',
    'xl' => 'w-16 h-16 text-xl',
    default => 'w-10 h-10 text-sm',
};
$initials = collect(explode(' ', $name))
    ->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->implode('');
@endphp

@if($src)
    <img
        src="{{ $src }}"
        alt="{{ $name }}"
        {{ $attributes->merge(['class' => "rounded-full object-cover $sizes"]) }}
    >
@else
    <span {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-full bg-blue-600 font-semibold text-white flex-shrink-0 $sizes"]) }}>
        {{ $initials }}
    </span>
@endif