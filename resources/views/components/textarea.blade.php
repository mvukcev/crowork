@props([
    'label' => null,
    'id' => null,
    'name' => null,
    'value' => null,
    'placeholder' => null,
    'rows' => 4,
    'required' => false,
    'disabled' => false,
    'error' => null,
    'hint' => null,
])

@php
    $textareaClasses = 'w-full px-3 py-2 text-body text-text-primary bg-background border border-border rounded-md transition-colors duration-normal focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent disabled:bg-surface disabled:text-text-disabled disabled:cursor-not-allowed resize-y';
    
    if ($error) {
        $textareaClasses .= ' border-danger focus:ring-danger';
    }
@endphp

<div {{ $attributes->merge(['class' => 'space-y-1']) }}>
    @if($label)
        <label for="{{ $id }}" class="block text-body font-semibold text-text-primary">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    @if($hint)
        <p class="text-body-sm text-text-secondary">{{ $hint }}</p>
    @endif
    
    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        class="{{ $textareaClasses }}"
    >{{ old($name, $value) }}</textarea>
    
    @if($error)
        <p class="text-body-sm text-danger mt-1">{{ $error }}</p>
    @elseif($errors->has($name))
        <p class="text-body-sm text-danger mt-1">{{ $errors->first($name) }}</p>
    @endif
</div>
