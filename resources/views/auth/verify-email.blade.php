<x-guest-layout>
    <x-slot name="title">Verify Email</x-slot>

    {{-- Acrylic Auth Card --}}
    <div class="acrylic-surface rounded-3xl shadow-elevation-3 p-8 sm:p-10">
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h1 class="text-3xl font-semibold text-text-primary mb-2">
                Verify your email
            </h1>
            <p class="text-body text-text-secondary">
                Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just emailed to you.
            </p>
        </div>

        {{-- Success Message --}}
        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 bg-success/10 border-l-4 border-success p-4 rounded-r-xl backdrop-blur-sm">
                <p class="text-body-sm text-success-800">
                    A new verification link has been sent to your email address.
                </p>
            </div>
        @endif

        {{-- Info Box --}}
        <div class="mb-6 bg-primary/10 border border-primary/20 rounded-xl p-4 backdrop-blur-sm">
            <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 text-primary flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1 text-body-sm text-text-primary">
                    <p class="font-semibold mb-1">Didn't receive the email?</p>
                    <p class="text-text-secondary">Check your spam folder or click the button below to resend the verification email.</p>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="space-y-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button 
                    type="submit" 
                    class="w-full px-6 py-3.5 text-base font-semibold text-white bg-primary hover:bg-primary-700 rounded-xl transition-all focus:outline-none focus:ring-2 focus:ring-primary/50 focus:ring-offset-2 shadow-md hover:shadow-lg"
                >
                    Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button 
                    type="submit" 
                    class="w-full px-6 py-3 text-base font-medium text-neutral-700 bg-white hover:bg-neutral-50 border border-neutral-300 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                >
                    Log Out
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
