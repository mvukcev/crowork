<x-app-layout>
    <x-slot name="title">{{ __('worker_profile.editor.title') }}</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-5xl">
            <div class="cw-cv-header">
                <p class="cw-kicker">{{ __('worker_profile.editor.kicker') }}</p>
                <h1 class="cw-display text-4xl md:text-6xl">{{ __('worker_profile.editor.headline') }}</h1>
                <p class="text-slate-600">{{ __('worker_profile.editor.subheadline') }}</p>
            </div>

            <div class="cw-surface p-4 mb-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-slate-700">{{ __('worker_profile.editor.completeness') }}</span>
                    <span class="text-sm font-semibold text-slate-900">{{ $completeness }}%</span>
                </div>
                <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-full bg-emerald-500" style="width: {{ $completeness }}%"></div>
                </div>
                <p class="mt-2 text-sm font-medium text-slate-700">{{ $completenessStateLabel ?? __('worker_profile.editor.status_fallback') }}</p>
                <p class="mt-1 text-sm text-slate-600">{{ $completenessHelperText ?? '' }}</p>
                @if(count($missingChecklist) > 0)
                    <div class="mt-3">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">{{ __('worker_profile.editor.missing_fields') }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach(array_slice($missingChecklist, 0, 10) as $item)
                                <span class="cw-chip max-w-full break-words">{{ $item }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            @if(session('success'))
                <div class="cw-surface p-3 mb-4 text-sm text-emerald-700 bg-emerald-50 border-emerald-200">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="cw-surface p-4 mb-4 border-red-200 bg-red-50">
                    <ul class="text-sm text-red-700 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('worker.profile.update') }}" enctype="multipart/form-data" class="cw-surface p-5 md:p-8 cw-cv-editor" x-data="cvBuilder(@js($initialSkills ?? []), @js($initialDesiredRoles ?? []), @js($languageRows ?? []), @js($experienceRows ?? []), @js($educationRows ?? []), @js($certificationRows ?? []), @js($referenceRows ?? []))" data-cw-track-submit="worker_profile_update">
                @csrf
                @method('PUT')

                <datalist id="country-options-list">
                    @foreach($countryOptions as $countryCode => $countryName)
                        <option value="{{ $countryName }}" data-code="{{ $countryCode }}"></option>
                    @endforeach
                </datalist>

                <datalist id="skill-suggestions-list">
                    @foreach($skillSuggestions as $skillSuggestion)
                        <option value="{{ $skillSuggestion }}"></option>
                    @endforeach
                </datalist>

                <template x-for="(role, index) in preservedDesiredRoles" :key="`desired-role-preserved-${index}`">
                    <input type="hidden" name="desired_roles[]" :value="role">
                </template>

                <section class="cw-cv-section">
                    <div class="cw-cv-section-head">
                        <h2 class="cw-cv-section-title">{{ __('worker_profile.editor.section_personal') }}</h2>
                    </div>

                    <div class="cv-grid">
                        <div>
                            <label class="cw-label" for="first_name">{{ __('worker_profile.editor.label_first_name') }}</label>
                            <input id="first_name" name="first_name" class="cw-field" value="{{ old('first_name', $profile->first_name) }}" autocomplete="given-name" required>
                        </div>
                        <div>
                            <label class="cw-label" for="last_name">{{ __('worker_profile.editor.label_last_name') }}</label>
                            <input id="last_name" name="last_name" class="cw-field" value="{{ old('last_name', $profile->last_name) }}" autocomplete="family-name" required>
                        </div>
                        <div>
                            <label class="cw-label" for="nationality_country_code">{{ __('worker_profile.editor.label_nationality') }}</label>
                            <input id="nationality_country_code" name="nationality_country_code" class="cw-field" list="country-options-list" autocomplete="country-name" value="{{ $nationalityDisplayValue }}" placeholder="{{ __('worker_profile.editor.placeholder_country') }}">
                        </div>
                        <div>
                            <label class="cw-label" for="birth_year">{{ __('worker_profile.editor.label_birth_year') }}</label>
                            <input id="birth_year" name="birth_year" type="number" class="cw-field" value="{{ old('birth_year', $profile->birth_year) }}">
                        </div>
                        <div>
                            <label class="cw-label" for="current_country">{{ __('worker_profile.editor.label_current_country') }}</label>
                            <input id="current_country" name="current_country" class="cw-field" list="country-options-list" autocomplete="country-name" value="{{ $currentCountryDisplayValue }}" placeholder="{{ __('worker_profile.editor.placeholder_country') }}">
                        </div>
                        <div>
                            <label class="cw-label" for="current_city">{{ __('worker_profile.editor.label_current_city') }}</label>
                            <input id="current_city" name="current_city" class="cw-field" autocomplete="address-level2" value="{{ old('current_city', $profile->current_city) }}">
                        </div>
                        <div>
                            <label class="cw-label" for="desired_city">{{ __('worker_profile.editor.label_desired_city') }}</label>
                            <input id="desired_city" name="desired_city" class="cw-field" value="{{ old('desired_city', $profile->desired_city) }}">
                        </div>
                        <div>
                            <label class="cw-label" for="availability_date">{{ __('worker_profile.editor.label_availability_date') }}</label>
                            <input id="availability_date" name="availability_date" type="date" class="cw-field" value="{{ old('availability_date', optional($profile->availability_date)->toDateString()) }}">
                        </div>
                    </div>
                </section>

                <section class="cw-cv-section">
                    <div class="cw-cv-section-head">
                        <h2 class="cw-cv-section-title">{{ __('worker_profile.editor.section_summary') }}</h2>
                    </div>

                    <div class="cv-grid">
                        <div class="cv-field--full">
                            <label class="cw-label" for="professional_summary">{{ __('worker_profile.editor.label_professional_summary') }}</label>
                            <textarea id="professional_summary" name="professional_summary" rows="4" class="cw-field cw-cv-textarea">{{ old('professional_summary', $profile->professional_summary) }}</textarea>
                        </div>
                        <div>
                            <label class="cw-label" for="salary_expectation">{{ __('worker_profile.editor.label_salary_expectation') }}</label>
                            <input id="salary_expectation" name="salary_expectation" type="number" min="0" class="cw-field" value="{{ old('salary_expectation', $profile->salary_expectation) }}">
                        </div>
                        <div>
                            <label class="cw-label" for="visa_work_permit_status">{{ __('worker_profile.editor.label_visa_status') }}</label>
                            <select id="visa_work_permit_status" name="visa_work_permit_status" class="cw-field">
                                <option value="">{{ __('worker_profile.editor.select_placeholder') }}</option>
                                @foreach($visaStatusOptions as $visaValue => $visaLabel)
                                    <option value="{{ $visaValue }}" @selected($visaCurrentValue === $visaValue)>{{ $visaLabel }}</option>
                                @endforeach
                                @if($visaCurrentValue !== '' && !array_key_exists($visaCurrentValue, $visaStatusOptions))
                                    <option value="{{ $visaCurrentValue }}" selected>{{ __('worker_profile.editor.legacy_value_prefix') }} {{ $visaCurrentValue }}</option>
                                @endif
                            </select>
                        </div>
                        <div>
                            <label class="cw-label" for="profile_visibility">{{ __('worker_profile.editor.label_profile_visibility') }}</label>
                            <select id="profile_visibility" name="profile_visibility" class="cw-field" required>
                                @foreach(
                                    \App\Models\WorkerProfile::visibilityOptions() as $key => $label
                                )
                                    <option value="{{ $key }}" @selected(old('profile_visibility', $profile->profile_visibility) === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end">
                            <label class="inline-flex items-center gap-2 text-sm text-slate-700 pb-2">
                                <input type="checkbox" name="accommodation_needed" value="1" class="rounded border-slate-300" @checked(old('accommodation_needed', $profile->accommodation_needed))>
                                {{ __('worker_profile.editor.label_accommodation_needed') }}
                            </label>
                        </div>
                    </div>
                </section>

                <section class="cw-cv-section">
                    <div class="cw-cv-section-head">
                        <h2 class="cw-cv-section-title">{{ __('worker_profile.editor.section_languages') }}</h2>
                        <button type="button" class="cw-button-secondary cw-cv-section-action" @click="addLanguage()">{{ __('worker_profile.editor.add_language') }}</button>
                    </div>

                    <div class="cw-cv-card-list">
                        <template x-for="(row, idx) in languages" :key="`language-${idx}`">
                            <div class="cw-cv-card">
                                <div class="cw-cv-card-head">
                                    <p class="cw-cv-card-title" x-text="@js(__('worker_profile.editor.item_language', ['number' => ''])) + (idx + 1)"></p>
                                    <button type="button" class="cw-cv-remove-button" @click="removeLanguage(idx)">{{ __('worker_profile.editor.remove') }}</button>
                                </div>

                                <div class="cv-grid">
                                    <div>
                                        <label class="cw-label">{{ __('worker_profile.editor.label_language') }}</label>
                                        <input class="cw-field" :placeholder="@js(__('worker_profile.editor.placeholder_language'))" x-model="row.language" :name="`languages[${idx}][language]`" autocomplete="language">
                                    </div>
                                    <div>
                                        <label class="cw-label">{{ __('worker_profile.editor.label_level') }}</label>
                                        <input class="cw-field" :placeholder="@js(__('worker_profile.editor.placeholder_level'))" x-model="row.level" :name="`languages[${idx}][level]`">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </section>

                <section class="cw-cv-section">
                    <div class="cw-cv-section-head">
                        <h2 class="cw-cv-section-title">{{ __('worker_profile.editor.section_education') }}</h2>
                        <button type="button" class="cw-button-secondary cw-cv-section-action" @click="addEducation()">{{ __('worker_profile.editor.add_education') }}</button>
                    </div>

                    <div class="cw-cv-card-list">
                    <template x-for="(education, idx) in educations" :key="`education-${idx}`">
                        <div class="cw-cv-card">
                            <div class="cw-cv-card-head">
                                <p class="cw-cv-card-title" x-text="@js(__('worker_profile.editor.item_education', ['number' => ''])) + (idx + 1)"></p>
                                <button type="button" class="cw-cv-remove-button" @click="removeEducation(idx)">{{ __('worker_profile.editor.remove') }}</button>
                            </div>

                            <div class="cv-grid">
                                <div>
                                    <label class="cw-label">{{ __('worker_profile.editor.label_institution') }}</label>
                                    <input class="cw-field" :placeholder="@js(__('worker_profile.editor.placeholder_institution'))" x-model="education.institution" :name="`educations[${idx}][institution]`">
                                </div>
                                <div>
                                    <label class="cw-label">{{ __('worker_profile.editor.label_degree') }}</label>
                                    <input class="cw-field" :placeholder="@js(__('worker_profile.editor.placeholder_degree'))" x-model="education.degree" :name="`educations[${idx}][degree]`">
                                </div>
                                <div>
                                    <label class="cw-label">{{ __('worker_profile.editor.label_field_of_study') }}</label>
                                    <input class="cw-field" :placeholder="@js(__('worker_profile.editor.placeholder_field'))" x-model="education.field_of_study" :name="`educations[${idx}][field_of_study]`">
                                </div>
                                <div>
                                    <label class="cw-label">{{ __('worker_profile.editor.label_city') }}</label>
                                    <input class="cw-field" :placeholder="@js(__('worker_profile.editor.placeholder_city'))" x-model="education.city" :name="`educations[${idx}][city]`" autocomplete="address-level2">
                                </div>
                                <div>
                                    <label class="cw-label">{{ __('worker_profile.editor.label_country') }}</label>
                                    <input class="cw-field" :placeholder="@js(__('worker_profile.editor.placeholder_country'))" x-model="education.country" :name="`educations[${idx}][country]`" list="country-options-list" autocomplete="country-name">
                                </div>
                                <div>
                                    <label class="cw-label">{{ __('worker_profile.editor.label_start') }}</label>
                                    <input type="date" class="cw-field" x-model="education.start_date" :name="`educations[${idx}][start_date]`">
                                </div>
                                <div>
                                    <label class="cw-label">{{ __('worker_profile.editor.label_end') }}</label>
                                    <input type="date" class="cw-field" x-model="education.end_date" :name="`educations[${idx}][end_date]`">
                                </div>
                                <div class="cv-field--full">
                                    <label class="cw-label">{{ __('worker_profile.editor.label_description') }}</label>
                                    <textarea class="cw-field cw-cv-textarea" rows="3" :placeholder="@js(__('worker_profile.editor.placeholder_description'))" x-model="education.description" :name="`educations[${idx}][description]`"></textarea>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <section class="cw-cv-section">
                    <div class="cw-cv-section-head">
                        <h2 class="cw-cv-section-title">{{ __('worker_profile.editor.section_experience') }}</h2>
                        <button type="button" class="cw-button-secondary cw-cv-section-action" @click="addExperience()">{{ __('worker_profile.editor.add_experience') }}</button>
                    </div>

                    <div class="cw-cv-card-list">
                    <template x-for="(experience, idx) in experiences" :key="`experience-${idx}`">
                        <div class="cw-cv-card">
                            <div class="cw-cv-card-head">
                                <p class="cw-cv-card-title" x-text="@js(__('worker_profile.editor.item_experience', ['number' => ''])) + (idx + 1)"></p>
                                <button type="button" class="cw-cv-remove-button" @click="removeExperience(idx)">{{ __('worker_profile.editor.remove') }}</button>
                            </div>

                            <div class="cv-grid">
                                <div>
                                    <label class="cw-label">{{ __('worker_profile.editor.label_position') }}</label>
                                    <input class="cw-field" :placeholder="@js(__('worker_profile.editor.placeholder_position'))" x-model="experience.job_title" :name="`experiences[${idx}][job_title]`">
                                </div>
                                <div>
                                    <label class="cw-label">{{ __('worker_profile.editor.label_employer') }}</label>
                                    <input class="cw-field" :placeholder="@js(__('worker_profile.editor.placeholder_employer'))" x-model="experience.company_name" :name="`experiences[${idx}][company_name]`" autocomplete="organization">
                                </div>
                                <div>
                                    <label class="cw-label">{{ __('worker_profile.editor.label_city') }}</label>
                                    <input class="cw-field" :placeholder="@js(__('worker_profile.editor.placeholder_city'))" x-model="experience.city" :name="`experiences[${idx}][city]`" autocomplete="address-level2">
                                </div>
                                <div>
                                    <label class="cw-label">{{ __('worker_profile.editor.label_country') }}</label>
                                    <input class="cw-field" :placeholder="@js(__('worker_profile.editor.placeholder_country'))" x-model="experience.country" :name="`experiences[${idx}][country]`" list="country-options-list" autocomplete="country-name">
                                </div>
                                <div>
                                    <label class="cw-label">{{ __('worker_profile.editor.label_start') }}</label>
                                    <input type="date" class="cw-field" x-model="experience.start_date" :name="`experiences[${idx}][start_date]`">
                                </div>
                                <div>
                                    <label class="cw-label">{{ __('worker_profile.editor.label_end') }}</label>
                                    <input type="date" class="cw-field" :disabled="experience.is_current" x-model="experience.end_date" :name="`experiences[${idx}][end_date]`">
                                </div>
                                <div class="cv-field--full">
                                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                        <input type="checkbox" value="1" class="rounded border-slate-300" x-model="experience.is_current" :name="`experiences[${idx}][is_current]`">
                                        {{ __('worker_profile.editor.label_current_role') }}
                                    </label>
                                </div>
                                <div class="cv-field--full">
                                    <label class="cw-label">{{ __('worker_profile.editor.label_description') }}</label>
                                    <textarea class="cw-field cw-cv-textarea" rows="3" :placeholder="@js(__('worker_profile.editor.placeholder_task_desc'))" x-model="experience.description" :name="`experiences[${idx}][description]`"></textarea>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <section class="cw-cv-section">
                    <div class="cw-cv-section-head">
                        <h2 class="cw-cv-section-title">{{ __('worker_profile.editor.section_certifications') }}</h2>
                        <button type="button" class="cw-button-secondary cw-cv-section-action" @click="addCertification()">{{ __('worker_profile.editor.add_certification') }}</button>
                    </div>

                    <div class="cw-cv-card-list">
                    <template x-for="(certification, idx) in certifications" :key="`certification-${idx}`">
                        <div class="cw-cv-card">
                            <div class="cw-cv-card-head">
                                <p class="cw-cv-card-title" x-text="@js(__('worker_profile.editor.item_certification', ['number' => ''])) + (idx + 1)"></p>
                                <button type="button" class="cw-cv-remove-button" @click="removeCertification(idx)">{{ __('worker_profile.editor.remove') }}</button>
                            </div>

                            <div class="cv-grid">
                                <div>
                                    <label class="cw-label">{{ __('worker_profile.editor.label_cert_name') }}</label>
                                    <input class="cw-field" :placeholder="@js(__('worker_profile.editor.placeholder_cert_name'))" x-model="certification.name" :name="`certifications_list[${idx}][name]`">
                                </div>
                                <div>
                                    <label class="cw-label">{{ __('worker_profile.editor.label_issuer') }}</label>
                                    <input class="cw-field" :placeholder="@js(__('worker_profile.editor.placeholder_issuer'))" x-model="certification.issuer" :name="`certifications_list[${idx}][issuer]`">
                                </div>
                                <div>
                                    <label class="cw-label">{{ __('worker_profile.editor.label_issued_on') }}</label>
                                    <input type="date" class="cw-field" x-model="certification.issued_on" :name="`certifications_list[${idx}][issued_on]`">
                                </div>
                                <div>
                                    <label class="cw-label">{{ __('worker_profile.editor.label_expires_on') }}</label>
                                    <input type="date" class="cw-field" x-model="certification.expires_on" :name="`certifications_list[${idx}][expires_on]`">
                                </div>
                                <div>
                                    <label class="cw-label">{{ __('worker_profile.editor.label_credential_id') }}</label>
                                    <input class="cw-field" :placeholder="@js(__('worker_profile.editor.placeholder_credential_id'))" x-model="certification.credential_id" :name="`certifications_list[${idx}][credential_id]`">
                                </div>
                                <div>
                                    <label class="cw-label">{{ __('worker_profile.editor.label_credential_url') }}</label>
                                    <input class="cw-field" :placeholder="@js(__('worker_profile.editor.placeholder_url'))" x-model="certification.credential_url" :name="`certifications_list[${idx}][credential_url]`">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <section class="cw-cv-section">
                    <div class="cw-cv-section-head">
                        <h2 class="cw-cv-section-title">{{ __('worker_profile.editor.section_skills') }}</h2>
                    </div>

                    <div class="cv-grid">
                        <div class="cv-field--full">
                            <label class="cw-label">{{ __('worker_profile.editor.section_skills') }}</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <input x-model="skillInput" class="cw-field" list="skill-suggestions-list" :placeholder="@js(__('worker_profile.editor.placeholder_skill'))" @keydown.enter.prevent="addSkill()">
                                <button type="button" class="cw-button-secondary w-full" @click="addSkill()">{{ __('worker_profile.editor.add_skill') }}</button>
                            </div>
                            <div class="cw-cv-chip-list">
                                <template x-for="(skill, index) in skills" :key="index">
                                    <span class="cw-cv-chip">
                                        <span x-text="skill"></span>
                                        <button type="button" class="cw-cv-chip-remove" @click="removeSkill(index)">{{ __('worker_profile.editor.remove') }}</button>
                                    </span>
                                </template>
                            </div>
                            <template x-for="(skill, index) in skills" :key="'skill-input-' + index">
                                <input type="hidden" name="skills[]" :value="skill">
                            </template>
                        </div>
                    </div>
                </section>

                <section class="cw-cv-section">
                    <div class="cw-cv-section-head">
                        <h2 class="cw-cv-section-title">{{ __('worker_profile.editor.section_references') }}</h2>
                        <button type="button" class="cw-button-secondary cw-cv-section-action" @click="addReference()">{{ __('worker_profile.editor.add_reference') }}</button>
                    </div>

                    <div class="cw-cv-card-list">
                    <template x-for="(reference, idx) in references" :key="`reference-${idx}`">
                        <div class="cw-cv-card">
                            <div class="cw-cv-card-head">
                                <p class="cw-cv-card-title" x-text="@js(__('worker_profile.editor.item_reference', ['number' => ''])) + (idx + 1)"></p>
                                <button type="button" class="cw-cv-remove-button" @click="removeReference(idx)">{{ __('worker_profile.editor.remove') }}</button>
                            </div>

                            <div class="cv-grid">
                                <div>
                                    <label class="cw-label">{{ __('worker_profile.editor.label_full_name') }}</label>
                                    <input class="cw-field" :placeholder="@js(__('worker_profile.editor.placeholder_name'))" x-model="reference.full_name" :name="`references_list[${idx}][full_name]`">
                                </div>
                                <div>
                                    <label class="cw-label">{{ __('worker_profile.editor.label_position') }}</label>
                                    <input class="cw-field" :placeholder="@js(__('worker_profile.editor.placeholder_position'))" x-model="reference.position" :name="`references_list[${idx}][position]`">
                                </div>
                                <div>
                                    <label class="cw-label">{{ __('worker_profile.editor.label_company') }}</label>
                                    <input class="cw-field" :placeholder="@js(__('worker_profile.editor.placeholder_company'))" x-model="reference.company" :name="`references_list[${idx}][company]`" autocomplete="organization">
                                </div>
                                <div>
                                    <label class="cw-label">{{ __('worker_profile.editor.label_email') }}</label>
                                    <input class="cw-field" :placeholder="@js(__('worker_profile.editor.placeholder_email'))" x-model="reference.contact_email" :name="`references_list[${idx}][contact_email]`" autocomplete="email">
                                </div>
                                <div>
                                    <label class="cw-label">{{ __('worker_profile.editor.label_phone') }}</label>
                                    <input class="cw-field" :placeholder="@js(__('worker_profile.editor.placeholder_phone'))" x-model="reference.contact_phone" :name="`references_list[${idx}][contact_phone]`" autocomplete="tel">
                                </div>
                                <div class="cv-field--full">
                                    <label class="cw-label">{{ __('worker_profile.editor.label_note') }}</label>
                                    <textarea class="cw-field cw-cv-textarea" rows="3" :placeholder="@js(__('worker_profile.editor.placeholder_note'))" x-model="reference.notes" :name="`references_list[${idx}][notes]`"></textarea>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <section class="cw-cv-section" x-data="{ 
                    photoPreview: null, 
                    photoFile: null,
                    isDragover: false,
                    uploadProgress: 0,
                    hasPhoto: {{ $profile->photo_path ? 'true' : 'false' }},
                    handlePhotoSelect(file) {
                        if (file && file.type.startsWith('image/')) {
                            this.photoFile = file;
                            const reader = new FileReader();
                            reader.onload = (e) => this.photoPreview = e.target.result;
                            reader.readAsDataURL(file);
                        }
                    },
                    handlePhotoChange(event) {
                        this.handlePhotoSelect(event.target.files[0]);
                    },
                    handleDrop(event) {
                        event.preventDefault();
                        this.isDragover = false;
                        this.handlePhotoSelect(event.dataTransfer.files[0]);
                    },
                    clearPhotoPreview() {
                        this.photoPreview = null;
                        this.photoFile = null;
                        this.$refs.photoInput.value = '';
                    }
                }">
                    <div class="cw-cv-section-head">
                        <h2 class="cw-cv-section-title">{{ __('worker_profile.editor.section_photo') }}</h2>
                    </div>

                    <label 
                        @dragover="isDragover = true" 
                        @dragleave="isDragover = false" 
                        @drop="handleDrop"
                        class="block border-2 border-dashed rounded-xl p-6 transition-colors cursor-pointer"
                        :class="isDragover ? 'border-blue-500 bg-blue-50' : 'border-slate-300 bg-slate-50 hover:border-slate-400'">
                        <input 
                            x-ref="photoInput"
                            id="photo" 
                            type="file" 
                            name="photo" 
                            accept="image/jpeg,image/png,image/webp" 
                            class="hidden"
                            @change="handlePhotoChange">

                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20a4 4 0 004 4h24a4 4 0 004-4V20m-8-12l-3.172-3.172a4 4 0 00-5.656 0L28 12m0 0l4 4m4-4h8v8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <p class="mt-2 text-sm font-medium text-slate-900">{{ __('worker_profile.editor.photo_drop') }}</p>
                            <p class="text-xs text-slate-500 mt-1">{{ __('worker_profile.editor.photo_meta') }}</p>
                        </div>
                    </label>
                    
                    <!-- Photo Preview -->
                    <div x-show="photoPreview || hasPhoto" class="mt-4">
                        <p class="text-sm font-semibold text-slate-700 mb-3">{{ __('worker_profile.editor.photo_preview') }}</p>
                        <div class="flex items-end gap-4">
                            <div class="relative">
                                <img 
                                    x-show="photoPreview" 
                                    :src="photoPreview" 
                                    alt="{{ __('worker_profile.editor.photo_preview_alt') }}" 
                                    class="h-20 w-20 rounded-full object-cover border-2 border-slate-200">
                                <img 
                                    x-show="!photoPreview && hasPhoto"
                                    src="{{ $profile->photoUrl() ?? '' }}" 
                                    alt="{{ __('worker_profile.editor.photo_current_alt') }}" 
                                    class="h-20 w-20 rounded-full object-cover border-2 border-slate-200"
                                    onerror="this.onerror=null;this.src='{{ asset('assets/placeholders/worker/worker-avatar-400x400.jpg') }}';">
                            </div>
                            <div class="flex gap-2">
                                <button 
                                    x-show="photoPreview"
                                    type="button"
                                    @click="clearPhotoPreview()"
                                    class="cw-button-secondary">
                                    {{ __('worker_profile.editor.cancel') }}
                                </button>
                                <button 
                                    x-show="!photoPreview && hasPhoto"
                                    type="button"
                                    @click="if (confirm(@js(__('worker_profile.editor.confirm_delete_photo')))) { document.getElementById('deletePhotoForm').submit(); }"
                                    class="cw-cv-remove-button">
                                    {{ __('worker_profile.editor.delete_photo') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="cw-cv-footer-actions">
                    <button type="submit" class="cw-button-primary">{{ __('worker_profile.editor.save_profile') }}</button>
                    <a href="{{ route('worker.profile.preview') }}" class="cw-button-secondary">{{ __('worker_profile.editor.preview_cv') }}</a>
                    <a href="{{ route('worker.settings.edit') }}" class="cw-button-secondary">{{ __('worker_profile.editor.go_to_settings') }}</a>
                </div>
            </form>
        </div>
    </section>

        <!-- Delete Photo Form -->
        <form id="deletePhotoForm" method="POST" action="{{ route('worker.profile.photo.delete') }}" style="display: none;">
            @csrf
            @method('DELETE')
        </form>

    <script>
        function cvBuilder(initialSkills, initialDesiredRoles, initialLanguages, initialExperiences, initialEducations, initialCertifications, initialReferences) {
            return {
                skills: Array.isArray(initialSkills) ? initialSkills : [],
                skillInput: '',
                preservedDesiredRoles: Array.isArray(initialDesiredRoles) ? initialDesiredRoles : [],
                languages: Array.isArray(initialLanguages) && initialLanguages.length ? initialLanguages : [{ language: '', level: '' }],
                experiences: Array.isArray(initialExperiences) && initialExperiences.length ? initialExperiences : [{ job_title: '', company_name: '', country: '', city: '', start_date: '', end_date: '', is_current: false, description: '' }],
                educations: Array.isArray(initialEducations) && initialEducations.length ? initialEducations : [{ institution: '', degree: '', field_of_study: '', country: '', city: '', start_date: '', end_date: '', description: '' }],
                certifications: Array.isArray(initialCertifications) && initialCertifications.length ? initialCertifications : [{ name: '', issuer: '', issued_on: '', expires_on: '', credential_id: '', credential_url: '' }],
                references: Array.isArray(initialReferences) && initialReferences.length ? initialReferences : [{ full_name: '', position: '', company: '', contact_email: '', contact_phone: '', notes: '' }],

                addSkill() {
                    const value = (this.skillInput || '').trim();
                    if (!value) return;
                    const lower = value.toLowerCase();
                    const duplicate = this.skills.some((skill) => (skill || '').toLowerCase() === lower);
                    if (!duplicate) this.skills.push(value);
                    this.skillInput = '';
                },

                removeSkill(index) {
                    this.skills.splice(index, 1);
                },

                addLanguage() {
                    this.languages.push({ language: '', level: '' });
                },

                removeLanguage(index) {
                    this.languages.splice(index, 1);
                },

                addExperience() {
                    this.experiences.push({ job_title: '', company_name: '', country: '', city: '', start_date: '', end_date: '', is_current: false, description: '' });
                },

                removeExperience(index) {
                    this.experiences.splice(index, 1);
                },

                addEducation() {
                    this.educations.push({ institution: '', degree: '', field_of_study: '', country: '', city: '', start_date: '', end_date: '', description: '' });
                },

                removeEducation(index) {
                    this.educations.splice(index, 1);
                },

                addCertification() {
                    this.certifications.push({ name: '', issuer: '', issued_on: '', expires_on: '', credential_id: '', credential_url: '' });
                },

                removeCertification(index) {
                    this.certifications.splice(index, 1);
                },

                addReference() {
                    this.references.push({ full_name: '', position: '', company: '', contact_email: '', contact_phone: '', notes: '' });
                },

                removeReference(index) {
                    this.references.splice(index, 1);
                },
            };
        }
    </script>
</x-app-layout>
