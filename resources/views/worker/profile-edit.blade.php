<x-app-layout>
    <x-slot name="title">Radnički profil</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-5xl">
            <div class="cw-cv-header">
                <p class="cw-kicker">Radnički profil</p>
                <h1 class="cw-display text-4xl md:text-6xl">Kreiraj svoj standardizirani životopis.</h1>
                <p class="text-slate-600">Popuni svoj profil jednom i brže se prijavljuj na buduće poslove.</p>
            </div>

            <div class="cw-surface p-4 mb-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-slate-700">Kompletnost profila</span>
                    <span class="text-sm font-semibold text-slate-900">{{ $completeness }}%</span>
                </div>
                <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-full bg-emerald-500" style="width: {{ $completeness }}%"></div>
                </div>
                <p class="mt-2 text-sm font-medium text-slate-700">{{ $completenessStateLabel ?? 'Status profila' }}</p>
                <p class="mt-1 text-sm text-slate-600">{{ $completenessHelperText ?? '' }}</p>
                @if(count($missingChecklist) > 0)
                    <div class="mt-3">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Nedostaju polja</p>
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

                <template x-for="(role, index) in preservedDesiredRoles" :key="`desired-role-preserved-${index}`">
                    <input type="hidden" name="desired_roles[]" :value="role">
                </template>

                <section class="cw-cv-section">
                    <div class="cw-cv-section-head">
                        <h2 class="cw-cv-section-title">Osobni podaci</h2>
                    </div>

                    <div class="cv-grid">
                        <div>
                            <label class="cw-label" for="first_name">Ime</label>
                            <input id="first_name" name="first_name" class="cw-field" value="{{ old('first_name', $profile->first_name) }}" required>
                        </div>
                        <div>
                            <label class="cw-label" for="last_name">Prezime</label>
                            <input id="last_name" name="last_name" class="cw-field" value="{{ old('last_name', $profile->last_name) }}" required>
                        </div>
                        <div>
                            <label class="cw-label" for="nationality_country_code">Nacionalnost</label>
                            <input id="nationality_country_code" name="nationality_country_code" class="cw-field" value="{{ old('nationality_country_code', $profile->nationality_country_code) }}">
                        </div>
                        <div>
                            <label class="cw-label" for="birth_year">Godina rođenja</label>
                            <input id="birth_year" name="birth_year" type="number" class="cw-field" value="{{ old('birth_year', $profile->birth_year) }}">
                        </div>
                        <div>
                            <label class="cw-label" for="current_country">Trenutna država</label>
                            <input id="current_country" name="current_country" class="cw-field" value="{{ old('current_country', $profile->current_country) }}">
                        </div>
                        <div>
                            <label class="cw-label" for="current_city">Trenutni grad</label>
                            <input id="current_city" name="current_city" class="cw-field" value="{{ old('current_city', $profile->current_city) }}">
                        </div>
                        <div>
                            <label class="cw-label" for="desired_city">Željeni grad/lokacija u Hrvatskoj</label>
                            <input id="desired_city" name="desired_city" class="cw-field" value="{{ old('desired_city', $profile->desired_city) }}">
                        </div>
                        <div>
                            <label class="cw-label" for="availability_date">Dostupnost / početni datum</label>
                            <input id="availability_date" name="availability_date" type="date" class="cw-field" value="{{ old('availability_date', optional($profile->availability_date)->toDateString()) }}">
                        </div>
                    </div>
                </section>

                <section class="cw-cv-section">
                    <div class="cw-cv-section-head">
                        <h2 class="cw-cv-section-title">Sažetak i preferencije</h2>
                    </div>

                    <div class="cv-grid">
                        <div class="cv-field--full">
                            <label class="cw-label" for="professional_summary">Kratki stručni sažetak</label>
                            <textarea id="professional_summary" name="professional_summary" rows="4" class="cw-field cw-cv-textarea">{{ old('professional_summary', $profile->professional_summary) }}</textarea>
                        </div>
                        <div>
                            <label class="cw-label" for="salary_expectation">Očekivana plaća (EUR / mjesec)</label>
                            <input id="salary_expectation" name="salary_expectation" type="number" min="0" class="cw-field" value="{{ old('salary_expectation', $profile->salary_expectation) }}">
                        </div>
                        <div>
                            <label class="cw-label" for="visa_work_permit_status">Status vize/radne dozvole</label>
                            <input id="visa_work_permit_status" name="visa_work_permit_status" class="cw-field" value="{{ old('visa_work_permit_status', $profile->visa_work_permit_status) }}">
                        </div>
                        <div>
                            <label class="cw-label" for="profile_visibility">Postavka privatnosti / vidljivosti</label>
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
                                Trebam podršku za smještaj
                            </label>
                        </div>
                    </div>
                </section>

                <section class="cw-cv-section">
                    <div class="cw-cv-section-head">
                        <h2 class="cw-cv-section-title">Jezici</h2>
                        <button type="button" class="cw-button-secondary cw-cv-section-action" @click="addLanguage()">Dodaj jezik</button>
                    </div>

                    <div class="cw-cv-card-list">
                        <template x-for="(row, idx) in languages" :key="`language-${idx}`">
                            <div class="cw-cv-card">
                                <div class="cw-cv-card-head">
                                    <p class="cw-cv-card-title" x-text="`Jezik #${idx + 1}`"></p>
                                    <button type="button" class="cw-cv-remove-button" @click="removeLanguage(idx)">Ukloni</button>
                                </div>

                                <div class="cv-grid">
                                    <div>
                                        <label class="cw-label">Jezik</label>
                                        <input class="cw-field" placeholder="Npr. English" x-model="row.language" :name="`languages[${idx}][language]`">
                                    </div>
                                    <div>
                                        <label class="cw-label">Razina</label>
                                        <input class="cw-field" placeholder="A2, B1, B2, C1" x-model="row.level" :name="`languages[${idx}][level]`">
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </section>

                <section class="cw-cv-section">
                    <div class="cw-cv-section-head">
                        <h2 class="cw-cv-section-title">Obrazovanje</h2>
                        <button type="button" class="cw-button-secondary cw-cv-section-action" @click="addEducation()">Dodaj obrazovanje</button>
                    </div>

                    <div class="cw-cv-card-list">
                    <template x-for="(education, idx) in educations" :key="`education-${idx}`">
                        <div class="cw-cv-card">
                            <div class="cw-cv-card-head">
                                <p class="cw-cv-card-title" x-text="`Obrazovanje #${idx + 1}`"></p>
                                <button type="button" class="cw-cv-remove-button" @click="removeEducation(idx)">Ukloni</button>
                            </div>

                            <div class="cv-grid">
                                <div>
                                    <label class="cw-label">Ustanova</label>
                                    <input class="cw-field" placeholder="Ustanova" x-model="education.institution" :name="`educations[${idx}][institution]`">
                                </div>
                                <div>
                                    <label class="cw-label">Diploma / razina</label>
                                    <input class="cw-field" placeholder="Diploma / razina" x-model="education.degree" :name="`educations[${idx}][degree]`">
                                </div>
                                <div>
                                    <label class="cw-label">Smjer</label>
                                    <input class="cw-field" placeholder="Smjer" x-model="education.field_of_study" :name="`educations[${idx}][field_of_study]`">
                                </div>
                                <div>
                                    <label class="cw-label">Grad</label>
                                    <input class="cw-field" placeholder="Grad" x-model="education.city" :name="`educations[${idx}][city]`">
                                </div>
                                <div>
                                    <label class="cw-label">Država</label>
                                    <input class="cw-field" placeholder="Država" x-model="education.country" :name="`educations[${idx}][country]`">
                                </div>
                                <div>
                                    <label class="cw-label">Početak</label>
                                    <input type="date" class="cw-field" x-model="education.start_date" :name="`educations[${idx}][start_date]`">
                                </div>
                                <div>
                                    <label class="cw-label">Završetak</label>
                                    <input type="date" class="cw-field" x-model="education.end_date" :name="`educations[${idx}][end_date]`">
                                </div>
                                <div class="cv-field--full">
                                    <label class="cw-label">Opis</label>
                                    <textarea class="cw-field cw-cv-textarea" rows="3" placeholder="Opis" x-model="education.description" :name="`educations[${idx}][description]`"></textarea>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <section class="cw-cv-section">
                    <div class="cw-cv-section-head">
                        <h2 class="cw-cv-section-title">Radno iskustvo</h2>
                        <button type="button" class="cw-button-secondary cw-cv-section-action" @click="addExperience()">Dodaj iskustvo</button>
                    </div>

                    <div class="cw-cv-card-list">
                    <template x-for="(experience, idx) in experiences" :key="`experience-${idx}`">
                        <div class="cw-cv-card">
                            <div class="cw-cv-card-head">
                                <p class="cw-cv-card-title" x-text="`Iskustvo #${idx + 1}`"></p>
                                <button type="button" class="cw-cv-remove-button" @click="removeExperience(idx)">Ukloni</button>
                            </div>

                            <div class="cv-grid">
                                <div>
                                    <label class="cw-label">Pozicija</label>
                                    <input class="cw-field" placeholder="Pozicija" x-model="experience.job_title" :name="`experiences[${idx}][job_title]`">
                                </div>
                                <div>
                                    <label class="cw-label">Poslodavac</label>
                                    <input class="cw-field" placeholder="Poslodavac" x-model="experience.company_name" :name="`experiences[${idx}][company_name]`">
                                </div>
                                <div>
                                    <label class="cw-label">Grad</label>
                                    <input class="cw-field" placeholder="Grad" x-model="experience.city" :name="`experiences[${idx}][city]`">
                                </div>
                                <div>
                                    <label class="cw-label">Država</label>
                                    <input class="cw-field" placeholder="Država" x-model="experience.country" :name="`experiences[${idx}][country]`">
                                </div>
                                <div>
                                    <label class="cw-label">Početak</label>
                                    <input type="date" class="cw-field" x-model="experience.start_date" :name="`experiences[${idx}][start_date]`">
                                </div>
                                <div>
                                    <label class="cw-label">Završetak</label>
                                    <input type="date" class="cw-field" :disabled="experience.is_current" x-model="experience.end_date" :name="`experiences[${idx}][end_date]`">
                                </div>
                                <div class="cv-field--full">
                                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                        <input type="checkbox" value="1" class="rounded border-slate-300" x-model="experience.is_current" :name="`experiences[${idx}][is_current]`">
                                        Trenutno radim na ovoj poziciji
                                    </label>
                                </div>
                                <div class="cv-field--full">
                                    <label class="cw-label">Opis</label>
                                    <textarea class="cw-field cw-cv-textarea" rows="3" placeholder="Opis zadataka i postignuća" x-model="experience.description" :name="`experiences[${idx}][description]`"></textarea>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <section class="cw-cv-section">
                    <div class="cw-cv-section-head">
                        <h2 class="cw-cv-section-title">Certifikati</h2>
                        <button type="button" class="cw-button-secondary cw-cv-section-action" @click="addCertification()">Dodaj certifikat</button>
                    </div>

                    <div class="cw-cv-card-list">
                    <template x-for="(certification, idx) in certifications" :key="`certification-${idx}`">
                        <div class="cw-cv-card">
                            <div class="cw-cv-card-head">
                                <p class="cw-cv-card-title" x-text="`Certifikat #${idx + 1}`"></p>
                                <button type="button" class="cw-cv-remove-button" @click="removeCertification(idx)">Ukloni</button>
                            </div>

                            <div class="cv-grid">
                                <div>
                                    <label class="cw-label">Naziv certifikata</label>
                                    <input class="cw-field" placeholder="Naziv certifikata" x-model="certification.name" :name="`certifications_list[${idx}][name]`">
                                </div>
                                <div>
                                    <label class="cw-label">Izdavatelj</label>
                                    <input class="cw-field" placeholder="Izdavatelj" x-model="certification.issuer" :name="`certifications_list[${idx}][issuer]`">
                                </div>
                                <div>
                                    <label class="cw-label">Datum izdavanja</label>
                                    <input type="date" class="cw-field" x-model="certification.issued_on" :name="`certifications_list[${idx}][issued_on]`">
                                </div>
                                <div>
                                    <label class="cw-label">Datum isteka</label>
                                    <input type="date" class="cw-field" x-model="certification.expires_on" :name="`certifications_list[${idx}][expires_on]`">
                                </div>
                                <div>
                                    <label class="cw-label">ID vjerodajnice</label>
                                    <input class="cw-field" placeholder="ID vjerodajnice" x-model="certification.credential_id" :name="`certifications_list[${idx}][credential_id]`">
                                </div>
                                <div>
                                    <label class="cw-label">Link na vjerodajnicu</label>
                                    <input class="cw-field" placeholder="https://..." x-model="certification.credential_url" :name="`certifications_list[${idx}][credential_url]`">
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <section class="cw-cv-section">
                    <div class="cw-cv-section-head">
                        <h2 class="cw-cv-section-title">Vještine</h2>
                    </div>

                    <div class="cv-grid">
                        <div class="cv-field--full">
                            <label class="cw-label">Vještine</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <input x-model="skillInput" class="cw-field" placeholder="Dodaj vještinu" @keydown.enter.prevent="addSkill()">
                                <button type="button" class="cw-button-secondary w-full" @click="addSkill()">Dodaj vještinu</button>
                            </div>
                            <div class="cw-cv-chip-list">
                                <template x-for="(skill, index) in skills" :key="index">
                                    <span class="cw-cv-chip">
                                        <span x-text="skill"></span>
                                        <button type="button" class="cw-cv-chip-remove" @click="removeSkill(index)">Ukloni</button>
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
                        <h2 class="cw-cv-section-title">Reference</h2>
                        <button type="button" class="cw-button-secondary cw-cv-section-action" @click="addReference()">Dodaj referencu</button>
                    </div>

                    <div class="cw-cv-card-list">
                    <template x-for="(reference, idx) in references" :key="`reference-${idx}`">
                        <div class="cw-cv-card">
                            <div class="cw-cv-card-head">
                                <p class="cw-cv-card-title" x-text="`Referenca #${idx + 1}`"></p>
                                <button type="button" class="cw-cv-remove-button" @click="removeReference(idx)">Ukloni</button>
                            </div>

                            <div class="cv-grid">
                                <div>
                                    <label class="cw-label">Ime i prezime</label>
                                    <input class="cw-field" placeholder="Ime i prezime" x-model="reference.full_name" :name="`references_list[${idx}][full_name]`">
                                </div>
                                <div>
                                    <label class="cw-label">Pozicija</label>
                                    <input class="cw-field" placeholder="Pozicija" x-model="reference.position" :name="`references_list[${idx}][position]`">
                                </div>
                                <div>
                                    <label class="cw-label">Tvrtka</label>
                                    <input class="cw-field" placeholder="Tvrtka" x-model="reference.company" :name="`references_list[${idx}][company]`">
                                </div>
                                <div>
                                    <label class="cw-label">Email</label>
                                    <input class="cw-field" placeholder="Email" x-model="reference.contact_email" :name="`references_list[${idx}][contact_email]`">
                                </div>
                                <div>
                                    <label class="cw-label">Telefon</label>
                                    <input class="cw-field" placeholder="Telefon" x-model="reference.contact_phone" :name="`references_list[${idx}][contact_phone]`">
                                </div>
                                <div class="cv-field--full">
                                    <label class="cw-label">Napomena</label>
                                    <textarea class="cw-field cw-cv-textarea" rows="3" placeholder="Napomena" x-model="reference.notes" :name="`references_list[${idx}][notes]`"></textarea>
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
                        <h2 class="cw-cv-section-title">Foto profila</h2>
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
                            <p class="mt-2 text-sm font-medium text-slate-900">Prevuci fotografiju ovdje ili klikni za odabir</p>
                            <p class="text-xs text-slate-500 mt-1">PNG, JPG, WebP do 2MB</p>
                        </div>
                    </label>
                    
                    <!-- Photo Preview -->
                    <div x-show="photoPreview || hasPhoto" class="mt-4">
                        <p class="text-sm font-semibold text-slate-700 mb-3">Pregled:</p>
                        <div class="flex items-end gap-4">
                            <div class="relative">
                                <img 
                                    x-show="photoPreview" 
                                    :src="photoPreview" 
                                    alt="Pregled foto" 
                                    class="h-20 w-20 rounded-full object-cover border-2 border-slate-200">
                                <img 
                                    x-show="!photoPreview && hasPhoto"
                                    src="{{ $profile->photoUrl() ?? '' }}" 
                                    alt="Foto profila" 
                                    class="h-20 w-20 rounded-full object-cover border-2 border-slate-200">
                            </div>
                            <div class="flex gap-2">
                                <button 
                                    x-show="photoPreview"
                                    type="button"
                                    @click="clearPhotoPreview()"
                                    class="cw-button-secondary">
                                    Odustani
                                </button>
                                <button 
                                    x-show="!photoPreview && hasPhoto"
                                    type="button"
                                    @click="if (confirm('Obriši trenutnu fotografiju?')) { document.getElementById('deletePhotoForm').submit(); }"
                                    class="cw-cv-remove-button">
                                    Obriši foto
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="cw-cv-footer-actions">
                    <button type="submit" class="cw-button-primary">Spremi profil</button>
                    <a href="{{ route('worker.profile.preview') }}" class="cw-button-secondary">Pregled CV</a>
                    <a href="{{ route('worker.settings.edit') }}" class="cw-button-secondary">Idi na postavke</a>
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
                    if (!this.skills.includes(value)) this.skills.push(value);
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
