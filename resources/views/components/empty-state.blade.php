@props([
    'icon'        => 'inbox',
    'title'       => 'Tidak ada data',
    'description' => '',
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-16 text-center']) }}>
    <div class="rounded-full bg-gray-100 p-4 mb-4">
        <x-icon :name="$icon" class="w-8 h-8 text-gray-400" />
    </div>
    <h3 class="text-base font-semibold text-gray-900">{{ $title }}</h3>
    @if($description)
        <p class="mt-1 text-sm text-gray-500 max-w-sm">{{ $description }}</p>
    @endif
    @isset($action)
        <div class="mt-4">{{ $action }}</div>
    @endisset
</div>