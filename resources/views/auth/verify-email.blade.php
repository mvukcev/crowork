<x-guest-layout>
    <div class="cw-surface p-7 md:p-8">
        <p class="cw-kicker mb-2">Verify email</p>
        <h1 class="text-2xl font-semibold text-slate-900 mb-2">Check your inbox</h1>
        <p class="text-sm text-slate-600 mb-6">Before getting started, confirm your email by clicking the verification link we sent.</p>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl p-3">A new verification link has been sent.</div>
        @endif

        <div class="flex flex-wrap gap-2">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="cw-button-primary">Resend verification email</button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="cw-button-secondary">Log out</button>
            </form>
        </div>
    </div>
</x-guest-layout>
