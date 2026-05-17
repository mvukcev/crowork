@if($educations->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 items-stretch">
        @foreach($educations as $education)
            @php
                $provider = $education->createdByUser?->employer?->company_display_name
                    ?? $education->createdByUser?->employer?->company_name
                    ?? $education->createdByUser?->name;
                $providerLogoUrl = $education->createdByUser?->employer?->logo_path
                    ? asset('storage/' . $education->createdByUser->employer->logo_path)
                    : null;
                $haystack = mb_strtolower(trim(($education->title ?? '') . ' ' . ($education->description ?? '')));
                $hasCertificate = str_contains($haystack, 'certificate') || str_contains($haystack, 'certifikat');
                $isBeginnerFriendly = str_contains($haystack, 'beginner')
                    || str_contains($haystack, 'starter')
                    || str_contains($haystack, 'početnik')
                    || str_contains($haystack, 'pocetnik');
            @endphp
            <div class="cw-listing-card-wrap" style="--cw-card-delay: {{ min($loop->index * 55, 385) }}ms;">
                <x-education-card
                    :title="$education->title"
                    :provider="$provider"
                    :provider_logo_url="$providerLogoUrl"
                    :city="$education->city"
                    :is_online="$education->is_online"
                    :has_certificate="$hasCertificate"
                    :is_beginner_friendly="$isBeginnerFriendly"
                    :start_date="$education->start_date"
                    :price_cents="$education->price_cents"
                    :currency="$education->currency ?? 'EUR'"
                    :posted_at="$education->published_at ?? $education->created_at"
                    :href="route('educations.show', $education)"
                />
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $educations->links() }}
    </div>
@else
    <div class="cw-surface p-6 md:p-8">
        <x-empty-state
            icon="search"
            :title="__('ui.educations_page.no_results_heading')"
            :description="__('ui.educations_page.no_results_description')"
            :actionHref="route('educations.index')"
            :actionLabel="__('ui.educations_page.no_results_clear')"
        />
        <div class="mt-4 flex justify-center">
            <a href="{{ route('educations.index') }}" class="cw-button-primary">{{ __('ui.educations_page.no_results_browse') }}</a>
        </div>
    </div>
@endif
