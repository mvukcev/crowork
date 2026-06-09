<x-app-layout>
    <x-slot name="title">{{ __('about.seo.title') }}</x-slot>
    <x-slot name="description">{{ __('about.seo.description') }}</x-slot>
    <x-slot name="canonical">{{ route('about') }}</x-slot>

    @push('head')
        <script type="application/ld+json">{!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'AboutPage',
            'name' => __('about.seo.title'),
            'description' => __('about.seo.description'),
            'url' => route('about'),
            'inLanguage' => app()->getLocale(),
            'about' => [
                '@type' => 'Organization',
                'name' => config('app.name', 'CroWork'),
                'url' => route('home'),
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
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
                    'name' => __('ui.navigation.about'),
                    'item' => route('about'),
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

    <section class="cw-section relative overflow-hidden">
        <div class="cw-container">
            <div class="about-hero-frame relative overflow-hidden rounded-3xl border border-white/50 shadow-[0_20px_70px_rgba(15,23,42,0.12)]">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_15%,rgba(59,130,246,0.32),transparent_52%),radial-gradient(circle_at_75%_70%,rgba(124,58,237,0.30),transparent_48%),linear-gradient(180deg,rgba(2,6,23,0.56),rgba(2,6,23,0.78))]"></div>

                <div class="relative z-10 px-6 py-12 md:px-10 md:py-16 lg:px-14 lg:py-20">
                    <p class="cw-kicker text-white/80 mb-3">{{ __('about.hero.eyebrow') }}</p>
                    <h1 class="cw-display text-3xl md:text-5xl max-w-4xl text-white">{!! __('about.hero.headline_html', [
                        'highlight_1' => '<span class="about-h1-highlight">' . __('about.hero.headline_highlight_1') . '</span>',
                        'highlight_2' => '<span class="about-h1-highlight">' . __('about.hero.headline_highlight_2') . '</span>',
                    ]) !!}</h1>
                    <p class="mt-5 text-base md:text-lg max-w-3xl" style="color:rgba(255,255,255,0.92);">{{ __('about.hero.supporting') }}</p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('jobs.index') }}" class="cw-button-accent">{{ __('about.hero.cta_jobs') }}</a>
                        <a href="{{ route('for-employers') }}" class="cw-button-secondary about-hero-secondary-cta bg-white/10 border-white/30 text-white hover:bg-white/20">{{ __('about.hero.cta_employers') }}</a>
                    </div>

                    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-3 max-w-3xl">
                        <article class="rounded-2xl bg-white/15 backdrop-blur border border-white/20 p-4">
                            <h2 class="text-base font-semibold text-white">{{ __('about.hero.card_1_title') }}</h2>
                            <p class="about-hero-card-text mt-1 text-sm">{{ __('about.hero.card_1_text') }}</p>
                        </article>
                        <article class="rounded-2xl bg-white/15 backdrop-blur border border-white/20 p-4">
                            <h2 class="text-base font-semibold text-white">{{ __('about.hero.card_2_title') }}</h2>
                            <p class="about-hero-card-text mt-1 text-sm">{{ __('about.hero.card_2_text') }}</p>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cw-section">
        <div class="cw-container">
            <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_420px] gap-8 items-start">
                <article class="cw-surface p-7 md:p-9 rounded-3xl">
                    <h2 class="cw-display text-3xl md:text-5xl">{{ __('about.why.headline') }}</h2>
                    <p class="mt-4 text-slate-700 leading-relaxed">{{ __('about.why.body_1') }}</p>
                    <p class="mt-4 text-slate-700 leading-relaxed">{{ __('about.why.body_2') }}</p>
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach(__('about.why.points') as $point)
                            <div class="rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-700">{{ $point }}</div>
                        @endforeach
                    </div>
                </article>
                <aside class="about-visual-frame relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <img src="{{ marketing_image_url('about.fragmented_work') }}" alt="{{ marketing_image_alt('about.fragmented_work') }}" class="about-visual-image w-full h-full object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/55 via-slate-900/20 to-transparent"></div>
                </aside>
            </div>
        </div>
    </section>

    <section class="cw-section">
        <div class="cw-container">
            <h2 class="cw-display text-3xl md:text-5xl mb-6">{{ __('about.what.headline') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach(__('about.what.items') as $item)
                    <article class="about-what-card group relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_12px_28px_rgba(15,23,42,0.06)] hover:-translate-y-1 hover:shadow-[0_20px_42px_rgba(15,23,42,0.10)] transition">
                        <div class="absolute -top-10 -right-10 h-28 w-28 rounded-full bg-gradient-to-br from-violet-200/50 to-sky-200/40 blur-2xl"></div>
                        <h3 class="text-xl font-semibold text-slate-900 relative">{{ $item['title'] }}</h3>
                        <p class="mt-3 text-slate-600 leading-relaxed relative">{{ $item['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="cw-section">
        <div class="cw-container">
            <h2 class="cw-display text-3xl md:text-5xl mb-6">{{ __('about.audience.headline') }}</h2>
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.1fr] gap-6">
                <article class="about-audience-card rounded-3xl border border-slate-200 bg-white p-6 md:p-8 shadow-sm">
                    <div class="about-audience-row">
                        <div class="about-audience-main">
                            <h3 class="text-2xl font-semibold text-brand-violet">{{ __('about.audience.workers_title') }}</h3>
                            <div class="mt-5 space-y-2">
                                @foreach(__('about.audience.workers_points') as $point)
                                    <p class="text-slate-700">{{ $point }}</p>
                                @endforeach
                            </div>
                        </div>
                        <div class="about-audience-media">
                            <img src="{{ marketing_image_url('about.workers_card') }}" alt="{{ marketing_image_alt('about.workers_card') }}" class="h-full w-full object-cover" />
                        </div>
                    </div>
                </article>

                <article class="about-audience-card rounded-3xl border border-slate-200 bg-white p-6 md:p-8 shadow-sm">
                    <div class="about-audience-row">
                        <div class="about-audience-main">
                            <h3 class="text-2xl font-semibold text-sky-700">{{ __('about.audience.employers_title') }}</h3>
                            <div class="mt-5 space-y-2">
                                @foreach(__('about.audience.employers_points') as $point)
                                    <p class="text-slate-700">{{ $point }}</p>
                                @endforeach
                            </div>
                        </div>
                        <div class="about-audience-media">
                            <img src="{{ marketing_image_url('about.employers_card') }}" alt="{{ marketing_image_alt('about.employers_card') }}" class="h-full w-full object-cover" />
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="cw-section">
        <div class="cw-container">
            <div class="cw-surface rounded-3xl p-6 md:p-10">
                <h2 class="cw-display text-3xl md:text-5xl">{{ __('about.approach.headline') }}</h2>
                <p class="mt-3 text-slate-600 max-w-3xl">{{ __('about.approach.subtitle') }}</p>
                <div class="mt-7 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach(__('about.approach.principles') as $index => $principle)
                        <article class="rounded-2xl border border-slate-200 bg-white p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.08em] text-brand-violet">{{ str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) }}</p>
                            <h3 class="mt-1 text-lg font-semibold text-slate-900">{{ $principle['title'] }}</h3>
                            <p class="mt-2 text-slate-600">{{ $principle['text'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="cw-section">
        <div class="cw-container">
            <div class="grid grid-cols-1 lg:grid-cols-[1.2fr_1fr] gap-6 items-stretch">
                <article class="about-visual-frame relative overflow-hidden rounded-3xl border border-slate-200 shadow-sm">
                    <img src="{{ marketing_image_url('about.croatia_modern_work') }}" alt="{{ marketing_image_alt('about.croatia_modern_work') }}" class="about-visual-image h-full w-full object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/70 via-slate-900/25 to-transparent"></div>
                    <div class="absolute bottom-0 p-6 md:p-8">
                        <h2 class="cw-display text-3xl md:text-4xl text-white">{{ __('about.future.headline') }}</h2>
                        <p class="mt-3 text-white/90 max-w-2xl">{{ __('about.future.body') }}</p>
                    </div>
                </article>

                <article class="cw-surface rounded-3xl p-6 md:p-8">
                    <h2 class="cw-display text-3xl md:text-4xl">{{ __('about.platform.headline') }}</h2>
                    <p class="mt-3 text-slate-600">{{ __('about.platform.subtitle') }}</p>
                    <div class="mt-6 space-y-5">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ __('about.platform.dash_title') }}</h3>
                            <p class="text-slate-600">{{ __('about.platform.dash_text') }}</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ __('about.platform.jobs_title') }}</h3>
                            <p class="text-slate-600">{{ __('about.platform.jobs_text') }}</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">{{ __('about.platform.onboarding_title') }}</h3>
                            <p class="text-slate-600">{{ __('about.platform.onboarding_text') }}</p>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="cw-section pt-4">
        <div class="cw-container">
            <div class="cw-surface rounded-3xl p-6 md:p-8">
                <h2 class="cw-display text-3xl md:text-4xl">{{ __('about.trust.headline') }}</h2>
                <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach(__('about.trust.items') as $item)
                        <div class="rounded-xl border border-slate-200 bg-white p-4 text-slate-700">{{ $item }}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="cw-section pt-0">
        <div class="cw-container">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <img src="{{ marketing_image_url('about.bottom_01') }}" alt="{{ marketing_image_alt('about.bottom_01') }}" class="about-bottom-image w-full rounded-2xl object-cover" />
                <img src="{{ marketing_image_url('about.bottom_02') }}" alt="{{ marketing_image_alt('about.bottom_02') }}" class="about-bottom-image w-full rounded-2xl object-cover" />
            </div>
        </div>
    </section>

    @push('styles')
        <style>
            .about-hero-frame,
            .about-visual-frame,
            .about-hero-image,
            .about-visual-image {
                border-radius: 1.5rem !important;
            }

            .about-hero-frame,
            .about-visual-frame {
                overflow: hidden;
            }

            .about-hero-card-text {
                color: rgba(255, 255, 255, 0.9) !important;
            }

            .about-h1-highlight {
                color: #fe5000;
            }

            .about-bottom-image {
                height: 16.5rem !important;
            }

            .about-what-card,
            .about-audience-card {
                border-radius: 1.5rem !important;
            }

            .about-audience-row {
                display: flex;
                flex-direction: column;
                gap: 1.25rem;
            }

            .about-audience-main {
                flex: 1 1 auto;
            }

            .about-audience-media {
                width: 100%;
                height: 220px;
                overflow: hidden;
                border-radius: 1rem;
            }

            @media (min-width: 768px) {
                .about-audience-row {
                    flex-direction: row;
                    align-items: stretch;
                }

                .about-audience-media {
                    width: 16rem;
                    flex: 0 0 16rem;
                    height: auto;
                }

                .about-bottom-image {
                    height: 17rem !important;
                }
            }
        </style>
    @endpush
</x-app-layout>
