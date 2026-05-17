<x-app-layout>
    <x-slot name="title">{{ __('worker.application_pages.educations.page_title') }}</x-slot>

    <section class="cw-section">
        <div class="cw-container">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <div>
                    <p class="cw-kicker mb-1">{{ __('worker.application_pages.kicker') }}</p>
                    <h1 class="cw-display text-4xl md:text-6xl">{{ __('worker.application_pages.educations.title') }}</h1>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('worker.applications.index') }}" class="cw-button-secondary">{{ __('worker.application_pages.educations.jobs_link') }}</a>
                    <a href="{{ route('worker.settings.edit') }}" class="cw-button-secondary">{{ __('worker.application_pages.common.settings') }}</a>
                </div>
            </div>

            <div class="cw-surface overflow-hidden">
                @if($applications->count() > 0)
                    <div class="overflow-x-auto">
                    <table class="cw-table min-w-[760px]">
                        <thead>
                            <tr>
                                <th>{{ __('worker.application_pages.educations.columns.program') }}</th>
                                <th>{{ __('worker.application_pages.educations.columns.provider') }}</th>
                                <th>{{ __('worker.application_pages.educations.columns.status') }}</th>
                                <th>{{ __('worker.application_pages.educations.columns.applied') }}</th>
                                <th>{{ __('worker.application_pages.educations.columns.motivation') }}</th>
                                <th>{{ __('worker.application_pages.educations.columns.snapshot') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applications as $application)
                                <tr>
                                    <td>{{ $application->education?->title ?? 'N/A' }}</td>
                                    <td>{{ $application->education?->createdByUser?->name ?? 'N/A' }}</td>
                                    <td><x-badge tone="info">{{ __('applications.' . $application->status) }}</x-badge></td>
                                    <td>{{ $application->created_at?->translatedFormat('d.m.Y.') }}</td>
                                    <td class="max-w-xs">
                                        <p class="text-sm text-slate-700 line-clamp-2">{{ $application->message ?: __('worker.application_pages.common.no_message') }}</p>
                                    </td>
                                    <td>{{ !empty($application->profile_snapshot) ? __('worker.application_pages.common.stored') : 'N/A' }}</td>
                                    <td>
                                        @if($application->education)
                                            <a href="{{ route('educations.show', $application->education) }}" class="cw-button-secondary">{{ __('worker.application_pages.common.view') }}</a>
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
                            icon="calendar"
                            :title="__('worker.application_pages.educations.empty.title')"
                            :description="__('worker.application_pages.educations.empty.body')"
                            :actionHref="route('educations.index')"
                            :actionLabel="__('worker.application_pages.educations.empty.browse')"
                        />
                        <div class="mt-4 flex justify-center">
                            <a href="{{ route('worker.profile.edit') }}" class="cw-button-secondary">{{ __('worker.application_pages.educations.empty.review_profile') }}</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-app-layout>
