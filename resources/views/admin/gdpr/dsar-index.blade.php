@extends('layouts.app')

@section('content')
<div class="cw-surface min-h-screen">
    <div class="cw-shell-spacing py-8">
        <h1 class="cw-heading-1 mb-4">{{ __('gdpr_admin.dsar_requests') }}</h1>
        @include('admin.gdpr.partials-nav')

        @if(session('success'))
            <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif

        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-5">
            <select name="status" class="cw-field">
                <option value="">{{ __('gdpr_admin.all_statuses') }}</option>
                @foreach(['open','in_review','waiting_for_user','fulfilled','rejected','closed'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
            <select name="request_type" class="cw-field">
                <option value="">{{ __('gdpr_admin.all_types') }}</option>
                @foreach(['access_export','deletion','rectification','objection_restriction','portability','consent_inquiry','other'] as $type)
                    <option value="{{ $type }}" @selected(request('request_type') === $type)>{{ $type }}</option>
                @endforeach
            </select>
            <button class="cw-button-secondary" type="submit">{{ __('gdpr_admin.filter') }}</button>
        </form>

        <div class="cw-card-shell p-4 mb-6">
            <h2 class="cw-heading-3 mb-3">{{ __('gdpr_admin.dsar_create_title') }}</h2>
            <form method="POST" action="{{ route('admin.gdpr.dsar.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @csrf
                <input class="cw-field" name="requester_user_id" placeholder="{{ __('gdpr_admin.requester_user_id_optional') }}">
                <input class="cw-field" name="requester_email" placeholder="{{ __('gdpr_admin.requester_email_optional') }}">
                <select class="cw-field" name="request_type" required>
                    @foreach(['access_export','deletion','rectification','objection_restriction','portability','consent_inquiry','other'] as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
                <select class="cw-field" name="status" required>
                    @foreach(['open','in_review','waiting_for_user','fulfilled','rejected','closed'] as $status)
                        <option value="{{ $status }}">{{ $status }}</option>
                    @endforeach
                </select>
                <select class="cw-field" name="priority" required>
                    @foreach(['low','normal','high','urgent'] as $priority)
                        <option value="{{ $priority }}">{{ $priority }}</option>
                    @endforeach
                </select>
                <input class="cw-field" type="datetime-local" name="due_at">
                <select class="cw-field" name="assigned_admin_id">
                    <option value="">{{ __('gdpr_admin.unassigned') }}</option>
                    @foreach($admins as $admin)
                        <option value="{{ $admin->id }}">{{ $admin->name }} (#{{ $admin->id }})</option>
                    @endforeach
                </select>
                <textarea class="cw-field md:col-span-2" name="internal_notes" rows="3" placeholder="{{ __('gdpr_admin.internal_notes') }}"></textarea>
                <button class="cw-button-primary md:col-span-2" type="submit">{{ __('gdpr_admin.dsar_create_action') }}</button>
            </form>
        </div>

        <div class="cw-card-shell overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-3 py-2 text-left">ID</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.type') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.status') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.priority') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.requester') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.due') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.assigned') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $row)
                        <tr class="border-t border-neutral-200">
                            <td class="px-3 py-2">#{{ $row->id }}</td>
                            <td class="px-3 py-2">{{ $row->request_type }}</td>
                            <td class="px-3 py-2"><span class="cw-badge cw-badge-neutral">{{ $row->status }}</span></td>
                            <td class="px-3 py-2">{{ $row->priority }}</td>
                            <td class="px-3 py-2">{{ $row->requester_email ?: ('user#'.$row->requester_user_id) }}</td>
                            <td class="px-3 py-2">{{ $row->due_at?->format('Y-m-d H:i') ?: __('gdpr_admin.na') }}</td>
                            <td class="px-3 py-2">{{ $row->assignedAdmin?->name ?: __('gdpr_admin.unassigned') }}</td>
                            <td class="px-3 py-2"><a href="{{ route('admin.gdpr.dsar.show', $row) }}" class="text-blue-700">{{ __('gdpr_admin.open') }}</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-3">{{ $requests->links() }}</div>
        </div>
    </div>
</div>
@endsection
