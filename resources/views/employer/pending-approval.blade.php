<x-app-layout>
    <x-slot name="title">{{ __('notifications.pending') }}</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-3xl">
            <div class="cw-surface p-7 text-center">
                <p class="cw-kicker mb-2">{{ __('notifications.pending') }}</p>
                <h1 class="cw-display text-4xl md:text-6xl mb-3">{{ __('auth.status_account_created_verify_pending') }}</h1>
                <p class="text-slate-600 mb-6">{{ __('auth.status_account_created_verify_pending') }}</p>
                <div class="flex flex-wrap justify-center gap-2">
                    <a href="{{ url('/for-employers') }}" class="cw-button-secondary">{{ __('navigation.for_employers') }}</a>
                    <a href="{{ url('/contact') }}" class="cw-button-secondary">{{ __('auth.contact') }}</a>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
