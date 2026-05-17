@extends('layouts.app')

@section('content')
<div class="cw-surface min-h-screen">
    <div class="cw-shell-spacing py-8">
        <h1 class="cw-heading-1 mb-2">Admin GDPR Console</h1>
        <p class="text-sm text-neutral-600 mb-4">Centralized privacy operations dashboard for DSAR, export history, anonymization, legal hold, and breach tracking.</p>

        @include('admin.gdpr.partials-nav')

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="cw-card-shell p-4"><p class="text-xs text-neutral-500">Open DSAR requests</p><p class="text-2xl font-semibold">{{ $cards['open_dsar'] }}</p></div>
            <div class="cw-card-shell p-4"><p class="text-xs text-neutral-500">Pending deletion requests</p><p class="text-2xl font-semibold">{{ $cards['pending_deletions'] }}</p></div>
            <div class="cw-card-shell p-4"><p class="text-xs text-neutral-500">Anonymizations scheduled</p><p class="text-2xl font-semibold">{{ $cards['anonymizations_scheduled'] }}</p></div>
            <div class="cw-card-shell p-4"><p class="text-xs text-neutral-500">Anonymizations completed</p><p class="text-2xl font-semibold">{{ $cards['anonymizations_completed'] }}</p></div>
            <div class="cw-card-shell p-4"><p class="text-xs text-neutral-500">Users under legal hold</p><p class="text-2xl font-semibold">{{ $cards['users_under_legal_hold'] }}</p></div>
            <div class="cw-card-shell p-4"><p class="text-xs text-neutral-500">Open breach incidents</p><p class="text-2xl font-semibold">{{ $cards['breach_incidents_open'] }}</p></div>
            <div class="cw-card-shell p-4 md:col-span-2">
                <p class="text-xs text-neutral-500">Retention automation</p>
                <p class="text-sm mt-1">Enabled: <strong>{{ $retentionStatus['enabled'] ? 'yes' : 'no' }}</strong></p>
                <p class="text-sm">Dry-run mode: <strong>{{ $retentionStatus['dry_run_mode'] ? 'yes' : 'no' }}</strong></p>
            </div>
        </div>

        <div class="cw-card-shell p-5">
            <h2 class="cw-heading-3 mb-3">Recent Privacy Events</h2>
            @if($recentEvents->isEmpty())
                <p class="text-sm text-neutral-600">No events tracked yet.</p>
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
