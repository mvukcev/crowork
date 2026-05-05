@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-theme-employers-light via-white to-background py-10 px-4">
    <div class="w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
            <a href="{{ url('/') }}" class="flex items-center justify-center space-x-2 group mb-8">
                <div class="w-10 h-10 bg-primary rounded-md flex items-center justify-center group-hover:bg-primary-hover transition-colors duration-normal">
                    <span class="text-white font-bold text-lg">C</span>
                </div>
                <span class="text-title-1 font-semibold text-text-primary">CroWork</span>
            </a>
            <h1 class="text-title-1 font-semibold text-text-primary mb-2">Create Employer Account</h1>
            <p class="text-body text-text-secondary">
                Post jobs and find verified international workers
            </p>
        </div>

        <!-- Registration Form Card -->
        <x-card class="border border-border/70 shadow-elevation-2">
            <form method="POST" action="{{ route('employer.register') }}" class="space-y-6">
                @csrf

                <!-- Company Name -->
                <div>
                    <x-input-label for="company_name" :value="__('Company Name')" />
                    <x-text-input
                        id="company_name"
                        class="block w-full mt-1"
                        type="text"
                        name="company_name"
                        :value="old('company_name')"
                        required
                        autofocus
                        autocomplete="organization"
                        placeholder="Your company name"
                    />
                    <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                </div>

                <!-- Contact Name (Optional) -->
                <div>
                    <x-input-label for="contact_name" :value="__('Your Name (Optional)')" />
                    <x-text-input
                        id="contact_name"
                        class="block w-full mt-1"
                        type="text"
                        name="contact_name"
                        :value="old('contact_name')"
                        autocomplete="name"
                        placeholder="Your full name"
                    />
                    <x-input-error :messages="$errors->get('contact_name')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email Address')" />
                    <x-text-input
                        id="email"
                        class="block w-full mt-1"
                        type="email"
                        name="email"
                        :value="old('email')"
                        required
                        autocomplete="email"
                        placeholder="hello@company.com"
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- City (Optional) -->
                <div>
                    <x-input-label for="city" :value="__('City (Optional)')" />
                    <x-text-input
                        id="city"
                        class="block w-full mt-1"
                        type="text"
                        name="city"
                        :value="old('city')"
                        placeholder="Zagreb"
                    />
                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input
                        id="password"
                        class="block w-full mt-1"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />
                    <p class="text-caption text-text-tertiary mt-2">
                        At least 8 characters, mix of uppercase, lowercase, numbers, and symbols.
                    </p>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input
                        id="password_confirmation"
                        class="block w-full mt-1"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="••••••••"
                    />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Terms Agreement (implied) -->
                <div class="bg-primary-light rounded-lg border border-primary-border p-4 text-body-sm text-text-secondary">
                    By creating an account, you agree to our 
                    <a href="{{ url('/terms') }}" class="text-primary hover:text-primary-hover font-medium transition-colors duration-normal">Terms of Service</a> 
                    and 
                    <a href="{{ url('/privacy') }}" class="text-primary hover:text-primary-hover font-medium transition-colors duration-normal">Privacy Policy</a>.
                </div>

                <!-- Submit Button -->
                <x-button type="submit" variant="primary" class="w-full py-3 text-base font-semibold">
                    Create Employer Account
                </x-button>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-border"></div>
                </div>
                <div class="relative flex justify-center text-body-sm">
                    <span class="px-2 bg-background text-text-tertiary">Already have an account?</span>
                </div>
            </div>

            <!-- Sign In Link -->
            <a href="{{ route('login') }}" class="block text-center text-body-sm text-primary hover:text-primary-hover font-medium transition-colors duration-normal">
                Sign in as an employer
            </a>
        </x-card>

        <!-- Help Text -->
        <div class="mt-8 text-center text-body-sm text-text-secondary">
            <p class="mb-2">Questions? <a href="{{ url('/contact') }}" class="text-primary hover:text-primary-hover font-medium transition-colors duration-normal">Contact us</a></p>
            <p>Looking to apply for jobs? <a href="{{ route('register') }}" class="text-primary hover:text-primary-hover font-medium transition-colors duration-normal">Create a worker account</a></p>
        </div>
    </div>
</div>

@endsection
