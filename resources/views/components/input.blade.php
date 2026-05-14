@props([
    'label' => null,
    'id' => null,
    'name' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'error' => null,
    'hint' => null,
    'autocomplete' => null,
])

@php
    $hasError = $error || $errors->has($name);
    $errorId = $id ? $id . '-error' : null;
    $hintId = $id && $hint ? $id . '-hint' : null;
@endphp

<div {{ $attributes->merge(['class' => 'space-y-1']) }}>
    @if($label)
        <label for="{{ $id }}" @class(['cw-label', 'cw-label-required' => $required])>
            {{ $label }}
        </label>
    @endif

    @if($hint)
        <p class="text-xs text-slate-500" id="{{ $hintId }}">{{ $hint }}</p>
    @endif

    <input
        type="{{ $type }}"
        id="{{ $id }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required aria-required="true"' : '' }}
        {{ $disabled ? 'disabled aria-disabled="true"' : '' }}
        {{ $autocomplete ? 'autocomplete="' . $autocomplete . '"' : '' }}
        @if($hasError) aria-invalid="true" @if($errorId) aria-describedby="{{ $errorId }}" @endif @endif
        @if($hintId && !$hasError) aria-describedby="{{ $hintId }}" @endif
        class="cw-field @if($hasError) border-red-400 focus:border-red-500 focus:shadow-red-100/50 @endif"
    />

    @if($error)
        <p class="text-xs text-red-600 mt-1" id="{{ $errorId }}" role="alert">{{ $error }}</p>
    @elseif($errors->has($name))
        <p class="text-xs text-red-600 mt-1" id="{{ $errorId }}" role="alert">{{ $errors->first($name) }}</p>
    @endif
</div>
