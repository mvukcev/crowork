@extends('layouts.app')

@section('content')

<div class="py-8 md:py-12 bg-background min-h-screen">
    <div class="container-base">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-display-md font-semibold text-text-primary mb-2">Worker Profile</h1>
            <p class="text-body text-text-secondary">
                Create your standardized CV for job applications. No PDFs needed – all information is stored digitally.
            </p>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-success-light border border-success-border rounded-lg flex items-start gap-3" role="alert">
                <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-body-sm text-success-text font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Validation Errors -->
        @if($errors->any())
            <div class="mb-6 p-4 bg-danger-light border border-danger-border rounded-lg" role="alert">
                <div class="flex items-start gap-3 mb-2">
                    <svg class="w-5 h-5 text-danger flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-body-sm text-danger-text font-semibold mb-2">Please fix the following errors:</p>
                        <ul class="list-disc list-inside text-body-sm text-danger-text space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('worker.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content - 2 columns -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Personal Information -->
                    <x-card>
                        <x-section-header 
                            title="Personal Information"
                            subtitle="Basic details about you"
                            class="mb-6"
                        />

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-input
                                name="first_name"
                                label="First Name"
                                :value="old('first_name', $profile->first_name)"
                                required
                                placeholder="e.g. John"
                                hint="Your legal first name"
                            />

                            <x-input
                                name="last_name"
                                label="Last Name"
                                :value="old('last_name', $profile->last_name)"
                                required
                                placeholder="e.g. Smith"
                                hint="Your legal last name"
                            />

                            <x-input
                                name="nationality_country_code"
                                label="Nationality"
                                :value="old('nationality_country_code', $profile->nationality_country_code)"
                                required
                                placeholder="e.g. HR, DE, US"
                                hint="2-letter ISO country code"
                                maxlength="2"
                                pattern="[A-Z]{2}"
                                class="uppercase"
                            />

                            <x-input
                                name="birth_year"
                                label="Birth Year"
                                type="number"
                                :value="old('birth_year', $profile->birth_year)"
                                required
                                placeholder="e.g. 1990"
                                :min="1940"
                                :max="now()->year - 14"
                                hint="Must be at least 14 years old"
                            />
                        </div>
                    </x-card>

                    <!-- Education -->
                    <x-card>
                        <x-section-header 
                            title="Education"
                            subtitle="Your educational background"
                            class="mb-6"
                        />

                        <x-textarea
                            name="education_summary"
                            label="Education Summary"
                            :value="old('education_summary', $profile->education_summary)"
                            rows="6"
                            placeholder="Example:&#10;• Bachelor of Science in Computer Science, University of Zagreb (2015-2019)&#10;• High School Diploma, Zagreb High School (2011-2015)"
                            hint="List your educational qualifications, degrees, certifications"
                        />
                    </x-card>

                    <!-- Work Experience -->
                    <x-card>
                        <x-section-header 
                            title="Work Experience"
                            subtitle="Your professional background"
                            class="mb-6"
                        />

                        <x-textarea
                            name="work_experience"
                            label="Work Experience"
                            :value="old('work_experience', $profile->work_experience)"
                            rows="8"
                            placeholder="Example:&#10;• Software Developer at ABC Company (2020-2024)&#10;  - Developed web applications using Laravel and Vue.js&#10;  - Led team of 3 developers&#10;&#10;• Junior Developer at XYZ Startup (2019-2020)&#10;  - Built REST APIs and database schemas"
                            hint="List your work history with roles, companies, and key responsibilities"
                        />
                    </x-card>

                    <!-- Skills -->
                    <x-card>
                        <x-section-header 
                            title="Skills"
                            subtitle="Your professional competencies"
                            class="mb-6"
                        />

                        <div 
                            x-data="skillsManager({{ json_encode(old('skills', $profile->skills ?? [])) }})"
                            class="space-y-4"
                        >
                            <!-- Skills Display -->
                            <div>
                                <label class="block text-body-sm font-medium text-text-primary mb-2">
                                    Your Skills <span class="text-text-tertiary">(max 30)</span>
                                </label>
                                
                                <!-- Skills Tags -->
                                <div class="flex flex-wrap gap-2 mb-3 min-h-[40px] p-3 border border-border rounded-lg bg-surface">
                                    <template x-for="(skill, index) in skills" :key="index">
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary-light text-primary text-body-sm font-medium rounded-md border border-primary-border">
                                            <span x-text="skill"></span>
                                            <button 
                                                type="button"
                                                @click="removeSkill(index)"
                                                class="text-primary hover:text-primary-hover transition-colors duration-fast"
                                                :aria-label="'Remove ' + skill"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                    <div x-show="skills.length === 0" class="text-text-tertiary text-body-sm">
                                        No skills added yet. Add your first skill below.
                                    </div>
                                </div>

                                <!-- Add Skill Input -->
                                <div class="flex gap-2">
                                    <input
                                        type="text"
                                        x-model="newSkill"
                                        @keydown.enter.prevent="addSkill()"
                                        placeholder="e.g. Laravel, JavaScript, Customer Service"
                                        maxlength="40"
                                        class="flex-1 px-3 py-2 border border-border rounded-md text-body text-text-primary placeholder-text-tertiary focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition-colors duration-normal"
                                        :disabled="skills.length >= 30"
                                    />
                                    <x-button 
                                        type="button"
                                        variant="secondary"
                                        @click="addSkill()"
                                        :disabled="skills.length >= 30"
                                    >
                                        Add
                                    </x-button>
                                </div>
                                <p class="text-caption text-text-tertiary mt-2">
                                    <span x-text="skills.length"></span> / 30 skills added. Press Enter or click Add to include a skill.
                                </p>

                                <!-- Hidden inputs for form submission -->
                                <template x-for="(skill, index) in skills" :key="'input-' + index">
                                    <input type="hidden" name="skills[]" :value="skill">
                                </template>
                            </div>

                            <!-- No-JS Fallback -->
                            <noscript>
                                <x-textarea
                                    name="skills_fallback"
                                    label="Skills (one per line)"
                                    rows="6"
                                    placeholder="Laravel&#10;JavaScript&#10;Customer Service"
                                    hint="Enter one skill per line (JavaScript disabled)"
                                />
                            </noscript>
                        </div>
                    </x-card>

                    <!-- Recommendations -->
                    <x-card>
                        <x-section-header 
                            title="Recommendations & References"
                            subtitle="Testimonials or references from previous employers"
                            class="mb-6"
                        />

                        <x-textarea
                            name="recommendations"
                            label="Recommendations"
                            :value="old('recommendations', $profile->recommendations)"
                            rows="6"
                            placeholder="Example:&#10;'Excellent team player with strong problem-solving skills' - Jane Doe, Manager at ABC Company&#10;&#10;Or list contact information for references."
                            hint="Optional: paste testimonials or list references"
                        />
                    </x-card>
                </div>

                <!-- Sidebar - 1 column -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Photo Upload -->
                    <x-card>
                        <x-section-header 
                            title="Profile Photo"
                            subtitle="Optional professional photo"
                            class="mb-6"
                        />

                        <div class="space-y-4">
                            <!-- Current Photo Preview -->
                            @if($profile->photo_path)
                                <div class="relative">
                                    <img 
                                        src="{{ Storage::url($profile->photo_path) }}" 
                                        alt="Profile photo"
                                        class="w-full aspect-square object-cover rounded-lg border border-border"
                                    />
                                    <form method="POST" action="{{ route('worker.profile.photo.delete') }}" class="mt-3">
                                        @csrf
                                        @method('DELETE')
                                        <x-button 
                                            type="submit"
                                            variant="danger"
                                            size="sm"
                                            class="w-full"
                                            onclick="return confirm('Are you sure you want to delete your photo?')"
                                        >
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete Photo
                                        </x-button>
                                    </form>
                                </div>
                            @else
                                <div class="w-full aspect-square bg-surface border-2 border-dashed border-border rounded-lg flex items-center justify-center">
                                    <div class="text-center p-4">
                                        <svg class="w-12 h-12 mx-auto text-text-tertiary mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <p class="text-body-sm text-text-secondary">No photo uploaded</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Upload Input -->
                            <div>
                                <label class="block text-body-sm font-medium text-text-primary mb-2">
                                    {{ $profile->photo_path ? 'Replace Photo' : 'Upload Photo' }}
                                </label>
                                <input
                                    type="file"
                                    name="photo"
                                    accept="image/jpeg,image/png,image/webp"
                                    class="block w-full text-body-sm text-text-secondary file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-body-sm file:font-medium file:bg-primary-light file:text-primary hover:file:bg-primary-hover hover:file:text-white file:transition-colors file:duration-normal"
                                />
                                <p class="text-caption text-text-tertiary mt-2">
                                    JPEG, PNG, or WebP. Max 2MB.
                                </p>
                            </div>
                        </div>
                    </x-card>

                    <!-- Help Card -->
                    <x-card class="bg-primary-light border-primary-border">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-primary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-body-sm font-semibold text-primary mb-2">Digital CV Benefits</p>
                                <ul class="text-caption text-text-primary space-y-1 list-disc list-inside">
                                    <li>No PDF uploads needed</li>
                                    <li>Always up-to-date</li>
                                    <li>Searchable by employers</li>
                                    <li>Standardized format</li>
                                </ul>
                            </div>
                        </div>
                    </x-card>

                    <!-- Save Button (Desktop) -->
                    <div class="hidden lg:block sticky top-24">
                        <x-button 
                            type="submit"
                            variant="primary"
                            class="w-full py-3 text-base font-semibold"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Save Profile
                        </x-button>
                    </div>
                </div>
            </div>

            <!-- Save Button (Mobile) - Fixed Bottom -->
            <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-background border-t border-border shadow-lg p-4 z-40">
                <x-button 
                    type="submit"
                    variant="primary"
                    class="w-full py-3 text-base font-semibold"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Save Profile
                </x-button>
            </div>
        </form>

        <!-- Mobile Bottom Padding -->
        <div class="lg:hidden h-20"></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Alpine.js component for skills management
    document.addEventListener('alpine:init', () => {
        Alpine.data('skillsManager', (initialSkills) => ({
            skills: initialSkills || [],
            newSkill: '',

            addSkill() {
                const skill = this.newSkill.trim();
                
                // Validation
                if (!skill) {
                    return;
                }
                
                if (skill.length > 40) {
                    alert('Skill name must be 40 characters or less');
                    return;
                }
                
                if (this.skills.length >= 30) {
                    alert('Maximum 30 skills allowed');
                    return;
                }
                
                if (this.skills.includes(skill)) {
                    alert('This skill is already added');
                    return;
                }
                
                // Add skill
                this.skills.push(skill);
                this.newSkill = '';
            },

            removeSkill(index) {
                this.skills.splice(index, 1);
            }
        }));
    });
</script>
@endpush
