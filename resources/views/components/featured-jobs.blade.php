{{-- Featured Jobs Section --}}
@php
    $featuredJobs = isset($featuredJobs) && $featuredJobs instanceof \Illuminate\Support\Collection
        ? $featuredJobs
        : collect();
@endphp

@if($featuredJobs->isEmpty())
    <x-empty-state :title="__('ui.homepage.empty_state.no_jobs')" icon="star" />
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($featuredJobs as $job)
            @php
                $employer = $job->employer;
                $company_href = null;
                if ($employer && $employer instanceof \App\Models\Employer && $employer->getKey() && $employer->getRouteKey()) {
                    try {
                        $company_href = route('companies.show', $employer);
                    } catch (Exception $e) {
                        $company_href = null;
                    }
                }
            @endphp
            <x-job-card :title="$job->title"
                        :company="$employer?->company_name"
                        :company_href="$company_href"
                        :job_cover_url="$job->cover_image_path ? asset('storage/' . $job->cover_image_path) : null"
                        :city="$job->location_city"
                        :salary_min="$job->salary_min"
                        :salary_max="$job->salary_max"
                        :salary_currency="$job->salary_currency"
                        :salary_period="$job->salary_period"
                        :employment_type="$job->contract_type"
                        :experience_level="$job->experience_level"
                        :education_required="$job->education_required"
                        :positions_available="$job->positions_available"
                        :working_hours="$job->working_hours"
                        :start_date="$job->start_date"
                        :start_flexibility="$job->start_flexibility"
                        :accommodation_provided="$job->accommodation_provided"
                        :visa_support="$job->visa_support"
                        :is_featured="$job->is_featured"
                        :is_urgent="$job->is_urgent"
                        :languages="$job->languages"
                        :posted_at="$job->published_at ?? $job->created_at"
                        :href="route('jobs.show', $job)"
            />
        @endforeach
    </div>
@endif
