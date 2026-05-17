<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex,nofollow">
        <title>{{ __('auth.continue_to_crowork') }}</title>
        <x-theme-init />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    @php
        $consentRequired = \App\Services\ConsentConfigService::isConsentRequired();
        $analyticsEnabled = \App\Services\AnalyticsConfigService::isAnalyticsEnabled();
        $marketingEnabled = \App\Services\MetaPixelConfigService::isTrackingEnabled();
    @endphp
    <body
        class="h-full cw-page"
        data-cw-consent-required="{{ $consentRequired ? '1' : '0' }}"
        data-cw-analytics-enabled="{{ $analyticsEnabled ? '1' : '0' }}"
        data-cw-marketing-enabled="{{ $marketingEnabled ? '1' : '0' }}"
    >
        @php
            $enabledLocales = collect(setting('enabled_locales', ['en', 'hr']))
                ->filter(fn ($locale) => is_string($locale) && $locale !== '')
                ->map(fn ($locale) => strtolower(trim((string) $locale)))
                ->values()
                ->all();

            if ($enabledLocales === []) {
                $enabledLocales = ['en'];
            }

            $activeLocale = strtolower((string) app()->getLocale());
            if (! in_array($activeLocale, $enabledLocales, true)) {
                $activeLocale = $enabledLocales[0];
            }

            $themePreference = strtolower((string) (session('theme') ?? request()->cookie('cw_theme') ?? 'system'));
            if (! in_array($themePreference, ['light', 'dark', 'system'], true)) {
                $themePreference = 'system';
            }

            $currentUrl = request()->fullUrl();
            $localeLabels = [
                'en' => __('settings.language_english'),
                'hr' => __('settings.language_croatian'),
            ];
            $themeLabels = [
                'system' => __('settings.theme_system'),
                'light' => __('settings.theme_light'),
                'dark' => __('settings.theme_dark'),
            ];
        @endphp
        <div class="min-h-screen flex flex-col cw-page-shell">
            <div class="cw-page-ambient cw-organic-bg" aria-hidden="true">
                <span class="cw-orb cw-orb-blue hidden md:block" style="width: 320px; height: 320px; left: -96px; top: 5rem;"></span>
                <span class="cw-orb cw-orb-orange hidden md:block" style="width: 260px; height: 260px; right: -80px; top: 10rem;"></span>
            </div>
            <header class="cw-container py-4" style="position: relative; z-index: 80;">
                <div class="flex items-center justify-between gap-3" data-cw-dropdown-root>
                    <a href="{{ route('home') }}" class="inline-flex items-center h-8">
                        <img src="{{ asset('assets/branding/CW-Logo-Dark.svg') }}" alt="CroWork" class="h-full cw-logo-on-light" loading="lazy">
                        <img src="{{ asset('assets/branding/CW-Logo-Light.svg') }}" alt="CroWork" class="h-full cw-logo-on-dark" loading="lazy">
                    </a>
                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('preferences.locale') }}" data-cw-locale-form>
                            @csrf
                            <input type="hidden" name="redirect" value="{{ $currentUrl }}">
                            <input type="hidden" name="locale" data-cw-locale-input value="{{ $activeLocale }}">
                        </form>

                        <form method="POST" action="{{ route('preferences.theme') }}" data-cw-theme-form>
                            @csrf
                            <input type="hidden" name="redirect" value="{{ $currentUrl }}">
                            <input type="hidden" name="theme" data-cw-theme-input value="{{ $themePreference }}">
                        </form>

                        <div class="relative">
                            <button type="button" class="cw-icon-control cw-icon-ghost" aria-label="{{ __('settings.language_toggle_aria') }}" aria-expanded="false" aria-controls="cw-access-language-menu" data-cw-dropdown-trigger="cw-access-language-menu">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.85" aria-hidden="true">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path stroke-linecap="round" d="M3 12h18M12 3c2.2 2.3 3.3 5.3 3.3 9S14.2 18.7 12 21M12 3C9.8 5.3 8.7 8.3 8.7 12s1.1 6.7 3.3 9"/>
                                </svg>
                            </button>
                            <div id="cw-access-language-menu" data-cw-dropdown-panel aria-hidden="true" style="display: none; z-index: 120;" class="cw-dropdown-panel absolute right-0 mt-2">
                                @foreach($enabledLocales as $locale)
                                    <button
                                        type="button"
                                        class="cw-dropdown-item {{ $activeLocale === $locale ? 'cw-dropdown-item-active' : '' }}"
                                        data-cw-dropdown-select="locale"
                                        data-cw-locale-value="{{ $locale }}"
                                        data-cw-language-option="{{ $locale }}"
                                    >
                                        <span>{{ $localeLabels[$locale] ?? strtoupper($locale) }}</span>
                                        @if($activeLocale === $locale)
                                            <span class="text-[11px]">✓</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="relative">
                            <button type="button" class="cw-icon-control cw-icon-ghost" aria-label="{{ __('settings.theme_toggle_aria') }}" aria-expanded="false" aria-controls="cw-access-theme-menu" data-cw-dropdown-trigger="cw-access-theme-menu">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.85" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.2M12 18.8V21M5.64 5.64l1.56 1.56M16.8 16.8l1.56 1.56M3 12h2.2M18.8 12H21M5.64 18.36l1.56-1.56M16.8 7.2l1.56-1.56"/>
                                    <circle cx="12" cy="12" r="3.8"/>
                                </svg>
                            </button>
                            <div id="cw-access-theme-menu" data-cw-dropdown-panel aria-hidden="true" style="display: none; z-index: 120;" class="cw-dropdown-panel absolute right-0 mt-2">
                                @foreach($themeLabels as $value => $label)
                                    <button
                                        type="button"
                                        class="cw-dropdown-item {{ $themePreference === $value ? 'cw-dropdown-item-active' : '' }}"
                                        data-cw-dropdown-select="theme"
                                        data-cw-theme-value="{{ $value }}"
                                        data-cw-theme-option="{{ $value }}"
                                    >
                                        <span>{{ $label }}</span>
                                        @if($themePreference === $value)
                                            <span class="text-[11px]">✓</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="cw-container flex-1 flex items-start sm:items-center justify-center pb-8 sm:pb-12">
                <section class="cw-content-form">
                    <h1 class="text-3xl font-semibold text-slate-900 mb-2">{{ __('auth.continue_to_crowork') }}</h1>
                    <p class="text-sm text-slate-600 mb-6">{{ __('auth.use_email_to_sign_in') }}</p>

                    @if (session('status'))
                        <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl p-3">{{ session('status') }}</div>
                    @endif

                    {{-- ─── Stage: EMAIL ──────────────────────────────────────── --}}
                    @if ($stage === 'email')
                        @error('email')
                            <div class="mb-4 rounded-xl bg-red-50 border border-red-200 p-3 text-sm text-red-700">{{ $message }}</div>
                        @enderror

                        <form method="POST" action="{{ route('access.email') }}" class="space-y-4" data-cw-track-submit="registration_start">
                            @csrf
                            <input type="hidden" name="intent_type" value="{{ $intentType ?? 'worker' }}">
                            <div class="w-full">
                                <label class="cw-label" for="email">{{ __('auth.email') }}</label>
                                   <input id="email" class="cw-field w-full" type="email" name="email" value="{{ old('email', $email ?? '') }}" required autofocus autocomplete="username" placeholder="alex@example.com">
                                @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <button type="submit" class="cw-button-primary w-full">{{ __('auth.continue') }}</button>
                        </form>

                    {{-- ─── Stage: LOGIN ──────────────────────────────────────── --}}
                    @elseif ($stage === 'login')
                        <form method="POST" action="{{ route('access.login') }}" class="space-y-4" data-cw-track-submit="login">
                            @csrf
                            <div class="w-full">
                                <label class="cw-label" for="email">{{ __('auth.email') }}</label>
                                   <input id="email" class="cw-field w-full" type="email" name="email" value="{{ $email }}" required autocomplete="username" readonly>
                            </div>
                            <div class="w-full">
                                <label class="cw-label" for="password">{{ __('auth.password') }}</label>
                                <input id="password" class="cw-field" type="password" name="password" required autocomplete="current-password" autofocus>
                                @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                <input type="checkbox" name="remember" value="1" class="rounded border-slate-300" @checked(old('remember'))>
                                {{ __('auth.remember_me') }}
                            </label>
                            <button type="submit" class="cw-button-primary w-full">{{ __('auth.sign_in') }}</button>
                            <button type="submit" form="cwAccessResetFormLogin" class="inline-flex justify-center w-full text-sm text-slate-600 hover:text-slate-900">{{ __('auth.use_different_email') }}</button>
                        </form>
                        <form id="cwAccessResetFormLogin" method="POST" action="{{ route('access.reset') }}" data-cw-track-submit="auth_back_to_email">
                            @csrf
                        </form>

                    {{-- ─── Stage: VERIFY CODE ────────────────────────────────── --}}
                    @elseif ($stage === 'verify_code')
                        @php
                            $digitClass = 'w-full h-14 text-center text-2xl font-semibold border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white transition-colors caret-transparent';
                        @endphp
                        <div x-data="cwCodeInput()" @paste.prevent="handlePaste($event)">
                            <p class="text-sm text-slate-600 mb-1">
                                {{ __('auth.we_sent_code') }} <strong>{{ $email }}</strong>.
                            </p>
                            <p class="text-xs text-slate-500 mb-5">{{ __('auth.code_expires_in') }}</p>
                            @if(isset($devCode) && $devCode)
                                <div class="rounded-xl bg-amber-50 border border-amber-200 p-3 mb-4 text-sm text-amber-800">
                                    <strong>{{ __('auth.dev_mode') }}</strong> — {{ __('auth.verification_code') }}: <code class="font-mono font-bold tracking-widest">{{ $devCode }}</code>
                                </div>
                            @endif
                            @if(session('resend_success'))
                                <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 mb-4 text-sm text-emerald-700">{{ session('resend_success') }}</div>
                            @endif
                            @error('code')
                                <div class="rounded-xl bg-red-50 border border-red-200 p-3 mb-4 text-sm text-red-700">{{ $message }}</div>
                            @enderror
                            @error('resend')
                                <div class="rounded-xl bg-red-50 border border-red-200 p-3 mb-4 text-sm text-red-700">{{ $message }}</div>
                            @enderror
                            <form method="POST" action="{{ route('access.verify-code') }}" @submit="onSubmit($event)" class="space-y-5" data-cw-track-submit="email_verification_completed">
                                @csrf
                                <input type="hidden" name="email" value="{{ $email }}">
                                <input type="hidden" name="intent_type" value="{{ $intentType ?? 'worker' }}">
                                <input type="hidden" name="code" :value="fullCode">
                                <div class="w-full">
                                    <label class="cw-label" for="email">{{ __('auth.email') }}</label>
                                    <input id="email" class="cw-field w-full" type="email" name="email" value="{{ $email }}" required autocomplete="username" readonly>
                                </div>
                                <div>
                                    <label class="cw-label sr-only">{{ __('auth.verification_code') }}</label>
                                    <div class="grid grid-cols-6 gap-2">
                                        <input type="text" inputmode="numeric" pattern="[0-9]" maxlength="1"
                                               x-ref="d0" :value="digits[0]"
                                               @input="onInput(0, $event)" @keydown.backspace.prevent="onBackspace(0)"
                                               autocomplete="one-time-code" aria-label="{{ __('auth.verification_code') }} 1/6"
                                               class="{{ $digitClass }}">
                                        <input type="text" inputmode="numeric" pattern="[0-9]" maxlength="1"
                                               x-ref="d1" :value="digits[1]"
                                               @input="onInput(1, $event)" @keydown.backspace.prevent="onBackspace(1)"
                                               aria-label="{{ __('auth.verification_code') }} 2/6"
                                               class="{{ $digitClass }}">
                                        <input type="text" inputmode="numeric" pattern="[0-9]" maxlength="1"
                                               x-ref="d2" :value="digits[2]"
                                               @input="onInput(2, $event)" @keydown.backspace.prevent="onBackspace(2)"
                                               aria-label="{{ __('auth.verification_code') }} 3/6"
                                               class="{{ $digitClass }}">
                                        <input type="text" inputmode="numeric" pattern="[0-9]" maxlength="1"
                                               x-ref="d3" :value="digits[3]"
                                               @input="onInput(3, $event)" @keydown.backspace.prevent="onBackspace(3)"
                                               aria-label="{{ __('auth.verification_code') }} 4/6"
                                               class="{{ $digitClass }}">
                                        <input type="text" inputmode="numeric" pattern="[0-9]" maxlength="1"
                                               x-ref="d4" :value="digits[4]"
                                               @input="onInput(4, $event)" @keydown.backspace.prevent="onBackspace(4)"
                                               aria-label="{{ __('auth.verification_code') }} 5/6"
                                               class="{{ $digitClass }}">
                                        <input type="text" inputmode="numeric" pattern="[0-9]" maxlength="1"
                                               x-ref="d5" :value="digits[5]"
                                               @input="onInput(5, $event)" @keydown.backspace.prevent="onBackspace(5)"
                                               aria-label="{{ __('auth.verification_code') }} 6/6"
                                               class="{{ $digitClass }}">
                                    </div>
                                </div>
                                <button type="submit"
                                    class="cw-button-primary w-full"
                                    :class="{ 'opacity-50 cursor-not-allowed': fullCode.length < 6 }"
                                >{{ __('auth.verify_email') }}</button>
                            </form>
                            <form method="POST" action="{{ route('access.resend-code') }}" id="cwResendForm" data-cw-track-submit="email_verification_resend">
                                @csrf
                                <input type="hidden" name="email" value="{{ $email }}">
                                <input type="hidden" name="intent_type" value="{{ $intentType ?? 'worker' }}">
                            </form>
                            <div class="mt-4 text-center text-sm" id="cwResendWrap">
                                <span id="cwResendCountdown" class="text-slate-500">
                                    {!! __('auth.resend_available_in', ['seconds' => '<span id="cwResendSeconds" class="font-medium tabular-nums">' . (($canResendImmediately ?? false) ? 0 : 60) . '</span>']) !!}
                                </span>
                                <button
                                    type="button"
                                    id="cwResendBtn"
                                    onclick="document.getElementById('cwResendForm').submit()"
                                    class="text-slate-700 hover:text-slate-900 underline"
                                    style="display:none"
                                >{{ __('auth.resend_code') }}</button>
                            </div>
                            <form method="POST" action="{{ route('access.reset') }}" class="mt-4 text-center">
                                @csrf
                                <button type="submit" class="text-sm text-slate-600 hover:text-slate-900">{{ __('auth.use_different_email') }}</button>
                            </form>
                        </div>

                    {{-- ─── Stage: REGISTER ───────────────────────────────────── --}}
                    @else
                        <form method="POST" action="{{ route('access.register') }}" class="space-y-4" data-cw-track-submit="registration_complete">
                            @csrf
                            <div class="w-full">
                                <label class="cw-label" for="email">{{ __('auth.email') }}</label>
                                <input id="email" class="cw-field w-full" type="email" name="email" value="{{ $email }}" required autocomplete="username" readonly>
                                @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div class="w-full">
                                <label class="cw-label" for="name">{{ __('auth.name') }}</label>
                                <input id="name" class="cw-field w-full" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div class="w-full">
                                <label class="cw-label" for="account_type">{{ __('auth.account_type') }}</label>
                                <select id="account_type" name="account_type" class="cw-field w-full" required>
                                    <option value="worker" @selected(old('account_type', $intentType ?? 'worker') === 'worker')>{{ __('auth.worker') }}</option>
                                    <option value="employer" @selected(old('account_type', $intentType ?? 'worker') === 'employer')>{{ __('auth.employer') }}</option>
                                </select>
                                @error('account_type')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div class="w-full">
                                <label class="cw-label" for="password">{{ __('auth.password') }}</label>
                                <input id="password" class="cw-field w-full" type="password" name="password" required autocomplete="new-password">
                                @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div class="w-full">
                                <label class="cw-label" for="password_confirmation">{{ __('auth.confirm_password') }}</label>
                                <input id="password_confirmation" class="cw-field w-full" type="password" name="password_confirmation" required autocomplete="new-password">
                            </div>
                            <button type="submit" class="cw-button-primary w-full">{{ __('auth.create_account') }}</button>
                            <button type="submit" form="cwAccessResetFormRegister" class="inline-flex justify-center w-full text-sm text-slate-600 hover:text-slate-900">{{ __('auth.use_different_email') }}</button>
                        </form>
                        <form id="cwAccessResetFormRegister" method="POST" action="{{ route('access.reset') }}">
                            @csrf
                        </form>
                    @endif

                    <div class="mt-6 text-xs text-slate-500 leading-relaxed text-center">
                        {!! __('auth.by_continuing', [
                            'terms' => '<a href="' . route('terms') . '" class="text-slate-700 hover:text-slate-900">' . __('footer.terms') . '</a>',
                            'privacy' => '<a href="' . route('privacy') . '" class="text-slate-700 hover:text-slate-900">' . __('footer.privacy') . '</a>',
                        ]) !!}
                    </div>
                </section>
            </main>

            <footer class="cw-container pb-6 text-center text-xs text-slate-500">
                <a href="{{ route('cookies') }}" class="hover:text-slate-900">{{ __('footer.cookie.link_label') }}</a>
            </footer>

            <section class="cw-cookie-banner cw-soft-reveal" data-cw-cookie-banner hidden>
                <div class="cw-cookie-inner">
                    <p class="cw-cookie-text">
                        {!! __('footer.cookie.text', [
                            'link' => '<a href="' . route('cookies') . '" class="font-medium text-slate-900 underline">' . __('footer.cookie.link_label') . '</a>',
                        ]) !!}
                    </p>
                    <div class="cw-cookie-actions">
                        <button type="button" class="cw-button-secondary" data-cw-cookie-choice="required">{{ __('footer.cookie.required_only') }}</button>
                        <button type="button" class="cw-button-primary" data-cw-cookie-choice="all">{{ __('footer.cookie.allow_all') }}</button>
                    </div>
                </div>
            </section>
        </div>

        <script>
            function cwCodeInput() {
                return {
                    digits: ['', '', '', '', '', ''],

                    get fullCode() {
                        return this.digits.join('');
                    },

                    focus(idx) {
                        const el = this.$refs['d' + idx];
                        if (el) this.$nextTick(() => el.focus());
                    },

                    onInput(idx, e) {
                        const raw = e.target.value.replace(/\D/g, '');
                        this.digits[idx] = raw ? raw.slice(-1) : '';
                        e.target.value = this.digits[idx];
                        if (this.digits[idx] && idx < 5) this.focus(idx + 1);
                    },

                    onBackspace(idx) {
                        if (this.digits[idx] !== '') {
                            this.digits[idx] = '';
                            const el = this.$refs['d' + idx];
                            if (el) el.value = '';
                        } else if (idx > 0) {
                            this.digits[idx - 1] = '';
                            const prev = this.$refs['d' + (idx - 1)];
                            if (prev) prev.value = '';
                            this.focus(idx - 1);
                        }
                    },

                    handlePaste(e) {
                        const text = (e.clipboardData || window.clipboardData)
                            .getData('text')
                            .replace(/\D/g, '');
                        if (!text) return;
                        for (let i = 0; i < 6; i++) {
                            this.digits[i] = text[i] || '';
                            const el = this.$refs['d' + i];
                            if (el) el.value = this.digits[i];
                        }
                        this.focus(Math.min(text.length, 5));
                    },

                    onSubmit(e) {
                        const code = this.fullCode;
                        if (code.length < 6) {
                            e.preventDefault();
                            return;
                        }
                        // Ensure the hidden input has the value regardless of binding timing
                        const codeInput = e.target.querySelector('input[name="code"]');
                        if (codeInput) codeInput.value = code;
                    },
                };
            }

            function cwResendTimer(initialSeconds) {
                return {
                    seconds: Math.max(0, parseInt(initialSeconds) || 0),

                    init() {
                        if (this.seconds > 0) this.startTimer();
                    },

                    startTimer() {
                        const iv = setInterval(() => {
                            this.seconds--;
                            if (this.seconds <= 0) {
                                this.seconds = 0;
                                clearInterval(iv);
                            }
                        }, 1000);
                    },
                };
            }
            // Vanilla fallback: populate hidden code input from digit fields if Alpine hasn't done it
            document.addEventListener('DOMContentLoaded', function () {
                // Resend countdown timer
                var secondsEl = document.getElementById('cwResendSeconds');
                var countdownEl = document.getElementById('cwResendCountdown');
                var resendBtn = document.getElementById('cwResendBtn');
                if (secondsEl && countdownEl && resendBtn) {
                    var seconds = parseInt(secondsEl.textContent, 10) || 0;
                    if (seconds <= 0) {
                        countdownEl.style.display = 'none';
                        resendBtn.style.display = '';
                    } else {
                        var iv = setInterval(function () {
                            seconds--;
                            secondsEl.textContent = seconds;
                            if (seconds <= 0) {
                                clearInterval(iv);
                                countdownEl.style.display = 'none';
                                resendBtn.style.display = '';
                            }
                        }, 1000);
                    }
                }
                const verifyForm = document.querySelector('form[action*="verify-code"]');
                if (!verifyForm) return;
                verifyForm.addEventListener('submit', function () {
                    const codeInput = verifyForm.querySelector('input[name="code"]');
                    if (!codeInput || codeInput.value.length === 6) return;
                    const digits = Array.from(
                        verifyForm.querySelectorAll('.grid input[type="text"]')
                    ).map(function (el) { return el.value; }).join('');
                    codeInput.value = digits;
                });
            });
        </script>
    </body>
</html>
    </body>
</html>
