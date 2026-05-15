<x-app-layout>
    <x-slot name="title">Jobs in Croatia</x-slot>
    <x-slot name="description">Browse verified jobs and migration opportunities in Croatia.</x-slot>
    <x-slot name="canonical">{{ route('jobs.index') }}</x-slot>

    @push('head')
        @if($jobs->currentPage() > 1)
            <meta name="robots" content="index,follow">
        @endif
        @if($jobs->previousPageUrl())
            <link rel="prev" href="{{ $jobs->previousPageUrl() }}">
        @endif
        @if($jobs->nextPageUrl())
            <link rel="next" href="{{ $jobs->nextPageUrl() }}">
        @endif
    @endpush

    <section class="cw-section">
        <div class="cw-container">
            <div class="cw-content-wide mb-8">
                <p class="cw-kicker mb-2">Jobs</p>
                <h1 class="cw-display text-3xl md:text-5xl mb-3">Find your next role in Croatia.</h1>
                <p class="text-base text-slate-600">Search roles by city, category, salary, accommodation, and language requirements.</p>
            </div>

            @php
                $removeFilterQuery = function (string $key): array {
                    $query = request()->query();
                    unset($query[$key], $query['page']);

                    return $query;
                };

                $activeChips = [];

                if (filled($filters['q'] ?? null)) {
                    $activeChips[] = [
                        'label' => 'Search: ' . $filters['q'],
                        'href' => route('jobs.index', $removeFilterQuery('q')),
                        'tone' => 'blue',
                    ];
                }

                if (filled($filters['city'] ?? null)) {
                    $activeChips[] = [
                        'label' => (string) $filters['city'],
                        'href' => route('jobs.index', $removeFilterQuery('city')),
                        'tone' => 'blue',
                    ];
                }

                if (filled($filters['category'] ?? null)) {
                    $activeChips[] = [
                        'label' => (string) $filters['category'],
                        'href' => route('jobs.index', $removeFilterQuery('category')),
                        'tone' => 'blue',
                    ];
                }

                if (filled($filters['employment_type'] ?? null)) {
                    $activeChips[] = [
                        'label' => \Illuminate\Support\Str::headline(str_replace(['-', '_'], ' ', (string) $filters['employment_type'])),
                        'href' => route('jobs.index', $removeFilterQuery('employment_type')),
                        'tone' => 'blue',
                    ];
                }

                if (filled($filters['experience_level'] ?? null)) {
                    $activeChips[] = [
                        'label' => \Illuminate\Support\Str::headline(str_replace(['-', '_'], ' ', (string) $filters['experience_level'])),
                        'href' => route('jobs.index', $removeFilterQuery('experience_level')),
                        'tone' => 'blue',
                    ];
                }

                if (filled($filters['salary_min'] ?? null)) {
                    $activeChips[] = [
                        'label' => 'EUR ' . number_format((int) $filters['salary_min']) . '+',
                        'href' => route('jobs.index', $removeFilterQuery('salary_min')),
                        'tone' => 'orange',
                    ];
                }

                if (request()->input('accommodation') == '1') {
                    $activeChips[] = [
                        'label' => 'Accommodation',
                        'href' => route('jobs.index', $removeFilterQuery('accommodation')),
                        'tone' => 'orange',
                    ];
                }

                if (request()->input('visa_support') == '1') {
                    $activeChips[] = [
                        'label' => 'Visa support',
                        'href' => route('jobs.index', $removeFilterQuery('visa_support')),
                        'tone' => 'orange',
                    ];
                }

                if (request()->input('featured') == '1') {
                    $activeChips[] = [
                        'label' => 'Featured',
                        'href' => route('jobs.index', $removeFilterQuery('featured')),
                        'tone' => 'orange',
                    ];
                }

                if (request()->input('urgent') == '1') {
                    $activeChips[] = [
                        'label' => 'Urgent',
                        'href' => route('jobs.index', $removeFilterQuery('urgent')),
                        'tone' => 'orange',
                    ];
                }

                if (filled($filters['language'] ?? null)) {
                    $code = strtoupper((string) $filters['language']);
                    $activeChips[] = [
                        'label' => $languages[$code] ?? $code,
                        'href' => route('jobs.index', $removeFilterQuery('language')),
                        'tone' => 'blue',
                    ];
                }

                if (filled($filters['education_required'] ?? null)) {
                    $activeChips[] = [
                        'label' => (string) $filters['education_required'],
                        'href' => route('jobs.index', $removeFilterQuery('education_required')),
                        'tone' => 'blue',
                    ];
                }
            @endphp

            <form
                method="GET"
                action="{{ route('jobs.index') }}"
                data-cw-track-submit="job_search"
                class="mb-6"
                x-data="{
                    desktopAdvancedOpen: false,
                    mobilePanelOpen: false,
                    submitting: false,
                    isDesktop() {
                        return window.matchMedia('(min-width: 768px)').matches;
                    },
                    shouldShowPanel() {
                        return this.isDesktop() ? this.desktopAdvancedOpen : this.mobilePanelOpen;
                    },
                    closePanel() {
                        this.desktopAdvancedOpen = false;
                        this.mobilePanelOpen = false;
                    }
                }"
                @submit="submitting = true"
                @keydown.escape.window="closePanel()"
            >
                <div class="cw-filter-shell">
                    <div class="cw-surface p-3 md:p-4">
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <div class="text-sm text-slate-600">
                                <span class="font-semibold text-slate-900">{{ number_format($jobs->total()) }}</span>
                                <span>{{ \Illuminate\Support\Str::plural('result', $jobs->total()) }}</span>
                            </div>
                            @if(count($activeChips) > 0)
                                <a href="{{ route('jobs.index') }}" class="cw-button-secondary !px-3 !py-2 text-xs" data-cw-track-click="job_filter_reset">Clear all</a>
                            @endif
                        </div>

                        <div class="cw-filter-topbar">
                            <div>
                                <label class="cw-label" for="q">Search</label>
                                <input id="q" name="q" value="{{ $filters['q'] ?? request('q') }}" class="cw-field" placeholder="Role, company, skill">
                            </div>
                            <div>
                                <label class="cw-label" for="city">City</label>
                                <select id="city" name="city" class="cw-field">
                                    <option value="">All cities</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city }}" @selected(($filters['city'] ?? request('city')) === $city)>{{ $city }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-end gap-2">
                                <button
                                    class="cw-button-primary min-w-[120px]"
                                    type="submit"
                                    :disabled="submitting"
                                >
                                    <span x-text="submitting ? 'Updating...' : 'Search'" aria-live="polite"></span>
                                </button>
                                <button
                                    type="button"
                                    class="cw-button-secondary hidden md:inline-flex"
                                    aria-controls="job-advanced-filters"
                                    :aria-expanded="desktopAdvancedOpen ? 'true' : 'false'"
                                    @click="desktopAdvancedOpen = !desktopAdvancedOpen"
                                    data-cw-track-click="job_filter_open"
                                >
                                    <span>More filters</span>
                                    <span class="ml-1 text-xs text-slate-500" x-text="desktopAdvancedOpen ? 'Hide' : 'Show'"></span>
                                </button>
                            </div>
                        </div>

                        <div class="md:hidden mt-3">
                            <button
                                type="button"
                                class="cw-button-secondary w-full"
                                @click="mobilePanelOpen = true"
                                    data-cw-track-click="job_filter_open"
                                aria-controls="job-advanced-filters"
                                :aria-expanded="mobilePanelOpen ? 'true' : 'false'"
                            >
                                Advanced filters
                            </button>
                        </div>

                        <p class="text-xs text-slate-500 mt-2" x-show="submitting" x-cloak aria-live="polite">Refreshing job results...</p>
                    </div>

                    <div class="md:hidden" x-cloak>
                        <div class="cw-filter-overlay" x-show="mobilePanelOpen" x-transition.opacity @click="mobilePanelOpen = false"></div>
                    </div>

                    <div
                        id="job-advanced-filters"
                        x-cloak
                        x-show="shouldShowPanel()"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-2"
                        class="fixed md:relative inset-x-0 bottom-0 z-[70] md:z-auto"
                        :class="isDesktop() ? 'mt-3' : 'cw-filter-bottom-sheet'"
                    >
                        <div class="cw-filter-panel p-4 md:p-5" :class="isDesktop() ? '' : 'rounded-b-none border-b-0 pb-6'">
                            <div class="flex items-center justify-between mb-3 md:hidden">
                                <h2 class="text-sm font-semibold text-slate-900">Advanced filters</h2>
                                <button type="button" class="cw-button-secondary !px-3 !py-2 text-xs" @click="mobilePanelOpen = false">Close</button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-5">
                                <div>
                                    <p class="cw-filter-heading">Role</p>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="cw-label" for="category">Category</label>
                                            <select id="category" name="category" class="cw-field">
                                                <option value="">All categories</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category }}" @selected(($filters['category'] ?? request('category')) === $category)>{{ $category }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="cw-label" for="employment_type">Employment type</label>
                                            <select id="employment_type" name="employment_type" class="cw-field">
                                                <option value="">Any type</option>
                                                @foreach($employmentTypes as $employmentType)
                                                    <option value="{{ $employmentType }}" @selected(($filters['employment_type'] ?? request('employment_type')) === $employmentType)>{{ \Illuminate\Support\Str::headline(str_replace(['-', '_'], ' ', $employmentType)) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="cw-label" for="experience_level">Experience level</label>
                                            <select id="experience_level" name="experience_level" class="cw-field">
                                                <option value="">Any level</option>
                                                @foreach($experienceLevels as $experienceLevel)
                                                    <option value="{{ $experienceLevel }}" @selected(($filters['experience_level'] ?? request('experience_level')) === $experienceLevel)>{{ \Illuminate\Support\Str::headline(str_replace(['-', '_'], ' ', $experienceLevel)) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <p class="cw-filter-heading">Compensation</p>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="cw-label" for="salary_min">Minimum salary (EUR)</label>
                                            <input id="salary_min" name="salary_min" value="{{ $filters['salary_min'] ?? request('salary_min') }}" class="cw-field" type="number" min="0" placeholder="e.g. 1500">
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <p class="cw-filter-heading">Relocation</p>
                                    <div class="space-y-3">
                                        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                            <input type="checkbox" name="accommodation" value="1" class="rounded border-slate-300" @checked(($filters['accommodation'] ?? request('accommodation')))> 
                                            Accommodation provided
                                        </label>
                                        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                            <input type="checkbox" name="visa_support" value="1" class="rounded border-slate-300" @checked(($filters['visa_support'] ?? request('visa_support')))> 
                                            Visa/work permit support
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <p class="cw-filter-heading">Qualifications</p>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="cw-label" for="language">Language</label>
                                            <select id="language" name="language" class="cw-field">
                                                <option value="">Any language</option>
                                                @foreach($languages as $code => $name)
                                                    <option value="{{ $code }}" @selected(($filters['language'] ?? request('language')) === $code)>{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="cw-label" for="education_required">Education</label>
                                            <select id="education_required" name="education_required" class="cw-field">
                                                <option value="">Any education</option>
                                                @foreach($educationRequirements as $educationRequired)
                                                    <option value="{{ $educationRequired }}" @selected(($filters['education_required'] ?? request('education_required')) === $educationRequired)>{{ $educationRequired }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <p class="cw-filter-heading">Visibility</p>
                                    <div class="space-y-3">
                                        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                            <input type="checkbox" name="featured" value="1" class="rounded border-slate-300" @checked(($filters['featured'] ?? request('featured')))> 
                                            Featured only
                                        </label>
                                        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                            <input type="checkbox" name="urgent" value="1" class="rounded border-slate-300" @checked(($filters['urgent'] ?? request('urgent')))> 
                                            Urgent only
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 flex flex-wrap items-center gap-2">
                                <button
                                    class="cw-button-primary"
                                    type="submit"
                                    data-cw-track-click="job_filter_apply"
                                    :disabled="submitting"
                                >
                                    <span x-text="submitting ? 'Applying...' : 'Apply filters'" aria-live="polite"></span>
                                </button>
                                <a href="{{ route('jobs.index') }}" class="cw-button-secondary" data-cw-track-click="job_filter_reset">Reset</a>
                                <button type="button" class="cw-button-secondary md:hidden" @click="mobilePanelOpen = false">Done</button>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="cw-button-primary cw-filter-fab md:hidden" @click="mobilePanelOpen = true">
                    Filters
                </button>
            </form>

            @if(count($activeChips) > 0)
                <div class="mb-5 flex flex-wrap items-center gap-2">
                    @foreach($activeChips as $chip)
                        <x-filter-chip :href="$chip['href']" :label="$chip['label']" :tone="$chip['tone']" />
                    @endforeach
                    <a href="{{ route('jobs.index') }}" class="cw-button-secondary !px-3 !py-2 text-xs">Clear all</a>
                </div>
            @endif

            @include('jobs._results', ['jobs' => $jobs])
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                window.cwTrack?.('job_search_results_view', {
                    page: {{ $jobs->currentPage() }},
                    total_results: {{ $jobs->total() }}
                });
            });
        </script>
    @endpush
</x-app-layout>
