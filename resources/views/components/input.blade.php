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
])

@php
    $inputClasses = 'w-full px-4 py-3 text-body text-text-primary bg-white border border-border rounded-xl transition-all duration-normal focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary disabled:bg-surface-subtle disabled:text-text-disabled disabled:cursor-not-allowed min-h-[44px] shadow-sm focus:shadow-md';
    
    if ($error) {
        $inputClasses .= ' border-danger focus:ring-danger/50 focus:border-danger';
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
    
    <input
        type="{{ $type }}"
        id="{{ $id }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        class="{{ $inputClasses }}"
    />
    
    @if($error)
        <p class="text-body-sm text-danger mt-1">{{ $error }}</p>
    @elseif($errors->has($name))
        <p class="text-body-sm text-danger mt-1">{{ $errors->first($name) }}</p>
    @endif
</div>
