@props([
    'label' => '',
    'name' => '',
    'type' => 'text',
    'required' => false,
    'error' => null,
    'hint' => null,
    'icon' => null,
])

@php
$hasError = !empty($error) || $errors->has($name);
$errorMessage = $error ?? ($errors->has($name) ? $errors->first($name) : null);

$inputClasses = $hasError 
    ? 'border-danger focus:border-danger focus:ring-danger'
    : 'border-stroke-default focus:border-control-border-focus focus:ring-control-border-focus';
@endphp

<div {{ $attributes->merge(['class' => 'space-y-1.5']) }}>
    @if($label)
        <label for="{{ $name }}" class="block text-body-sm font-medium text-text-primary">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-text-tertiary">
                {!! $icon !!}
            </div>
        @endif
        
        @if($type === 'textarea')
            <textarea
                id="{{ $name }}"
                name="{{ $name }}"
                rows="4"
                {{ $required ? 'required' : '' }}
                class="block w-full rounded-control border {{ $inputClasses }} bg-surface-base text-text-primary placeholder:text-text-tertiary focus:outline-none focus:ring-2 focus:ring-offset-0 transition-colors duration-120 text-body px-3 py-2 {{ $icon ? 'pl-10' : '' }}"
                {{ $attributes->except(['class', 'label', 'error', 'hint', 'icon']) }}
            >{{ $slot }}</textarea>
        @else
            <input
                id="{{ $name }}"
                name="{{ $name }}"
                type="{{ $type }}"
                {{ $required ? 'required' : '' }}
                class="block w-full rounded-control border {{ $inputClasses }} bg-surface-base text-text-primary placeholder:text-text-tertiary focus:outline-none focus:ring-2 focus:ring-offset-0 transition-colors duration-120 text-body px-3 py-2 h-10 {{ $icon ? 'pl-10' : '' }}"
                {{ $attributes->except(['class', 'label', 'error', 'hint', 'icon']) }}
            />
        @endif
    </div>
    
    @if($hint && !$hasError)
        <p class="text-caption text-text-tertiary">{{ $hint }}</p>
    @endif
    
    @if($hasError)
        <p class="text-caption text-danger">{{ $errorMessage }}</p>
    @endif
</div>
