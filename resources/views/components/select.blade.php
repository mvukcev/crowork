@props([
    'label' => null,
    'id' => null,
    'name' => null,
    'value' => null,
    'options' => [],
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

    <select id="{{ $id }}" name="{{ $name }}" {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} class="cw-field {{ $error || $errors->has($name) ? 'border-red-400 focus:border-red-500 focus:shadow-none' : '' }}">
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" {{ old($name, $value) == $optionValue ? 'selected' : '' }}>{{ $optionLabel }}</option>
        @endforeach
    </select>

    @if($error)
        <p class="text-xs text-red-600 mt-1">{{ $error }}</p>
    @elseif($errors->has($name))
        <p class="text-xs text-red-600 mt-1">{{ $errors->first($name) }}</p>
    @endif
</div>
