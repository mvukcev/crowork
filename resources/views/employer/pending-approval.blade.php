@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-theme-employers-light via-white to-background py-10 px-4">
    <div class="w-full max-w-md">
        <!-- Card -->
        <x-card class="border border-border/70 shadow-elevation-2 text-center">
            <!-- Icon -->
            <div class="w-16 h-16 rounded-full bg-primary-light border border-primary-border mx-auto mb-6 flex items-center justify-center">
                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <!-- Heading -->
            <h1 class="text-title-1 font-semibold text-text-primary mb-3">
                Account Pending Approval
            </h1>

            <!-- Message -->
            <div class="space-y-4 mb-8">
                <p class="text-body text-text-secondary leading-relaxed">
                    Thank you for creating your CroWork employer account!
                </p>

                <div class="bg-primary-light rounded-lg border border-primary-border p-4 text-body-sm text-text-secondary space-y-3">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-primary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
                        </svg>
                        <span>
                            <strong>Email verification:</strong> Check your inbox for a verification link.
                        </span>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-primary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
                        </svg>
                        <span>
                            <strong>Account approval:</strong> Our team will review and approve your account within 24-48 hours.
                        </span>
                    </div>
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-primary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
                        </svg>
                        <span>
                            <strong>You'll receive an email:</strong> When your account is approved and you can start posting jobs.
                        </span>
                    </div>
                </div>

                <p class="text-body-sm text-text-secondary leading-relaxed">
                    We verify all employer accounts to protect both workers and employers on our platform.
                </p>
            </div>

            <!-- Actions -->
            <div class="space-y-3 mb-6">
                <a href="{{ url('/') }}" class="block">
                    <x-button variant="primary" class="w-full py-3 text-base font-semibold">
                        Return to Home
                    </x-button>
                </a>
                <a href="{{ url('/for-employers') }}" class="block">
                    <x-button variant="secondary" class="w-full py-3 text-base font-semibold">
                        Learn More
                    </x-button>
                </a>
            </div>

            <!-- Help Text -->
            <div class="border-t border-border pt-6">
                <p class="text-body-sm text-text-secondary mb-2">
                    Didn't receive a verification email?
                </p>
                <a href="{{ route('verification.send') }}" class="text-body-sm text-primary hover:text-primary-hover font-medium transition-colors duration-normal">
                    Resend verification email
                </a>
            </div>
        </x-card>

        <!-- Footer Help -->
        <div class="mt-8 text-center">
            <p class="text-body-sm text-text-secondary mb-3">
                Need help? We're here to assist.
            </p>
            <a href="{{ url('/contact') }}" class="text-body-sm text-primary hover:text-primary-hover font-medium transition-colors duration-normal">
                Contact our support team
            </a>
        </div>
    </div>
</div>

@endsection
