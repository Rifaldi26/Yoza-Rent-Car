@props([
    'variant' => 'gray',
    'size'    => 'md',
])

@php
$colors = match($variant) {
    'success' => 'bg-green-100 text-green-800',
    'warning' => 'bg-yellow-100 text-yellow-800',
    'danger'  => 'bg-red-100 text-red-800',
    'info'    => 'bg-blue-100 text-blue-800',
    'purple'  => 'bg-purple-100 text-purple-800',
    'orange'  => 'bg-orange-100 text-orange-800',
    default   => 'bg-gray-100 text-gray-700',
};
$sizes = match($size) {
    'sm' => 'px-1.5 py-0.5 text-[10px]',
    'lg' => 'px-3 py-1 text-sm',
    default => 'px-2.5 py-0.5 text-xs',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-medium rounded-full $colors $sizes"]) }}>
    {{ $slot }}
</span>