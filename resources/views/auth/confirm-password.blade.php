<x-guest-layout>
    <div class="cw-surface p-7 md:p-8">
        <p class="cw-kicker mb-2">{{ __('auth.security_check') }}</p>
        <h1 class="text-2xl font-semibold text-slate-900 mb-2">{{ __('auth.confirm_password_title') }}</h1>
        <p class="text-sm text-slate-600 mb-6">{{ __('auth.confirm_password_intro') }}</p>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
            @csrf
            <div>
                <label class="cw-label" for="password">{{ __('auth.password') }}</label>
                <input id="password" class="cw-field" type="password" name="password" required autocomplete="current-password">
                @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="cw-button-primary">{{ __('auth.verify') }}</button>
        </form>
    </div>
</x-guest-layout>
