<button {{ $attributes->merge(['type' => 'submit', 'class' => 'cw-button-primary cw-press']) }}>
    {{ $slot }}
</button>
