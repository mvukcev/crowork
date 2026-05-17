<x-guest-layout>
    <div class="cw-surface p-6 md:p-8">
        <p class="cw-kicker mb-2">{{ __('auth.create_account') }}</p>
        <h1 class="text-3xl md:text-4xl font-semibold text-slate-900 mb-2">{{ __('auth.register') }}</h1>
        <p class="text-sm text-slate-600 mb-6">{{ __('auth.use_email_to_sign_in') }}</p>

        <form method="POST" action="{{ route('register') }}" class="space-y-4" x-data="{ step: {{ ($errors->has('password') || $errors->has('password_confirmation') || $errors->has('accept_terms') || $errors->has('accept_privacy')) ? 2 : 1 }} }">
            @csrf

            <div class="flex items-center justify-between text-xs uppercase tracking-[0.08em] text-slate-500 mb-6">
                <span :class="step === 1 ? 'text-slate-900 font-semibold' : ''">{{ __('auth.step_basic_info') }}</span>
                <div class="flex-1 mx-2 h-px bg-slate-200"></div>
                <span :class="step === 2 ? 'text-slate-900 font-semibold' : ''">{{ __('auth.step_security') }}</span>
            </div>

            <div x-show="step === 1" x-transition.opacity.duration.150ms>
                <div>
                    <label class="cw-label" for="name">{{ __('auth.your_full_name') }}</label>
                    <input id="name" class="cw-field" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="e.g., Ana Horvat">
                    @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mt-4">
                    <label class="cw-label" for="email">{{ __('auth.email_address') }}</label>
                    <input id="email" class="cw-field" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="ana@example.com">
                    @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mt-4">
                    <label class="cw-label" for="role">{{ __('auth.account_type') }}</label>
                    <select id="role" name="role" class="cw-field" required>
                        <option value="worker" @selected(old('role', 'worker') === 'worker')>{{ __('auth.worker_option') }}</option>
                        <option value="employer" @selected(old('role') === 'employer')>{{ __('auth.employer_option') }}</option>
                    </select>
                    @error('role')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <button type="button" class="cw-button-primary w-full mt-6" @click="step = 2">{{ __('auth.continue') }}</button>
                
                <p class="text-xs text-slate-500 mt-4 text-center">{{ __('auth.data_safe_note') }}</p>
            </div>

            <div x-show="step === 2" x-transition.opacity.duration.150ms x-cloak>
                <p class="text-sm text-slate-600 mb-4">{{ __('auth.create_strong_password') }}</p>
                
                <div>
                    <label class="cw-label" for="password">{{ __('auth.password') }}</label>
                    <input id="password" class="cw-field" type="password" name="password" required autocomplete="new-password" placeholder="Minimum 8 characters">
                    @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mt-4">
                    <label class="cw-label" for="password_confirmation">{{ __('auth.confirm_password') }}</label>
                    <input id="password_confirmation" class="cw-field" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="{{ __('auth.confirm_password') }}">
                </div>

                <div class="mt-6 space-y-3">
                    <label class="inline-flex items-start gap-3 text-sm text-slate-700">
                        <input type="checkbox" name="accept_terms" value="1" class="mt-1 rounded border-slate-300" @checked(old('accept_terms')) required>
                        <span>{!! __('auth.agree_terms', ['terms' => '<a href="' . route('terms') . '" target="_blank" class="font-medium text-slate-900 underline hover:text-slate-700">' . __('auth.terms_of_use') . '</a>']) !!}</span>
                    </label>
                    @error('accept_terms')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror

                    <label class="inline-flex items-start gap-3 text-sm text-slate-700">
                        <input type="checkbox" name="accept_privacy" value="1" class="mt-1 rounded border-slate-300" @checked(old('accept_privacy')) required>
                        <span>{!! __('auth.agree_privacy', ['privacy' => '<a href="' . route('privacy') . '" target="_blank" class="font-medium text-slate-900 underline hover:text-slate-700">' . __('auth.privacy_policy') . '</a>']) !!}</span>
                    </label>
                    @error('accept_privacy')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center gap-3 mt-6">
                    <button type="button" class="cw-button-secondary flex-1" @click="step = 1">{{ __('auth.back') }}</button>
                    <button type="submit" class="cw-button-primary flex-1">{{ __('auth.create_account') }}</button>
                </div>
            </div>
        </form>

        <p class="text-sm text-slate-600 mt-6 text-center">{{ __('auth.already_have_account') }} <a href="{{ route('login') }}" class="text-slate-900 font-semibold hover:underline">{{ __('auth.sign_in') }}</a></p>
    </div>
</x-guest-layout>
