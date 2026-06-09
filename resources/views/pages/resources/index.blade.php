<x-app-layout>
    <x-slot name="title">{{ __('resources.headline') }}</x-slot>
    <x-slot name="description">{{ __('resources.supporting') }}</x-slot>
    <x-slot name="canonical">{{ route('resources.index') }}</x-slot>

    @php
        $heroHeadline = __('resources.headline');
        if (app()->getLocale() === 'hr') {
            $heroHeadline = str_replace(['rad', 'preseljenje'], [
                '<span class="resources-highlight">rad</span>',
                '<span class="resources-highlight">preseljenje</span>',
            ], $heroHeadline);
        } else {
            $heroHeadline = str_replace(['working', 'relocating'], [
                '<span class="resources-highlight">working</span>',
                '<span class="resources-highlight">relocating</span>',
            ], $heroHeadline);
        }

        $slugCategoryMap = [
            'work-permits' => 'work_permits',
            'documents-needed' => 'documents',
            'accommodation' => 'housing',
            'working-in-croatia' => 'rights',
            'employer-obligations' => 'employer_support',
            'faq-foreign-workers' => 'faq',
        ];

        $faqItems = __('resources.faq.items');

        $guideImageMap = [
            'work-permits' => 'resources.guide_01',
            'documents-needed' => 'resources.guide_02',
            'accommodation' => 'resources.guide_03',
            'working-in-croatia' => 'resources.guide_04',
            'employer-obligations' => 'resources.guide_05',
            'faq-foreign-workers' => 'resources.guide_06',
        ];

        $resourcesCollectionSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => __('resources.headline'),
            'description' => __('resources.supporting'),
            'url' => route('resources.index'),
            'inLanguage' => app()->getLocale(),
            'mainEntity' => [
                '@type' => 'ItemList',
                'numberOfItems' => count($resources),
                'itemListElement' => collect($resources)->values()->map(function ($resource, $index) {
                    return [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'url' => route('resources.show', $resource['slug']),
                        'name' => $resource['title'],
                    ];
                })->all(),
            ],
        ];
    @endphp

    @push('head')
        <script type="application/ld+json">{!! json_encode($resourcesCollectionSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        <script type="application/ld+json">{!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => __('ui.navigation.home'),
                    'item' => route('home'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => __('resources.headline'),
                    'item' => route('resources.index'),
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

    <div x-data="{ query: '', activeCat: 'all', visibleGuides: {{ count($resources) }}, refreshVisibleGuides() { this.$nextTick(() => { this.visibleGuides = Array.from(document.querySelectorAll('[data-cw-resource-guide]')).filter((el) => el.style.display !== 'none').length; }); } }" x-init="refreshVisibleGuides()" @input.debounce.120ms="refreshVisibleGuides()" @click="refreshVisibleGuides()">
    <section class="cw-section relative overflow-hidden">
        <div class="cw-container">
            <div class="resources-hero-frame relative overflow-hidden rounded-3xl border border-white/60 shadow-[0_20px_70px_rgba(15,23,42,0.08)]">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_25%_20%,rgba(139,132,215,0.30),transparent_55%),radial-gradient(circle_at_80%_70%,rgba(254,80,0,0.24),transparent_48%),linear-gradient(180deg,rgba(15,23,42,0.56),rgba(15,23,42,0.72))]"></div>

                <div class="relative z-10 px-6 py-12 md:px-10 md:py-16 lg:px-14 lg:py-20">
                    <p class="cw-kicker mb-3 text-white">{{ __('resources.eyebrow') }}</p>
                    <h1 class="cw-display text-3xl md:text-5xl leading-[0.98] max-w-4xl" style="color: #fff;">{!! $heroHeadline !!}</h1>
                    <p class="mt-5 text-base md:text-lg max-w-3xl" style="color: rgba(255, 255, 255, 0.92);">{{ __('resources.supporting') }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cw-section">
        <div class="cw-container">
            <div class="mb-8">
                <h2 class="cw-display text-3xl md:text-5xl">{{ __('resources.featured_title') }}</h2>
                <p class="mt-3 text-slate-600 max-w-2xl">{{ __('resources.featured_subtitle') }}</p>
            </div>

            <div class="mb-8">
                <div class="resources-search-wrap px-0 py-0">
                    <label for="resources-search" class="sr-only">{{ __('resources.search_placeholder') }}</label>
                    <input id="resources-search" type="text" x-model="query" data-cw-resource-search class="resources-search-input cw-input w-full px-5 py-3 rounded-2xl bg-white/90 backdrop-blur shadow-[0_12px_40px_rgba(15,23,42,0.08)]" placeholder="{{ __('resources.search_placeholder') }}" />
                    <div class="mt-3 overflow-x-auto">
                        <div class="resources-pill-row flex items-center gap-2 min-w-max">
                            <button type="button" @click="activeCat='all'" :class="activeCat==='all' ? 'bg-brand-violet text-white' : 'bg-brand-violet-soft text-brand-violet'" class="resources-pill rounded-full px-4 py-2 text-sm font-medium transition">{{ __('resources.filter_all') }}</button>
                            @foreach(__('resources.categories') as $key => $label)
                                <button type="button" @click="activeCat='{{ $key }}'" :class="activeCat==='{{ $key }}' ? 'bg-brand-violet text-white' : 'bg-brand-violet-soft text-brand-violet'" class="resources-pill rounded-full px-4 py-2 text-sm font-medium transition">{{ $label }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach($resources as $resource)
                    @php
                        $categoryKey = $resource['category_key'] ?? ($slugCategoryMap[$resource['slug']] ?? 'onboarding');
                        $readTime = $resource['read_time'] ?? __('resources.read_times.' . $resource['slug']);
                        if ($readTime === 'resources.read_times.' . $resource['slug']) {
                            $readTime = __('resources.read_time_default');
                        }
                        $guideImageKey = $guideImageMap[$resource['slug']] ?? 'resources.guide_06';
                        $cardImage = ! empty($resource['featured_image_path'])
                            ? \Illuminate\Support\Facades\Storage::disk('public')->url($resource['featured_image_path'])
                            : marketing_image_url($guideImageKey);
                        $cardImageAlt = ! empty($resource['featured_image_path'])
                            ? $resource['title']
                            : marketing_image_alt($guideImageKey);
                    @endphp
                    <article
                        x-show="($el.dataset.title.toLowerCase().includes(query.toLowerCase()) || $el.dataset.description.toLowerCase().includes(query.toLowerCase())) && (activeCat === 'all' || activeCat === '{{ $categoryKey }}')"
                        data-title="{{ strtolower($resource['title']) }}"
                        data-description="{{ strtolower($resource['description']) }}"
                        data-cw-resource-guide
                        class="resources-guide-card group relative overflow-hidden rounded-3xl border border-slate-200/70 bg-white shadow-[0_12px_30px_rgba(15,23,42,0.06)] hover:shadow-[0_20px_45px_rgba(15,23,42,0.10)] hover:-translate-y-1 transition"
                    >
                        <img src="{{ $cardImage }}" alt="{{ $cardImageAlt }}" class="h-44 w-full object-cover" loading="lazy" decoding="async" width="800" height="600" />
                        <div class="absolute inset-x-0 top-0 h-20 bg-gradient-to-b from-black/35 to-transparent"></div>
                        <div class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-slate-700">{{ __('resources.categories.' . $categoryKey) }}</div>
                        <div class="p-5">
                            <h3 class="text-xl font-semibold text-slate-900">{{ $resource['title'] }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $resource['description'] }}</p>
                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-xs text-slate-500">{{ $readTime }}</span>
                                <a href="{{ route('resources.show', $resource['slug']) }}" class="cw-button-secondary" data-cw-track-click="guide_open" data-cw-item-type="resource_guide" data-cw-item-slug="{{ $resource['slug'] }}">{{ __('resources.cta.read_guide') }}</a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div x-show="visibleGuides === 0" x-cloak class="mt-6">
                <x-empty-state
                    icon="search"
                    :title="__('resources.search_placeholder')"
                    :description="__('common.adjust_filters_or_search')"
                    :actionHref="route('resources.index')"
                    :actionLabel="__('ui.jobs_page.reset_filters')"
                />
            </div>
        </div>
    </section>
    </div>

    <section class="cw-section">
        <div class="cw-container">
            <div class="cw-surface p-6 md:p-10 rounded-3xl overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-[420px_minmax(0,1fr)] gap-8 lg:gap-10 items-start">
                    <div>
                        <h2 class="cw-display text-3xl md:text-5xl">{{ __('resources.relocation_journey.title') }}</h2>
                        <p class="mt-3 text-slate-600">{{ __('resources.relocation_journey.subtitle') }}</p>
                        <img src="{{ marketing_image_url('resources.relocation_path') }}" alt="{{ marketing_image_alt('resources.relocation_path') }}" class="mt-6 rounded-2xl w-full object-cover shadow-sm" loading="lazy" decoding="async" width="1200" height="800" />
                    </div>
                    <ol class="space-y-4">
                        @foreach(__('resources.relocation_journey.steps') as $index => $step)
                            <li class="flex items-center gap-3">
                                <span class="resources-step-badge flex h-10 w-10 shrink-0 items-center justify-center rounded-full border text-sm font-semibold">{{ $index + 1 }}</span>
                                <article class="flex-1 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                                    <h3 class="text-base font-semibold text-slate-900">{{ $step['title'] }}</h3>
                                    <p class="mt-1 text-sm text-slate-600">{{ $step['desc'] }}</p>
                                </article>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="cw-section">
        <div class="cw-container">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-10 items-stretch">
                <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_12px_28px_rgba(15,23,42,0.06)]">
                    <img src="{{ marketing_image_url('resources.life_work') }}" alt="{{ marketing_image_alt('resources.life_work') }}" class="h-full w-full object-cover" loading="lazy" decoding="async" width="1200" height="800" />
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/55 via-slate-900/20 to-transparent"></div>
                    <blockquote class="absolute bottom-0 p-6 md:p-8 text-white">
                        <p class="text-sm uppercase tracking-[0.08em] text-brand-violet">{{ __('resources.life_work.title') }}</p>
                        <p class="mt-2 text-lg leading-relaxed">{{ __('resources.life_work.quote') }}</p>
                    </blockquote>
                </div>

                <div class="cw-surface p-6 md:p-8 rounded-3xl">
                    <h2 class="cw-display text-3xl md:text-5xl">{{ __('resources.life_work.title') }}</h2>
                    <p class="mt-3 text-slate-600">{{ __('resources.life_work.subtitle') }}</p>

                    <div class="mt-6 space-y-3">
                        @foreach(__('resources.life_work.topics') as $topic)
                            <article class="rounded-2xl border border-slate-200 bg-white p-4">
                                <h3 class="text-base font-semibold text-brand-violet">{{ $topic['title'] }}</h3>
                                <p class="mt-1 text-sm text-slate-600">{{ $topic['desc'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cw-section">
        <div class="cw-container">
            <div class="cw-surface p-6 md:p-10 rounded-3xl" x-data="{ open: null, search: '' }">
                <h2 class="cw-display text-3xl md:text-5xl">{{ __('resources.faq.title') }}</h2>
                <p class="mt-3 text-slate-600 max-w-3xl">{{ __('resources.faq.subtitle') }}</p>

                <div class="mt-6 grid grid-cols-1 lg:grid-cols-[360px_minmax(0,1fr)] gap-6 lg:gap-8 items-start">
                    <img src="{{ marketing_image_url('resources.faq_help') }}" alt="{{ marketing_image_alt('resources.faq_help') }}" class="rounded-2xl w-full object-cover" loading="lazy" decoding="async" width="800" height="600" />

                    <div>
                        <input type="text" x-model="search" class="cw-input w-full" placeholder="{{ __('resources.search_placeholder') }}" />

                        <div class="mt-4 space-y-3">
                            <template x-for="(item, idx) in {{ json_encode($faqItems, JSON_UNESCAPED_UNICODE) }}.filter(x => x.q.toLowerCase().includes(search.toLowerCase()))" :key="idx">
                                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                                    <button
                                        type="button"
                                        @click="open === idx ? open = null : open = idx"
                                        class="w-full text-left px-4 py-4 flex items-start justify-between gap-4"
                                        data-cw-faq-toggle
                                        data-cw-faq-section="resources_index"
                                        :aria-expanded="open === idx ? 'true' : 'false'"
                                        :aria-controls="`cw-resource-faq-panel-${idx}`"
                                        :id="`cw-resource-faq-trigger-${idx}`"
                                    >
                                        <span class="font-medium text-slate-900" x-text="item.q"></span>
                                        <span class="text-brand-violet" x-text="open === idx ? '−' : '+'"></span>
                                    </button>
                                    <div
                                        x-show="open === idx"
                                        class="px-4 pb-4 text-sm text-slate-600"
                                        x-text="item.a"
                                        role="region"
                                        :id="`cw-resource-faq-panel-${idx}`"
                                        :aria-labelledby="`cw-resource-faq-trigger-${idx}`"
                                    ></div>
                                </article>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                window.cwTrack?.('resource_view', {
                    page_type: 'resources_index',
                    resource_count: {{ count($resources) }},
                });
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .resources-highlight {
                color: #fe5000;
                font-weight: 600;
            }

            .resources-step-badge {
                border-color: rgba(139, 132, 215, 0.36);
                background-color: #8b84d7;
                color: #ffffff;
            }

            .resources-hero-frame,
            .resources-guide-card {
                border-radius: 1.5rem !important;
                overflow: hidden;
            }

            .resources-search-wrap,
            .resources-pill,
            .resources-pill-row {
                border: 0 !important;
            }

            .resources-search-input {
                border: 0 !important;
                padding: 0.85rem 1.1rem !important;
            }
        </style>
    @endpush
</x-app-layout>