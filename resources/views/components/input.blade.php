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

<div {{ $attributes->merge(['class' => 'space-y-1']) }}>
    @if($label)
        <label for="{{ $id }}" class="cw-label">{{ $label }}@if($required)<span class="text-red-600">*</span>@endif</label>
    @endif

    @if($hint)
        <p class="text-xs text-slate-500">{{ $hint }}</p>
    @endif

    <input
        type="{{ $type }}"
        id="{{ $id }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        class="cw-field {{ $error || $errors->has($name) ? 'border-red-400 focus:border-red-500 focus:shadow-none' : '' }}"
    />

    @if($error)
        <p class="text-xs text-red-600 mt-1">{{ $error }}</p>
    @elseif($errors->has($name))
        <p class="text-xs text-red-600 mt-1">{{ $errors->first($name) }}</p>
    @endif
</div>
