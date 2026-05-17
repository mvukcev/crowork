@extends('layouts.app')

@section('content')
<div class="cw-surface min-h-screen">
    <div class="cw-shell-spacing py-8">
        <h1 class="cw-heading-1 mb-4">{{ __('gdpr_admin.breach_incidents') }}</h1>
        @include('admin.gdpr.partials-nav')

        @if(session('success'))
            <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif

        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
            <select name="status" class="cw-field">
                <option value="">{{ __('gdpr_admin.all_statuses') }}</option>
                @foreach(['open','investigating','contained','resolved','closed'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
            <select name="severity" class="cw-field">
                <option value="">{{ __('gdpr_admin.all_severities') }}</option>
                @foreach(['low','medium','high','critical'] as $severity)
                    <option value="{{ $severity }}" @selected(request('severity') === $severity)>{{ $severity }}</option>
                @endforeach
            </select>
            <button class="cw-button-secondary" type="submit">{{ __('gdpr_admin.filter') }}</button>
        </form>

        <div class="cw-card-shell p-4 mb-6">
            <h2 class="cw-heading-3 mb-3">{{ __('gdpr_admin.create_incident') }}</h2>
            <form method="POST" action="{{ route('admin.gdpr.breaches.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @csrf
                <input class="cw-field" name="title" required placeholder="{{ __('gdpr_admin.title') }}">
                <input class="cw-field" type="datetime-local" name="detected_at" required>
                <select class="cw-field" name="severity" required>
                    @foreach(['low','medium','high','critical'] as $severity)
                        <option value="{{ $severity }}">{{ $severity }}</option>
                    @endforeach
                </select>
                <select class="cw-field" name="status" required>
                    @foreach(['open','investigating','contained','resolved','closed'] as $status)
                        <option value="{{ $status }}">{{ $status }}</option>
                    @endforeach
                </select>
                <input class="cw-field" type="datetime-local" name="reported_at">
                <input class="cw-field" type="number" min="0" name="affected_user_count" placeholder="{{ __('gdpr_admin.affected_user_count') }}">
                <input class="cw-field" name="affected_data_categories" placeholder="{{ __('gdpr_admin.categories_comma') }}">
                <select class="cw-field" name="owner_admin_id">
                    <option value="">{{ __('gdpr_admin.no_owner') }}</option>
                    @foreach($admins as $admin)
                        <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                    @endforeach
                </select>
                <label class="text-sm"><input type="checkbox" name="authority_notification_required" value="1"> {{ __('gdpr_admin.authority_notification_required') }}</label>
                <label class="text-sm"><input type="checkbox" name="users_notification_required" value="1"> {{ __('gdpr_admin.user_notification_required') }}</label>
                <textarea class="cw-field md:col-span-2" name="summary" rows="3" required placeholder="{{ __('gdpr_admin.summary') }}"></textarea>
                <textarea class="cw-field md:col-span-2" name="internal_notes" rows="3" placeholder="{{ __('gdpr_admin.internal_notes') }}"></textarea>
                <button class="cw-button-primary md:col-span-2" type="submit">{{ __('gdpr_admin.create_incident_action') }}</button>
            </form>
        </div>

        <div class="cw-card-shell overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-3 py-2 text-left">ID</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.title') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.severity') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.status') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.detected') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.owner') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($incidents as $incident)
                        <tr class="border-t border-neutral-200">
                            <td class="px-3 py-2">#{{ $incident->id }}</td>
                            <td class="px-3 py-2">{{ $incident->title }}</td>
                            <td class="px-3 py-2">{{ $incident->severity }}</td>
                            <td class="px-3 py-2">{{ $incident->status }}</td>
                            <td class="px-3 py-2">{{ $incident->detected_at?->format('Y-m-d H:i') }}</td>
                            <td class="px-3 py-2">{{ $incident->ownerAdmin?->name ?: __('gdpr_admin.na') }}</td>
                            <td class="px-3 py-2"><a class="text-blue-700" href="{{ route('admin.gdpr.breaches.show', $incident) }}">{{ __('gdpr_admin.open') }}</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-3">{{ $incidents->links() }}</div>
        </div>
    </div>
</div>
@endsection
