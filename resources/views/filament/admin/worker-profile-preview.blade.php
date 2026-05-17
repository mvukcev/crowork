@php
    $profile = $worker->workerProfile;
    $languages = $profile?->languagesArray() ?? [];
    $skills = $profile?->skillsArray() ?? [];
    $desiredRoles = is_array($profile?->desired_roles ?? null) ? $profile->desired_roles : [];
    $experiences = $profile?->experienceSnapshot() ?? [];
    $educations = $profile?->educationSnapshot() ?? [];
    $certifications = $profile?->certificationSnapshot() ?? [];
    $references = $profile?->referenceSnapshot() ?? [];
@endphp

<div class="space-y-5">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs uppercase tracking-[0.08em] text-slate-500">Worker CV Preview</p>
            <h3 class="mt-1 text-lg font-semibold text-slate-900">{{ $worker->name }}</h3>
            <p class="text-sm text-slate-600">{{ $worker->email }}</p>
        </div>
        <div class="text-right text-sm text-slate-600">
            <p>Applications: {{ $worker->applications_count }}</p>
            <p>Education applications: {{ $worker->education_applications_count }}</p>
        </div>
    </div>

    @if(! $profile)
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
            This worker does not have a profile yet.
        </div>
    @else
        <div class="rounded-xl border border-slate-200 bg-white p-4 space-y-4">
            <div class="grid gap-3 md:grid-cols-2 text-sm">
                <p><strong>Completeness:</strong> {{ $profile->completenessPercent() }}%</p>
                <p><strong>Visibility:</strong> {{ \App\Models\WorkerProfile::visibilityOptions()[$profile->profile_visibility] ?? 'N/A' }}</p>
                <p><strong>Current location:</strong> {{ trim(($profile->current_city ?? '') . ', ' . ($profile->current_country ?? ''), ', ') ?: 'N/A' }}</p>
                <p><strong>Desired city:</strong> {{ $profile->desired_city ?: 'N/A' }}</p>
                <p><strong>Availability:</strong> {{ $profile->availability_date?->format('M j, Y') ?: 'N/A' }}</p>
                <p><strong>Communication language:</strong> {{ $profile->communication_language ?: $worker->communication_language ?: 'N/A' }}</p>
            </div>

            @if($profile->professional_summary)
                <div>
                    <h4 class="text-sm font-semibold text-slate-900 mb-1">Professional summary</h4>
                    <p class="text-sm text-slate-700 whitespace-pre-line">{{ $profile->professional_summary }}</p>
                </div>
            @endif

            @if($educations !== [])
                <div>
                    <h4 class="text-sm font-semibold text-slate-900 mb-1">Education</h4>
                    <ul class="space-y-1 text-sm text-slate-700">
                        @foreach($educations as $education)
                            <li>{{ $education['institution'] ?? 'N/A' }}{{ !empty($education['degree']) ? ' - ' . $education['degree'] : '' }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($experiences !== [])
                <div>
                    <h4 class="text-sm font-semibold text-slate-900 mb-1">Work experience</h4>
                    <ul class="space-y-1 text-sm text-slate-700">
                        @foreach($experiences as $experience)
                            <li>{{ $experience['job_title'] ?? 'N/A' }}{{ !empty($experience['company_name']) ? ' @ ' . $experience['company_name'] : '' }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($skills !== [])
                <div>
                    <h4 class="text-sm font-semibold text-slate-900 mb-1">Skills</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($skills as $skill)
                            <span class="rounded-full border border-slate-200 px-2.5 py-1 text-xs text-slate-700">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($languages !== [])
                <div>
                    <h4 class="text-sm font-semibold text-slate-900 mb-1">Languages</h4>
                    <ul class="space-y-1 text-sm text-slate-700">
                        @foreach($languages as $language)
                            @php
                                $languageName = is_array($language) ? trim((string) ($language['language'] ?? '')) : trim((string) $language);
                                $languageLevel = is_array($language) ? trim((string) ($language['level'] ?? '')) : '';
                            @endphp
                            @if($languageName !== '')
                                <li>{{ $languageName }}{{ $languageLevel !== '' ? ' (' . $languageLevel . ')' : '' }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($desiredRoles !== [])
                <div>
                    <h4 class="text-sm font-semibold text-slate-900 mb-1">Desired roles</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($desiredRoles as $role)
                            <span class="rounded-full border border-slate-200 px-2.5 py-1 text-xs text-slate-700">{{ $role }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($certifications !== [])
                <div>
                    <h4 class="text-sm font-semibold text-slate-900 mb-1">Certifications</h4>
                    <ul class="space-y-1 text-sm text-slate-700">
                        @foreach($certifications as $certification)
                            <li>{{ $certification['name'] ?? 'N/A' }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($references !== [])
                <div>
                    <h4 class="text-sm font-semibold text-slate-900 mb-1">References</h4>
                    <ul class="space-y-1 text-sm text-slate-700">
                        @foreach($references as $reference)
                            <li>{{ $reference['full_name'] ?? 'N/A' }}{{ !empty($reference['company']) ? ' - ' . $reference['company'] : '' }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif
</div>