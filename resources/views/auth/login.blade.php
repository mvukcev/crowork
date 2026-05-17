<x-guest-layout>
    <div class="cw-surface p-6 md:p-8">
        <p class="cw-kicker mb-2">{{ __('auth.sign_in') }}</p>
        <h1 class="text-3xl md:text-4xl font-semibold text-slate-900 mb-2">{{ __('auth.welcome_back') }}</h1>
        <p class="text-sm text-slate-600 mb-6">{{ __('auth.login_intro') }}</p>

        @if (session('status'))
            <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl p-3">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ step: {{ $errors->has('password') ? 2 : 1 }} }">
            @csrf

            <div class="flex items-center justify-between text-xs uppercase tracking-[0.08em] text-slate-500">
                <span :class="step === 1 ? 'text-slate-900 font-semibold' : ''">{{ __('auth.step_identity') }}</span>
                <span :class="step === 2 ? 'text-slate-900 font-semibold' : ''">{{ __('auth.step_access') }}</span>
            </div>

            <div x-show="step === 1" x-transition.opacity.duration.150ms>
                <label for="email" class="cw-label">{{ __('auth.email') }}</label>
                <input id="email" class="cw-field" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="alex@example.com" />
                @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror

                <button type="button" class="cw-button-primary mt-4" @click="step = 2">{{ __('auth.continue') }}</button>
            </div>

            <div x-show="step === 2" x-transition.opacity.duration.150ms x-cloak>
                <div class="mb-3 text-xs text-slate-500">{!! __('auth.signing_in_as', ['account' => '<span class="font-medium text-slate-700">' . (old('email') ?: __('auth.your_account')) . '</span>']) !!}</div>

                <label for="password" class="cw-label">{{ __('auth.password') }}</label>
                <input id="password" class="cw-field" type="password" name="password" required autocomplete="current-password" />
                @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror

                <label class="inline-flex items-center gap-2 text-sm text-slate-700 mt-3">
                    <input type="checkbox" name="remember" class="rounded border-slate-300" />
                    {{ __('auth.remember_me') }}
                </label>

                <div class="flex flex-wrap items-center justify-between gap-2 mt-4">
                    <button type="button" class="cw-button-secondary" @click="step = 1">{{ __('auth.back') }}</button>
                    @if (Route::has('password.request'))
                        <a class="text-sm text-slate-600 hover:text-slate-900" href="{{ route('password.request') }}">{{ __('auth.forgot_password') }}</a>
                    @endif
                    <button type="submit" class="cw-button-primary">{{ __('auth.sign_in') }}</button>
                </div>
            </div>
        </form>

        <p class="text-sm text-slate-600 mt-5">{{ __('auth.no_account') }} <a href="{{ route('register') }}" class="text-slate-900 font-medium">{{ __('auth.create_one') }}</a></p>
    </div>
</x-guest-layout>
