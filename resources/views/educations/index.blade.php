<x-app-layout>
    <x-slot name="title">Browse Education Programs in Croatia</x-slot>
    <x-slot name="description">Discover education and training programs in Croatia. Browse courses, certifications, and professional development opportunities.</x-slot>
    <x-slot name="canonical">{{ route('educations.index') }}</x-slot>

    <!-- Hero Section with Green Theme -->
    <x-hero
        size="sm"
        title="Browse Education Programs"
        subtitle="Discover amazing learning opportunities in Croatia. Explore courses, certifications, and professional development programs."
        theme="education"
    />

    <!-- Alpine.js component for progressive enhancement -->
    <div
        x-data="educationsFilter()"
        x-init="init()"
        @popstate.window="handlePopState($event)"
        @paginate.window="handlePagination($event)"
        class="section-spacing-tight"
    >
        <div class="container-base">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Filters Sidebar -->
                <aside class="lg:col-span-4 xl:col-span-3">
                    <div class="premium-glass rounded-3xl p-5 sticky top-28 border border-white/75">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-title-2 font-semibold text-text-primary mb-0">Filters</h2>
                            <span class="text-caption text-text-tertiary">Live update</span>
                        </div>

                        <form
                            action="{{ route('educations.index') }}"
                            method="GET"
                            @submit.prevent="applyFilters()"
                            class="space-y-3.5"
                        >
                            <!-- Search -->
                            <div>
                                <label for="q" class="filter-label">
                                    Search
                                </label>
                                <input
                                    type="text"
                                    id="q"
                                    name="q"
                                    x-model="filters.q"
                                    @input.debounce.500ms="applyFilters()"
                                    placeholder="Title or provider"
                                    value="{{ $filters['q'] ?? '' }}"
                                    class="filter-control"
                                />
                            </div>

                            <!-- Online/In-Person -->
                            <div>
                                <label for="is_online" class="filter-label">
                                    Format
                                </label>
                                <select
                                    id="is_online"
                                    name="is_online"
                                    x-model="filters.is_online"
                                    @change="applyFilters()"
                                    class="filter-control"
                                >
                                    <option value="">All Formats</option>
                                    <option value="1" {{ ($filters['is_online'] ?? '') == '1' ? 'selected' : '' }}>Online</option>
                                    <option value="0" {{ ($filters['is_online'] ?? '') === '0' ? 'selected' : '' }}>In-Person</option>
                                </select>
                            </div>

                            <!-- City (only for in-person) -->
                            <div x-show="filters.is_online !== '1'">
                                <label for="city" class="filter-label">
                                    City
                                </label>
                                <select
                                    id="city"
                                    name="city"
                                    x-model="filters.city"
                                    @change="applyFilters()"
                                    class="filter-control"
                                >
                                    <option value="">All Cities</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city }}" {{ ($filters['city'] ?? '') == $city ? 'selected' : '' }}>
                                            {{ $city }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Start Date From -->
                            <div>
                                <label for="start_from" class="filter-label">
                                    Starting From
                                </label>
                                <input
                                    type="date"
                                    id="start_from"
                                    name="start_from"
                                    x-model="filters.start_from"
                                    @change="applyFilters()"
                                    value="{{ $filters['start_from'] ?? '' }}"
                                    class="filter-control"
                                />
                            </div>

                            <!-- Maximum Price -->
                            <div>
                                <label for="price_max" class="filter-label">
                                    Max. Price (€)
                                </label>
                                <input
                                    type="number"
                                    id="price_max"
                                    name="price_max"
                                    x-model="filters.price_max"
                                    @input.debounce.500ms="applyFilters()"
                                    placeholder="e.g. 500"
                                    min="0"
                                    step="50"
                                    value="{{ $filters['price_max'] ?? '' }}"
                                    class="filter-control"
                                />
                                <p class="text-caption text-text-tertiary mt-1">Includes free programs</p>
                            </div>

                            <!-- Clear Filters -->
                            <div class="pt-4 border-t border-border/30">
                                <button
                                    type="button"
                                    @click="clearFilters()"
                                    class="w-full px-3 py-2 rounded-xl text-body-sm font-medium text-text-secondary hover:text-primary hover:bg-primary/10 transition-all duration-normal"
                                >
                                    Clear all filters
                                </button>
                            </div>

                            <!-- Submit for no-JS fallback -->
                            <noscript>
                                <x-button type="submit" variant="primary" class="w-full">
                                    Apply Filters
                                </x-button>
                            </noscript>
                        </form>
                    </div>
                </aside>

                <!-- Results Area -->
                <main class="lg:col-span-8 xl:col-span-9">
                    <!-- Loading State -->
                    <div x-show="loading" x-cloak class="space-y-4">
                        <x-progress-indicator :show="true" />
                        @for($i = 0; $i < 6; $i++)
                            <x-skeleton-loader type="card" />
                        @endfor
                    </div>

                    <!-- Results Container -->
                    <div id="educations-results" x-show="!loading" class="motion-fade-in">
                        @include('educations._results', ['educations' => $educations])
                    </div>
                </main>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function educationsFilter() {
            return {
                filters: {
                    q: '{{ $filters["q"] ?? "" }}',
                    city: '{{ $filters["city"] ?? "" }}',
                    is_online: '{{ $filters["is_online"] ?? "" }}',
                    start_from: '{{ $filters["start_from"] ?? "" }}',
                    price_max: '{{ $filters["price_max"] ?? "" }}'
                },
                loading: false,
                totalEducations: {{ $educations->total() }},
                currentPage: 1,

                init() {
                    // Parse initial filters from URL
                    const urlParams = new URLSearchParams(window.location.search);
                    this.filters.q = urlParams.get('q') || '';
                    this.filters.city = urlParams.get('city') || '';
                    this.filters.is_online = urlParams.get('is_online') || '';
                    this.filters.start_from = urlParams.get('start_from') || '';
                    this.filters.price_max = urlParams.get('price_max') || '';
                    this.currentPage = parseInt(urlParams.get('page')) || 1;
                },

                async applyFilters(page = 1) {
                    this.loading = true;
                    this.currentPage = page;

                    // Build query string
                    const params = new URLSearchParams();
                    if (this.filters.q) params.set('q', this.filters.q);
                    if (this.filters.city) params.set('city', this.filters.city);
                    if (this.filters.is_online) params.set('is_online', this.filters.is_online);
                    if (this.filters.start_from) params.set('start_from', this.filters.start_from);
                    if (this.filters.price_max) params.set('price_max', this.filters.price_max);
                    if (page > 1) params.set('page', page);

                    const queryString = params.toString();
                    const url = `{{ route('educations.partial') }}${queryString ? '?' + queryString : ''}`;

                    try {
                        const response = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (response.ok) {
                            const html = await response.text();
                            document.getElementById('educations-results').innerHTML = html;

                            // Update browser URL
                            const newUrl = `{{ route('educations.index') }}${queryString ? '?' + queryString : ''}`;
                            window.history.pushState({ filters: this.filters, page: page }, '', newUrl);

                            // Update total count if visible
                            const totalElement = document.querySelector('[x-text="totalEducations"]');
                            if (totalElement) {
                                const match = html.match(/data-total="(\d+)"/);
                                if (match) {
                                    this.totalEducations = match[1];
                                }
                            }

                            // Scroll to top of results
                            document.getElementById('educations-results').scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    } catch (error) {
                        console.error('Error fetching educations:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                handlePagination(event) {
                    const page = event.detail;
                    this.applyFilters(page);
                },

                handlePopState(event) {
                    if (event.state && event.state.filters) {
                        this.filters = event.state.filters;
                        this.applyFilters(event.state.page || 1);
                    } else {
                        // Reset to initial state
                        window.location.reload();
                    }
                },

                clearFilters() {
                    this.filters = {
                        q: '',
                        city: '',
                        is_online: '',
                        start_from: '',
                        price_max: ''
                    };
                    this.currentPage = 1;
                    this.applyFilters(1);
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
