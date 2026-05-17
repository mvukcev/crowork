@extends('layouts.app')

@section('title', __('errors.403.title'))

<section class="cw-section min-h-screen flex items-center justify-center">
    <div class="cw-container max-w-lg text-center">
        <div class="mb-6">
            <div class="text-6xl font-bold text-amber-600 mb-4">403</div>
            <h1 class="text-4xl font-bold text-slate-900 mb-2">{{ __('errors.403.heading') }}</h1>
            <p class="text-lg text-slate-600 mb-6">
                {{ __('errors.403.body') }}
            </p>
        </div>

        <div class="space-y-3">
            <a href="{{ route('home') }}" class="cw-button-accent block">
                {{ __('errors.actions.return_home') }}
            </a>
            <a href="javascript:history.back()" class="cw-button-secondary block">
                {{ __('errors.actions.go_back') }}
            </a>
        </div>
    </div>
</section>
