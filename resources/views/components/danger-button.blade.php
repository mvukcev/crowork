<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 min-h-[40px] bg-red-600 border border-transparent rounded-lg font-medium text-sm text-white hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-200 focus:ring-offset-2 transition-all duration-160']) }}>
    {{ $slot }}
</button>
