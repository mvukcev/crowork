<x-app-layout>
    <x-slot name="title">{{ __('worker_profile.preview.title') }}</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-4xl">
            <div class="flex items-start justify-between gap-3 mb-5">
                <div>
                    <p class="cw-kicker mb-1">{{ __('worker_profile.preview.kicker') }}</p>
                    <h1 class="cw-display text-4xl md:text-6xl">{{ __('worker_profile.preview.headline') }}</h1>
                    <p class="text-slate-600 mt-2">{{ __('worker_profile.preview.subheadline') }}</p>
                </div>
                <a href="{{ route('worker.profile.edit') }}" class="cw-button-secondary">{{ __('worker_profile.preview.edit_profile') }}</a>
            </div>

            <div class="cw-surface p-5 mb-5">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-slate-700">{{ __('worker_profile.preview.completeness') }}</span>
                    <span class="text-sm font-semibold text-slate-900">{{ $completeness }}%</span>
                </div>
                <div class="h-2 rounded-full bg-slate-100 overflow-hidden mb-3">
                    <div class="h-full bg-emerald-500" style="width: {{ $completeness }}%"></div>
                </div>
                <p class="text-sm font-medium text-slate-700">{{ $completenessStateLabel ?? __('worker_profile.preview.status_fallback') }}</p>
                <p class="mt-1 text-sm text-slate-600">{{ $completenessHelperText ?? '' }}</p>
                @if(count($missingChecklist) > 0)
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($missingChecklist as $item)
                            <span class="cw-chip max-w-full break-words">{{ $item }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            <article class="cw-surface p-6 space-y-5">
                @php
                    $currentCountryLabel = \App\Support\CvProfileOptions::displayCountryName($profile->current_country, app()->getLocale());
                    $nationalityLabel = \App\Support\CvProfileOptions::displayCountryName($profile->nationality_country_code, app()->getLocale());
                    $visaStatusLabel = \App\Support\CvProfileOptions::displayVisaStatusLabel($profile->visa_work_permit_status);
                    $skills = $profile->skillsArray();
                    $languages = $profile->languagesArray();
                    $experiences = $profile->experienceSnapshot();
                    $educations = $profile->educationSnapshot();
                    $certifications = $profile->certificationSnapshot();
                    $references = $profile->referenceSnapshot();
                @endphp
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-2xl font-semibold text-slate-900">{{ trim(($profile->first_name ?? '') . ' ' . ($profile->last_name ?? '')) ?: __('worker_profile.preview.unnamed_worker') }}</h2>
                        <p class="text-sm text-slate-600">{{ $profile->professional_summary ?: __('worker_profile.preview.no_summary') }}</p>
                    </div>
                    @if($profile->photo_path)
                        <img src="{{ $profile->photoUrl() }}" alt="{{ __('worker_profile.preview.worker_photo_alt') }}" class="h-20 w-20 rounded-full object-cover border border-slate-200" onerror="this.onerror=null;this.src='{{ asset('assets/placeholders/worker/worker-avatar-400x400.svg') }}';">
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <p><strong>{{ __('worker_profile.preview.current_location') }}</strong> {{ trim(($profile->current_city ?? '') . ', ' . ($currentCountryLabel ?? ''), ', ') ?: __('worker_profile.preview.na') }}</p>
                    <p><strong>{{ __('worker_profile.preview.desired_location') }}</strong> {{ $profile->desired_city ?: __('worker_profile.preview.na') }}</p>
                    <p><strong>{{ __('worker_profile.preview.nationality') }}</strong> {{ $nationalityLabel ?: __('worker_profile.preview.na') }}</p>
                    <p><strong>{{ __('worker_profile.preview.availability') }}</strong> {{ $profile->availability_date?->format('d.m.Y.') ?: __('worker_profile.preview.na') }}</p>
                    <p><strong>{{ __('worker_profile.preview.salary') }}</strong> {{ $profile->salary_expectation ? number_format($profile->salary_expectation) . ' EUR' : __('worker_profile.preview.na') }}</p>
                    <p><strong>{{ __('worker_profile.preview.accommodation') }}</strong> {{ is_null($profile->accommodation_needed) ? __('worker_profile.preview.na') : ($profile->accommodation_needed ? __('worker_profile.preview.yes') : __('worker_profile.preview.no')) }}</p>
                    <p><strong>{{ __('worker_profile.preview.visa') }}</strong> {{ $visaStatusLabel ?: __('worker_profile.preview.na') }}</p>
                    <p><strong>{{ __('worker_profile.preview.visibility') }}</strong> {{ \App\Models\WorkerProfile::visibilityOptions()[$profile->profile_visibility ?? \App\Models\WorkerProfile::VISIBILITY_EMPLOYERS] ?? __('worker_profile.preview.employers') }}</p>
                </div>

                @if($profile->professional_summary)
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">{{ __('worker_profile.preview.professional_summary') }}</h3>
                        <p class="text-sm text-slate-700 whitespace-pre-line">{{ $profile->professional_summary }}</p>
                    </div>
                @endif

                @if($educations !== [])
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">{{ __('worker_profile.preview.education') }}</h3>
                        <ul class="space-y-1 text-sm text-slate-700">
                            @foreach($educations as $education)
                                <li>{{ $education['institution'] ?? 'N/A' }}{{ !empty($education['degree']) ? ' - ' . $education['degree'] : '' }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($experiences !== [])
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">{{ __('worker_profile.preview.work_experience') }}</h3>
                        <ul class="space-y-1 text-sm text-slate-700">
                            @foreach($experiences as $experience)
                                <li>{{ $experience['job_title'] ?? 'N/A' }}{{ !empty($experience['company_name']) ? ' @ ' . $experience['company_name'] : '' }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($skills !== [])
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">{{ __('worker_profile.preview.skills') }}</h3>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($skills as $skill)
                                <span class="cw-chip">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($languages !== [])
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">{{ __('worker_profile.preview.languages') }}</h3>
                        <ul class="text-sm text-slate-700 space-y-1">
                            @foreach($languages as $language)
                                @if(!empty($language['language']))
                                    <li>{{ $language['language'] }}{{ !empty($language['level']) ? ' (' . $language['level'] . ')' : '' }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($certifications !== [])
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">{{ __('worker_profile.preview.certifications') }}</h3>
                        <ul class="text-sm text-slate-700 space-y-1">
                            @foreach($certifications as $certification)
                                <li>{{ $certification['name'] ?? 'N/A' }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(is_array($profile->desired_roles) && count($profile->desired_roles) > 0)
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">{{ __('worker_profile.preview.desired_roles') }}</h3>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($profile->desired_roles as $role)
                                <span class="cw-chip">{{ $role }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($references !== [])
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">{{ __('worker_profile.preview.references') }}</h3>
                        <ul class="text-sm text-slate-700 space-y-1">
                            @foreach($references as $reference)
                                <li>{{ $reference['full_name'] ?? 'N/A' }}{{ !empty($reference['company']) ? ' - ' . $reference['company'] : '' }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </article>
        </div>
    </section>
</x-app-layout>
