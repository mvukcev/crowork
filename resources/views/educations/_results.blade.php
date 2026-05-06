<!-- Hidden data attribute for total count -->
<div data-total="{{ $educations->total() }}" style="display: none;"></div>

@if($educations->count() > 0)
    <!-- Educations Grid -->
    <div class="grid grid-cols-1 gap-4 mb-6">
        @foreach($educations as $education)
            @php
                // Get provider name from creator's employer
                $provider = null;
                if ($education->createdByUser && $education->createdByUser->employer) {
                    $provider = $education->createdByUser->employer->company_name;
                }
            @endphp
            <x-education-card
                :title="$education->title"
                :provider="$provider"
                :city="$education->city"
                :is_online="$education->is_online"
                :start_date="$education->start_date"
                :price_cents="$education->price_cents"
                :currency="$education->currency ?? 'EUR'"
                :posted_at="$education->published_at ?? $education->created_at"
                :href="route('educations.show', $education->slug)"
            />
        @endforeach
    </div>

    <!-- Pagination -->
    @if($educations->hasPages())
        <div class="flex items-center justify-center space-x-2" x-data>
            {{-- Previous Page Link --}}
            @if ($educations->onFirstPage())
                <span class="px-3 py-2 text-body-sm text-text-disabled bg-white/70 border border-border rounded-xl cursor-not-allowed">
                    Previous
                </span>
            @else
                <button 
                    type="button"
                    @click="$dispatch('paginate', {{ $educations->currentPage() - 1 }})"
                    class="px-3 py-2 text-body-sm text-text-primary bg-white/75 border border-border rounded-xl hover:bg-white hover:border-primary transition-colors duration-normal"
                >
                    Previous
                </button>
            @endif

            {{-- Page Numbers --}}
            @foreach(range(1, $educations->lastPage()) as $page)
                @if($page == $educations->currentPage())
                    <span class="px-3 py-2 text-body-sm font-semibold text-white bg-primary border border-primary rounded-xl">
                        {{ $page }}
                    </span>
                @elseif($page == 1 || $page == $educations->lastPage() || abs($page - $educations->currentPage()) <= 2)
                    <button 
                        type="button"
                        @click="$dispatch('paginate', {{ $page }})"
                        class="px-3 py-2 text-body-sm text-text-primary bg-white/75 border border-border rounded-xl hover:bg-white hover:border-primary transition-colors duration-normal"
                    >
                        {{ $page }}
                    </button>
                @elseif(abs($page - $educations->currentPage()) == 3)
                    <span class="px-3 py-2 text-body-sm text-text-tertiary">...</span>
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($educations->hasMorePages())
                <button 
                    type="button"
                    @click="$dispatch('paginate', {{ $educations->currentPage() + 1 }})"
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
    @endif
@else
    <!-- No Results -->
    <div class="bg-surface border border-border rounded-lg p-12 text-center">
        <svg class="w-16 h-16 mx-auto mb-4 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <h3 class="text-title-2 font-semibold text-text-primary mb-2">No education programs found</h3>
        <p class="text-body text-text-secondary mb-6">
            Try adjusting your filters or search criteria to find more opportunities.
        </p>
        <button 
            type="button"
            @click="$dispatch('clear-filters')"
            class="text-body-sm text-primary hover:text-primary-hover transition-colors duration-normal"
        >
            Clear all filters
        </button>
    </div>
@endif
