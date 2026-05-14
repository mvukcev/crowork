@extends('layouts.app')

@section('title', 'Access Denied')

<section class="cw-section min-h-screen flex items-center justify-center">
    <div class="cw-container max-w-lg text-center">
        <div class="mb-6">
            <div class="text-6xl font-bold text-amber-600 mb-4">403</div>
            <h1 class="text-4xl font-bold text-slate-900 mb-2">Access Denied</h1>
            <p class="text-lg text-slate-600 mb-6">
                You don't have permission to access this resource.
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
    </div>
</section>
