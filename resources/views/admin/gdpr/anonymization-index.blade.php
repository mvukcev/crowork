@extends('layouts.app')

@section('content')
<div class="cw-surface min-h-screen">
    <div class="cw-shell-spacing py-8">
        <h1 class="cw-heading-1 mb-4">{{ __('gdpr_admin.anonymization_logs_title') }}</h1>
        @include('admin.gdpr.partials-nav')

        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
            <select name="status" class="cw-field">
                <option value="">{{ __('gdpr_admin.all_statuses') }}</option>
                @foreach(['started','completed','failed','blocked'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
            <input class="cw-field" name="target_type" value="{{ request('target_type') }}" placeholder="{{ __('gdpr_admin.target_type_filter') }}">
            <button class="cw-button-secondary" type="submit">{{ __('gdpr_admin.filter') }}</button>
        </form>

        <div class="cw-card-shell overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-3 py-2 text-left">ID</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.target') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.action') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.reason') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.status') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.started') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.completed') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.summary') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr class="border-t border-neutral-200">
                            <td class="px-3 py-2">#{{ $log->id }}</td>
                            <td class="px-3 py-2">{{ class_basename($log->target_type) }}:{{ $log->target_id ?: __('gdpr_admin.na') }}</td>
                            <td class="px-3 py-2">{{ $log->action }}</td>
                            <td class="px-3 py-2">{{ $log->reason ?: __('gdpr_admin.na') }}</td>
                            <td class="px-3 py-2"><span class="cw-badge cw-badge-neutral">{{ $log->status }}</span></td>
                            <td class="px-3 py-2">{{ $log->started_at?->format('Y-m-d H:i') ?: __('gdpr_admin.na') }}</td>
                            <td class="px-3 py-2">{{ $log->completed_at?->format('Y-m-d H:i') ?: __('gdpr_admin.na') }}</td>
                            <td class="px-3 py-2 text-xs">
                                @if($log->summary_json)
                                    <div>{{ json_encode($log->summary_json) }}</div>
                                    @if(($log->summary_json['legal_hold_reason'] ?? null) !== null)
                                        <div class="text-amber-800 mt-1">{{ __('gdpr_admin.legal_hold_inline') }} {{ $log->summary_json['legal_hold_reason'] }} ({{ $log->summary_json['legal_hold_placed_at'] ?? __('gdpr_admin.na_lower') }})</div>
                                    @endif
                                @else
                                    {{ $log->failure_reason ?: __('gdpr_admin.na') }}
                                @endif
                            </td>
                            <td class="px-3 py-2"><a class="text-blue-700 hover:underline" href="{{ route('admin.gdpr.anonymization.show', $log) }}">{{ __('gdpr_admin.verify') }}</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-3">{{ $logs->links() }}</div>
        </div>
    </div>
</div>
@endsection
