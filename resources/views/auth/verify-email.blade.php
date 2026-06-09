<x-guest-layout>
    <div class="cw-surface p-6 md:p-8">
        <div class="inline-flex items-center gap-3 rounded-2xl border border-slate-200/80 bg-white/75 px-4 py-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[rgba(var(--cw-orange)/0.12)] text-[rgba(var(--cw-orange)/1)]">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16v12H4z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4 7 8 6 8-6" />
                </svg>
            </span>
            <div>
                <p class="cw-kicker mb-1">{{ __('auth.verify_email') }}</p>
                <p class="text-xs text-slate-500">{{ __('auth.verify_email_title') }}</p>
            </div>
        </div>

        <h1 class="mt-6 text-3xl font-semibold tracking-tight text-slate-900">{{ __('auth.verify_email_title') }}</h1>
        <p class="mt-3 text-[17px] leading-relaxed text-slate-600">{{ __('auth.verify_email_intro') }}</p>

        <div class="mt-6 rounded-2xl border border-slate-200/85 bg-white/80 p-4 md:p-5">
            <p class="text-sm font-semibold text-slate-900">{{ __('auth.email') }}: {{ auth()->user()?->email }}</p>
            <p class="mt-1 text-sm text-slate-600">Open your inbox, click the verification link, then return here and refresh this page.</p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ __('auth.new_verification_link_sent') }}</div>
        @endif

        <div class="mt-6 grid gap-3 sm:grid-cols-2">
            <form method="POST" action="{{ route('verification.send') }}" class="sm:col-span-2">
                @csrf
                <button type="submit" class="cw-button-primary w-full justify-center">{{ __('auth.resend_verification_email') }}</button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="sm:col-span-1">
                @csrf
                <button type="submit" class="cw-button-secondary w-full justify-center">{{ __('auth.logout') }}</button>
            </form>

            <a href="{{ route('home') }}" class="cw-button-secondary flex w-full items-center justify-center sm:col-span-1">{{ __('auth.back_home') }}</a>
        </div>
    </div>
</x-guest-layout>
