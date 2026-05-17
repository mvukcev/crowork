<x-app-layout>
    <x-slot name="title">Radnički profil</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-4xl">
            <p class="cw-kicker mb-2">Radnički profil</p>
            <h1 class="cw-display text-4xl md:text-6xl mb-2">Kreiraj svoj standardizirani životopis.</h1>
            <p class="text-slate-600 mb-5">Popuni svoj profil jednom i brže se prijavljuj na buduće poslove.</p>

            <div class="cw-surface p-4 mb-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-slate-700">Kompletnost profila</span>
                    <span class="text-sm font-semibold text-slate-900">{{ $completeness }}%</span>
                </div>
                <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-full bg-emerald-500" style="width: {{ $completeness }}%"></div>
                </div>
                @if(count($missingChecklist) > 0)
                    <div class="mt-3">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Nedostaju polja</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach(array_slice($missingChecklist, 0, 8) as $item)
                                <span class="cw-chip">{{ $item }}</span>
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

            <form method="POST" action="{{ route('worker.profile.update') }}" enctype="multipart/form-data" class="cw-surface p-6 space-y-6" x-data="cvBuilder(@js($initialSkills ?? []), @js($initialDesiredRoles ?? []), @js($languageRows ?? []))" data-cw-track-submit="worker_profile_update">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="first_name">Ime</label>
                        <input id="first_name" name="first_name" class="cw-field" value="{{ old('first_name', $profile->first_name) }}" required>
                    </div>
                    <div>
                        <label class="cw-label" for="last_name">Prezime</label>
                        <input id="last_name" name="last_name" class="cw-field" value="{{ old('last_name', $profile->last_name) }}" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="nationality_country_code">Nacionalnost</label>
                        <input id="nationality_country_code" name="nationality_country_code" class="cw-field" value="{{ old('nationality_country_code', $profile->nationality_country_code) }}">
                    </div>
                    <div>
                        <label class="cw-label" for="birth_year">Godina rođenja</label>
                        <input id="birth_year" name="birth_year" type="number" class="cw-field" value="{{ old('birth_year', $profile->birth_year) }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="current_country">Trenutna država</label>
                        <input id="current_country" name="current_country" class="cw-field" value="{{ old('current_country', $profile->current_country) }}">
                    </div>
                    <div>
                        <label class="cw-label" for="current_city">Trenutni grad</label>
                        <input id="current_city" name="current_city" class="cw-field" value="{{ old('current_city', $profile->current_city) }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="desired_city">Željeni grad/lokacija u Hrvatskoj</label>
                        <input id="desired_city" name="desired_city" class="cw-field" value="{{ old('desired_city', $profile->desired_city) }}">
                    </div>
                    <div>
                        <label class="cw-label" for="availability_date">Dostupnost / početni datum</label>
                        <input id="availability_date" name="availability_date" type="date" class="cw-field" value="{{ old('availability_date', optional($profile->availability_date)->toDateString()) }}">
                    </div>
                </div>

                <div>
                    <label class="cw-label">Jezici sa razinama</label>
                    <div class="space-y-2">
                        <template x-for="(row, idx) in languages" :key="idx">
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-2">
                                <input class="cw-field md:col-span-3" placeholder="Jezik" x-model="row.language" :name="`languages[${idx}][language]`">
                                <input class="cw-field md:col-span-2" placeholder="Razina (A2, B1, B2, C1...)" x-model="row.level" :name="`languages[${idx}][level]`">
                            </div>
                        </template>
                    </div>
                    <button type="button" class="cw-button-secondary mt-2" @click="addLanguage()">Dodaj jezik</button>
                </div>

                <div>
                    <label class="cw-label" for="professional_summary">Kratka stručna sažetak</label>
                    <textarea id="professional_summary" name="professional_summary" rows="4" class="cw-field">{{ old('professional_summary', $profile->professional_summary) }}</textarea>
                </div>

                <div>
                    <label class="cw-label" for="education_summary">Sažetak obrazovanja</label>
                    <textarea id="education_summary" name="education_summary" rows="4" class="cw-field">{{ old('education_summary', $profile->education_summary) }}</textarea>
                </div>

                <div>
                    <label class="cw-label" for="work_experience">Radno iskustvo</label>
                    <textarea id="work_experience" name="work_experience" rows="4" class="cw-field">{{ old('work_experience', $profile->work_experience) }}</textarea>
                </div>

                <div>
                    <label class="cw-label" for="certifications">Certifikati</label>
                    <textarea id="certifications" name="certifications" rows="3" class="cw-field">{{ old('certifications', $profile->certifications) }}</textarea>
                </div>

                <div>
                    <label class="cw-label">Vještine</label>
                    <div class="flex gap-2 mb-2">
                        <input x-model="skillInput" class="cw-field" placeholder="Dodaj vještinu" @keydown.enter.prevent="addSkill()">
                        <button type="button" class="cw-button-secondary" @click="addSkill()">Dodaj</button>
                    </div>
                    <template x-for="(skill, index) in skills" :key="index">
                        <div class="inline-flex items-center gap-2 mr-2 mb-2 px-3 py-1 rounded-full border border-slate-200 bg-slate-50 text-sm">
                            <span x-text="skill"></span>
                            <button type="button" class="text-slate-500" @click="removeSkill(index)">x</button>
                        </div>
                    </template>
                    <template x-for="(skill, index) in skills" :key="'skill-input-' + index">
                        <input type="hidden" name="skills[]" :value="skill">
                    </template>
                </div>

                <div>
                    <label class="cw-label">Željene uloge/kategorije</label>
                    <div class="flex gap-2 mb-2">
                        <input x-model="roleInput" class="cw-field" placeholder="Dodaj željenu ulogu" @keydown.enter.prevent="addRole()">
                        <button type="button" class="cw-button-secondary" @click="addRole()">Dodaj</button>
                    </div>
                    <template x-for="(role, index) in desiredRoles" :key="index">
                        <div class="inline-flex items-center gap-2 mr-2 mb-2 px-3 py-1 rounded-full border border-slate-200 bg-slate-50 text-sm">
                            <span x-text="role"></span>
                            <button type="button" class="text-slate-500" @click="removeRole(index)">x</button>
                        </div>
                    </template>
                    <template x-for="(role, index) in desiredRoles" :key="'role-input-' + index">
                        <input type="hidden" name="desired_roles[]" :value="role">
                    </template>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="salary_expectation">Očekivana plaća (EUR / mjesec)</label>
                        <input id="salary_expectation" name="salary_expectation" type="number" min="0" class="cw-field" value="{{ old('salary_expectation', $profile->salary_expectation) }}">
                    </div>
                    <div>
                        <label class="cw-label" for="visa_work_permit_status">Status vize/radne dozvole</label>
                        <input id="visa_work_permit_status" name="visa_work_permit_status" class="cw-field" value="{{ old('visa_work_permit_status', $profile->visa_work_permit_status) }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700 mt-7">
                        <input type="checkbox" name="accommodation_needed" value="1" class="rounded border-slate-300" @checked(old('accommodation_needed', $profile->accommodation_needed))>
                        Trebam podršku za smještaj
                    </label>
                    <div>
                        <label class="cw-label" for="profile_visibility">Postavka privatnosti / vidljivosti</label>
                        <select id="profile_visibility" name="profile_visibility" class="cw-field" required>
                            @foreach(\App\Models\WorkerProfile::visibilityOptions() as $key => $label)
                                <option value="{{ $key }}" @selected(old('profile_visibility', $profile->profile_visibility) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="cw-label" for="recommendations">Preporuke / reference</label>
                    <textarea id="recommendations" name="recommendations" rows="4" class="cw-field">{{ old('recommendations', $profile->recommendations) }}</textarea>
                </div>

                <div x-data="{ 
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
                    <label class="cw-label" for="photo">Foto profila</label>
                    <label 
                        @dragover="isDragover = true" 
                        @dragleave="isDragover = false" 
                        @drop="handleDrop"
                        class="block border-2 border-dashed rounded-lg p-6 transition-colors cursor-pointer"
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
                                    class="cw-button-secondary text-amber-700 border-amber-200 bg-amber-50">
                                    Odustani
                                </button>
                                    <button 
                                        x-show="!photoPreview && hasPhoto"
                                        type="button"
                                        @click="if (confirm('Obriši trenutnu fotografiju?')) { document.getElementById('deletePhotoForm').submit(); }"
                                        class="cw-button-secondary text-red-700 border-red-200 bg-red-50">
                                        Obriši foto
                                    </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2">
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
        function cvBuilder(initialSkills, initialDesiredRoles, initialLanguages) {
            return {
                skills: Array.isArray(initialSkills) ? initialSkills : [],
                skillInput: '',
                desiredRoles: Array.isArray(initialDesiredRoles) ? initialDesiredRoles : [],
                roleInput: '',
                languages: Array.isArray(initialLanguages) && initialLanguages.length ? initialLanguages : [{ language: '', level: '' }],

                addSkill() {
                    const value = (this.skillInput || '').trim();
                    if (!value) return;
                    if (!this.skills.includes(value)) this.skills.push(value);
                    this.skillInput = '';
                },

                removeSkill(index) {
                    this.skills.splice(index, 1);
                },

                addRole() {
                    const value = (this.roleInput || '').trim();
                    if (!value) return;
                    if (!this.desiredRoles.includes(value)) this.desiredRoles.push(value);
                    this.roleInput = '';
                },

                removeRole(index) {
                    this.desiredRoles.splice(index, 1);
                },

                addLanguage() {
                    this.languages.push({ language: '', level: '' });
                },
            };
        }
    </script>
</x-app-layout>
