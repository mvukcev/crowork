@extends('layouts.app')

@section('content')
<div class="cw-surface min-h-screen">
    <div class="cw-shell-spacing py-8">
        <h1 class="cw-heading-1 mb-4">{{ __('gdpr_admin.export_history_title') }}</h1>
        @include('admin.gdpr.partials-nav')

        <form method="GET" class="mb-4 flex gap-3">
            <select name="status" class="cw-field max-w-xs">
                <option value="">{{ __('gdpr_admin.all_statuses') }}</option>
                @foreach(['pending','completed','failed'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
            <button class="cw-button-secondary" type="submit">{{ __('gdpr_admin.filter') }}</button>
        </form>

        <div class="cw-card-shell overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-3 py-2 text-left">ID</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.user') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.requested_by') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.type') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.status') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.generated') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.expires') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.failure') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr class="border-t border-neutral-200">
                            <td class="px-3 py-2">#{{ $log->id }}</td>
                            <td class="px-3 py-2">{{ $log->user?->email ? 'user#'.$log->user_id : __('gdpr_admin.na') }}</td>
                            <td class="px-3 py-2">{{ $log->requestedByAdmin?->name ?: ('user#'.$log->requested_by_user_id) }}</td>
                            <td class="px-3 py-2">{{ $log->export_type }}</td>
                            <td class="px-3 py-2"><span class="cw-badge cw-badge-neutral">{{ $log->status }}</span></td>
                            <td class="px-3 py-2">{{ $log->generated_at?->format('Y-m-d H:i') ?: __('gdpr_admin.na') }}</td>
                            <td class="px-3 py-2">{{ $log->expires_at?->format('Y-m-d H:i') ?: __('gdpr_admin.na') }}</td>
                            <td class="px-3 py-2 text-xs text-red-700">{{ $log->failure_reason ?: __('gdpr_admin.na') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-3">{{ $logs->links() }}</div>
        </div>
    </div>
</div>
@endsection
