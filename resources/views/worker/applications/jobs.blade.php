<x-app-layout>
    <x-slot name="title">{{ __('worker.application_pages.jobs.page_title') }}</x-slot>

    <section class="cw-section">
        <div class="cw-container">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <div>
                    <p class="cw-kicker mb-1">{{ __('worker.application_pages.kicker') }}</p>
                    <h1 class="cw-display text-4xl md:text-6xl">{{ __('worker.application_pages.jobs.title') }}</h1>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('worker.education-applications.index') }}" class="cw-button-secondary">{{ __('worker.application_pages.jobs.education_link') }}</a>
                    <a href="{{ route('worker.settings.edit') }}" class="cw-button-secondary">{{ __('worker.application_pages.common.settings') }}</a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-6">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-xs uppercase tracking-wide text-slate-500">{{ __('worker.application_pages.jobs.summary.internal') }}</p>
                    <p class="text-xl font-semibold text-slate-900 mt-1">{{ $internalCount }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-xs uppercase tracking-wide text-slate-500">{{ __('worker.application_pages.jobs.summary.hzz_sent') }}</p>
                    <p class="text-xl font-semibold text-emerald-700 mt-1">{{ $hzzEmailSentCount }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-xs uppercase tracking-wide text-slate-500">{{ __('worker.application_pages.jobs.summary.hzz_failed') }}</p>
                    <p class="text-xl font-semibold text-amber-700 mt-1">{{ $hzzEmailFailedCount }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="text-xs uppercase tracking-wide text-slate-500">{{ __('worker.application_pages.jobs.summary.hzz_external') }}</p>
                    <p class="text-xl font-semibold text-slate-900 mt-1">{{ $hzzExternalOpenCount }}</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 mb-4">
                <a href="{{ route('worker.applications.index', ['channel' => 'all']) }}" class="{{ $channel === 'all' ? 'cw-button-primary' : 'cw-button-secondary' }}">{{ __('worker.application_pages.jobs.filters.all') }}</a>
                <a href="{{ route('worker.applications.index', ['channel' => 'internal']) }}" class="{{ $channel === 'internal' ? 'cw-button-primary' : 'cw-button-secondary' }}">{{ __('worker.application_pages.jobs.filters.internal') }}</a>
                <a href="{{ route('worker.applications.index', ['channel' => 'hzz']) }}" class="{{ $channel === 'hzz' ? 'cw-button-primary' : 'cw-button-secondary' }}">{{ __('worker.application_pages.jobs.filters.hzz') }}</a>
            </div>

            <div class="cw-surface overflow-hidden">
                @if($applications->count() > 0)
                    <div class="overflow-x-auto">
                    <table class="cw-table min-w-[1060px]">
                        <thead>
                            <tr>
                                <th>{{ __('worker.application_pages.jobs.columns.job') }}</th>
                                <th>{{ __('worker.application_pages.jobs.columns.employer') }}</th>
                                <th>{{ __('worker.application_pages.jobs.columns.channel') }}</th>
                                <th>{{ __('worker.application_pages.jobs.columns.delivery') }}</th>
                                <th>{{ __('worker.application_pages.jobs.columns.status') }}</th>
                                <th>{{ __('worker.application_pages.jobs.columns.applied') }}</th>
                                <th>{{ __('worker.application_pages.jobs.columns.submitted_at') }}</th>
                                <th>{{ __('worker.application_pages.jobs.columns.status_updated') }}</th>
                                <th>{{ __('worker.application_pages.jobs.columns.motivation') }}</th>
                                <th>{{ __('worker.application_pages.jobs.columns.snapshot') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applications as $application)
                                <tr>
                                    <td>{{ $application->job?->title ?? 'N/A' }}</td>
                                    <td>{{ $application->job?->employer?->company_name ?? 'N/A' }}</td>
                                    <td>
                                        @if($application->apply_channel === \App\Models\JobApplication::CHANNEL_HZZ_EMAIL)
                                            <x-badge tone="warning">HZZ / CroWork</x-badge>
                                        @elseif($application->apply_channel === \App\Models\JobApplication::CHANNEL_HZZ_EXTERNAL)
                                            <x-badge tone="warning">HZZ / External</x-badge>
                                        @else
                                            <x-badge tone="info">CroWork</x-badge>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $delivery = (string) ($application->submission_status ?: 'pending');
                                        @endphp
                                        @if($delivery === 'sent')
                                            <x-badge tone="success">{{ __('worker.application_pages.jobs.delivery.sent') }}</x-badge>
                                        @elseif($delivery === 'failed')
                                            <x-badge tone="danger">{{ __('worker.application_pages.jobs.delivery.failed') }}</x-badge>
                                        @else
                                            <x-badge tone="info">{{ __('worker.application_pages.jobs.delivery.pending') }}</x-badge>
                                        @endif
                                        @if($application->submitted_to_email)
                                            <p class="text-[11px] text-slate-500 mt-1">{{ $application->submitted_to_email }}</p>
                                        @endif
                                    </td>
                                    <td><x-badge tone="info">{{ __('applications.' . $application->status) }}</x-badge></td>
                                    <td>{{ $application->created_at?->translatedFormat('d.m.Y.') }}</td>
                                    <td>{{ $application->submitted_at?->translatedFormat('d.m.Y. H:i') ?? 'N/A' }}</td>
                                    <td>{{ $application->status_updated_at?->translatedFormat('d.m.Y. H:i') ?? 'N/A' }}</td>
                                    <td class="max-w-xs">
                                        <p class="text-sm text-slate-700 line-clamp-2">{{ $application->message ?: __('worker.application_pages.common.no_message') }}</p>
                                    </td>
                                    <td>{{ !empty($application->profile_snapshot) ? __('worker.application_pages.common.stored') : 'N/A' }}</td>
                                    <td>
                                        @if($application->job)
                                            <a href="{{ route('jobs.show', $application->job) }}" class="cw-button-secondary">{{ __('worker.application_pages.common.view') }}</a>
                                        @else
                                            <span class="text-xs text-slate-500">{{ __('worker.application_pages.common.unavailable') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    <div class="p-4">{{ $applications->links() }}</div>
                @else
                    <div class="p-6 md:p-8">
                        <x-empty-state
                            icon="inbox"
                            :title="__('worker.application_pages.jobs.empty.title')"
                            :description="__('worker.application_pages.jobs.empty.body')"
                            :actionHref="route('jobs.index')"
                            :actionLabel="__('worker.application_pages.jobs.empty.browse')"
                        />
                        <p class="text-sm text-slate-500 mt-4 text-center">{{ __('worker.application_pages.jobs.empty.note') }}</p>
                        <div class="mt-4 flex justify-center">
                            <a href="{{ route('worker.profile.edit') }}" class="cw-button-secondary">{{ __('worker.application_pages.jobs.empty.complete_profile') }}</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-app-layout>
