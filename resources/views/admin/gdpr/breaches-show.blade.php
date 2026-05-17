@extends('layouts.app')

@section('content')
<div class="cw-surface min-h-screen">
    <div class="cw-shell-spacing py-8 max-w-4xl">
        <h1 class="cw-heading-1 mb-4">{{ __('gdpr_admin.breach_incident') }} #{{ $incident->id }}</h1>
        @include('admin.gdpr.partials-nav')

        @if(session('success'))
            <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif

        <div class="cw-card-shell p-5">
            <form method="POST" action="{{ route('admin.gdpr.breaches.update', $incident) }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @csrf
                @method('PATCH')
                <select class="cw-field" name="severity" required>
                    @foreach(['low','medium','high','critical'] as $severity)
                        <option value="{{ $severity }}" @selected($incident->severity === $severity)>{{ $severity }}</option>
                    @endforeach
                </select>
                <select class="cw-field" name="status" required>
                    @foreach(['open','investigating','contained','resolved','closed'] as $status)
                        <option value="{{ $status }}" @selected($incident->status === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <input class="cw-field" type="datetime-local" name="reported_at" value="{{ $incident->reported_at?->format('Y-m-d\\TH:i') }}">
                <input class="cw-field" type="number" min="0" name="affected_user_count" value="{{ $incident->affected_user_count }}">
                <input class="cw-field" name="affected_data_categories" value="{{ is_array($incident->affected_data_categories) ? implode(', ', $incident->affected_data_categories) : '' }}" placeholder="{{ __('gdpr_admin.categories_comma') }}">
                <select class="cw-field" name="owner_admin_id">
                    <option value="">{{ __('gdpr_admin.no_owner') }}</option>
                    @foreach($admins as $admin)
                        <option value="{{ $admin->id }}" @selected((int)$incident->owner_admin_id === (int)$admin->id)>{{ $admin->name }}</option>
                    @endforeach
                </select>
                <label class="text-sm"><input type="checkbox" name="authority_notification_required" value="1" @checked($incident->authority_notification_required)> {{ __('gdpr_admin.authority_notification_required') }}</label>
                <label class="text-sm"><input type="checkbox" name="users_notification_required" value="1" @checked($incident->users_notification_required)> {{ __('gdpr_admin.user_notification_required') }}</label>
                <textarea class="cw-field md:col-span-2" name="summary" rows="3" required>{{ $incident->summary }}</textarea>
                <textarea class="cw-field md:col-span-2" name="internal_notes" rows="4">{{ $incident->internal_notes }}</textarea>
                <button class="cw-button-primary md:col-span-2" type="submit" onclick="return confirm('{{ __('gdpr_admin.confirm_update_incident') }}')">{{ __('gdpr_admin.update_incident') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
