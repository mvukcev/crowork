<x-guest-layout>
    <div class="cw-surface p-7 md:p-8">
        <p class="cw-kicker mb-2">{{ __('auth.password_reset') }}</p>
        <h1 class="text-2xl font-semibold text-slate-900 mb-2">{{ __('auth.forgot_password_title') }}</h1>
        <p class="text-sm text-slate-600 mb-6">{{ __('auth.forgot_password_intro') }}</p>

        @if (session('status'))
            <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl p-3">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4" data-cw-track-submit="password_reset_request">
            @csrf
            <div>
                <label class="cw-label" for="email">{{ __('auth.email') }}</label>
                <input id="email" class="cw-field" type="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="cw-button-primary">{{ __('auth.email_reset_link') }}</button>
        </form>
    </div>
</x-guest-layout>
