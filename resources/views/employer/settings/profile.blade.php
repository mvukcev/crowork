@extends('layouts.app')

@section('content')
<div class="cw-surface min-h-screen">
    <!-- Header -->
    <div class="cw-surface-header border-b border-neutral-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('employer.dashboard') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                    ← Dashboard
                </a>
                <h1 class="cw-heading-1">Company Profile Settings</h1>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                @if($errors->any())
                    <div class="cw-surface border border-red-200 bg-red-50 rounded-lg p-4 mb-6">
                        <p class="font-medium text-red-900 mb-2">Please fix the following errors:</p>
                        <ul class="list-disc list-inside space-y-1 text-sm text-red-800">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="cw-surface border border-emerald-200 bg-emerald-50 rounded-lg p-4 mb-6">
                        <p class="text-emerald-900 font-medium">{{ session('success') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('employer.settings.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="border border-neutral-200 rounded-xl p-5 bg-white/70 backdrop-blur-sm">
                        <h3 class="cw-heading-3 mb-4">Branding</h3>

                        <div>
                            <label class="cw-label">Company Display Name</label>
                            <input type="text"
                                   name="company_display_name"
                                   class="cw-field"
                                   value="{{ old('company_display_name', $employer->company_display_name) }}"
                                   placeholder="How your brand appears publicly">
                            <p class="text-xs text-neutral-600 mt-1">Shown on job cards, company profile, and employer headers.</p>
                        </div>

                        <div class="mt-5">
                            <label class="cw-label">Company Logo</label>
                            <input type="file" name="logo" accept="image/jpeg,image/png" class="cw-field" data-logo-input>
                            <p class="text-xs text-neutral-600 mt-1">JPG or PNG, square format only. Stored as optimized 1024x1024.</p>

                            <div class="mt-3 flex items-center gap-4">
                                <div class="h-20 w-20 rounded-full border border-slate-200 overflow-hidden bg-slate-50 shadow-sm" data-logo-preview>
                                    @if($employer->logo_path)
                                        <img src="{{ asset('storage/' . $employer->logo_path) }}" alt="{{ $employer->company_name }} logo" class="h-full w-full object-cover" data-logo-preview-image>
                                    @else
                                        <div class="h-full w-full grid place-items-center text-sm font-semibold text-slate-500" data-logo-preview-fallback>
                                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($employer->company_display_name ?: $employer->company_name, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="text-xs text-neutral-600">
                                    <p>Preview uses circular crop to match marketplace cards.</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                            <div>
                                <label class="cw-label">Contact Email</label>
                                <input type="email"
                                       name="contact_email"
                                       class="cw-field"
                                       value="{{ old('contact_email', $employer->contact_email) }}"
                                       placeholder="contact@company.com">
                            </div>

                            <div>
                                <label class="cw-label">Contact Phone</label>
                                <input type="text"
                                       name="contact_phone"
                                       class="cw-field"
                                       value="{{ old('contact_phone', $employer->contact_phone) }}"
                                       placeholder="+385 ...">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="cw-label">Company Address</label>
                            <input type="text"
                                   name="company_address"
                                   class="cw-field"
                                   value="{{ old('company_address', $employer->company_address) }}"
                                   placeholder="Street, city, postal code">
                        </div>
                    </div>

                    <div>
                        <label class="cw-label">Company Name</label>
                        <input type="text" 
                               name="company_name" 
                               class="cw-field"
                               value="{{ old('company_name', $employer->company_name) }}"
                               required>
                        <p class="text-xs text-neutral-600 mt-1">The name of your company as it appears to job seekers</p>
                    </div>

                    <!-- City -->
                    <div>
                        <label class="cw-label">City</label>
                        <input type="text" 
                               name="city" 
                               class="cw-field"
                               value="{{ old('city', $employer->city) }}"
                               placeholder="e.g., London, Berlin, Paris">
                        <p class="text-xs text-neutral-600 mt-1">Headquarters or main office location</p>
                    </div>

                    <div>
                        <label class="cw-label">Country</label>
                        <input type="text"
                               name="country"
                               class="cw-field"
                               value="{{ old('country', $employer->country) }}"
                               placeholder="e.g., Croatia, Germany, Italy">
                        <p class="text-xs text-neutral-600 mt-1">Country shown on your public company profile</p>
                    </div>

                    <!-- Industry -->
                    <div>
                        <label class="cw-label">Industry</label>
                        <select name="industry" class="cw-field">
                            <option value="">Select an industry</option>
                            <option value="Technology" @selected(old('industry', $employer->industry) === 'Technology')>Technology</option>
                            <option value="Healthcare" @selected(old('industry', $employer->industry) === 'Healthcare')>Healthcare</option>
                            <option value="Finance" @selected(old('industry', $employer->industry) === 'Finance')>Finance</option>
                            <option value="Retail" @selected(old('industry', $employer->industry) === 'Retail')>Retail</option>
                            <option value="Manufacturing" @selected(old('industry', $employer->industry) === 'Manufacturing')>Manufacturing</option>
                            <option value="Education" @selected(old('industry', $employer->industry) === 'Education')>Education</option>
                            <option value="Hospitality" @selected(old('industry', $employer->industry) === 'Hospitality')>Hospitality</option>
                            <option value="Construction" @selected(old('industry', $employer->industry) === 'Construction')>Construction</option>
                            <option value="Transportation" @selected(old('industry', $employer->industry) === 'Transportation')>Transportation</option>
                            <option value="Agriculture" @selected(old('industry', $employer->industry) === 'Agriculture')>Agriculture</option>
                            <option value="Other" @selected(old('industry', $employer->industry) === 'Other')>Other</option>
                        </select>
                        <p class="text-xs text-neutral-600 mt-1">What sector does your company operate in?</p>
                    </div>

                    <!-- Website -->
                    <div>
                        <label class="cw-label">Website</label>
                        <input type="url" 
                               name="website" 
                               class="cw-field"
                               value="{{ old('website', $employer->website) }}"
                               placeholder="https://example.com">
                        <p class="text-xs text-neutral-600 mt-1">Your company's website URL</p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="cw-label">Company Description</label>
                        <textarea name="description" 
                                  class="cw-field h-32 resize-none"
                                  placeholder="Tell job seekers about your company, mission, culture, and what makes you unique...">{{ old('description', $employer->description) }}</textarea>
                        <p class="text-xs text-neutral-600 mt-1">Maximum 2000 characters</p>
                    </div>

                    <!-- Support Flags -->
                    <div class="border-t border-neutral-200 pt-6">
                        <h3 class="cw-heading-3 mb-4">Support & Benefits</h3>
                        
                        <div class="space-y-4">
                            <label class="flex items-start gap-3">
                                <input type="checkbox" 
                                       name="relocation_support" 
                                       value="1"
                                       class="mt-1"
                                       @checked(old('relocation_support', $employer->relocation_support))>
                                <div>
                                    <p class="font-medium text-neutral-900">Relocation Support</p>
                                    <p class="text-sm text-neutral-600">Your company offers support for candidates who need to relocate</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-3">
                                <input type="checkbox" 
                                       name="accommodation_support" 
                                       value="1"
                                       class="mt-1"
                                       @checked(old('accommodation_support', $employer->accommodation_support))>
                                <div>
                                    <p class="font-medium text-neutral-900">Accommodation Support</p>
                                    <p class="text-sm text-neutral-600">Your company can provide accommodation for employees</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="border-t border-neutral-200 pt-6 flex gap-3">
                        <button type="submit" class="cw-button-primary">
                            Save Changes
                        </button>
                        <a href="{{ route('employer.dashboard') }}" class="cw-button-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Profile Completeness -->
                <div class="cw-surface border border-neutral-200 rounded-lg p-6 mb-6">
                    <h3 class="cw-heading-3 mb-4">Profile Completeness</h3>
                    
                    <div class="relative w-32 h-32 mx-auto mb-4">
                        <svg class="w-full h-full" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="45" fill="none" stroke="#e5e7eb" stroke-width="8"/>
                            <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="8" 
                                class="text-emerald-600" 
                                stroke-dasharray="{{ $readiness * 2.83 }},283"
                                stroke-linecap="round"
                                style="transform: rotate(-90deg); transform-origin: 50% 50%;"
                            />
                            <text x="50" y="55" text-anchor="middle" font-size="20" font-weight="bold" fill="currentColor">
                                {{ $readiness }}%
                            </text>
                        </svg>
                    </div>

                    <p class="text-sm text-center text-neutral-600 mb-4">
                        @if($readiness === 100)
                            Your profile is complete!
                        @elseif($readiness >= 75)
                            Almost there! Just a few more fields to complete.
                        @elseif($readiness >= 50)
                            You're halfway there. Complete your profile to attract more candidates.
                        @else
                            Let's get started! Fill in your company details.
                        @endif
                    </p>

                    @if($missing)
                        <div class="bg-neutral-50 rounded p-3">
                            <p class="text-xs font-medium text-neutral-700 mb-2">Missing fields:</p>
                            <ul class="space-y-1 text-xs text-neutral-600">
                                @foreach($missing as $field => $label)
                                    <li>• {{ $label }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <!-- Info Card -->
                <div class="cw-surface border border-neutral-200 rounded-lg p-6">
                    <h3 class="cw-heading-3 mb-3">Why Complete Your Profile?</h3>
                    <ul class="space-y-2 text-sm text-neutral-700">
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Builds trust with job seekers</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Improves job listing visibility</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Attracts quality candidates</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Shows candidate support & culture</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fileInput = document.querySelector('[data-logo-input]');
            const preview = document.querySelector('[data-logo-preview]');

            if (!fileInput || !preview) {
                return;
            }

            fileInput.addEventListener('change', function (event) {
                const file = event.target.files?.[0];
                if (!file) {
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    const existingImage = preview.querySelector('[data-logo-preview-image]');
                    const fallback = preview.querySelector('[data-logo-preview-fallback]');

                    if (existingImage) {
                        existingImage.remove();
                    }
                    if (fallback) {
                        fallback.remove();
                    }

                    const img = document.createElement('img');
                    img.src = String(e.target?.result || '');
                    img.alt = 'Logo preview';
                    img.className = 'h-full w-full object-cover';
                    img.setAttribute('data-logo-preview-image', 'true');
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        });
    </script>
@endpush
@endsection
