@extends('layouts.app')

@section('content')
<div class="cw-surface min-h-screen">
    <div class="cw-shell-spacing py-8">
        <h1 class="cw-heading-1 mb-4">{{ __('gdpr_admin.legal_holds') }}</h1>
        @include('admin.gdpr.partials-nav')

        @if(session('success'))
            <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif

        <div class="cw-card-shell p-4 mb-6">
            <h2 class="cw-heading-3 mb-3">{{ __('gdpr_admin.place_legal_hold') }}</h2>
            <form method="POST" action="{{ route('admin.gdpr.legal-holds.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @csrf
                <input class="cw-field" name="user_id" placeholder="{{ __('gdpr_admin.user_id_optional') }}">
                <input class="cw-field" name="target_type" placeholder="{{ __('gdpr_admin.target_type_optional') }}">
                <input class="cw-field" name="target_id" placeholder="{{ __('gdpr_admin.target_id_optional') }}">
                <input class="cw-field" name="reason" required placeholder="{{ __('gdpr_admin.reason') }}">
                <textarea class="cw-field md:col-span-2" name="notes" rows="3" placeholder="{{ __('gdpr_admin.notes') }}"></textarea>
                <button class="cw-button-primary md:col-span-2" type="submit" onclick="return confirm('{{ __('gdpr_admin.confirm_place_legal_hold') }}')">{{ __('gdpr_admin.place_legal_hold_action') }}</button>
            </form>
        </div>

        <div class="cw-card-shell p-4 mb-6 overflow-x-auto">
            <h2 class="cw-heading-3 mb-3">{{ __('gdpr_admin.active_holds') }}</h2>
            <table class="min-w-full text-sm">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-3 py-2 text-left">ID</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.scope') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.reason') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.placed') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activeHolds as $hold)
                        <tr class="border-t border-neutral-200">
                            <td class="px-3 py-2">#{{ $hold->id }}</td>
                            <td class="px-3 py-2">{{ $hold->user_id ? ('user#'.$hold->user_id) : (($hold->target_type ?: __('gdpr_admin.na')).':'.($hold->target_id ?: __('gdpr_admin.na'))) }}</td>
                            <td class="px-3 py-2">{{ $hold->reason }}</td>
                            <td class="px-3 py-2">{{ $hold->placed_at?->format('Y-m-d H:i') }}</td>
                            <td class="px-3 py-2">
                                <form method="POST" action="{{ route('admin.gdpr.legal-holds.release', $hold) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="cw-button-secondary" type="submit" onclick="return confirm('{{ __('gdpr_admin.confirm_release_legal_hold') }}')">{{ __('gdpr_admin.release') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3">{{ $activeHolds->links() }}</div>
        </div>

        <div class="cw-card-shell p-4 overflow-x-auto">
            <h2 class="cw-heading-3 mb-3">{{ __('gdpr_admin.released_holds') }}</h2>
            <table class="min-w-full text-sm">
                <thead class="bg-neutral-50">
                    <tr>
                        <th class="px-3 py-2 text-left">ID</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.scope') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.reason') }}</th>
                        <th class="px-3 py-2 text-left">{{ __('gdpr_admin.released') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($releasedHolds as $hold)
                        <tr class="border-t border-neutral-200">
                            <td class="px-3 py-2">#{{ $hold->id }}</td>
                            <td class="px-3 py-2">{{ $hold->user_id ? ('user#'.$hold->user_id) : (($hold->target_type ?: __('gdpr_admin.na')).':'.($hold->target_id ?: __('gdpr_admin.na'))) }}</td>
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
