@props([
    'title'    => null,
    'subtitle' => null,
    'padding'  => true,
])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-gray-200 bg-white shadow-sm']) }}>
    @if($title)
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
            <div>
                <h3 class="text-base font-semibold text-gray-900">{{ $title }}</h3>
                @if($subtitle)
                    <p class="text-sm text-gray-500 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
            @isset($action)
                <div class="flex-shrink-0">{{ $action }}</div>
            @endisset
        </div>
    @endif
    <div @class(['p-6' => $padding])>
        {{ $slot }}
    </div>
    @isset($footer)
        <div class="border-t border-gray-100 px-6 py-3 bg-gray-50 rounded-b-xl">
            {{ $footer }}
        </div>
    @endisset
</div>