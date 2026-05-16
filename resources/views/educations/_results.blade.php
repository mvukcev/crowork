@if($educations->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($educations as $education)
            @php
                $provider = $education->createdByUser?->employer?->company_name ?? $education->createdByUser?->name;
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
                :href="route('educations.show', $education)"
            />
        @endforeach
    </div>

    <div class="mt-6">
        {{ $educations->links() }}
    </div>
@else
    <div class="cw-surface p-10 text-center rounded-2xl border-2 border-dashed border-slate-200">
        <h3 class="text-xl font-semibold text-slate-900 mb-2">No educations found</h3>
        <p class="text-slate-600 mb-5">Try broader filters to find language, onboarding, and certification programs.</p>
        <div class="flex flex-wrap justify-center gap-2">
            <a href="{{ route('educations.index') }}" class="cw-button-secondary">Clear filters</a>
            <a href="{{ route('jobs.index') }}" class="cw-button-primary">Explore jobs</a>
        </div>
    </div>
@endif
