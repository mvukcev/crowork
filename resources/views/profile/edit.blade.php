<x-app-layout>
    <x-slot name="title">{{ __('auth.profile_settings') }}</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-4xl space-y-5">
            <p class="cw-kicker mb-2">{{ __('auth.profile_settings') }}</p>
            @if (session('status'))
                @php
                    $statusKey = 'auth.status_' . str_replace('-', '_', (string) session('status'));
                    $translatedStatus = __($statusKey);
                @endphp
                <div class="cw-surface p-3 text-sm text-emerald-700 bg-emerald-50 border-emerald-200">{{ $translatedStatus !== $statusKey ? $translatedStatus : session('status') }}</div>
            @endif

            <div class="cw-surface p-6">@include('profile.partials.update-profile-information-form')</div>
            <div class="cw-surface p-6">@include('profile.partials.update-password-form')</div>
            <div class="cw-surface p-6">@include('profile.partials.delete-user-form')</div>
        </div>
    </section>
</x-app-layout>
