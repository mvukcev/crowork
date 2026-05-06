<x-guest-layout>
    <x-slot name="title">Create Employer Account</x-slot>

    {{-- Acrylic Auth Card --}}
    <div class="acrylic-surface rounded-3xl shadow-elevation-3 p-7 sm:p-9 ring-1 ring-white/70">

        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-semibold text-text-primary mb-2 text-balance">
                Create Employer Account
            </h1>
            <p class="text-body text-text-secondary m-0">
                Post jobs and find verified international workers
            </p>
        </div>

        {{-- Session status --}}
        @if (session('status'))
            <div class="mb-6 bg-success/10 backdrop-blur-sm border border-success/20 p-4 rounded-xl">
                <p class="text-body-sm text-success m-0">{{ session('status') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('employer.register') }}" class="space-y-5">
            @csrf

            {{-- Company Name --}}
            <div>
                <label for="company_name" class="block text-body-sm font-semibold text-text-primary mb-2">
                    Company Name
                </label>
                <input
                    id="company_name"
                    type="text"
                    name="company_name"
                    value="{{ old('company_name') }}"
                    required
                    autofocus
                    autocomplete="organization"
                    placeholder="Your company name"
                    class="w-full px-4 py-3 border border-border bg-white/90 backdrop-blur-sm rounded-xl text-text-primary placeholder:text-text-tertiary focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all shadow-sm @error('company_name') border-danger focus:ring-danger/50 @enderror"
                >
                @error('company_name')
                    <p class="mt-2 text-body-sm text-danger m-0">{{ $message }}</p>
                @enderror
            </div>

            {{-- Contact Name (optional) --}}
            <div>
                <label for="contact_name" class="block text-body-sm font-semibold text-text-primary mb-2">
                    Your Name <span class="font-normal text-text-tertiary">(Optional)</span>
                </label>
                <input
                    id="contact_name"
                    type="text"
                    name="contact_name"
                    value="{{ old('contact_name') }}"
                    autocomplete="name"
                    placeholder="Your full name"
                    class="w-full px-4 py-3 border border-border bg-white/90 backdrop-blur-sm rounded-xl text-text-primary placeholder:text-text-tertiary focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all shadow-sm @error('contact_name') border-danger focus:ring-danger/50 @enderror"
                >
                @error('contact_name')
                    <p class="mt-2 text-body-sm text-danger m-0">{{ $message }}</p>
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
                    autocomplete="email"
                    placeholder="hello@company.com"
                    class="w-full px-4 py-3 border border-border bg-white/90 backdrop-blur-sm rounded-xl text-text-primary placeholder:text-text-tertiary focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all shadow-sm @error('email') border-danger focus:ring-danger/50 @enderror"
                >
                @error('email')
                    <p class="mt-2 text-body-sm text-danger m-0">{{ $message }}</p>
                @enderror
            </div>

            {{-- City (optional) --}}
            <div>
                <label for="city" class="block text-body-sm font-semibold text-text-primary mb-2">
                    City <span class="font-normal text-text-tertiary">(Optional)</span>
                </label>
                <input
                    id="city"
                    type="text"
                    name="city"
                    value="{{ old('city') }}"
                    placeholder="Zagreb"
                    class="w-full px-4 py-3 border border-border bg-white/90 backdrop-blur-sm rounded-xl text-text-primary placeholder:text-text-tertiary focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all shadow-sm @error('city') border-danger focus:ring-danger/50 @enderror"
                >
                @error('city')
                    <p class="mt-2 text-body-sm text-danger m-0">{{ $message }}</p>
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
                    autocomplete="new-password"
                    placeholder="••••••••"
                    class="w-full px-4 py-3 border border-border bg-white/90 backdrop-blur-sm rounded-xl text-text-primary placeholder:text-text-tertiary focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all shadow-sm @error('password') border-danger focus:ring-danger/50 @enderror"
                >
                <p class="mt-2 text-caption text-text-tertiary m-0">
                    At least 8 characters, mix of uppercase, lowercase, numbers, and symbols.
                </p>
                @error('password')
                    <p class="mt-1 text-body-sm text-danger m-0">{{ $message }}</p>
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
                    placeholder="••••••••"
                    class="w-full px-4 py-3 border border-border bg-white/90 backdrop-blur-sm rounded-xl text-text-primary placeholder:text-text-tertiary focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all shadow-sm @error('password_confirmation') border-danger focus:ring-danger/50 @enderror"
                >
                @error('password_confirmation')
                    <p class="mt-2 text-body-sm text-danger m-0">{{ $message }}</p>
                @enderror
            </div>

            {{-- Terms notice --}}
            <div class="bg-white/40 backdrop-blur-sm rounded-xl border border-white/60 p-4 text-body-sm text-text-secondary">
                By creating an account you agree to our
                <a href="{{ url('/terms') }}"   class="font-medium text-primary hover:text-primary-hover transition-colors">Terms of Service</a>
                and
                <a href="{{ url('/privacy') }}" class="font-medium text-primary hover:text-primary-hover transition-colors">Privacy Policy</a>.
            </div>

            {{-- Submit --}}
            <button
                type="submit"
                class="w-full py-3 px-6 bg-primary hover:bg-primary-hover text-white font-semibold rounded-xl transition-all duration-normal shadow-md hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:ring-offset-2 focus:ring-offset-transparent"
            >
                Create Employer Account
            </button>
        </form>

        {{-- Divider --}}
        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-black/10"></div>
            </div>
            <div class="relative flex justify-center text-body-sm">
                <span class="px-3 bg-white/60 backdrop-blur-sm rounded-full text-text-tertiary">
                    Already have an account?
                </span>
            </div>
        </div>

        <a href="{{ route('login') }}"
           class="block text-center text-body-sm font-semibold text-primary hover:text-primary-hover transition-colors focus:outline-none focus:underline">
            Sign in to your account
        </a>
    </div>

    {{-- Below-card help text --}}
    <div class="mt-6 text-center text-body-sm text-text-primary/80 space-y-1">
        <p class="m-0">
            Questions? <a href="{{ url('/contact') }}" class="text-primary font-semibold hover:text-primary-hover transition-colors">Contact us</a>
        </p>
        <p class="m-0">
            Looking to apply for jobs?
            <a href="{{ route('register') }}" class="text-primary font-semibold hover:text-primary-hover transition-colors">Create a worker account</a>
        </p>
    </div>

</x-guest-layout>
