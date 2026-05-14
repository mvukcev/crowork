<x-app-layout>
    <x-slot name="title">Worker Profile</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-4xl">
            <p class="cw-kicker mb-2">Worker profile</p>
            <h1 class="cw-display text-4xl md:text-6xl mb-4">Complete your profile.</h1>

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

            <form method="POST" action="{{ route('worker.profile.update') }}" enctype="multipart/form-data" class="cw-surface p-6 space-y-4" x-data="skillsManager(@js($initialSkills ?? []))">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="first_name">First name</label>
                        <input id="first_name" name="first_name" class="cw-field" value="{{ old('first_name', $profile->first_name) }}" required>
                    </div>
                    <div>
                        <label class="cw-label" for="last_name">Last name</label>
                        <input id="last_name" name="last_name" class="cw-field" value="{{ old('last_name', $profile->last_name) }}" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="nationality_country_code">Nationality</label>
                        <input id="nationality_country_code" name="nationality_country_code" class="cw-field" value="{{ old('nationality_country_code', $profile->nationality_country_code) }}">
                    </div>
                    <div>
                        <label class="cw-label" for="birth_year">Birth year</label>
                        <input id="birth_year" name="birth_year" type="number" class="cw-field" value="{{ old('birth_year', $profile->birth_year) }}">
                    </div>
                </div>

                <div>
                    <label class="cw-label" for="education_summary">Education summary</label>
                    <textarea id="education_summary" name="education_summary" rows="4" class="cw-field">{{ old('education_summary', $profile->education_summary) }}</textarea>
                </div>

                <div>
                    <label class="cw-label" for="work_experience">Work experience</label>
                    <textarea id="work_experience" name="work_experience" rows="4" class="cw-field">{{ old('work_experience', $profile->work_experience) }}</textarea>
                </div>

                <div>
                    <label class="cw-label">Skills</label>
                    <div class="flex gap-2 mb-2">
                        <input x-model="skillInput" class="cw-field" placeholder="Add a skill" @keydown.enter.prevent="addSkill()">
                        <button type="button" class="cw-button-secondary" @click="addSkill()">Add</button>
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

                <div class="flex gap-2">
                    <button type="submit" class="cw-button-primary">Save profile</button>
                    <a href="{{ route('worker.settings.edit') }}" class="cw-button-secondary">Go to settings</a>
                </div>
            </form>
        </div>
    </section>

    <script>const normalizeSkills = (value) => {
                if (Array.isArray(value)) {
                    return value
                        .map((item) => (item ?? '').toString().trim())
                        .filter(Boolean);
                }

                if (typeof value === 'string') {
                    const trimmed = value.trim();
                    if (!trimmed) return [];

                    try {
                        const decoded = JSON.parse(trimmed);
                        if (Array.isArray(decoded)) {
                            return decoded
                                .map((item) => (item ?? '').toString().trim())
                                .filter(Boolean);
                        }
                    } catch (_) {
                        // Fallback to comma/newline parsing
                    }

                    return trimmed
                        .split(/[\n,]+/)
                        .map((item) => item.trim())
                        .filter(Boolean);
                }

                return [];
            };

            return {
                skills: [...new Set(normalizeSkills(initialSkills))
            return {
                skills: Array.isArray(initialSkills) ? initialSkills : [],
                skillInput: '',
                addSkill() {
                    const value = (this.skillInput || '').trim();
                    if (!value) return;
                    if (!this.skills.includes(value)) this.skills.push(value);
                    this.skillInput = '';
                },
                removeSkill(index) {
                    this.skills.splice(index, 1);
                }
            }
        }
    </script>
</x-app-layout>
