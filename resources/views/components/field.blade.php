@props([
    'label' => '',
    'name' => '',
    'type' => 'text',
    'required' => false,
    'error' => null,
    'hint' => null,
])

@php
$hasError = !empty($error) || $errors->has($name);
$errorMessage = $error ?? ($errors->has($name) ? $errors->first($name) : null);
@endphp

<div {{ $attributes->merge(['class' => 'space-y-1.5']) }}>
    @if($label)
        <label for="{{ $name }}" class="cw-label">
            {{ $label }}
            @if($required)
                <span class="text-red-600">*</span>
            @endif
        </label>
    @endif

    @if($type === 'textarea')
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            rows="4"
            {{ $required ? 'required' : '' }}
            class="cw-field {{ $hasError ? 'border-red-400 focus:border-red-500 focus:shadow-none' : '' }}"
            {{ $attributes->except(['class', 'label', 'error', 'hint']) }}
        >{{ $slot }}</textarea>
    @else
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            {{ $required ? 'required' : '' }}
            class="cw-field {{ $hasError ? 'border-red-400 focus:border-red-500 focus:shadow-none' : '' }}"
            {{ $attributes->except(['class', 'label', 'error', 'hint']) }}
        />
    @endif

    @if($hint && !$hasError)
        <p class="text-xs text-slate-500">{{ $hint }}</p>
    @endif

    @if($hasError)
        <p class="text-xs text-red-600">{{ $errorMessage }}</p>
    @endif
</div>
