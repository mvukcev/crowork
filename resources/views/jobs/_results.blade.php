@if($jobs->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($jobs as $job)
            <x-job-card
                :title="$job->title"
                :company="$job->employer?->company_name"
                :company_href="$job->employer?->slug ? route('companies.show', $job->employer) : null"
                :city="$job->location_city"
                :salary_min="$job->salary_min"
                :salary_max="$job->salary_max"
                :salary_currency="$job->salary_currency ?? 'EUR'"
                :salary_period="$job->salary_period ?? 'month'"
                :employment_type="$job->contract_type"
                :experience_level="$job->experience_level"
                :education_required="$job->education_required"
                :positions_available="$job->positions_available"
                :working_hours="$job->working_hours"
                :start_date="$job->start_date"
                :start_flexibility="$job->start_flexibility"
                :accommodation_provided="$job->accommodation_provided"
                :visa_support="$job->visa_support"
                :is_urgent="$job->is_urgent"
                :is_featured="$job->is_featured"
                :languages="$job->languages ?? []"
                :posted_at="$job->published_at ?? $job->created_at"
                :href="route('jobs.show', $job)"
            />
        @endforeach
    </div>

    <div class="mt-6">
        {{ $jobs->links() }}
    </div>
@else
    <div class="cw-surface p-8 text-center">
        <h3 class="text-xl font-semibold text-slate-900 mb-2">No jobs found</h3>
        <p class="text-slate-600">Try adjusting search filters or check back later.</p>
    </div>
@endif
