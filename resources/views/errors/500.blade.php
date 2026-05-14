@extends('layouts.app')

@section('title', 'Server Error')

<section class="cw-section min-h-screen flex items-center justify-center">
    <div class="cw-container max-w-lg text-center">
        <div class="mb-6">
            <div class="text-6xl font-bold text-red-600 mb-4">500</div>
            <h1 class="text-4xl font-bold text-slate-900 mb-2">Server Error</h1>
            <p class="text-lg text-slate-600 mb-6">
                We encountered an unexpected error. Our team has been notified and is working on a fix.
            </p>
        </div>

        <div class="space-y-3">
            <a href="{{ route('home') }}" class="cw-button-accent block">
                Return to Home
            </a>
            <a href="javascript:history.back()" class="cw-button-secondary block">
                Go Back
            </a>
        </div>

        <div class="mt-8 pt-8 border-t border-slate-200">
            <p class="text-sm text-slate-500">
                If the problem persists, please contact our support team.
            </p>
        </div>
    </div>
</section>
