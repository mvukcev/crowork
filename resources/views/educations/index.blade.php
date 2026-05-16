<x-app-layout>
    <x-slot name="title">Education Pathways</x-slot>
    <x-slot name="description">Explore language and certification pathways for work in Croatia.</x-slot>
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
                <p class="cw-kicker mb-2">Education</p>
                <h1 class="cw-display text-3xl md:text-5xl mb-3">Build skills for relocation and work.</h1>
                <p class="text-base text-slate-600">Find programs by city, online format, dates, and price.</p>
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
                        'label' => 'Online only',
                        'href' => route('educations.index', $removeFilterQuery('is_online')),
                        'tone' => 'orange',
                    ];
                }

                if (filled($filters['start_from'] ?? null)) {
                    $activeChips[] = [
                        'label' => 'Starts from ' . \Illuminate\Support\Carbon::parse((string) $filters['start_from'])->format('M j, Y'),
                        'href' => route('educations.index', $removeFilterQuery('start_from')),
                        'tone' => 'blue',
                    ];
                }

                if (filled($filters['price_max'] ?? null)) {
                    $activeChips[] = [
                        'label' => 'Up to EUR ' . number_format((int) $filters['price_max']),
                        'href' => route('educations.index', $removeFilterQuery('price_max')),
                        'tone' => 'orange',
                    ];
                }
            @endphp

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
                                <span class="font-semibold text-slate-900">{{ number_format($educations->total()) }}</span>
                                <span>{{ \Illuminate\Support\Str::plural('program', $educations->total()) }}</span>
                            </div>
                            @if(count($activeChips) > 0)
                                <a href="{{ route('educations.index') }}" class="cw-button-secondary !px-3 !py-2 text-xs" data-cw-track-click="education_filter_reset">Clear all</a>
                            @endif
                        </div>

                        <div class="cw-filter-topbar">
                            <div>
                                <label class="cw-label" for="q">Search</label>
                                <input id="q" name="q" value="{{ $filters['q'] ?? request('q') }}" class="cw-field" placeholder="Program, topic, language">
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
                                    <span data-cw-submit-label data-default-label="Search" data-loading-label="Updating..." aria-live="polite">Search</span>
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
                                    <span>More filters</span>
                                    <span class="ml-1 text-xs text-slate-500" x-text="desktopAdvancedOpen ? 'Hide' : 'Show'"></span>
                                </button>
                            </div>
                        </div>

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
                                Advanced filters
                            </button>
                        </div>

                        <p class="text-xs text-slate-500 mt-2" x-show="submitting" x-cloak aria-live="polite">Refreshing education results...</p>
                    </div>

                    <div class="md:hidden" x-cloak>
                        <div class="cw-filter-overlay" data-cw-filter-overlay x-show="mobilePanelOpen" x-transition.opacity @click="mobilePanelOpen = false"></div>
                    </div>

                    <div
                        id="education-advanced-filters"
                        x-cloak
                        x-show="shouldShowPanel()"
                        style="display: none;"
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
                                <button type="button" class="cw-button-secondary !px-3 !py-2 text-xs" data-cw-filter-close aria-controls="education-advanced-filters" @click="mobilePanelOpen = false">Close</button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                <div>
                                    <p class="cw-filter-heading">Format</p>
                                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                        <input type="checkbox" name="is_online" value="1" class="rounded border-slate-300" @checked(($filters['is_online'] ?? request('is_online')))> 
                                        Online only
                                    </label>
                                </div>

                                <div>
                                    <p class="cw-filter-heading">Schedule</p>
                                    <label class="cw-label" for="start_from">Start from</label>
                                    <input id="start_from" name="start_from" value="{{ $filters['start_from'] ?? request('start_from') }}" class="cw-field" type="date">
                                </div>

                                <div>
                                    <p class="cw-filter-heading">Budget</p>
                                    <label class="cw-label" for="price_max">Max price (EUR)</label>
                                    <input id="price_max" name="price_max" value="{{ $filters['price_max'] ?? request('price_max') }}" class="cw-field" type="number" min="0" step="1" placeholder="e.g. 800">
                                </div>
                            </div>

                            <div class="mt-5 flex flex-wrap items-center gap-2">
                                <button
                                    class="cw-button-primary"
                                    type="submit"
                                    data-cw-track-click="education_filter_apply"
                                    :disabled="submitting"
                                >
                                    <span data-cw-submit-label data-default-label="Apply filters" data-loading-label="Applying..." aria-live="polite">Apply filters</span>
                                </button>
                                <a href="{{ route('educations.index') }}" class="cw-button-secondary" data-cw-track-click="education_filter_reset">Reset</a>
                                <button type="button" class="cw-button-secondary md:hidden" data-cw-filter-close aria-controls="education-advanced-filters" @click="mobilePanelOpen = false">Done</button>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="cw-button-primary cw-filter-fab md:hidden" data-cw-filter-open aria-controls="education-advanced-filters" @click="mobilePanelOpen = true">
                    Filters
                </button>
            </form>

            @if(count($activeChips) > 0)
                <div class="mb-5 flex flex-wrap items-center gap-2">
                    @foreach($activeChips as $chip)
                        <x-filter-chip :href="$chip['href']" :label="$chip['label']" :tone="$chip['tone']" />
                    @endforeach
                    <a href="{{ route('educations.index') }}" class="cw-button-secondary !px-3 !py-2 text-xs">Clear all</a>
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
