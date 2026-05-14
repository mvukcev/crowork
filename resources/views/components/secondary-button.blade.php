<button {{ $attributes->merge(['type' => 'button', 'class' => 'cw-button-secondary cw-press']) }}>
    {{ $slot }}
</button>
