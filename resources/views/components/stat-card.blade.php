@props([
    'label'   => '',
    'value'   => '0',
    'icon'    => 'chart-bar',
    'color'   => 'blue',
    'trend'   => null,
    'trendUp' => true,
])

@php
$iconBg = match($color) {
    'green'  => 'bg-green-100 text-green-700',
    'yellow' => 'bg-yellow-100 text-yellow-700',
    'red'    => 'bg-red-100 text-red-700',
    'purple' => 'bg-purple-100 text-purple-700',
    default  => 'bg-blue-100 text-blue-700',
};
@endphp

<div {{ $attributes->merge(['class' => 'rounded-xl border border-gray-200 bg-white p-6 shadow-sm']) }}>
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $value }}</p>
            @if($trend)
                <p class="mt-1 flex items-center gap-1 text-xs font-medium
                    {{ $trendUp ? 'text-green-600' : 'text-red-600' }}">
                    <x-icon :name="$trendUp ? 'trending-up' : 'trending-down'" class="w-3 h-3" />
                    {{ $trend }}
                </p>
            @endif
        </div>
        <div class="rounded-lg p-3 {{ $iconBg }}">
            <x-icon :name="$icon" class="w-6 h-6" />
        </div>
    </div>
</div>