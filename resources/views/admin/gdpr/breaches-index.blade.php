@extends('layouts.app')

@section('content')
<div class="cw-surface min-h-screen">
    <div class="cw-shell-spacing py-8">
        <h1 class="cw-heading-1 mb-4">Breach Incidents</h1>
        @include('admin.gdpr.partials-nav')

        @if(session('success'))
            <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif

        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
            <select name="status" class="cw-field">
                <option value="">All statuses</option>
                @foreach(['open','investigating','contained','resolved','closed'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                @endforeach
            </select>
            <select name="severity" class="cw-field">
                <option value="">All severities</option>
                @foreach(['low','medium','high','critical'] as $severity)
                    <option value="{{ $severity }}" @selected(request('severity') === $severity)>{{ $severity }}</option>
                @endforeach
            </select>
            <button class="cw-button-secondary" type="submit">Filter</button>
        </form>

        <div class="cw-card-shell p-4 mb-6">
            <h2 class="cw-heading-3 mb-3">Create Incident</h2>
            <form method="POST" action="{{ route('admin.gdpr.breaches.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @csrf
                <input class="cw-field" name="title" required placeholder="Title">
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
                <input class="cw-field" type="number" min="0" name="affected_user_count" placeholder="Affected user count">
                <input class="cw-field" name="affected_data_categories" placeholder="Categories (comma separated)">
                <select class="cw-field" name="owner_admin_id">
                    <option value="">No owner</option>
                    @foreach($admins as $admin)
                        <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                    @endforeach
                </select>
                <label class="text-sm"><input type="checkbox" name="authority_notification_required" value="1"> Authority notification required</label>
                <label class="text-sm"><input type="checkbox" name="users_notification_required" value="1"> User notification required</label>
                <textarea class="cw-field md:col-span-2" name="summary" rows="3" required placeholder="Summary"></textarea>
                <textarea class="cw-field md:col-span-2" name="internal_notes" rows="3" placeholder="Internal notes"></textarea>
                <button class="cw-button-primary md:col-span-2" type="submit">Create incident</button>
            </form>
        </div>

        <div class="cw-card-shell overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-3 py-2 text-left">ID</th>
                        <th class="px-3 py-2 text-left">Title</th>
                        <th class="px-3 py-2 text-left">Severity</th>
                        <th class="px-3 py-2 text-left">Status</th>
                        <th class="px-3 py-2 text-left">Detected</th>
                        <th class="px-3 py-2 text-left">Owner</th>
                        <th class="px-3 py-2 text-left">Action</th>
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
                            <td class="px-3 py-2">{{ $incident->ownerAdmin?->name ?: 'N/A' }}</td>
                            <td class="px-3 py-2"><a class="text-blue-700" href="{{ route('admin.gdpr.breaches.show', $incident) }}">Open</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-3">{{ $incidents->links() }}</div>
        </div>
    </div>
</div>
@endsection
