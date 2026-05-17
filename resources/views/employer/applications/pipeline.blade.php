@extends('layouts.app')

@section('content')
<div class="cw-surface min-h-screen">
    <!-- Header -->
    <div class="cw-surface-header border-b border-neutral-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="cw-heading-1">{{ __('employer.applications_pipeline.title') }}</h1>
                <a href="{{ route('employer.dashboard') }}" class="cw-button-secondary sm:ml-auto">
                    {{ __('employer.applications_pipeline.back_to_dashboard') }}
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <p id="pipelineStatusNotice" class="sr-only" aria-live="polite"></p>

        <div class="mb-6 rounded-xl border border-sky-200 bg-sky-50 p-4">
            <p class="text-sm font-semibold text-sky-900">{{ __('employer.gdpr.pipeline_notice_title') }}</p>
            <p class="text-sm text-sky-800 mt-1">{{ __('employer.gdpr.pipeline_notice_body') }}</p>
        </div>

        <!-- Filters -->
        <div class="mb-6 flex flex-col gap-4 lg:flex-row">
            <form method="GET" class="flex flex-col gap-4 flex-1 lg:flex-row" id="filterForm">
                <!-- Job Filter -->
                <div class="flex-1">
                    <label class="block text-sm font-medium text-neutral-700 mb-2">{{ __('employer.applications_pipeline.filter_by_job') }}</label>
                    <select name="job_id" class="cw-field" onchange="document.getElementById('filterForm').submit()">
                        <option value="">{{ __('employer.applications_pipeline.all_jobs') }}</option>
                        @foreach($jobs as $job)
                            <option value="{{ $job->id }}" @selected(request('job_id') == $job->id)>
                                {{ $job->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="flex-1">
                    <label class="block text-sm font-medium text-neutral-700 mb-2">{{ __('employer.applications_pipeline.filter_by_status') }}</label>
                    <select name="status" class="cw-field" onchange="document.getElementById('filterForm').submit()">
                        <option value="">{{ __('employer.applications_pipeline.all_statuses') }}</option>
                        @foreach(\App\Models\JobApplication::statusOptions() as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') == $value)>
                                {{ __('employer.statuses.' . $value) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Clear Filters -->
                @if(request('job_id') || request('status'))
                    <div class="flex items-end">
                        <a href="{{ route('employer.applications.pipeline') }}" class="cw-button-secondary">
                            {{ __('employer.applications_pipeline.clear_filters') }}
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Applications Table -->
        @if($applications->count() > 0)
            <div class="cw-surface border border-neutral-200 rounded-lg overflow-x-auto">
                <table class="min-w-[960px] w-full">
                    <thead class="bg-neutral-50 border-b border-neutral-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-700 uppercase tracking-wider">{{ __('employer.applications_pipeline.candidate') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-700 uppercase tracking-wider">{{ __('employer.applications_pipeline.job') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-700 uppercase tracking-wider">{{ __('employer.applications_pipeline.status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-700 uppercase tracking-wider">{{ __('employer.applications_pipeline.rating') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-700 uppercase tracking-wider">{{ __('employer.applications_pipeline.applied') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-700 uppercase tracking-wider">{{ __('employer.applications_pipeline.interview') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-700 uppercase tracking-wider">{{ __('employer.gdpr.data_access') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-neutral-700 uppercase tracking-wider">{{ __('employer.applications_pipeline.action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200">
                        @foreach($applications as $application)
                            @php
                                $profile = $application->masked_profile ?? [];
                                $candidateName = $profile['first_name'] ?? __('employer.applications_pipeline.candidate_fallback');
                                if ($profile['last_name'] ?? null) {
                                    $candidateName .= ' ' . $profile['last_name'];
                                }
                            @endphp
                            <tr class="hover:bg-neutral-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $candidatePhotoUrl = $profile['photo_url'] ?? (($profile['photo_path'] ?? null)
                                                ? route('worker.profile.photo.show', ['path' => $profile['photo_path']])
                                                : null);
                                        @endphp
                                        @if($candidatePhotoUrl)
                                            <img src="{{ $candidatePhotoUrl }}" 
                                                 alt="{{ $candidateName }}" 
                                                 class="w-10 h-10 rounded-full object-cover">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-neutral-300 flex items-center justify-center">
                                                <span class="text-sm font-medium text-neutral-600">{{ substr($candidateName, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-neutral-900">{{ $candidateName }}</p>
                                            <p class="text-xs text-neutral-500">{{ __('employer.applications_pipeline.id_short') }} {{ substr((string) $application->id, 0, 8) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-900">
                                    {{ $application->job->title }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <select data-app-id="{{ $application->id }}" 
                                            class="status-selector cw-field text-sm px-2 py-1">
                                        @foreach(\App\Models\JobApplication::statusOptions() as $value => $label)
                                            <option value="{{ $value }}" @selected($application->status === $value)>
                                                {{ __('employer.statuses.' . $value) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        @if($application->score)
                                            <div class="flex items-center gap-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 @if($i <= $application->score) text-yellow-400 @else text-neutral-300 @endif" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="text-sm text-neutral-600">{{ $application->score }}/5</span>
                                        @else
                                            <span class="text-sm text-neutral-500">{{ __('employer.applications_pipeline.not_available') }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-neutral-600">
                                    {{ $application->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($application->interview_at)
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span>{{ $application->interview_at->format('M d') }}</span>
                                        </div>
                                    @else
                                        <span class="text-neutral-500">{{ __('employer.applications_pipeline.not_available') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @php($access = $application->candidate_data_access ?? null)
                                    @if(is_array($access))
                                        <div class="flex flex-col gap-1">
                                            <span class="font-medium text-neutral-800">{{ $access['label'] ?? '' }}</span>
                                            @if(!empty($access['data_available_until_human']))
                                                <span class="text-xs text-neutral-500">{{ __('employer.gdpr.available_until', ['date' => $access['data_available_until_human']]) }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-neutral-500">{{ __('employer.applications_pipeline.not_available') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('employer.applications.candidate', $application) }}" 
                                       class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                                        {{ __('employer.applications_pipeline.view') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="bg-neutral-50 border-t border-neutral-200 px-6 py-4">
                    {{ $applications->links() }}
                </div>
            </div>
        @else
            <div class="cw-surface border-2 border-dashed border-neutral-200 rounded-lg p-12 text-center bg-gradient-to-br from-neutral-50 to-white">
                <svg class="w-12 h-12 text-neutral-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-lg font-semibold text-neutral-900 mb-2">{{ __('employer.applications_pipeline.empty_title') }}</h3>
                <p class="text-neutral-600 mb-2">{{ __('employer.applications_pipeline.empty_description') }}</p>
                <p class="text-sm text-neutral-500 mb-6">{{ __('employer.applications_pipeline.empty_hint') }}</p>
                <div class="flex flex-wrap gap-3 justify-center">
                    <a href="{{ route('employer.jobs.create') }}" class="cw-button-primary">{{ __('employer.applications_pipeline.post_job') }}</a>
                    <a href="{{ route('employer.applications.pipeline') }}" class="cw-button-secondary">{{ __('employer.applications_pipeline.clear_filters') }}</a>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
document.querySelectorAll('.status-selector').forEach(select => {
    select.addEventListener('change', function() {
        const appId = this.dataset.appId;
        const status = this.value;
        
        fetch(`/employer/applications/${appId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ status }),
        })
        .then(res => res.json())
        .catch(err => {
            console.error('Error updating status:', err);
            this.value = this.dataset.previousValue;
        });
        
        this.dataset.previousValue = status;
    });
    select.dataset.previousValue = select.value;
});
</script>
@endsection
