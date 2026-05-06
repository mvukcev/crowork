<!-- Hidden data attribute for total count -->
<div data-total="{{ $jobs->total() }}" style="display: none;"></div>

@if($jobs->count() > 0)
    <!-- Jobs Grid -->
    <div class="grid grid-cols-1 gap-4 mb-6">
        @foreach($jobs as $job)
            <x-job-card
                :title="$job->title"
                :company="$job->company_name"
                :city="$job->location_city"
                :salary_min="$job->salary_min"
                :salary_max="$job->salary_max"
                :salary_currency="$job->salary_currency ?? 'EUR'"
                :salary_period="$job->salary_period ?? 'month'"
                :accommodation_provided="$job->accommodation_provided"
                :languages="$job->languages"
                :posted_at="$job->published_at ?? $job->created_at"
                :href="route('jobs.show', $job)"
            />
        @endforeach
    </div>

    <!-- Pagination -->
    @if($jobs->hasPages())
        <div class="flex items-center justify-center space-x-2" x-data>
            {{-- Previous Page Link --}}
            @if ($jobs->onFirstPage())
                <span class="px-3 py-2 text-body-sm text-text-disabled bg-white/70 border border-border rounded-xl cursor-not-allowed">
                    Previous
                </span>
            @else
                <button 
                    type="button"
                    @click="$dispatch('paginate', {{ $jobs->currentPage() - 1 }})"
                    class="px-3 py-2 text-body-sm text-text-primary bg-white/75 border border-border rounded-xl hover:bg-white hover:border-primary transition-colors duration-normal"
                >
                    Previous
                </button>
            @endif

            {{-- Page Numbers --}}
            @foreach(range(1, $jobs->lastPage()) as $page)
                @if($page == $jobs->currentPage())
                    <span class="px-3 py-2 text-body-sm font-semibold text-white bg-primary border border-primary rounded-xl">
                        {{ $page }}
                    </span>
                @elseif($page == 1 || $page == $jobs->lastPage() || abs($page - $jobs->currentPage()) <= 2)
                    <button 
                        type="button"
                        @click="$dispatch('paginate', {{ $page }})"
                        class="px-3 py-2 text-body-sm text-text-primary bg-white/75 border border-border rounded-xl hover:bg-white hover:border-primary transition-colors duration-normal"
                    >
                        {{ $page }}
                    </button>
                @elseif(abs($page - $jobs->currentPage()) == 3)
                    <span class="px-3 py-2 text-body-sm text-text-tertiary">...</span>
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($jobs->hasMorePages())
                <button 
                    type="button"
                    @click="$dispatch('paginate', {{ $jobs->currentPage() + 1 }})"
                    class="px-3 py-2 text-body-sm text-text-primary bg-white/75 border border-border rounded-xl hover:bg-white hover:border-primary transition-colors duration-normal"
                >
                    Next
                </button>
            @else
                <span class="px-3 py-2 text-body-sm text-text-disabled bg-white/70 border border-border rounded-xl cursor-not-allowed">
                    Next
                </span>
            @endif
        </div>

        <p class="text-center text-body-sm text-text-tertiary mt-4">
            Showing {{ $jobs->firstItem() }} to {{ $jobs->lastItem() }} of {{ $jobs->total() }} jobs
        </p>
    @endif
@else
    <!-- Empty State -->
    <div class="text-center py-12">
        <x-card elevated class="max-w-md mx-auto">
            <div class="py-8">
                <svg class="w-16 h-16 mx-auto text-text-tertiary mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <h3 class="text-title-3 font-semibold text-text-primary mb-2">No jobs found</h3>
                <p class="text-body text-text-secondary mb-4">
                    Try adjusting your filters or search terms
                </p>
                <x-button 
                    onclick="window.location.href='{{ route('jobs.index') }}'" 
                    variant="outline"
                >
                    Clear All Filters
                </x-button>
            </div>
        </x-card>
    </div>
@endif
