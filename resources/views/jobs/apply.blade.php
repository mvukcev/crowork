@extends('layouts.app')

@section('title', "Apply to {$job->title} – CroWork")

@section('content')
@php
    $rawSkills = $profile->skills;
    if (is_string($rawSkills)) {
        $profileSkills = array_values(array_filter(array_map('trim', explode(',', $rawSkills))));
    } elseif (is_array($rawSkills)) {
        $profileSkills = array_values(array_filter(array_map(static fn ($skill) => is_string($skill) ? trim($skill) : '', $rawSkills)));
    } else {
        $profileSkills = [];
    }
@endphp
<div class="min-h-screen bg-neutral-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Breadcrumb Navigation --}}
        <nav class="mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-sm text-neutral-600">
                <li><a href="{{ route('jobs.index') }}" class="hover:text-primary-600 transition-colors">Jobs</a></li>
                <li><span class="text-neutral-400">/</span></li>
                <li><a href="{{ route('jobs.show', $job) }}" class="hover:text-primary-600 transition-colors">{{ Str::limit($job->title, 40) }}</a></li>
                <li><span class="text-neutral-400">/</span></li>
                <li class="text-neutral-900 font-medium" aria-current="page">Apply</li>
            </ol>
        </nav>

        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-3xl sm:text-4xl font-semibold text-neutral-900 mb-2">
                Apply to this job
            </h1>
            <p class="text-lg text-neutral-600">
                Review your profile and submit your application
            </p>
        </div>

        @if($alreadyApplied)
            {{-- Already Applied State --}}
            <div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-8 mb-6">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <svg class="h-12 w-12 text-success-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-semibold text-neutral-900 mb-2">
                            Application submitted
                        </h2>
                        <p class="text-neutral-700 mb-4">
                            You applied to this job on <strong>{{ $existingApplication->created_at->format('F j, Y') }}</strong> at {{ $existingApplication->created_at->format('g:i A') }}.
                        </p>
                        <p class="text-neutral-600 mb-6">
                            The employer has received your application and will review your profile. If you are a good fit, they will contact you directly.
                        </p>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('jobs.show', $job) }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Back to job details
                            </a>
                            <a href="{{ route('jobs.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-neutral-700 bg-white hover:bg-neutral-50 border border-neutral-300 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                Browse more jobs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- Application Form --}}
            
            {{-- Job Summary Card --}}
            <div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-6 mb-6">
                <div class="flex items-start space-x-4">
                    @if($job->employer && $job->employer->logo_path)
                        <img src="{{ Storage::url($job->employer->logo_path) }}" 
                             alt="{{ $job->employer->company_name }}" 
                             class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                    @else
                        <div class="w-16 h-16 rounded-lg bg-neutral-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-8 h-8 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <h2 class="text-xl font-semibold text-neutral-900 mb-1">
                            {{ $job->title }}
                        </h2>
                        <p class="text-neutral-600 mb-2">
                            {{ $job->employer->company_name ?? 'Company' }}
                        </p>
                        <div class="flex flex-wrap items-center gap-3 text-sm text-neutral-600">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $job->city }}
                            </span>
                            @if($job->salary_min || $job->salary_max)
                                <span class="flex items-center font-medium text-success-700">
                                    <svg class="w-4 h-4 mr-1 text-success-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    @if($job->salary_min && $job->salary_max)
                                        €{{ number_format($job->salary_min) }} – €{{ number_format($job->salary_max) }} / month
                                    @elseif($job->salary_min)
                                        From €{{ number_format($job->salary_min) }} / month
                                    @elseif($job->salary_max)
                                        Up to €{{ number_format($job->salary_max) }} / month
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info Message --}}
            <div class="bg-primary-50 border border-primary-200 rounded-lg p-4 mb-6">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-primary-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div class="flex-1 text-sm text-primary-800">
                        <p class="font-medium mb-1">You are applying with your saved profile</p>
                        <p>The employer will receive a snapshot of your profile as it appears right now. Make sure your information is up to date before submitting.</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('jobs.apply.store', $job) }}" method="POST" class="space-y-6">
                @csrf

                {{-- Profile Preview Card --}}
                <div class="bg-white rounded-xl border border-neutral-200 shadow-sm overflow-hidden">
                    <div class="border-b border-neutral-200 bg-neutral-50 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-neutral-900">Your Profile</h3>
                            <a href="{{ route('worker.profile.edit') }}" 
                               class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors"
                               target="_blank">
                                Edit profile
                                <svg class="inline w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        {{-- Personal Information --}}
                        <div class="flex items-start space-x-4">
                            @if($profile->photo_path)
                                <img src="{{ Storage::url($profile->photo_path) }}" 
                                     alt="{{ $profile->first_name }}" 
                                     class="w-20 h-20 rounded-full object-cover flex-shrink-0 ring-2 ring-neutral-100">
                            @else
                                <div class="w-20 h-20 rounded-full bg-neutral-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-10 h-10 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h4 class="text-xl font-semibold text-neutral-900 mb-2">
                                    {{ $profile->first_name }} {{ $profile->last_name }}
                                </h4>
                                <div class="space-y-1 text-sm text-neutral-600">
                                    <p><strong class="text-neutral-700">Nationality:</strong> {{ strtoupper($profile->nationality_country_code) }}</p>
                                    <p><strong class="text-neutral-700">Birth Year:</strong> {{ $profile->birth_year }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Education --}}
                        @if($profile->education_summary)
                            <div>
                                <h5 class="text-sm font-semibold text-neutral-900 mb-2 uppercase tracking-wide">Education</h5>
                                <p class="text-neutral-700 whitespace-pre-line">{{ $profile->education_summary }}</p>
                            </div>
                        @endif

                        {{-- Work Experience --}}
                        @if($profile->work_experience)
                            <div>
                                <h5 class="text-sm font-semibold text-neutral-900 mb-2 uppercase tracking-wide">Work Experience</h5>
                                <p class="text-neutral-700 whitespace-pre-line">{{ $profile->work_experience }}</p>
                            </div>
                        @endif

                        {{-- Skills --}}
                        @if(!empty($profileSkills))
                            <div>
                                <h5 class="text-sm font-semibold text-neutral-900 mb-2 uppercase tracking-wide">Skills</h5>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($profileSkills as $skill)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-50 text-primary-700 border border-primary-200">
                                            {{ $skill }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Recommendations --}}
                        @if($profile->recommendations)
                            <div>
                                <h5 class="text-sm font-semibold text-neutral-900 mb-2 uppercase tracking-wide">Recommendations</h5>
                                <p class="text-neutral-700 whitespace-pre-line">{{ $profile->recommendations }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Message to Employer (Optional) --}}
                <div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-6">
                    <label for="message" class="block text-sm font-semibold text-neutral-900 mb-2">
                        Message to Employer <span class="text-neutral-500 font-normal">(Optional)</span>
                    </label>
                    <p class="text-sm text-neutral-600 mb-3">
                        Add a personal message to introduce yourself or highlight why you're a great fit for this role.
                    </p>
                    <textarea 
                        name="message" 
                        id="message" 
                        rows="5" 
                        maxlength="1000"
                        class="w-full px-4 py-3 border border-neutral-300 rounded-lg text-neutral-900 placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all resize-y @error('message') border-danger-500 focus:ring-danger-500 @enderror"
                        placeholder="Example: I am very interested in this position because..."
                    >{{ old('message') }}</textarea>
                    
                    @error('message')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                    
                    <p class="mt-2 text-xs text-neutral-500">
                        <span id="charCount">0</span> / 1000 characters
                    </p>
                </div>

                {{-- Submit Button --}}
                <div class="bg-white rounded-xl border border-neutral-200 shadow-sm p-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-full bg-success-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-success-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-neutral-900 mb-2">Ready to apply?</h4>
                            <p class="text-neutral-600 mb-6">
                                By submitting your application, you confirm that the information provided is accurate and up to date. The employer will review your profile and contact you if you are selected for an interview.
                            </p>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <button 
                                    type="submit" 
                                    class="inline-flex items-center justify-center px-6 py-3 text-base font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 shadow-sm"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Submit Application
                                </button>
                                <a 
                                    href="{{ route('jobs.show', $job) }}" 
                                    class="inline-flex items-center justify-center px-6 py-3 text-base font-medium text-neutral-700 bg-white hover:bg-neutral-50 border border-neutral-300 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                >
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @endif

    </div>
</div>

{{-- Character Counter Script (Progressive Enhancement) --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.getElementById('message');
        const charCount = document.getElementById('charCount');
        
        if (textarea && charCount) {
            // Initialize count
            charCount.textContent = textarea.value.length;
            
            // Update on input
            textarea.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });
        }
    });
</script>
@endsection
