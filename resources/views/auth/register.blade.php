<x-guest-layout>
    <x-slot name="title">Create Account</x-slot>

    {{-- Acrylic Auth Card --}}
    <div class="acrylic-surface rounded-3xl shadow-elevation-3 p-7 sm:p-9 ring-1 ring-white/70">
        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-semibold text-text-primary mb-2 text-balance">
                Create your account
            </h1>
            <p class="text-body text-text-secondary">
                Join CroWork and start your journey in Croatia
            </p>
        </div>

        {{-- Register Form --}}
        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            {{-- Name --}}
            <div>
                <label for="name" class="block text-body-sm font-semibold text-text-primary mb-2">
                    Full Name
                </label>
                <input 
                    id="name" 
                    type="text" 
                    name="name" 
                    value="{{ old('name') }}"
                    required 
                    autofocus 
                    autocomplete="name"
                    class="w-full px-4 py-3 border border-border bg-white/90 backdrop-blur-sm rounded-xl text-text-primary placeholder:text-text-tertiary focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all shadow-sm @error('name') border-danger focus:ring-danger/50 @enderror"
                    placeholder="John Doe"
                >
                @error('name')
                    <p class="mt-2 text-body-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

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
                    autocomplete="username"
                    class="w-full px-4 py-3 border border-border bg-white/90 backdrop-blur-sm rounded-xl text-text-primary placeholder:text-text-tertiary focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all shadow-sm @error('email') border-danger focus:ring-danger/50 @enderror"
                    placeholder="your@email.com"
                >
                @error('email')
                    <p class="mt-2 text-body-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            {{-- Role Selection --}}
            <div>
                <label for="role" class="block text-body-sm font-semibold text-text-primary mb-2">
                    I am a
                </label>
                <select 
                    id="role" 
                    name="role" 
                    required
                    class="w-full px-4 py-3 border border-border bg-white/90 backdrop-blur-sm rounded-xl text-text-primary focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all shadow-sm @error('role') border-danger focus:ring-danger/50 @enderror"
                >
                    <option value="worker" {{ old('role', 'worker') == 'worker' ? 'selected' : '' }}>
                        Worker – Looking for jobs in Croatia
                    </option>
                    <option value="employer" {{ old('role') == 'employer' ? 'selected' : '' }}>
                        Employer – Hiring international talent
                    </option>
                </select>
                @error('role')
                    <p class="mt-2 text-body-sm text-danger">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-caption text-text-tertiary">
                    Choose the option that best describes you. You can't change this later.
                </p>
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-body-sm font-semibold text-text-primary mb-2">
                    Password
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
                    Confirm Password
                </label>
                <input 
                    id="password_confirmation" 
                    type="password" 
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password"
                    class="w-full px-4 py-3 border border-border bg-white/90 backdrop-blur-sm rounded-xl text-text-primary placeholder:text-text-tertiary focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all shadow-sm"
                    placeholder="Re-enter your password"
                >
            </div>

            {{-- Submit Button --}}
            <button 
                type="submit" 
                class="w-full px-6 py-3.5 text-base font-semibold text-white bg-primary hover:bg-primary-700 rounded-xl transition-all focus:outline-none focus:ring-2 focus:ring-primary/50 focus:ring-offset-2 shadow-md hover:shadow-lg"
            >
                Create Account
            </button>
        </form>

        {{-- Divider --}}
        <div class="relative my-8">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-divider"></div>
            </div>
            <div class="relative flex justify-center text-body-sm">
                <span class="px-4 bg-white/80 text-text-secondary">Already have an account?</span>
            </div>
        </div>

        {{-- Sign In Link --}}
        <div class="text-center">
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center w-full px-6 py-3 text-base font-medium text-text-primary bg-white/50 hover:bg-white/80 border border-border rounded-xl transition-all focus:outline-none focus:ring-2 focus:ring-primary/50 focus:ring-offset-2 shadow-sm">
                Sign In Instead
            </a>
        </div>
    </div>

    {{-- Help Text --}}
    <div class="mt-6 text-center">
        <p class="text-body-sm text-white/70">
            By creating an account, you agree to our 
            <a href="{{ url('/terms') }}" class="text-white font-medium hover:text-white/90 underline underline-offset-2 transition-colors">Terms of Service</a> 
            and 
            <a href="{{ url('/privacy') }}" class="text-white font-medium hover:text-white/90 underline underline-offset-2 transition-colors">Privacy Policy</a>.
        </p>
    </div>
</x-guest-layout>
