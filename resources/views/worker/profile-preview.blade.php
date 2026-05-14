<x-app-layout>
    <x-slot name="title">CV Preview</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-4xl">
            <div class="flex items-start justify-between gap-3 mb-5">
                <div>
                    <p class="cw-kicker mb-1">Standardized CV</p>
                    <h1 class="cw-display text-4xl md:text-6xl">CV Preview</h1>
                    <p class="text-slate-600 mt-2">This is the profile snapshot employers will see in your applications.</p>
                </div>
                <a href="{{ route('worker.profile.edit') }}" class="cw-button-secondary">Edit profile</a>
            </div>

            <div class="cw-surface p-5 mb-5">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-slate-700">Completeness</span>
                    <span class="text-sm font-semibold text-slate-900">{{ $completeness }}%</span>
                </div>
                <div class="h-2 rounded-full bg-slate-100 overflow-hidden mb-3">
                    <div class="h-full bg-emerald-500" style="width: {{ $completeness }}%"></div>
                </div>
                @if(count($missingChecklist) > 0)
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($missingChecklist as $item)
                            <span class="cw-chip">{{ $item }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            <article class="cw-surface p-6 space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-2xl font-semibold text-slate-900">{{ trim(($profile->first_name ?? '') . ' ' . ($profile->last_name ?? '')) ?: 'Unnamed worker' }}</h2>
                        <p class="text-sm text-slate-600">{{ $profile->job_title ?: 'No title provided' }}</p>
                    </div>
                    @if($profile->photo_path)
                        <img src="{{ asset('storage/' . $profile->photo_path) }}" alt="Worker photo" class="h-20 w-20 rounded-full object-cover border border-slate-200">
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <p><strong>Email:</strong> {{ $profile->email ?: 'N/A' }}</p>
                    <p><strong>Phone:</strong> {{ $profile->phone ?: 'N/A' }}</p>
                    <p><strong>Current location:</strong> {{ trim(($profile->current_city ?? '') . ', ' . ($profile->current_country ?? ''), ', ') ?: 'N/A' }}</p>
                    <p><strong>Desired location:</strong> {{ $profile->desired_city ?: 'N/A' }}</p>
                    <p><strong>Nationality:</strong> {{ $profile->nationality_country_code ?: 'N/A' }}</p>
                    <p><strong>Availability:</strong> {{ $profile->availability_date?->format('M j, Y') ?: 'N/A' }}</p>
                    <p><strong>Salary expectation:</strong> {{ $profile->salary_expectation ? number_format($profile->salary_expectation) . ' EUR' : 'N/A' }}</p>
                    <p><strong>Accommodation needed:</strong> {{ is_null($profile->accommodation_needed) ? 'N/A' : ($profile->accommodation_needed ? 'Yes' : 'No') }}</p>
                    <p><strong>Visa/work permit:</strong> {{ $profile->visa_work_permit_status ?: 'N/A' }}</p>
                    <p><strong>Profile visibility:</strong> {{ \App\Models\WorkerProfile::visibilityOptions()[$profile->profile_visibility ?? \App\Models\WorkerProfile::VISIBILITY_EMPLOYERS] ?? 'Employers' }}</p>
                </div>

                @if($profile->professional_summary)
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">Professional summary</h3>
                        <p class="text-sm text-slate-700 whitespace-pre-line">{{ $profile->professional_summary }}</p>
                    </div>
                @endif

                @if($profile->education_summary)
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">Education summary</h3>
                        <p class="text-sm text-slate-700 whitespace-pre-line">{{ $profile->education_summary }}</p>
                    </div>
                @endif

                @if($profile->work_experience)
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">Work experience</h3>
                        <p class="text-sm text-slate-700 whitespace-pre-line">{{ $profile->work_experience }}</p>
                    </div>
                @endif

                @if(is_array($profile->skills) && count($profile->skills) > 0)
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">Skills</h3>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($profile->skills as $skill)
                                <span class="cw-chip">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(is_array($profile->languages) && count($profile->languages) > 0)
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">Languages</h3>
                        <ul class="text-sm text-slate-700 space-y-1">
                            @foreach($profile->languages as $language)
                                @if(!empty($language['language']))
                                    <li>{{ $language['language'] }}{{ !empty($language['level']) ? ' (' . $language['level'] . ')' : '' }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($profile->certifications)
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">Certifications</h3>
                        <p class="text-sm text-slate-700 whitespace-pre-line">{{ $profile->certifications }}</p>
                    </div>
                @endif

                @if(is_array($profile->desired_roles) && count($profile->desired_roles) > 0)
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">Desired roles</h3>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($profile->desired_roles as $role)
                                <span class="cw-chip">{{ $role }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($profile->recommendations)
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">Recommendations</h3>
                        <p class="text-sm text-slate-700 whitespace-pre-line">{{ $profile->recommendations }}</p>
                    </div>
                @endif
            </article>
        </div>
    </section>
</x-app-layout>
