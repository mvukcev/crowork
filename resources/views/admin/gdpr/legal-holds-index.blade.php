@extends('layouts.app')

@section('content')
<div class="cw-surface min-h-screen">
    <div class="cw-shell-spacing py-8">
        <h1 class="cw-heading-1 mb-4">Legal Holds</h1>
        @include('admin.gdpr.partials-nav')

        @if(session('success'))
            <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif

        <div class="cw-card-shell p-4 mb-6">
            <h2 class="cw-heading-3 mb-3">Place Legal Hold</h2>
            <form method="POST" action="{{ route('admin.gdpr.legal-holds.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @csrf
                <input class="cw-field" name="user_id" placeholder="User ID (optional)">
                <input class="cw-field" name="target_type" placeholder="Target type (optional)">
                <input class="cw-field" name="target_id" placeholder="Target ID (optional)">
                <input class="cw-field" name="reason" required placeholder="Reason">
                <textarea class="cw-field md:col-span-2" name="notes" rows="3" placeholder="Notes"></textarea>
                <button class="cw-button-primary md:col-span-2" type="submit" onclick="return confirm('Place legal hold? This blocks anonymization until release.')">Place legal hold</button>
            </form>
        </div>

        <div class="cw-card-shell p-4 mb-6 overflow-x-auto">
            <h2 class="cw-heading-3 mb-3">Active Holds</h2>
            <table class="min-w-full text-sm">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-3 py-2 text-left">ID</th>
                        <th class="px-3 py-2 text-left">Scope</th>
                        <th class="px-3 py-2 text-left">Reason</th>
                        <th class="px-3 py-2 text-left">Placed</th>
                        <th class="px-3 py-2 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activeHolds as $hold)
                        <tr class="border-t border-neutral-200">
                            <td class="px-3 py-2">#{{ $hold->id }}</td>
                            <td class="px-3 py-2">{{ $hold->user_id ? ('user#'.$hold->user_id) : (($hold->target_type ?: 'N/A').':'.($hold->target_id ?: 'N/A')) }}</td>
                            <td class="px-3 py-2">{{ $hold->reason }}</td>
                            <td class="px-3 py-2">{{ $hold->placed_at?->format('Y-m-d H:i') }}</td>
                            <td class="px-3 py-2">
                                <form method="POST" action="{{ route('admin.gdpr.legal-holds.release', $hold) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="cw-button-secondary" type="submit" onclick="return confirm('Release this legal hold?')">Release</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3">{{ $activeHolds->links() }}</div>
        </div>

        <div class="cw-card-shell p-4 overflow-x-auto">
            <h2 class="cw-heading-3 mb-3">Released Holds</h2>
            <table class="min-w-full text-sm">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-3 py-2 text-left">ID</th>
                        <th class="px-3 py-2 text-left">Scope</th>
                        <th class="px-3 py-2 text-left">Reason</th>
                        <th class="px-3 py-2 text-left">Released</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($releasedHolds as $hold)
                        <tr class="border-t border-neutral-200">
                            <td class="px-3 py-2">#{{ $hold->id }}</td>
                            <td class="px-3 py-2">{{ $hold->user_id ? ('user#'.$hold->user_id) : (($hold->target_type ?: 'N/A').':'.($hold->target_id ?: 'N/A')) }}</td>
                            <td class="px-3 py-2">{{ $hold->reason }}</td>
                            <td class="px-3 py-2">{{ $hold->released_at?->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3">{{ $releasedHolds->links() }}</div>
        </div>
    </div>
</div>
@endsection
