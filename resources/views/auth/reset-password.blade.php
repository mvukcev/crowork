<x-guest-layout>
    <x-slot name="title">Reset Password</x-slot>

    {{-- Acrylic Auth Card --}}
    <div class="acrylic-surface rounded-3xl shadow-elevation-3 p-8 sm:p-10">
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h1 class="text-3xl font-semibold text-text-primary mb-2">
                Reset your password
            </h1>
            <p class="text-body text-text-secondary">
                Enter your email and choose a new secure password
            </p>
        </div>

        {{-- Reset Password Form --}}
        <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
            @csrf

            {{-- Password Reset Token --}}
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            {{-- Email Address --}}
            <div>
                <label for="email" class="block text-body-sm font-semibold text-text-primary mb-2">
                    Email Address
                </label>
                <input 
                    id="email" 
                    type="email" 
                    name="email" 
                    value="{{ old('email', $request->email) }}"
                    required 
                    autofocus 
                    autocomplete="username"
                    class="w-full px-4 py-3 border border-border bg-white/90 backdrop-blur-sm rounded-xl text-text-primary placeholder:text-text-tertiary focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all shadow-sm @error('email') border-danger focus:ring-danger/50 @enderror"
                    placeholder="your@email.com"
                >
                @error('email')
                    <p class="mt-2 text-body-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-body-sm font-semibold text-text-primary mb-2">
                    New Password
                </label>
                <input 
                    id="password" 
                    type="password" 
                    name="password" 
                    required 
                    autocomplete="new-password"
                    class="w-full px-4 py-3 border border-border bg-white/90 backdrop-blur-sm rounded-xl text-text-primary placeholder:text-text-tertiary focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all shadow-sm @error('password') border-danger focus:ring-danger/50 @enderror"
                    placeholder="At least 8 characters"
                >
                @error('password')
                    <p class="mt-2 text-body-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="password_confirmation" class="block text-body-sm font-semibold text-text-primary mb-2">
                    Confirm New Password
                </label>
                <input 
                    id="password_confirmation" 
                    type="password" 
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password"
                    class="w-full px-4 py-3 border border-border bg-white/90 backdrop-blur-sm rounded-xl text-text-primary placeholder:text-text-tertiary focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all shadow-sm"
                    placeholder="Re-enter your new password"
                >
            </div>

            {{-- Submit Button --}}
            <button 
                type="submit" 
                class="w-full px-6 py-3.5 text-base font-semibold text-white bg-primary hover:bg-primary-700 rounded-xl transition-all focus:outline-none focus:ring-2 focus:ring-primary/50 focus:ring-offset-2 shadow-md hover:shadow-lg"
            >
                Reset Password
            </button>
        </form>
    </div>
</x-guest-layout>
