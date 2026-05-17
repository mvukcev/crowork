<x-app-layout>
    <x-slot name="title">{{ __('ui.educations_page.page_title') }}</x-slot>
    <x-slot name="description">{{ __('ui.educations_page.page_description') }}</x-slot>
    <x-slot name="canonical">{{ route('educations.index') }}</x-slot>

    @push('head')
        @if($educations->previousPageUrl())
            <link rel="prev" href="{{ $educations->previousPageUrl() }}">
        @endif
        @if($educations->nextPageUrl())
            <link rel="next" href="{{ $educations->nextPageUrl() }}">
        @endif
    @endpush

    <section class="cw-section">
        <div class="cw-container">
            <div class="cw-content-wide mb-8">
                <p class="cw-kicker mb-2">{{ __('ui.navigation.educations') }}</p>
                <h1 class="cw-display text-3xl md:text-5xl mb-3">{!! __('ui.educations_page.hero_headline') !!}</h1>
                <p class="text-base text-slate-600 max-w-2xl">{{ __('ui.educations_page.hero_subheadline') }}</p>
            </div>

            @php
                $removeFilterQuery = function (string $key): array {
                    $query = request()->query();
                    unset($query[$key], $query['page']);

                    return $query;
                };

                $priceMinBound = 0;
                $priceMaxBound = 5000;
                $priceStep = 25;
                $priceMinValue = (int) ($filters['price_min'] ?? request('price_min') ?? 0);
                $priceMaxValue = (int) ($filters['price_max'] ?? request('price_max') ?? $priceMaxBound);

                $priceMinValue = max($priceMinBound, min($priceMinValue, $priceMaxBound - $priceStep));
                $priceMaxValue = max($priceMinValue + $priceStep, min($priceMaxValue, $priceMaxBound));

                $activeChips = [];

                if (filled($filters['q'] ?? null)) {
                    $activeChips[] = [
                        'label' => __('ui.educations_page.search_chip', ['value' => $filters['q']]),
                        'href' => route('educations.index', $removeFilterQuery('q')),
                        'tone' => 'blue',
                    ];
                }

                if (filled($filters['city'] ?? null)) {
                    $activeChips[] = [
                        'label' => (string) $filters['city'],
                        'href' => route('educations.index', $removeFilterQuery('city')),
                        'tone' => 'blue',
                    ];
                }

                if (request()->input('is_online') == '1') {
                    $activeChips[] = [
                        'label' => __('ui.educations_page.online_only_label'),
                        'href' => route('educations.index', $removeFilterQuery('is_online')),
                        'tone' => 'orange',
                    ];
                }

                if (filled($filters['start_from'] ?? null)) {
                    $activeChips[] = [
                        'label' => __('ui.educations_page.starts_from_chip', [
                            'date' => \Illuminate\Support\Carbon::parse((string) $filters['start_from'])->translatedFormat('j M Y'),
                        ]),
                        'href' => route('educations.index', $removeFilterQuery('start_from')),
                        'tone' => 'blue',
                    ];
                }

                if (filled($filters['price_max'] ?? null)) {
                    $activeChips[] = [
                        'label' => __('ui.educations_page.up_to_eur_chip', ['amount' => number_format((int) $filters['price_max'])]),
                        'href' => route('educations.index', $removeFilterQuery('price_max')),
                        'tone' => 'orange',
                    ];
                }

                if (filled($filters['price_min'] ?? null)) {
                    $activeChips[] = [
                        'label' => __('ui.educations_page.from_eur_chip', ['amount' => number_format((int) $filters['price_min'])]),
                        'href' => route('educations.index', $removeFilterQuery('price_min')),
                        'tone' => 'orange',
                    ];
                }

                $topicLabels = [
                    'certificate' => __('ui.educations_page.quick_certificate'),
                    'beginner' => __('ui.educations_page.quick_beginner'),
                    'career' => __('ui.educations_page.quick_career_growth'),
                    'language' => __('ui.educations_page.quick_language_learning'),
                    'integration' => __('ui.educations_page.quick_integration'),
                    'croatian' => __('ui.educations_page.quick_croatian_language'),
                    'skills' => __('ui.educations_page.quick_professional_skills'),
                ];

                foreach ((array) ($filters['topics'] ?? request()->input('topics', [])) as $topic) {
                    if (!isset($topicLabels[$topic])) {
                        continue;
                    }

                    $query = request()->query();
                    $topics = array_values(array_filter((array) ($query['topics'] ?? []), fn ($item) => $item !== $topic));
                    if (empty($topics)) {
                        unset($query['topics']);
                    } else {
                        $query['topics'] = $topics;
                    }
                    unset($query['page']);

                    $activeChips[] = [
                        'label' => $topicLabels[$topic],
                        'href' => route('educations.index', $query),
                        'tone' => 'blue',
                    ];
                }
            @endphp

            @if(count($activeChips) === 0)
                <!-- Why CroWork Education section -->
                <div class="mb-8">
                    <h2 class="text-2xl md:text-3xl font-semibold text-slate-900 mb-6">{{ __('ui.educations_page.why_crowork_title') }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                        <!-- Practical learning -->
                        <div class="cw-surface p-6 rounded-lg border border-slate-200 hover:border-slate-300 transition-colors">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-indigo-100">
                                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-base font-semibold text-slate-900 mb-1">{{ __('ui.educations_page.why_crowork.practical_title') }}</h3>
                                    <p class="text-sm text-slate-600">{{ __('ui.educations_page.why_crowork.practical_desc') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Career growth -->
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
                                    <h3 class="text-base font-semibold text-slate-900 mb-1">{{ __('ui.educations_page.why_crowork.career_title') }}</h3>
                                    <p class="text-sm text-slate-600">{{ __('ui.educations_page.why_crowork.career_desc') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Workplace integration -->
                        <div class="cw-surface p-6 rounded-lg border border-slate-200 hover:border-slate-300 transition-colors">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-purple-100">
                                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2zm0 0h6v-2a6 6 0 00-9-5.656v2.656A4 4 0 0115 21z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-base font-semibold text-slate-900 mb-1">{{ __('ui.educations_page.why_crowork.integration_title') }}</h3>
                                    <p class="text-sm text-slate-600">{{ __('ui.educations_page.why_crowork.integration_desc') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Build confidence -->
                        <div class="cw-surface p-6 rounded-lg border border-slate-200 hover:border-slate-300 transition-colors">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="flex items-center justify-center h-12 w-12 rounded-lg bg-emerald-100">
                                        <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-base font-semibold text-slate-900 mb-1">{{ __('ui.educations_page.why_crowork.confidence_title') }}</h3>
                                    <p class="text-sm text-slate-600">{{ __('ui.educations_page.why_crowork.confidence_desc') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form
                method="GET"
                action="{{ route('educations.index') }}"
                data-cw-filter-form
                data-cw-filter-panel-id="education-advanced-filters"
                data-cw-track-submit="education_search"
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
                    formatValue: @js((string) request('is_online')),
                    startFromValue: @js((string) ($filters['start_from'] ?? request('start_from') ?? '')),
                    priceMin: @js($priceMinValue),
                    priceMax: @js($priceMaxValue),
                    priceMinBound: @js($priceMinBound),
                    priceMaxBound: @js($priceMaxBound),
                    priceStep: @js($priceStep),
                    topicChips: {
                        certificate: @js(in_array('certificate', (array) ($filters['topics'] ?? request()->input('topics', [])), true)),
                        beginner: @js(in_array('beginner', (array) ($filters['topics'] ?? request()->input('topics', [])), true)),
                        career: @js(in_array('career', (array) ($filters['topics'] ?? request()->input('topics', [])), true)),
                        language: @js(in_array('language', (array) ($filters['topics'] ?? request()->input('topics', [])), true)),
                        integration: @js(in_array('integration', (array) ($filters['topics'] ?? request()->input('topics', [])), true)),
                        croatian: @js(in_array('croatian', (array) ($filters['topics'] ?? request()->input('topics', [])), true)),
                        skills: @js(in_array('skills', (array) ($filters['topics'] ?? request()->input('topics', [])), true)),
                    },
                    init() {
                        this.loadRecentCities();
                        if (this.cityValue) {
                            this.rememberCity(this.cityValue);
                        }
                        this.enforcePriceBounds();
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
                    enforcePriceBounds() {
                        if (this.priceMin > this.priceMax - this.priceStep) {
                            this.priceMin = this.priceMax - this.priceStep;
                        }
                        if (this.priceMax < this.priceMin + this.priceStep) {
                            this.priceMax = this.priceMin + this.priceStep;
                        }
                    },
                    priceFillStyle() {
                        const total = this.priceMaxBound - this.priceMinBound;
                        const left = ((this.priceMin - this.priceMinBound) / total) * 100;
                        const width = ((this.priceMax - this.priceMin) / total) * 100;
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
                        localStorage.setItem('cw_recent_education_cities', JSON.stringify(next));
                    },
                    loadRecentCities() {
                        try {
                            const saved = JSON.parse(localStorage.getItem('cw_recent_education_cities') || '[]');
                            if (Array.isArray(saved)) {
                                this.recentCities = saved.filter((city) => this.cities.includes(city)).slice(0, 3);
                            }
                        } catch (_) {
                            this.recentCities = [];
                        }
                    },
                    toggleTopic(topic) {
                        this.topicChips[topic] = !this.topicChips[topic];
                    }
                }"
                @submit="submitting = true"
                @keydown.escape.window="closePanel()"
            >
                <input type="hidden" name="city" :value="cityValue" :disabled="!cityValue">
                <input type="hidden" name="price_min" :value="priceMin" :disabled="priceMin <= priceMinBound">
                <input type="hidden" name="price_max" :value="priceMax" :disabled="priceMax >= priceMaxBound">
                <input type="hidden" name="start_from" :value="startFromValue" :disabled="!startFromValue">
                <input type="hidden" name="is_online" :value="formatValue" :disabled="formatValue !== '1'">
                <input type="checkbox" name="topics[]" value="certificate" class="hidden" :checked="topicChips.certificate">
                <input type="checkbox" name="topics[]" value="beginner" class="hidden" :checked="topicChips.beginner">
                <input type="checkbox" name="topics[]" value="career" class="hidden" :checked="topicChips.career">
                <input type="checkbox" name="topics[]" value="language" class="hidden" :checked="topicChips.language">
                <input type="checkbox" name="topics[]" value="integration" class="hidden" :checked="topicChips.integration">
                <input type="checkbox" name="topics[]" value="croatian" class="hidden" :checked="topicChips.croatian">
                <input type="checkbox" name="topics[]" value="skills" class="hidden" :checked="topicChips.skills">

                <div class="cw-filter-shell">
                    <div class="cw-surface p-4 md:p-5">
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <div class="text-sm text-slate-600">
                                <span class="font-semibold text-slate-900">{{ number_format($educations->total()) }}</span>
                                <span>{{ trans_choice('ui.educations_page.filter_results_count', $educations->total()) }}</span>
                            </div>
                            @if(count($activeChips) > 0)
                                <a href="{{ route('educations.index') }}" class="cw-button-secondary !px-3 !py-2 text-xs" data-cw-track-click="education_filter_reset">{{ __('ui.educations_page.reset_filters') }}</a>
                            @endif
                        </div>

                        <div class="cw-filter-topbar-modern">
                            <div>
                                <label class="cw-label" for="q">{{ __('ui.educations_page.search_educations_label') }}</label>
                                <input id="q" name="q" value="{{ $filters['q'] ?? request('q') }}" class="cw-field cw-field-premium" placeholder="{{ __('ui.educations_page.search_placeholder') }}">
                            </div>
                            <div class="cw-modern-select" @click.outside="cityOpen = false">
                                <label class="cw-label">{{ __('ui.educations_page.city_label') }}</label>
                                <button type="button" class="cw-field cw-field-premium w-full text-left flex items-center justify-between" @click="cityOpen = !cityOpen" :aria-expanded="cityOpen ? 'true' : 'false'" aria-controls="education-city-popover">
                                    <span x-text="cityValue || @js(__('ui.educations_page.all_cities'))"></span>
                                    <span class="text-xs text-slate-500">▾</span>
                                </button>
                                <div id="education-city-popover" x-show="cityOpen" x-cloak x-transition.opacity.scale.origin.top class="cw-select-popover mt-2">
                                    <input type="text" x-model="citySearch" class="cw-field cw-field-premium mb-2" :placeholder="@js(__('ui.educations_page.city_search_placeholder'))">
                                    <template x-if="recentCities.length">
                                        <div class="mb-2">
                                            <p class="cw-filter-heading !mb-2">{{ __('ui.educations_page.recent_locations') }}</p>
                                            <div class="flex flex-wrap gap-2">
                                                <template x-for="city in recentCities" :key="`recent-${city}`">
                                                    <button type="button" class="cw-filter-chip" @click="selectCity(city)" x-text="city"></button>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                    <div class="max-h-52 overflow-auto pr-1">
                                        <button type="button" class="cw-select-option" @click="clearCity()">{{ __('ui.educations_page.all_cities') }}</button>
                                        <template x-for="city in filteredCities()" :key="city">
                                            <button type="button" class="cw-select-option" @click="selectCity(city)" x-text="city"></button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-end gap-2">
                                <button
                                    class="cw-button-primary min-w-[132px]"
                                    type="submit"
                                    :disabled="submitting"
                                >
                                    <span data-cw-submit-label data-default-label="{{ __('ui.educations_page.search_button') }}" data-loading-label="{{ __('ui.educations_page.updating_results') }}" aria-live="polite">{{ __('ui.educations_page.search_button') }}</span>
                                </button>
                                <button
                                    type="button"
                                    class="cw-button-secondary hidden md:inline-flex"
                                    data-cw-filter-toggle
                                    aria-controls="education-advanced-filters"
                                    :aria-expanded="desktopAdvancedOpen ? 'true' : 'false'"
                                    @click="desktopAdvancedOpen = !desktopAdvancedOpen"
                                    data-cw-track-click="education_filter_open"
                                >
                                    <span>{{ __('ui.educations_page.more_filters') }}</span>
                                    <span class="ml-1 text-xs text-slate-500" x-text="desktopAdvancedOpen ? @js(__('ui.educations_page.toggle_hide')) : @js(__('ui.educations_page.toggle_show'))"></span>
                                </button>
                            </div>
                        </div>

                        <!-- Advanced filters panel: moved all filters except education name and city here -->
                        <template x-if="shouldShowPanel()">
                            <div id="education-advanced-filters" class="mt-4 space-y-4">
                                <!-- All advanced filters moved here -->
                                <div>
                                    <p class="cw-filter-heading">{{ __('ui.educations_page.primary_filters') }}</p>
                                    <div class="cw-pill-group">
                                        <button type="button" class="cw-choice-pill" :class="formatValue === '' ? 'is-active' : ''" @click="formatValue = ''">{{ __('ui.educations_page.online_offline') }}</button>
                                        <button type="button" class="cw-choice-pill" :class="formatValue === '1' ? 'is-active is-accent' : ''" @click="formatValue = '1'">{{ __('ui.educations_page.online_only_label') }}</button>
                                    </div>
                                </div>
                                <div class="cw-date-card">
                                    <div class="flex items-center justify-between gap-3 mb-2">
                                        <label class="cw-label mb-0" for="start_from_input">{{ __('ui.educations_page.start_date_label') }}</label>
                                        <button type="button" class="text-xs text-slate-500 hover:text-slate-700" @click="startFromValue = ''">{{ __('ui.educations_page.clear_date') }}</button>
                                    </div>
                                    <input id="start_from_input" :value="startFromValue" @input="startFromValue = $event.target.value" class="cw-field cw-field-premium cw-date-input" type="date">
                                </div>
                                <div class="cw-range-card">
                                    <div class="flex items-center justify-between gap-3">
                                        <label class="cw-label mb-0">{{ __('ui.educations_page.price_range_label') }}</label>
                                        <p class="text-sm font-medium text-slate-700" x-text="`€${Number(priceMin).toLocaleString()} - €${Number(priceMax).toLocaleString()}`"></p>
                                    </div>
                                    <div class="cw-range-control mt-3">
                                        <div class="cw-range-track"></div>
                                        <div class="cw-range-fill" :style="priceFillStyle()"></div>
                                        <input type="range" :min="priceMinBound" :max="priceMaxBound" :step="priceStep" x-model.number="priceMin" @input="enforcePriceBounds()" class="cw-range-input">
                                        <input type="range" :min="priceMinBound" :max="priceMaxBound" :step="priceStep" x-model.number="priceMax" @input="enforcePriceBounds()" class="cw-range-input">
                                    </div>
                                </div>
                                <div>
                                    <p class="cw-filter-heading">{{ __('ui.educations_page.quick_filters_title') }}</p>
                                    <div class="cw-pill-group">
                                        <button type="button" class="cw-choice-pill" :class="formatValue === '1' ? 'is-active is-accent' : ''" @click="formatValue = formatValue === '1' ? '' : '1'">{{ __('ui.educations_page.quick_online') }}</button>
                                        <button type="button" class="cw-choice-pill" :class="topicChips.certificate ? 'is-active is-accent' : ''" @click="toggleTopic('certificate')">{{ __('ui.educations_page.quick_certificate') }}</button>
                                        <button type="button" class="cw-choice-pill" :class="topicChips.beginner ? 'is-active is-accent' : ''" @click="toggleTopic('beginner')">{{ __('ui.educations_page.quick_beginner') }}</button>
                                        <button type="button" class="cw-choice-pill" :class="topicChips.career ? 'is-active is-accent' : ''" @click="toggleTopic('career')">{{ __('ui.educations_page.quick_career_growth') }}</button>
                                        <button type="button" class="cw-choice-pill" :class="topicChips.language ? 'is-active is-accent' : ''" @click="toggleTopic('language')">{{ __('ui.educations_page.quick_language_learning') }}</button>
                                        <button type="button" class="cw-choice-pill" :class="topicChips.integration ? 'is-active is-accent' : ''" @click="toggleTopic('integration')">{{ __('ui.educations_page.quick_integration') }}</button>
                                        <button type="button" class="cw-choice-pill" :class="topicChips.croatian ? 'is-active is-accent' : ''" @click="toggleTopic('croatian')">{{ __('ui.educations_page.quick_croatian_language') }}</button>
                                        <button type="button" class="cw-choice-pill" :class="topicChips.skills ? 'is-active is-accent' : ''" @click="toggleTopic('skills')">{{ __('ui.educations_page.quick_professional_skills') }}</button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Only the search and city fields are always visible. Advanced filters are only rendered in the panel above. -->

                        <div class="md:hidden mt-3">
                            <button
                                type="button"
                                class="cw-button-secondary w-full"
                                data-cw-filter-open
                                @click="mobilePanelOpen = true"
                                    data-cw-track-click="education_filter_open"
                                aria-controls="education-advanced-filters"
                                :aria-expanded="mobilePanelOpen ? 'true' : 'false'"
                            >
                                {{ __('ui.educations_page.more_filters') }}
                            </button>
                        </div>

                        <p class="text-xs text-slate-500 mt-2" x-show="submitting" x-cloak aria-live="polite">{{ __('ui.educations_page.refreshing_results') }}</p>
                    </div>

                    <div class="md:hidden" x-cloak>
                        <div class="cw-filter-overlay" data-cw-filter-overlay x-show="mobilePanelOpen" x-transition.opacity @click="mobilePanelOpen = false"></div>
                    </div>

                    <!-- Removed empty advanced filters container at the bottom. -->
                </div>

                <div class="cw-mobile-filter-bar md:hidden">
                    <p class="text-sm text-slate-600">
                        <span class="font-semibold text-slate-900">{{ number_format($educations->total()) }}</span>
                        {{ trans_choice('ui.educations_page.filter_results_count', $educations->total()) }}
                    </p>
                    <button class="cw-button-primary" type="submit" :disabled="submitting">{{ __('ui.educations_page.show_results') }}</button>
                </div>

                <button type="button" class="cw-button-primary cw-filter-fab md:hidden" data-cw-filter-open aria-controls="education-advanced-filters" @click="mobilePanelOpen = true">
                    {{ __('ui.educations_page.filters_button') }}
                </button>
            </form>

            @if(count($activeChips) > 0)
                <div class="mb-5 flex flex-wrap items-center gap-2">
                    @foreach($activeChips as $chip)
                        <x-filter-chip :href="$chip['href']" :label="$chip['label']" :tone="$chip['tone']" />
                    @endforeach
                    <a href="{{ route('educations.index') }}" class="cw-button-secondary !px-3 !py-2 text-xs">{{ __('ui.educations_page.reset_filters') }}</a>
                </div>
            @endif

            @include('educations._results', ['educations' => $educations])
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                window.cwTrack?.('education_search_results_view', {
                    page: {{ $educations->currentPage() }},
                    total_results: {{ $educations->total() }}
                });
            });
        </script>
    @endpush
</x-app-layout>
