<x-app-layout>
    <x-slot name="title">{{ __('ui.jobs_page.page_title') }}</x-slot>
    <x-slot name="description">{{ __('ui.jobs_page.page_description') }}</x-slot>
    <x-slot name="canonical">{{ route('jobs.index') }}</x-slot>

    @php
        $jobsCollectionSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => __('ui.jobs_page.page_title'),
            'description' => __('ui.jobs_page.page_description'),
            'url' => route('jobs.index'),
            'inLanguage' => app()->getLocale(),
            'mainEntity' => [
                '@type' => 'ItemList',
                'numberOfItems' => $jobs->count(),
                'itemListElement' => $jobs->values()->map(function ($job, $index) {
                    return [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'url' => route('jobs.show', $job),
                        'name' => $job->title,
                    ];
                })->all(),
            ],
        ];
    @endphp

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
        <script type="application/ld+json">{!! json_encode($jobsCollectionSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

    <section class="cw-section">
        <div class="cw-container">
            <div class="cw-content-wide mb-8">
                <p class="cw-kicker mb-2">{{ __('ui.navigation.jobs') }}</p>
                <h1 class="cw-display text-3xl md:text-5xl mb-3">{!! __('ui.jobs_page.hero_headline') !!}</h1>
                <p class="text-base text-slate-600 max-w-2xl">{{ __('ui.jobs_page.hero_subheadline') }}</p>
            </div>

            @php
                $removeFilterQuery = function (string $key): array {
                    $query = request()->query();
                    unset($query[$key], $query['page']);

                    return $query;
                };

                $beginnerExperienceOption = collect($experienceLevels)->first(function ($value) {
                    return preg_match('/entry|junior|beginner/i', (string) $value);
                });

                $salaryMinBound = 400;
                $salaryMaxBound = 6000;
                $salaryStep = 50;

                $salaryMinValue = (int) ($filters['salary_min'] ?? request('salary_min') ?? $salaryMinBound);
                $salaryMaxValue = (int) ($filters['salary_max'] ?? request('salary_max') ?? $salaryMaxBound);

                $salaryMinValue = max($salaryMinBound, min($salaryMinValue, $salaryMaxBound - $salaryStep));
                $salaryMaxValue = max($salaryMinValue + $salaryStep, min($salaryMaxValue, $salaryMaxBound));

                $activeChips = [];

                if (filled($filters['q'] ?? null)) {
                    $activeChips[] = [
                        'label' => __('ui.jobs_page.search_chip', ['value' => $filters['q']]),
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
                        'label' => (string) cw_localize_job_value('category', $filters['category']),
                        'href' => route('jobs.index', $removeFilterQuery('category')),
                        'tone' => 'blue',
                    ];
                }

                if (filled($filters['employment_type'] ?? null)) {
                    $activeChips[] = [
                        'label' => (string) cw_localize_job_value('employment_type', $filters['employment_type']),
                        'href' => route('jobs.index', $removeFilterQuery('employment_type')),
                        'tone' => 'blue',
                    ];
                }

                if (filled($filters['experience_level'] ?? null)) {
                    $activeChips[] = [
                        'label' => (string) cw_localize_job_value('experience_level', $filters['experience_level']),
                        'href' => route('jobs.index', $removeFilterQuery('experience_level')),
                        'tone' => 'blue',
                    ];
                }

                if (filled($filters['salary_min'] ?? null)) {
                    $activeChips[] = [
                        'label' => __('ui.jobs_page.salary_from_chip', ['amount' => number_format((int) $filters['salary_min'])]),
                        'href' => route('jobs.index', $removeFilterQuery('salary_min')),
                        'tone' => 'orange',
                    ];
                }

                if (filled($filters['salary_max'] ?? null)) {
                    $activeChips[] = [
                        'label' => __('ui.jobs_page.salary_to_chip', ['amount' => number_format((int) $filters['salary_max'])]),
                        'href' => route('jobs.index', $removeFilterQuery('salary_max')),
                        'tone' => 'orange',
                    ];
                }

                if (request()->input('accommodation') == '1') {
                    $activeChips[] = [
                        'label' => __('ui.jobs_page.accommodation_label'),
                        'href' => route('jobs.index', $removeFilterQuery('accommodation')),
                        'tone' => 'orange',
                    ];
                }

                if (request()->input('visa_support') == '1') {
                    $activeChips[] = [
                        'label' => __('ui.jobs_page.visa_support_label'),
                        'href' => route('jobs.index', $removeFilterQuery('visa_support')),
                        'tone' => 'orange',
                    ];
                }

                if (request()->input('featured') == '1') {
                    $activeChips[] = [
                        'label' => __('ui.jobs_page.featured_label'),
                        'href' => route('jobs.index', $removeFilterQuery('featured')),
                        'tone' => 'orange',
                    ];
                }

                if (request()->input('urgent') == '1') {
                    $activeChips[] = [
                        'label' => __('ui.jobs_page.urgent_label'),
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
                        'label' => (string) cw_localize_job_value('education_required', $filters['education_required']),
                        'href' => route('jobs.index', $removeFilterQuery('education_required')),
                        'tone' => 'blue',
                    ];
                }
            @endphp

            @if(count($activeChips) === 0)
                <!-- Why CroWork section -->
                <div class="mb-8">
                    <h2 class="text-2xl md:text-3xl font-semibold text-slate-900 mb-6">{{ __('ui.jobs_page.why_crowork_title') }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                        <!-- Transparent listings -->
                        <div class="cw-surface p-6 rounded-lg border border-slate-200 hover:border-slate-300 transition-colors">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-indigo-100">
                                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-base font-semibold text-slate-900 mb-1">{{ __('ui.jobs_page.why_crowork.transparent_title') }}</h3>
                                    <p class="text-sm text-slate-600">{{ __('ui.jobs_page.why_crowork.transparent_desc') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Faster applications -->
                        <div class="cw-surface p-6 rounded-lg border border-slate-200 hover:border-slate-300 transition-colors">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-blue-100">
                                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-base font-semibold text-slate-900 mb-1">{{ __('ui.jobs_page.why_crowork.faster_title') }}</h3>
                                    <p class="text-sm text-slate-600">{{ __('ui.jobs_page.why_crowork.faster_desc') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Modern hiring -->
                        <div class="cw-surface p-6 rounded-lg border border-slate-200 hover:border-slate-300 transition-colors">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-purple-100">
                                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-base font-semibold text-slate-900 mb-1">{{ __('ui.jobs_page.why_crowork.modern_title') }}</h3>
                                    <p class="text-sm text-slate-600">{{ __('ui.jobs_page.why_crowork.modern_desc') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Multilingual support -->
                        <div class="cw-surface p-6 rounded-lg border border-slate-200 hover:border-slate-300 transition-colors">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-emerald-100">
                                        <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 00.948-.684l1.498-4.493a1 1 0 011.502-.684l1.498 4.493a1 1 0 00.948.684H19a2 2 0 012 2v1M3 5a2 2 0 012-2h.5a1 1 0 01.855.468L7.5 4m0 0l1.5-2a1 1 0 011.414 0m0 0l1.5 2m-9 0h2m6 0h2"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-base font-semibold text-slate-900 mb-1">{{ __('ui.jobs_page.why_crowork.multilingual_title') }}</h3>
                                    <p class="text-sm text-slate-600">{{ __('ui.jobs_page.why_crowork.multilingual_desc') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form
                method="GET"
                action="{{ route('jobs.index') }}"
                data-cw-track-submit="job_search"
                class="mb-6"
                x-data="{
                    desktopAdvancedOpen: false,
                    mobilePanelOpen: false,
                    submitting: false,
                    cityOpen: false,
                    citySearch: '',
                    cities: @js(collect($cities)->values()->all()),
                    recentCities: [],
                    cityValue: @js((string) ($filters['city'] ?? request('city') ?? '')),
                    categoryValue: @js((string) ($filters['category'] ?? request('category') ?? '')),
                    employmentValue: @js((string) ($filters['employment_type'] ?? request('employment_type') ?? '')),
                    languageValue: @js((string) ($filters['language'] ?? request('language') ?? '')),
                    experienceValue: @js((string) ($filters['experience_level'] ?? request('experience_level') ?? '')),
                    salaryMin: @js($salaryMinValue),
                    salaryMax: @js($salaryMaxValue),
                    salaryMinBound: @js($salaryMinBound),
                    salaryMaxBound: @js($salaryMaxBound),
                    salaryStep: @js($salaryStep),
                    quick: {
                        accommodation: @js((string) request('accommodation') === '1'),
                        visa_support: @js((string) request('visa_support') === '1'),
                        featured: @js((string) request('featured') === '1'),
                        urgent: @js((string) request('urgent') === '1'),
                        english: @js(strtoupper((string) ($filters['language'] ?? request('language') ?? '')) === 'EN'),
                        beginner: @js(($beginnerExperienceOption !== null) && (($filters['experience_level'] ?? request('experience_level')) === $beginnerExperienceOption)),
                    },
                    init() {
                        this.loadRecentCities();
                        if (this.cityValue) {
                            this.rememberCity(this.cityValue);
                        }
                        this.enforceSalaryBounds();
                        this.syncQuickPresets();
                    },
                    isDesktop() {
                        return window.matchMedia('(min-width: 768px)').matches;
                    },
                    shouldShowPanel() {
                        return this.isDesktop() ? this.desktopAdvancedOpen : this.mobilePanelOpen;
                    },
                    closePanel() {
                        this.desktopAdvancedOpen = false;
                        this.mobilePanelOpen = false;
                    },
                    enforceSalaryBounds() {
                        if (this.salaryMin > this.salaryMax - this.salaryStep) {
                            this.salaryMin = this.salaryMax - this.salaryStep;
                        }
                        if (this.salaryMax < this.salaryMin + this.salaryStep) {
                            this.salaryMax = this.salaryMin + this.salaryStep;
                        }
                    },
                    salaryFillStyle() {
                        const total = this.salaryMaxBound - this.salaryMinBound;
                        const left = ((this.salaryMin - this.salaryMinBound) / total) * 100;
                        const width = ((this.salaryMax - this.salaryMin) / total) * 100;
                        return `left: ${left}%; width: ${width}%;`;
                    },
                    filteredCities() {
                        const term = this.citySearch.toLowerCase().trim();
                        if (!term) {
                            return this.cities;
                        }
                        return this.cities.filter((city) => city.toLowerCase().includes(term));
                    },
                    selectCity(city) {
                        this.cityValue = city;
                        this.cityOpen = false;
                        this.rememberCity(city);
                    },
                    clearCity() {
                        this.cityValue = '';
                        this.citySearch = '';
                        this.cityOpen = false;
                    },
                    rememberCity(city) {
                        const next = [city, ...this.recentCities.filter((item) => item !== city)].slice(0, 3);
                        this.recentCities = next;
                        localStorage.setItem('cw_recent_job_cities', JSON.stringify(next));
                    },
                    loadRecentCities() {
                        try {
                            const saved = JSON.parse(localStorage.getItem('cw_recent_job_cities') || '[]');
                            if (Array.isArray(saved)) {
                                this.recentCities = saved.filter((city) => this.cities.includes(city)).slice(0, 3);
                            }
                        } catch (_) {
                            this.recentCities = [];
                        }
                    },
                    toggleQuick(key) {
                        this.quick[key] = !this.quick[key];
                        this.syncQuickPresets();
                    },
                    syncQuickPresets() {
                        if (this.quick.english) {
                            this.languageValue = 'EN';
                        } else if (this.languageValue === 'EN') {
                            this.languageValue = '';
                        }

                        const beginnerValue = @js((string) ($beginnerExperienceOption ?? ''));
                        if (beginnerValue) {
                            if (this.quick.beginner) {
                                this.experienceValue = beginnerValue;
                            } else if (this.experienceValue === beginnerValue) {
                                this.experienceValue = '';
                            }
                        }
                    }
                }"
                @submit="syncQuickPresets(); submitting = true"
                @keydown.escape.window="closePanel()"
                data-cw-filter-form
                data-cw-filter-panel-id="job-advanced-filters"
            >
                <input type="hidden" name="city" :value="cityValue" :disabled="!cityValue">
                <input type="hidden" name="category" :value="categoryValue" :disabled="!categoryValue">
                <input type="hidden" name="employment_type" :value="employmentValue" :disabled="!employmentValue">
                <input type="hidden" name="language" :value="languageValue" :disabled="!languageValue">
                <input type="hidden" name="experience_level" :value="experienceValue" :disabled="!experienceValue">
                <input type="hidden" name="salary_min" :value="salaryMin" :disabled="salaryMin <= salaryMinBound">
                <input type="hidden" name="salary_max" :value="salaryMax" :disabled="salaryMax >= salaryMaxBound">
                <input type="checkbox" name="accommodation" value="1" class="hidden" :checked="quick.accommodation">
                <input type="checkbox" name="visa_support" value="1" class="hidden" :checked="quick.visa_support">
                <input type="checkbox" name="featured" value="1" class="hidden" :checked="quick.featured">
                <input type="checkbox" name="urgent" value="1" class="hidden" :checked="quick.urgent">

                <div class="cw-filter-shell">
                    <div class="cw-surface p-4 md:p-5">
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <div class="text-sm text-slate-600">
                                <span class="font-semibold text-slate-900">{{ number_format($jobs->total()) }}</span>
                                <span>{{ trans_choice('ui.jobs_page.filter_results_count', $jobs->total()) }}</span>
                            </div>
                            @if(count($activeChips) > 0)
                                <a href="{{ route('jobs.index') }}" class="cw-button-secondary !px-3 !py-2 text-xs" data-cw-track-click="filter_clear" data-cw-item-type="job">{{ __('ui.jobs_page.clear_all_filters') }}</a>
                            @endif
                        </div>

                        <div class="cw-filter-topbar-modern">
                            <div>
                                <label class="cw-label" for="q">{{ __('ui.jobs_page.search_jobs_label') }}</label>
                                <input id="q" name="q" value="{{ $filters['q'] ?? request('q') }}" class="cw-field cw-field-premium" placeholder="{{ __('ui.jobs_page.search_placeholder') }}">
                            </div>
                            <div class="cw-modern-select" @click.outside="cityOpen = false">
                                <label class="cw-label">{{ __('ui.jobs_page.city_label') }}</label>
                                <button type="button" class="cw-field cw-field-premium w-full text-left flex items-center justify-between" @click="cityOpen = !cityOpen" :aria-expanded="cityOpen ? 'true' : 'false'" aria-controls="job-city-popover">
                                    <span x-text="cityValue || @js(__('ui.jobs_page.all_cities'))"></span>
                                    <span class="text-xs text-slate-500">▾</span>
                                </button>
                                <div id="job-city-popover" x-show="cityOpen" x-cloak x-transition.opacity.scale.origin.top class="cw-select-popover mt-2">
                                    <input type="text" x-model="citySearch" class="cw-field cw-field-premium mb-2" :placeholder="@js(__('ui.jobs_page.city_search_placeholder'))">
                                    <template x-if="recentCities.length">
                                        <div class="mb-2">
                                            <p class="cw-filter-heading !mb-2">{{ __('ui.jobs_page.recent_locations') }}</p>
                                            <div class="flex flex-wrap gap-2">
                                                <template x-for="city in recentCities" :key="`recent-${city}`">
                                                    <button type="button" class="cw-filter-chip" @click="selectCity(city)" x-text="city"></button>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                    <div class="max-h-52 overflow-auto pr-1">
                                        <button type="button" class="cw-select-option" @click="clearCity()">{{ __('ui.jobs_page.all_cities') }}</button>
                                        <template x-for="city in filteredCities()" :key="city">
                                            <button type="button" class="cw-select-option" @click="selectCity(city)" x-text="city"></button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-end gap-2">
                                <button class="cw-button-primary min-w-[132px]" type="submit" :disabled="submitting">
                                    <span data-cw-submit-label data-default-label="{{ __('ui.jobs_page.search_button') }}" data-loading-label="Updating..." aria-live="polite">{{ __('ui.jobs_page.search_button') }}</span>
                                </button>
                                <button
                                    type="button"
                                    class="cw-button-secondary hidden md:inline-flex"
                                    data-cw-filter-toggle
                                    aria-controls="job-advanced-filters"
                                    :aria-expanded="desktopAdvancedOpen ? 'true' : 'false'"
                                    @click="desktopAdvancedOpen = !desktopAdvancedOpen"
                                    data-cw-track-click="filter_open"
                                    data-cw-item-type="job"
                                >
                                    <span>{{ __('ui.jobs_page.more_filters') }}</span>
                                    <span class="ml-1 text-xs text-slate-500" x-text="desktopAdvancedOpen ? @js(__('ui.jobs_page.toggle_hide')) : @js(__('ui.jobs_page.toggle_show'))"></span>
                                </button>
                            </div>
                        </div>

                        <!-- Only the search and city fields are always visible. Advanced filters are rendered in the panel below. -->

                        <p class="text-xs text-slate-500 mt-2" x-show="submitting" x-cloak aria-live="polite">{{ __('ui.jobs_page.refreshing_results') }}</p>
                    </div>

                    <div class="md:hidden" x-cloak>
                        <div class="cw-filter-overlay" data-cw-filter-overlay x-show="mobilePanelOpen" x-transition.opacity @click="mobilePanelOpen = false"></div>
                    </div>

                    <div
                        id="job-advanced-filters"
                        x-cloak
                        x-show="shouldShowPanel()"
                        style="display: none;"
                        x-transition:enter="transition ease-out duration-250"
                        x-transition:enter-start="opacity-0 translate-y-3 scale-[0.985] blur-[2px]"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100 blur-0"
                        x-transition:leave="transition ease-in duration-180"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100 blur-0"
                        x-transition:leave-end="opacity-0 translate-y-2 scale-[0.99] blur-[1px]"
                        class="fixed md:relative inset-x-0 bottom-0 z-[70] md:z-auto"
                        :class="isDesktop() ? 'mt-3' : 'cw-filter-bottom-sheet'"
                    >
                        <div class="cw-filter-panel p-4 md:p-5" style="border: 0; box-shadow: none;" :class="isDesktop() ? '' : 'rounded-b-none border-b-0 pb-24'">
                            <div class="flex items-center justify-between mb-3 md:hidden">
                                <h2 class="text-sm font-semibold text-slate-900">{{ __('ui.jobs_page.more_filters') }}</h2>
                                <button type="button" class="cw-button-secondary !px-3 !py-2 text-xs" data-cw-filter-close aria-controls="job-advanced-filters" @click="mobilePanelOpen = false">{{ __('ui.jobs_page.close_filters') }}</button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                                <div>
                                    <p class="cw-filter-heading">{{ __('ui.jobs_page.primary_filters') }}</p>
                                    <div class="cw-pill-group">
                                        <button type="button" class="cw-choice-pill" :class="employmentValue === '' ? 'is-active' : ''" @click="employmentValue = ''">{{ __('ui.jobs_page.any_type') }}</button>
                                        @foreach($employmentTypes as $employmentType)
                                            <button type="button" class="cw-choice-pill" :class="employmentValue === @js($employmentType) ? 'is-active' : ''" @click="employmentValue = @js($employmentType)">{{ cw_localize_job_value('employment_type', $employmentType) }}</button>
                                        @endforeach
                                    </div>
                                </div>

                                <div>
                                    <label class="cw-label mb-2">{{ __('ui.jobs_page.language_label') }}</label>
                                    <div class="cw-pill-group">
                                        <button type="button" class="cw-choice-pill" :class="languageValue === '' ? 'is-active' : ''" @click="languageValue = ''">{{ __('ui.jobs_page.any_language') }}</button>
                                        @foreach($languages as $code => $name)
                                            <button type="button" class="cw-choice-pill" :class="languageValue === @js($code) ? 'is-active' : ''" @click="languageValue = @js($code)">{{ $name }}</button>
                                        @endforeach
                                    </div>
                                </div>

                                <div>
                                    <label class="cw-label mb-2">{{ __('ui.jobs_page.experience_label') }}</label>
                                    <div class="cw-pill-group">
                                        <button type="button" class="cw-choice-pill" :class="experienceValue === '' ? 'is-active' : ''" @click="experienceValue = ''">{{ __('ui.jobs_page.any_level') }}</button>
                                        @foreach($experienceLevels as $experienceLevel)
                                            <button type="button" class="cw-choice-pill" :class="experienceValue === @js($experienceLevel) ? 'is-active' : ''" @click="experienceValue = @js($experienceLevel)">{{ cw_localize_job_value('experience_level', $experienceLevel) }}</button>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="md:col-span-2 xl:col-span-2">
                                    <div class="cw-range-card">
                                        <div class="flex items-center justify-between gap-3">
                                            <label class="cw-label mb-0">{{ __('ui.jobs_page.salary_range_label') }}</label>
                                            <p class="text-sm font-medium text-slate-700" x-text="`€${Number(salaryMin).toLocaleString()} - €${Number(salaryMax).toLocaleString()}`"></p>
                                        </div>
                                        <div class="cw-range-control mt-3">
                                            <div class="cw-range-track"></div>
                                            <div class="cw-range-fill" :style="salaryFillStyle()"></div>
                                            <input type="range" :min="salaryMinBound" :max="salaryMaxBound" :step="salaryStep" x-model.number="salaryMin" @input="enforceSalaryBounds()" class="cw-range-input">
                                            <input type="range" :min="salaryMinBound" :max="salaryMaxBound" :step="salaryStep" x-model.number="salaryMax" @input="enforceSalaryBounds()" class="cw-range-input">
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="cw-label" for="category">{{ __('ui.jobs_page.category_label') }}</label>
                                    <select id="category" class="cw-field cw-field-premium" x-model="categoryValue">
                                        <option value="">{{ __('ui.jobs_page.all_categories') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}">{{ cw_localize_job_value('category', $category) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="md:col-span-2 xl:col-span-3">
                                    <p class="cw-filter-heading">{{ __('ui.jobs_page.quick_filters_title') }}</p>
                                    <div class="cw-pill-group">
                                        <button type="button" class="cw-choice-pill" :class="quick.accommodation ? 'is-active is-accent' : ''" @click="toggleQuick('accommodation')">{{ __('ui.jobs_page.accommodation_label') }}</button>
                                        <button type="button" class="cw-choice-pill" :class="quick.visa_support ? 'is-active is-accent' : ''" @click="toggleQuick('visa_support')">{{ __('ui.jobs_page.visa_support_label') }}</button>
                                        <button type="button" class="cw-choice-pill" :class="quick.featured ? 'is-active is-accent' : ''" @click="toggleQuick('featured')">{{ __('ui.jobs_page.featured_label') }}</button>
                                        <button type="button" class="cw-choice-pill" :class="quick.urgent ? 'is-active is-accent' : ''" @click="toggleQuick('urgent')">{{ __('ui.jobs_page.urgent_label') }}</button>
                                        <button type="button" class="cw-choice-pill" :class="quick.english ? 'is-active is-accent' : ''" @click="toggleQuick('english')">{{ __('ui.jobs_page.english_speaking') }}</button>
                                        <button type="button" class="cw-choice-pill" :class="quick.beginner ? 'is-active is-accent' : ''" @click="toggleQuick('beginner')">{{ __('ui.jobs_page.beginner_friendly') }}</button>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 flex flex-wrap items-center gap-2">
                                <button
                                    class="cw-button-primary"
                                    type="submit"
                                    data-cw-track-click="filter_apply"
                                    data-cw-item-type="job"
                                    :disabled="submitting"
                                >
                                    <span data-cw-submit-label data-default-label="{{ __('ui.jobs_page.apply_filters') }}" data-loading-label="Applying..." aria-live="polite">{{ __('ui.jobs_page.apply_filters') }}</span>
                                </button>
                                <a href="{{ route('jobs.index') }}" class="cw-button-secondary" data-cw-track-click="filter_clear" data-cw-item-type="job">{{ __('ui.jobs_page.reset_filters') }}</a>
                                <button type="button" class="cw-button-secondary md:hidden" data-cw-filter-close aria-controls="job-advanced-filters" @click="mobilePanelOpen = false">{{ __('ui.jobs_page.done_filters') }}</button>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="cw-mobile-filter-bar md:hidden">
                    <p class="text-sm text-slate-600">
                        <span class="font-semibold text-slate-900">{{ number_format($jobs->total()) }}</span>
                        {{ trans_choice('ui.jobs_page.filter_results_count', $jobs->total()) }}
                    </p>
                    <button class="cw-button-primary" type="submit" :disabled="submitting">{{ __('ui.jobs_page.show_results') }}</button>
                </div>

                <button type="button" class="cw-button-primary cw-filter-fab md:hidden" data-cw-filter-open aria-controls="job-advanced-filters" @click="mobilePanelOpen = true">
                    {{ __('ui.jobs_page.filters_button') }}
                </button>
            </form>

            @if(count($activeChips) > 0)
                <div class="mb-5 flex flex-wrap items-center gap-2">
                    @foreach($activeChips as $chip)
                        <x-filter-chip :href="$chip['href']" :label="$chip['label']" :tone="$chip['tone']" />
                    @endforeach
                    <a href="{{ route('jobs.index') }}" class="cw-button-secondary !px-3 !py-2 text-xs">{{ __('ui.jobs_page.clear_all_filters') }}</a>
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
