@props(['messages'])

@php
    $messagesArray = array_values(array_filter((array) $messages));
@endphp

<div {{ $attributes->merge(['class' => 'cw-input-error-slot']) }}>
    @if ($messagesArray)
        <ul class="text-xs text-red-600 space-y-1" role="alert" aria-live="polite">
            @foreach ($messagesArray as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    @endif
</div>
