@props([
    'type'      => 'info',
    'dismissible' => false,
])

@php
$styles = match($type) {
    'success' => 'bg-green-50 border-green-200 text-green-800',
    'error'   => 'bg-red-50 border-red-200 text-red-800',
    'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800',
    default   => 'bg-blue-50 border-blue-200 text-blue-800',
};
$icon = match($type) {
    'success' => 'check-circle',
    'error'   => 'x-circle',
    'warning' => 'warning',
    default   => 'info',
};
@endphp

<div
    {{ $attributes->merge(['class' => "flex items-start gap-3 rounded-lg border p-4 $styles"]) }}
    x-data="{ show: true }"
    x-show="show"
    x-transition
    role="alert"
>
    <x-icon :name="$icon" class="w-5 h-5 flex-shrink-0 mt-0.5" />
    <div class="flex-1 text-sm">{{ $slot }}</div>
    @if($dismissible)
        <button @click="show = false" class="flex-shrink-0 opacity-60 hover:opacity-100 transition-opacity">
            <x-icon name="x" class="w-4 h-4" />
        </button>
    @endif
</div>