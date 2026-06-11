@props([
    'label' => null,
    'name'  => '',
    'rows'  => 4,
    'error' => null,
    'helper'=> null,
])

@php $error = $error ?? $errors->first($name); @endphp

<div {{ $attributes->only('class')->merge(['class' => 'space-y-1']) }}>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
        </label>
    @endif

    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        {{ $attributes->except(['class','label','name','rows','error','helper'])->merge([
            'class' => 'block w-full rounded-lg border text-sm transition-colors resize-y
                        focus:outline-none focus:ring-2 focus:ring-offset-0 py-2.5 px-3
                        ' . ($error
                            ? 'border-red-300 focus:border-red-400 focus:ring-red-200 bg-red-50'
                            : 'border-gray-300 focus:border-blue-400 focus:ring-blue-200 bg-white')
        ]) }}
    >{{ $slot }}</textarea>

    @if($error)
        <p class="text-xs text-red-600 flex items-center gap-1">
            <x-icon name="x-circle" class="w-3 h-3" />
            {{ $error }}
        </p>
    @elseif($helper)
        <p class="text-xs text-gray-400">{{ $helper }}</p>
    @endif
</div>