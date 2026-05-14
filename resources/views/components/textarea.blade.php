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

<div {{ $attributes->merge(['class' => 'space-y-1']) }}>
    @if($label)
        <label for="{{ $id }}" class="cw-label">{{ $label }}@if($required)<span class="text-red-600">*</span>@endif</label>
    @endif

    @if($hint)
        <p class="text-xs text-slate-500">{{ $hint }}</p>
    @endif

    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        class="cw-field min-h-28 {{ $error || $errors->has($name) ? 'border-red-400 focus:border-red-500 focus:shadow-none' : '' }}"
    >{{ old($name, $value) }}</textarea>

    @if($error)
        <p class="text-xs text-red-600 mt-1">{{ $error }}</p>
    @elseif($errors->has($name))
        <p class="text-xs text-red-600 mt-1">{{ $errors->first($name) }}</p>
    @endif
</div>
