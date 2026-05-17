@if($jobs->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 items-stretch">
        @foreach($jobs as $job)
            <div class="cw-listing-card-wrap" style="--cw-card-delay: {{ min($loop->index * 55, 385) }}ms;">
                <x-job-card
                    :title="$job->title"
                    :company="$job->employer?->company_display_name ?? $job->employer?->company_name"
                    :company_href="$job->employer?->slug ? route('companies.show', $job->employer) : null"
                    :employer_logo_url="$job->employer?->logo_path ? asset('storage/' . $job->employer->logo_path) : null"
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
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $jobs->links() }}
    </div>
@else
    <div class="cw-surface p-6 md:p-8">
        <x-empty-state
            icon="search"
            :title="__('ui.jobs_page.no_results_heading')"
            :description="__('ui.jobs_page.no_results_description')"
            :actionHref="route('jobs.index')"
            :actionLabel="__('ui.jobs_page.no_results_clear')"
        />
        <div class="mt-4 flex justify-center">
            <a href="{{ route('jobs.index') }}" class="cw-button-primary">{{ __('ui.jobs_page.no_results_browse') }}</a>
        </div>
    </div>
@endif
