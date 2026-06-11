@props([
    'label'    => null,
    'name'     => '',
    'options'  => [],
    'selected' => null,
    'placeholder' => 'Pilih...',
    'error'    => null,
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

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $attributes->except(['class','label','name','options','selected','placeholder','error'])->merge([
            'class' => 'block w-full rounded-lg border py-2.5 px-3 text-sm transition-colors
                        focus:outline-none focus:ring-2 focus:ring-offset-0
                        ' . ($error
                            ? 'border-red-300 focus:border-red-400 focus:ring-red-200 bg-red-50'
                            : 'border-gray-300 focus:border-blue-400 focus:ring-blue-200 bg-white')
        ]) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach($options as $value => $label)
            <option value="{{ $value }}" @selected($selected == $value)>{{ $label }}</option>
        @endforeach
    </select>

    @if($error)
        <p class="text-xs text-red-600 flex items-center gap-1">
            <x-icon name="x-circle" class="w-3 h-3" />
            {{ $error }}
        </p>
    @endif
</div>