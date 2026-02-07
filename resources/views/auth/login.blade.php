<x-guest-layout>
    <x-slot name="title">Sign In</x-slot>

    {{-- Acrylic Auth Card --}}
    <div class="acrylic-surface rounded-3xl shadow-elevation-3 p-8 sm:p-10">
        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-semibold text-text-primary mb-2">
                Sign in to CroWork
            </h1>
            <p class="text-body text-text-secondary">
                Access your account and manage your job applications
            </p>
        </div>

        {{-- Session Status --}}
        @if (session('status'))
            <div class="mb-6 bg-success/10 backdrop-blur-sm border border-success/20 p-4 rounded-xl">
                <p class="text-body-sm text-success">{{ session('status') }}</p>
            </div>
        @endif

        {{-- Login Form --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-6">
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
                    Password
                </label>
                <input 
                    id="password" 
                    type="password" 
                    name="password" 
                    required 
                    autocomplete="current-password"
                    class="w-full px-4 py-3 border border-border bg-white/90 backdrop-blur-sm rounded-xl text-text-primary placeholder:text-text-tertiary focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all shadow-sm @error('password') border-danger focus:ring-danger/50 @enderror"
                    placeholder="Enter your password"
                >
                @error('password')
                    <p class="mt-2 text-body-sm text-danger">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember Me & Forgot Password --}}
            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input 
                        id="remember_me" 
                        type="checkbox" 
                        name="remember"
                        class="w-5 h-5 rounded-md border-border text-primary focus:ring-2 focus:ring-primary/50 focus:ring-offset-0 transition-all cursor-pointer"
                    >
                    <span class="ml-2 text-body-sm text-text-secondary">Remember me</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-body-sm font-medium text-primary hover:text-primary-hover transition-colors focus:outline-none focus:underline">
                        Forgot password?
                    </a>
                @endif
            </div>

            {{-- Submit Button --}}
            <x-button 
                type="submit" 
                variant="primary"
                size="lg"
                class="w-full shadow-elevation-2 hover:shadow-elevation-3"
            >
                Sign In
            </x-button>
        </form>

        {{-- Divider --}}
        <div class="relative my-8">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-border/30"></div>
            </div>
            <div class="relative flex justify-center text-body-sm">
                <span class="px-4 bg-white/90 backdrop-blur-sm text-text-secondary">New to CroWork?</span>
            </div>
        </div>

        {{-- Sign Up Link --}}
        <div class="text-center">
            <p class="text-body text-text-secondary">
                Don't have an account? 
                <a href="{{ route('register') }}" class="font-semibold text-primary hover:text-primary-hover transition-colors focus:outline-none focus:underline">
                    Create account
                </a>
            </p>
        </div>
    </div>

    {{-- Help Text --}}
    <div class="mt-6 text-center">
        <p class="text-body-sm text-white/80 drop-shadow-sm">
            By signing in, you agree to our 
            <a href="{{ url('/terms') }}" class="text-white hover:text-white/90 underline transition-colors">Terms of Service</a> 
            and 
            <a href="{{ url('/privacy') }}" class="text-white hover:text-white/90 underline transition-colors">Privacy Policy</a>.
        </p>
    </div>
</x-guest-layout>
