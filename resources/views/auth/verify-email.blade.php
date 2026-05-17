<x-guest-layout>
    <div class="cw-surface p-7 md:p-8">
        <p class="cw-kicker mb-2">{{ __('auth.verify_email') }}</p>
        <h1 class="text-2xl font-semibold text-slate-900 mb-2">{{ __('auth.verify_email_title') }}</h1>
        <p class="text-sm text-slate-600 mb-6">{{ __('auth.verify_email_intro') }}</p>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl p-3">{{ __('auth.new_verification_link_sent') }}</div>
        @endif

        <div class="flex flex-wrap gap-2">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="cw-button-primary">{{ __('auth.resend_verification_email') }}</button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="cw-button-secondary">{{ __('auth.logout') }}</button>
            </form>
        </div>
    </div>
</x-guest-layout>
