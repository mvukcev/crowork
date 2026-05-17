@extends('layouts.app')

@section('content')
<div class="cw-surface min-h-screen">
    <!-- Header -->
    <div class="cw-surface-header border-b border-neutral-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('employer.applications.pipeline') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                    ← Back to Pipeline
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content (Left) -->
            <div class="lg:col-span-2">
                <!-- Candidate Profile Card -->
                <div class="cw-surface border border-neutral-200 rounded-lg p-6 mb-6">
                    <div class="flex items-start gap-6">
                        <!-- Photo -->
                        <div>
                            @php
                                $candidatePhotoUrl = $maskedProfile['photo_url'] ?? (($maskedProfile['photo_path'] ?? null)
                                    ? route('worker.profile.photo.show', ['path' => $maskedProfile['photo_path']])
                                    : null);
                            @endphp
                            @if($candidatePhotoUrl)
                                <img src="{{ $candidatePhotoUrl }}" 
                                     alt="Candidate" 
                                     class="w-20 h-20 rounded-full object-cover">
                            @else
                                <div class="w-20 h-20 rounded-full bg-neutral-300 flex items-center justify-center">
                                    <span class="text-2xl font-medium text-neutral-600">
                                        {{ substr($maskedProfile['first_name'] ?? 'C', 0, 1) }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Candidate Info -->
                        <div class="flex-1">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h1 class="cw-heading-1">
                                        @if($maskedProfile['first_name'] ?? null)
                                            {{ $maskedProfile['first_name'] }}
                                            {{ $maskedProfile['last_name'] ?? '' }}
                                        @else
                                            Candidate
                                        @endif
                                    </h1>
                                    @if($maskedProfile['nationality_country_code'] ?? null)
                                        <p class="text-neutral-600 mt-1">
                                            <strong>Nationality:</strong> {{ $maskedProfile['nationality_country_code'] }}
                                        </p>
                                    @endif
                                    @if($maskedProfile['birth_year'] ?? null)
                                        <p class="text-neutral-600">
                                            <strong>Age:</strong> {{ date('Y') - $maskedProfile['birth_year'] }}
                                        </p>
                                    @endif
                                </div>
                                <a href="{{ route('employer.applications.pipeline') }}" class="cw-button-secondary">
                                    Close
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Candidate Profile Sections -->
                @if($maskedProfile['skills'] ?? null)
                    <div class="cw-surface border border-neutral-200 rounded-lg p-6 mb-6">
                        <h2 class="cw-heading-2 mb-4">Skills</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach($maskedProfile['skills'] as $skill)
                                <span class="cw-chip">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($maskedProfile['education_summary'] ?? null)
                    <div class="cw-surface border border-neutral-200 rounded-lg p-6 mb-6">
                        <h2 class="cw-heading-2 mb-4">Education</h2>
                        <p class="text-neutral-700">{{ $maskedProfile['education_summary'] }}</p>
                    </div>
                @endif

                @if($maskedProfile['work_experience'] ?? null)
                    <div class="cw-surface border border-neutral-200 rounded-lg p-6 mb-6">
                        <h2 class="cw-heading-2 mb-4">Work Experience</h2>
                        <p class="text-neutral-700">{{ $maskedProfile['work_experience'] }}</p>
                    </div>
                @endif

                <!-- Motivation Letter -->
                @if($application->message)
                    <div class="cw-surface border border-neutral-200 rounded-lg p-6 mb-6">
                        <h2 class="cw-heading-2 mb-4">Motivation Letter</h2>
                        <p class="text-neutral-700 whitespace-pre-wrap">{{ $application->message }}</p>
                    </div>
                @endif

                <!-- Job Details -->
                <div class="cw-surface border border-neutral-200 rounded-lg p-6">
                    <h2 class="cw-heading-2 mb-4">Applied for: {{ $job->title }}</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-neutral-600">Location</p>
                            <p class="font-medium">{{ $job->location_city }}</p>
                        </div>
                        <div>
                            <p class="text-neutral-600">Job Type</p>
                            <p class="font-medium">{{ ucfirst($job->contract_type ?? 'N/A') }}</p>
                        </div>
                        @if($job->salary_min && $job->salary_max)
                            <div>
                                <p class="text-neutral-600">Salary Range</p>
                                <p class="font-medium">{{ $job->salary_min }} - {{ $job->salary_max }} {{ $job->salary_currency ?? 'EUR' }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-neutral-600">Experience Level</p>
                            <p class="font-medium">{{ ucfirst($job->experience_level ?? 'N/A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar (Right) -->
            <div class="col-span-1">
                <!-- Status and Actions -->
                <div class="cw-surface border border-neutral-200 rounded-lg p-6 mb-6">
                    <h3 class="cw-heading-3 mb-4">Application Status</h3>
                    
                    <!-- Status Selector -->
                    <select id="statusSelector" class="cw-field mb-4" data-app-id="{{ $application->id }}">
                        @foreach(\App\Models\JobApplication::statusOptions() as $value => $label)
                            <option value="{{ $value }}" @selected($application->status === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    <p class="text-xs text-neutral-600">
                        Last updated: {{ $application->status_updated_at?->format('M d, Y H:i') ?? $application->created_at->format('M d, Y H:i') }}
                    </p>
                </div>

                <!-- Rating -->
                <div class="cw-surface border border-neutral-200 rounded-lg p-6 mb-6">
                    <h3 class="cw-heading-3 mb-4">Rating</h3>
                    
                    <div id="ratingContainer" class="flex items-center gap-1 mb-4">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" 
                                    class="star-rating w-8 h-8 focus:outline-none transition"
                                    data-rating="{{ $i }}"
                                    data-app-id="{{ $application->id }}">
                                <svg class="w-full h-full @if($i <= ($application->score ?? 0)) text-yellow-400 @else text-neutral-300 @endif" 
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                        @endfor
                    </div>
                    
                    <p class="text-sm text-neutral-600">
                        @if($application->score)
                            You rated: <strong>{{ $application->score }}/5</strong>
                        @else
                            No rating yet
                        @endif
                    </p>
                </div>

                <!-- Interview Date -->
                <div class="cw-surface border border-neutral-200 rounded-lg p-6 mb-6">
                    <h3 class="cw-heading-3 mb-4">Interview Date</h3>
                    
                    <input type="date" 
                           id="interviewDate"
                           class="cw-field mb-4"
                           value="{{ $application->interview_at?->format('Y-m-d') }}"
                           data-app-id="{{ $application->id }}">
                    
                    @if($application->interview_at)
                        <p class="text-sm text-neutral-600">
                            Scheduled for: <strong>{{ $application->interview_at->format('M d, Y') }}</strong>
                        </p>
                    @else
                        <p class="text-sm text-neutral-600">
                            No interview scheduled yet
                        </p>
                    @endif
                </div>

                <!-- Internal Notes -->
                <div class="cw-surface border border-neutral-200 rounded-lg p-6">
                    <h3 class="cw-heading-3 mb-4">Internal Notes</h3>
                    
                    <textarea id="internalNotes" 
                              class="cw-field mb-4 h-32 resize-none"
                              placeholder="Add notes about this candidate..."
                              data-app-id="{{ $application->id }}">{{ $application->internal_note }}</textarea>
                    
                    <p class="text-xs text-neutral-600">
                        Last updated: {{ $application->updated_at->format('M d, Y H:i') }}
                    </p>
                </div>

                <!-- Application Timeline -->
                <div class="cw-surface border border-neutral-200 rounded-lg p-6 mt-6">
                    <h3 class="cw-heading-3 mb-4">Timeline</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-neutral-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="font-medium text-neutral-900">Applied</p>
                                <p class="text-neutral-600">{{ $application->created_at->format('M d, Y \a\t H:i') }}</p>
                            </div>
                        </div>
                        @if($application->status_updated_at)
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-neutral-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                                </svg>
                                <div>
                                    <p class="font-medium text-neutral-900">Status Changed</p>
                                    <p class="text-neutral-600">{{ $application->status_updated_at->format('M d, Y \a\t H:i') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Status selector
document.getElementById('statusSelector').addEventListener('change', function() {
    const appId = this.dataset.appId;
    const status = this.value;
    
    fetch(`/employer/applications/${appId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ status }),
    })
    .then(res => res.json())
    .catch(err => console.error('Error:', err));
});

// Rating selector
document.querySelectorAll('.star-rating').forEach(button => {
    button.addEventListener('click', function() {
        const appId = this.dataset.appId;
        const rating = this.dataset.rating;
        
        fetch(`/employer/applications/${appId}/score`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ score: rating }),
        })
        .then(res => res.json())
        .then(data => {
            // Update UI
            document.querySelectorAll('.star-rating').forEach((star, idx) => {
                const svg = star.querySelector('svg');
                if (idx < rating) {
                    svg.classList.remove('text-neutral-300');
                    svg.classList.add('text-yellow-400');
                } else {
                    svg.classList.remove('text-yellow-400');
                    svg.classList.add('text-neutral-300');
                }
            });
        })
        .catch(err => console.error('Error:', err));
    });
});

// Interview date
document.getElementById('interviewDate').addEventListener('change', function() {
    const appId = this.dataset.appId;
    const date = this.value;
    
    fetch(`/employer/applications/${appId}/interview-date`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ interview_at: date }),
    })
    .catch(err => console.error('Error:', err));
});

// Internal notes (debounced)
let notesTimeout;
document.getElementById('internalNotes').addEventListener('input', function() {
    clearTimeout(notesTimeout);
    const appId = this.dataset.appId;
    const note = this.value;
    
    notesTimeout = setTimeout(() => {
        fetch(`/employer/applications/${appId}/notes`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ internal_note: note }),
        })
        .catch(err => console.error('Error:', err));
    }, 1000);
});
</script>
@endsection
