<x-guest-layout>
    <div class="cw-surface p-6 md:p-8">
        <p class="cw-kicker mb-2">{{ __('auth.create_employer_account') }}</p>
        <h1 class="text-3xl md:text-4xl font-semibold text-slate-900 mb-2">{{ __('auth.create_employer_account') }}</h1>
        <p class="text-sm text-slate-600 mb-6">{{ __('auth.use_email_to_sign_in') }}</p>

        @if(session('status'))
            <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl p-3">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('employer.register') }}" class="space-y-4" x-data="{ step: {{ ($errors->has('password') || $errors->has('password_confirmation') || $errors->has('accept_terms') || $errors->has('accept_privacy')) ? 2 : 1 }} }">
            @csrf

            <div class="flex items-center justify-between text-xs uppercase tracking-[0.08em] text-slate-500 mb-6">
                <span :class="step === 1 ? 'text-slate-900 font-semibold' : ''">{{ __('auth.step_company') }}</span>
                <div class="flex-1 mx-2 h-px bg-slate-200"></div>
                <span :class="step === 2 ? 'text-slate-900 font-semibold' : ''">{{ __('auth.step_access') }}</span>
            </div>

            <div x-show="step === 1" x-transition.opacity.duration.150ms>
                <div>
                    <label class="cw-label" for="company_name">{{ __('auth.company_name') }}</label>
                    <input id="company_name" name="company_name" class="cw-field" value="{{ old('company_name') }}" placeholder="e.g., Tech Solutions Ltd" required @if($errors->has('company_name')) aria-invalid="true" @endif>
                    <x-input-error :messages="$errors->get('company_name')" />
                </div>

                <div class="mt-4">
                    <label class="cw-label" for="contact_name">{{ __('auth.contact_name') }}</label>
                    <input id="contact_name" name="contact_name" class="cw-field" value="{{ old('contact_name') }}" placeholder="Your full name" required @if($errors->has('contact_name')) aria-invalid="true" @endif>
                    <x-input-error :messages="$errors->get('contact_name')" />
                </div>

                <div class="mt-4">
                    <label class="cw-label" for="email">{{ __('auth.work_email') }}</label>
                    <input id="email" type="email" name="email" class="cw-field" value="{{ old('email') }}" placeholder="you@company.com" required @if($errors->has('email')) aria-invalid="true" @endif>
                    <x-input-error :messages="$errors->get('email')" />
                </div>

                <div class="mt-4">
                    <label class="cw-label" for="city">{{ __('auth.city_hiring') }}</label>
                    <input id="city" name="city" class="cw-field" value="{{ old('city') }}" placeholder="e.g., Zagreb, Split" required @if($errors->has('city')) aria-invalid="true" @endif>
                    <x-input-error :messages="$errors->get('city')" />
                </div>

                <div class="mt-4">
                    <label class="cw-label" for="oib">{{ __('auth.employer_oib') }}</label>
                    <input id="oib" name="oib" class="cw-field" value="{{ old('oib') }}" placeholder="{{ __('auth.employer_oib_placeholder') }}" maxlength="32" required @if($errors->has('oib')) aria-invalid="true" @endif>
                    <x-input-error :messages="$errors->get('oib')" />
                </div>

                <button type="button" class="cw-button-primary w-full mt-6" @click="step = 2">{{ __('auth.continue') }}</button>
                
                <p class="text-xs text-slate-500 mt-4 text-center">{{ __('auth.data_secure_encryption') }}</p>
            </div>

            <div x-show="step === 2" x-transition.opacity.duration.150ms x-cloak>
                <p class="text-sm text-slate-600 mb-4">{{ __('auth.setup_login_credentials') }}</p>
                
                <div>
                    <label class="cw-label" for="password">{{ __('auth.password') }}</label>
                    <input id="password" type="password" name="password" class="cw-field" placeholder="Create a strong password" required @if($errors->has('password')) aria-invalid="true" @endif>
                    <x-input-error :messages="$errors->get('password')" />
                </div>

                <div class="mt-4">
                    <label class="cw-label" for="password_confirmation">{{ __('auth.confirm_password') }}</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="cw-field" placeholder="{{ __('auth.confirm_password') }}" required>
                </div>

                <div class="mt-6 space-y-3">
                    <label class="inline-flex items-start gap-3 text-sm text-slate-700">
                        <input type="checkbox" name="accept_terms" value="1" class="mt-1 rounded border-slate-300" @checked(old('accept_terms')) required>
                        <span>{!! __('auth.agree_terms', ['terms' => '<a href="' . route('terms') . '" target="_blank" class="font-medium text-slate-900 underline hover:text-slate-700">' . __('auth.terms_of_use') . '</a>']) !!}</span>
                    </label>
                    <x-input-error :messages="$errors->get('accept_terms')" />

                    <label class="inline-flex items-start gap-3 text-sm text-slate-700">
                        <input type="checkbox" name="accept_privacy" value="1" class="mt-1 rounded border-slate-300" @checked(old('accept_privacy')) required>
                        <span>{!! __('auth.agree_privacy', ['privacy' => '<a href="' . route('privacy') . '" target="_blank" class="font-medium text-slate-900 underline hover:text-slate-700">' . __('auth.privacy_policy') . '</a>']) !!}</span>
                    </label>
                    <x-input-error :messages="$errors->get('accept_privacy')" />
                </div>

                <div class="flex items-center gap-3 mt-6">
                    <button type="button" class="cw-button-secondary flex-1" @click="step = 1">{{ __('auth.back') }}</button>
                    <button type="submit" class="cw-button-primary flex-1">{{ __('auth.create_employer_account') }}</button>
                </div>
            </div>
        </form>

        <p class="text-sm text-slate-600 mt-6 text-center">{{ __('auth.already_have_account') }} <a href="{{ route('login') }}" class="text-slate-900 font-semibold hover:underline">{{ __('auth.signin_here') }}</a></p>
    </div>
</x-guest-layout>
