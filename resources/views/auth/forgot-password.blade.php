<x-guest-layout>
    <x-slot name="title">Forgot Password</x-slot>

    {{-- Acrylic Auth Card --}}
    <div class="acrylic-surface rounded-3xl shadow-elevation-3 p-8 sm:p-10">
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>
            <h1 class="text-3xl font-semibold text-text-primary mb-2">
                Forgot your password?
            </h1>
            <p class="text-body text-text-secondary">
                No problem. Just enter your email address and we'll send you a password reset link.
            </p>
        </div>

        {{-- Session Status --}}
        @if (session('status'))
            <div class="mb-6 bg-success/10 border-l-4 border-success p-4 rounded-r-xl backdrop-blur-sm">
                <p class="text-body-sm text-success-800">{{ session('status') }}</p>
            </div>
        @endif

        {{-- Forgot Password Form --}}
        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            {{-- Email Address --}}
            <div>
                <label for="email" class="block text-body-sm font-semibold text-text-primary mb-2">
                    Email Address
                </label>
                <input 
                    id="email" 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}"
                    required 
                    autofocus
                    class="w-full px-4 py-3 border border-border bg-white/90 backdrop-blur-sm rounded-xl text-text-primary placeholder:text-text-tertiary focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all shadow-sm @error('email') border-danger focus:ring-danger/50 @enderror"
                    placeholder="your@email.com"
                >
                @error('email')
                    <p class="mt-2 text-body-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Button --}}
            <button 
                type="submit" 
                class="w-full px-6 py-3.5 text-base font-semibold text-white bg-primary hover:bg-primary-700 rounded-xl transition-all focus:outline-none focus:ring-2 focus:ring-primary/50 focus:ring-offset-2 shadow-md hover:shadow-lg"
            >
                Email Password Reset Link
            </button>
        </form>

        {{-- Back to Login --}}
        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center text-body-sm font-medium text-primary hover:text-primary-700 transition-colors focus:outline-none focus:underline underline-offset-2">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to sign in
            </a>
        </div>
    </div>
</x-guest-layout>
