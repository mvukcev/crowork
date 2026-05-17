@extends('layouts.app')

@section('content')
<div class="cw-surface min-h-screen">
    <div class="cw-shell-spacing py-8">
        <h1 class="cw-heading-1 mb-4">{{ __('gdpr_admin.anonymization_verification') }}</h1>
        @include('admin.gdpr.partials-nav')

        <div class="cw-card-shell p-5 mb-4">
            <p class="text-sm text-neutral-600">{{ __('gdpr_admin.log') }} #{{ $log->id }} · {{ $log->action }} · <span class="cw-badge cw-badge-neutral">{{ $log->status }}</span></p>
            <p class="text-sm text-neutral-700 mt-2">{{ __('gdpr_admin.target') }} {{ class_basename($log->target_type) }}:{{ $log->target_id ?: __('gdpr_admin.na') }}</p>
            <p class="text-sm text-neutral-700">{{ __('gdpr_admin.started') }} {{ $log->started_at?->format('Y-m-d H:i') ?: __('gdpr_admin.na') }}</p>
            <p class="text-sm text-neutral-700">{{ __('gdpr_admin.completed') }} {{ $log->completed_at?->format('Y-m-d H:i') ?: __('gdpr_admin.na') }}</p>
            @if($log->failure_reason)
                <p class="text-sm text-red-700 mt-2">{{ __('gdpr_admin.failure') }} {{ $log->failure_reason }}</p>
            @endif
        </div>

        <div class="cw-card-shell p-5">
            <h2 class="cw-heading-3 mb-3">{{ __('gdpr_admin.verification_data') }}</h2>
            <pre class="text-xs bg-neutral-50 border border-neutral-200 rounded p-3 overflow-x-auto">{{ json_encode($verification, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>
</div>
@endsection
