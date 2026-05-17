@extends('layouts.app')

@section('content')
<div class="cw-surface min-h-screen">
    <div class="cw-shell-spacing py-8">
        <h1 class="cw-heading-1 mb-4">GDPR Anonymization Logs</h1>
        @include('admin.gdpr.partials-nav')

        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
            <select name="status" class="cw-field">
                <option value="">All statuses</option>
                @foreach(['started','completed','failed','blocked'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
            <input class="cw-field" name="target_type" value="{{ request('target_type') }}" placeholder="Target type filter">
            <button class="cw-button-secondary" type="submit">Filter</button>
        </form>

        <div class="cw-card-shell overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-3 py-2 text-left">ID</th>
                        <th class="px-3 py-2 text-left">Target</th>
                        <th class="px-3 py-2 text-left">Action</th>
                        <th class="px-3 py-2 text-left">Reason</th>
                        <th class="px-3 py-2 text-left">Status</th>
                        <th class="px-3 py-2 text-left">Started</th>
                        <th class="px-3 py-2 text-left">Completed</th>
                        <th class="px-3 py-2 text-left">Summary</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr class="border-t border-neutral-200">
                            <td class="px-3 py-2">#{{ $log->id }}</td>
                            <td class="px-3 py-2">{{ class_basename($log->target_type) }}:{{ $log->target_id ?: 'N/A' }}</td>
                            <td class="px-3 py-2">{{ $log->action }}</td>
                            <td class="px-3 py-2">{{ $log->reason ?: 'N/A' }}</td>
                            <td class="px-3 py-2"><span class="cw-badge cw-badge-neutral">{{ $log->status }}</span></td>
                            <td class="px-3 py-2">{{ $log->started_at?->format('Y-m-d H:i') ?: 'N/A' }}</td>
                            <td class="px-3 py-2">{{ $log->completed_at?->format('Y-m-d H:i') ?: 'N/A' }}</td>
                            <td class="px-3 py-2 text-xs">{{ $log->summary_json ? json_encode($log->summary_json) : ($log->failure_reason ?: 'N/A') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-3">{{ $logs->links() }}</div>
        </div>
    </div>
</div>
@endsection
