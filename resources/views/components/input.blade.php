@props([
    'label'       => null,
    'name'        => '',
    'type'        => 'text',
    'placeholder' => '',
    'error'       => null,
    'prefix'      => null,
    'suffix'      => null,
    'helper'      => null,
])

@php $error = $error ?? $errors->first($name); @endphp

<div {{ $attributes->only('class')->merge(['class' => 'space-y-1']) }}>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if($attributes->has('required'))
                <span class="text-red-500 ml-0.5">*</span>
            @endif
        </label>
    @endif

    <div class="relative flex rounded-lg shadow-sm">
        @if($prefix)
            <span class="inline-flex items-center rounded-l-lg border border-r-0 border-gray-300
                         bg-gray-50 px-3 text-gray-500 text-sm">
                {{ $prefix }}
            </span>
        @endif

        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->except(['class','label','name','type','placeholder','error','prefix','suffix','helper'])->merge([
                'class' => 'block w-full border text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-0 py-2.5 px-3
                    ' . ($prefix ? 'rounded-r-lg' : ($suffix ? 'rounded-l-lg' : 'rounded-lg')) . '
                    ' . ($error
                        ? 'border-red-300 focus:border-red-400 focus:ring-red-200 bg-red-50'
                        : 'border-gray-300 focus:border-primary-400 focus:ring-primary-200 bg-white')
            ]) }}
        >

        @if($suffix)
            <span class="inline-flex items-center rounded-r-lg border border-l-0 border-gray-300
                         bg-gray-50 px-3 text-gray-500 text-sm">
                {{ $suffix }}
            </span>
        @endif
    </div>

    @if($error)
        <p class="text-xs text-red-600 flex items-center gap-1">
            <x-icon name="x-circle" class="w-3 h-3" />
            {{ $error }}
        </p>
    @elseif($helper)
        <p class="text-xs text-gray-400">{{ $helper }}</p>
    @endif
</div>