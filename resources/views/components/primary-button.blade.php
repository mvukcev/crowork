<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-hover hover:text-white focus:bg-primary-hover active:bg-primary-pressed focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all duration-normal shadow-sm hover:shadow-md button-readable-hover']) }}>
    {{ $slot }}
</button>
