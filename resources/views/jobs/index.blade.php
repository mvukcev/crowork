<x-app-layout>
    <x-slot name="title">Browse Jobs in Croatia</x-slot>
    <x-slot name="description">Find your dream job in Croatia. Browse thousands of opportunities from verified employers with filter by location, salary, and more.</x-slot>
    <x-slot name="canonical">{{ route('jobs.index') }}</x-slot>

    <!-- Hero Section with Purple Theme -->
    <x-hero
        size="sm"
        title="Browse Jobs"
        subtitle="Scan verified openings with compact filters for location, salary, language, and accommodation."
        theme="jobs"
    />

    <!-- Alpine.js component for progressive enhancement -->
    <div
        x-data="jobsFilter()"
        x-init="init()"
        @popstate.window="handlePopState($event)"
        @paginate.window="handlePagination($event)"
        class="section-spacing-tight"
    >
        <div class="container-base">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">
                <!-- Filters Sidebar -->
                <aside class="lg:col-span-1">
                    <div class="bg-white/95 rounded-2xl shadow-elevation-1 p-4 sticky top-24 border border-border/60">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-subtitle font-semibold text-text-primary mb-0">Filters</h2>
                            <span class="text-caption text-text-tertiary">Live</span>
                        </div>

                        <form
                            action="{{ route('jobs.index') }}"
                            method="GET"
                            @submit.prevent="applyFilters()"
                            class="space-y-3"
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
                                    placeholder="Job title or company"
                                    value="{{ $filters['q'] ?? '' }}"
                                    class="filter-control"
                                />
                            </div>

                            <!-- City -->
                            <div>
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

                            <!-- Category -->
                            <div>
                                <label for="category" class="filter-label">
                                    Category
                                </label>
                                <select
                                    id="category"
                                    name="category"
                                    x-model="filters.category"
                                    @change="applyFilters()"
                                    class="filter-control"
                                >
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ ($filters['category'] ?? '') == $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Minimum Salary -->
                            <div>
                                <label for="salary_min" class="filter-label">
                                    Min. Salary (€/month)
                                </label>
                                <input
                                    type="number"
                                    id="salary_min"
                                    name="salary_min"
                                    x-model="filters.salary_min"
                                    @input.debounce.500ms="applyFilters()"
                                    placeholder="e.g. 2000"
                                    min="0"
                                    step="100"
                                    value="{{ $filters['salary_min'] ?? '' }}"
                                    class="filter-control"
                                />
                            </div>

                            <!-- Language -->
                            <div>
                                <label for="language" class="filter-label">
                                    Language
                                </label>
                                <select
                                    id="language"
                                    name="language"
                                    x-model="filters.language"
                                    @change="applyFilters()"
                                    class="filter-control"
                                >
                                    <option value="">Any Language</option>
                                    @foreach($languages as $code => $name)
                                        <option value="{{ $code }}" {{ ($filters['language'] ?? '') == $code ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Accommodation -->
                            <div>
                                <label class="flex items-center cursor-pointer group">
                                    <input
                                        type="checkbox"
                                        name="accommodation"
                                        value="1"
                                        x-model="filters.accommodation"
                                        @change="applyFilters()"
                                        {{ ($filters['accommodation'] ?? '') == '1' ? 'checked' : '' }}
                                        class="w-4 h-4 text-primary bg-white border-border rounded-md focus:ring-2 focus:ring-primary/50 transition-all duration-normal"
                                    />
                                    <span class="ml-2.5 text-body-sm text-text-primary group-hover:text-primary transition-colors duration-normal">
                                        Accommodation provided
                                    </span>
                                </label>
                            </div>

                            <!-- Clear Filters -->
                            <div class="pt-3 border-t border-border/30">
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
                <main class="lg:col-span-3">
                    <!-- Loading State -->
                    <div x-show="loading" x-cloak class="space-y-4">
                        <x-progress-indicator :show="true" />
                        @for($i = 0; $i < 6; $i++)
                            <x-skeleton-loader type="card" />
                        @endfor
                    </div>

                    <!-- Results Container -->
                    <div id="jobs-results" x-show="!loading" class="motion-fade-in">
                        @include('jobs._results', ['jobs' => $jobs])
                    </div>
                </main>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function jobsFilter() {
            return {
                filters: {
                    q: '{{ $filters["q"] ?? "" }}',
                    city: '{{ $filters["city"] ?? "" }}',
                    category: '{{ $filters["category"] ?? "" }}',
                    salary_min: '{{ $filters["salary_min"] ?? "" }}',
                    accommodation: {{ ($filters['accommodation'] ?? '') == '1' ? 'true' : 'false' }},
                    language: '{{ $filters["language"] ?? "" }}'
                },
                loading: false,
                totalJobs: {{ $jobs->total() }},
                currentPage: 1,

                init() {
                    // Parse initial filters from URL
                    const urlParams = new URLSearchParams(window.location.search);
                    this.filters.q = urlParams.get('q') || '';
                    this.filters.city = urlParams.get('city') || '';
                    this.filters.category = urlParams.get('category') || '';
                    this.filters.salary_min = urlParams.get('salary_min') || '';
                    this.filters.accommodation = urlParams.get('accommodation') === '1';
                    this.filters.language = urlParams.get('language') || '';
                    this.currentPage = parseInt(urlParams.get('page')) || 1;
                },

                async applyFilters(page = 1) {
                    this.loading = true;
                    this.currentPage = page;

                    // Build query string
                    const params = new URLSearchParams();
                    if (this.filters.q) params.set('q', this.filters.q);
                    if (this.filters.city) params.set('city', this.filters.city);
                    if (this.filters.category) params.set('category', this.filters.category);
                    if (this.filters.salary_min) params.set('salary_min', this.filters.salary_min);
                    if (this.filters.accommodation) params.set('accommodation', '1');
                    if (this.filters.language) params.set('language', this.filters.language);
                    if (page > 1) params.set('page', page);

                    const queryString = params.toString();
                    const url = `{{ route('jobs.partial') }}${queryString ? '?' + queryString : ''}`;

                    try {
                        const response = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (response.ok) {
                            const html = await response.text();
                            document.getElementById('jobs-results').innerHTML = html;

                            // Update browser URL
                            const newUrl = `{{ route('jobs.index') }}${queryString ? '?' + queryString : ''}`;
                            window.history.pushState({ filters: this.filters, page: page }, '', newUrl);

                            // Update total count if visible
                            const totalElement = document.querySelector('[x-text="totalJobs"]');
                            if (totalElement) {
                                const match = html.match(/data-total="(\d+)"/);
                                if (match) {
                                    this.totalJobs = match[1];
                                }
                            }

                            // Scroll to top of results
                            document.getElementById('jobs-results').scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    } catch (error) {
                        console.error('Error fetching jobs:', error);
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
                        category: '',
                        salary_min: '',
                        accommodation: false,
                        language: ''
                    };
                    this.currentPage = 1;
                    this.applyFilters(1);
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
