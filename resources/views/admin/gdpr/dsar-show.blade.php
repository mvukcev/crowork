@extends('layouts.app')

@section('content')
<div class="cw-surface min-h-screen">
    <div class="cw-shell-spacing py-8 max-w-4xl">
        <h1 class="cw-heading-1 mb-3">{{ __('gdpr_admin.dsar_single_title') }} #{{ $request->id }}</h1>
        @include('admin.gdpr.partials-nav')

        @if(session('success'))
            <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif

        <div class="cw-card-shell p-5 mb-5">
            <p class="text-sm text-neutral-700">{{ __('gdpr_admin.requester') }} {{ $request->requester_email ?: ('user#'.$request->requester_user_id) }}</p>
            <p class="text-sm text-neutral-700">{{ __('gdpr_admin.type') }} {{ $request->request_type }}</p>
            <p class="text-sm text-neutral-700">{{ __('gdpr_admin.status') }} {{ $request->status }}</p>
            <p class="text-sm text-neutral-700">{{ __('gdpr_admin.priority') }} {{ $request->priority }}</p>
            <p class="text-sm text-neutral-700">{{ __('gdpr_admin.due_at') }} {{ $request->due_at?->format('Y-m-d H:i') ?: __('gdpr_admin.na') }}</p>
            <p class="text-sm text-neutral-700">{{ __('gdpr_admin.fulfilled_at') }} {{ $request->fulfilled_at?->format('Y-m-d H:i') ?: __('gdpr_admin.na') }}</p>
            <p class="text-sm text-neutral-700">{{ __('gdpr_admin.closed_at') }} {{ $request->closed_at?->format('Y-m-d H:i') ?: __('gdpr_admin.na') }}</p>
        </div>

        <div class="cw-card-shell p-5">
            <h2 class="cw-heading-3 mb-3">{{ __('gdpr_admin.update_request') }}</h2>
            <form method="POST" action="{{ route('admin.gdpr.dsar.update', $request) }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @csrf
                @method('PATCH')
                <select class="cw-field" name="status" required>
                    @foreach(['open','in_review','waiting_for_user','fulfilled','rejected','closed'] as $status)
                        <option value="{{ $status }}" @selected($request->status === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                <select class="cw-field" name="priority" required>
                    @foreach(['low','normal','high','urgent'] as $priority)
                        <option value="{{ $priority }}" @selected($request->priority === $priority)>{{ $priority }}</option>
                    @endforeach
                </select>
                <input class="cw-field" type="datetime-local" name="due_at" value="{{ $request->due_at?->format('Y-m-d\\TH:i') }}">
                <select class="cw-field" name="assigned_admin_id">
                    <option value="">{{ __('gdpr_admin.unassigned') }}</option>
                    @foreach($admins as $admin)
                        <option value="{{ $admin->id }}" @selected((int)$request->assigned_admin_id === (int)$admin->id)>{{ $admin->name }} (#{{ $admin->id }})</option>
                    @endforeach
                </select>
                <textarea class="cw-field md:col-span-2" name="resolution_summary" rows="3" placeholder="{{ __('gdpr_admin.resolution_summary') }}">{{ $request->resolution_summary }}</textarea>
                <textarea class="cw-field md:col-span-2" name="internal_note_append" rows="3" placeholder="{{ __('gdpr_admin.add_internal_note') }}"></textarea>
                <button class="cw-button-primary md:col-span-2" type="submit" onclick="return confirm('{{ __('gdpr_admin.confirm_dsar_update') }}')">{{ __('gdpr_admin.update_dsar') }}</button>
            </form>
        </div>

        @if($request->internal_notes)
            <div class="cw-card-shell p-5 mt-5">
                <h2 class="cw-heading-3 mb-2">{{ __('gdpr_admin.internal_notes') }}</h2>
                <pre class="text-xs whitespace-pre-wrap text-neutral-700">{{ $request->internal_notes }}</pre>
            </div>
        @endif
    </div>
</div>
@endsection
