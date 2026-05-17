@extends('layouts.app')

@section('content')
<div class="cw-surface min-h-screen">
    <div class="cw-shell-spacing py-8">
        <h1 class="cw-heading-1 mb-2">{{ __('gdpr_admin.console_title') }}</h1>
        <p class="text-sm text-neutral-600 mb-4">{{ __('gdpr_admin.console_intro') }}</p>

        @include('admin.gdpr.partials-nav')

        @if(!$systemHealth['healthy'])
            <div class="cw-card-shell border border-amber-300 bg-amber-50 p-4 mb-4">
                <p class="text-sm font-semibold text-amber-900">{{ __('gdpr_admin.operational_warning') }}</p>
                <ul class="mt-2 list-disc pl-5 text-sm text-amber-900">
                    @foreach($systemHealth['warnings'] as $warning)
                        <li>{{ $warning }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="cw-card-shell p-4"><p class="text-xs text-neutral-500">{{ __('gdpr_admin.open_dsar') }}</p><p class="text-2xl font-semibold">{{ $cards['open_dsar'] }}</p></div>
            <div class="cw-card-shell p-4"><p class="text-xs text-neutral-500">{{ __('gdpr_admin.pending_deletions') }}</p><p class="text-2xl font-semibold">{{ $cards['pending_deletions'] }}</p></div>
            <div class="cw-card-shell p-4"><p class="text-xs text-neutral-500">{{ __('gdpr_admin.anonymizations_scheduled') }}</p><p class="text-2xl font-semibold">{{ $cards['anonymizations_scheduled'] }}</p></div>
            <div class="cw-card-shell p-4"><p class="text-xs text-neutral-500">{{ __('gdpr_admin.anonymizations_completed') }}</p><p class="text-2xl font-semibold">{{ $cards['anonymizations_completed'] }}</p></div>
            <div class="cw-card-shell p-4"><p class="text-xs text-neutral-500">{{ __('gdpr_admin.users_legal_hold') }}</p><p class="text-2xl font-semibold">{{ $cards['users_under_legal_hold'] }}</p></div>
            <div class="cw-card-shell p-4"><p class="text-xs text-neutral-500">{{ __('gdpr_admin.open_breaches') }}</p><p class="text-2xl font-semibold">{{ $cards['breach_incidents_open'] }}</p></div>
            <div class="cw-card-shell p-4 md:col-span-2">
                <p class="text-xs text-neutral-500">{{ __('gdpr_admin.retention_automation') }}</p>
                <p class="text-sm mt-1">{{ __('gdpr_admin.enabled') }} <strong>{{ $retentionStatus['enabled'] ? __('gdpr_admin.yes') : __('gdpr_admin.no') }}</strong></p>
                <p class="text-sm">{{ __('gdpr_admin.dry_run') }} <strong>{{ $retentionStatus['dry_run_mode'] ? __('gdpr_admin.yes') : __('gdpr_admin.no') }}</strong></p>
            </div>
            <div class="cw-card-shell p-4 md:col-span-2">
                <p class="text-xs text-neutral-500">{{ __('gdpr_admin.operational_health') }}</p>
                <p class="text-sm mt-1">{{ __('gdpr_admin.scheduler_heartbeat') }} <strong>{{ $systemHealth['scheduler_last_run_at']?->diffForHumans() ?? __('gdpr_admin.never') }}</strong></p>
                <p class="text-sm">{{ __('gdpr_admin.failed_jobs') }} <strong>{{ $systemHealth['gdpr_failed_jobs'] }}</strong></p>
                <p class="text-sm">{{ __('gdpr_admin.stuck_items') }} <strong>{{ $systemHealth['stuck_anonymizations'] }}</strong> / <strong>{{ $systemHealth['stuck_exports'] }}</strong></p>
            </div>
        </div>

        <div class="cw-card-shell p-5">
            <h2 class="cw-heading-3 mb-3">{{ __('gdpr_admin.recent_events') }}</h2>
            @if($recentEvents->isEmpty())
                <p class="text-sm text-neutral-600">{{ __('gdpr_admin.no_events') }}</p>
            @else
                <div class="space-y-2">
                    @foreach($recentEvents as $event)
                        <a href="{{ $event['url'] }}" class="block rounded border border-neutral-200 p-3 hover:border-blue-300">
                            <div class="flex justify-between gap-3">
                                <p class="text-sm font-medium text-neutral-900">{{ $event['title'] }}</p>
                                <span class="text-xs text-neutral-500">{{ $event['at']?->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-neutral-600 mt-1">{{ strtoupper($event['type']) }} · {{ $event['status'] }}</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
