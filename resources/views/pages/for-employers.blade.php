<x-app-layout>
    <x-slot name="title">{{ __('for_employers.seo.title') }}</x-slot>
    <x-slot name="description">{{ __('for_employers.seo.description') }}</x-slot>
    <x-slot name="canonical">{{ route('for-employers') }}</x-slot>

    @push('head')
        <script type="application/ld+json">{!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => __('for_employers.seo.title'),
            'description' => __('for_employers.seo.description'),
            'serviceType' => 'Employer onboarding and hiring workflow support',
            'url' => route('for-employers'),
            'inLanguage' => app()->getLocale(),
            'provider' => [
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
                    'name' => __('ui.navigation.for_employers'),
                    'item' => route('for-employers'),
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

    @php
        $previewImages = [
            asset('assets/employers/dashboard/employer-dashboard-1600x900.jpg'),
            asset('assets/employers/hiring/job-posting-flow-1400x900.jpg'),
            asset('assets/employers/workflow/application-review-1400x900.jpg'),
            asset('assets/employers/onboarding/onboarding-tools-1400x900.jpg'),
            asset('assets/employers/workflow/hiring-workflow-1600x900.jpg'),
        ];
    @endphp

    <div class="for-employers-page">

    <section class="cw-section relative overflow-hidden">
        <div class="cw-container">
            <div class="for-employers-hero relative overflow-hidden rounded-3xl border border-white/60 px-6 py-8 md:px-10 md:py-10 lg:px-12 lg:py-12">
                <div class="for-employers-hero-glow for-employers-hero-glow-a" aria-hidden="true"></div>
                <div class="for-employers-hero-glow for-employers-hero-glow-b" aria-hidden="true"></div>

                <div class="relative z-10 grid grid-cols-1 xl:grid-cols-[1.05fr_1fr] gap-8 lg:gap-10 items-start">
                    <div>
                        <p class="cw-kicker mb-3">{{ __('for_employers.hero.eyebrow') }}</p>
                        <h1 class="cw-display text-3xl md:text-5xl max-w-3xl">{!! __('for_employers.hero.headline_html', [
                            'highlight_1' => '<span class="for-employers-highlight">' . __('for_employers.hero.headline_highlight_1') . '</span>',
                            'highlight_2' => '<span class="for-employers-highlight">' . __('for_employers.hero.headline_highlight_2') . '</span>',
                            'highlight_3' => '<span class="for-employers-highlight">' . __('for_employers.hero.headline_highlight_3') . '</span>',
                        ]) !!}</h1>
                        <p class="mt-4 text-base md:text-lg text-slate-700 max-w-2xl leading-relaxed">{{ __('for_employers.hero.supporting') }}</p>

                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="{{ url('/employer/register') }}" class="cw-button-accent for-employers-primary-cta" data-cw-track-click="post_job_click" data-cw-item-type="cta">{{ __('for_employers.hero.cta_primary') }}</a>
                            <a href="#platform-previews" class="cw-button-secondary">{{ __('for_employers.hero.cta_secondary') }}</a>
                        </div>

                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3 max-w-3xl">
                            @foreach(__('for_employers.hero.signals') as $signal)
                                <div class="rounded-2xl border border-slate-200 bg-white/75 backdrop-blur p-3 text-sm text-slate-700">{{ $signal }}</div>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <article class="for-employers-window sm:col-span-2">
                            <img src="{{ asset('assets/employers/hero/employer-hero-dashboard-1600x900.jpg') }}" alt="{{ __('for_employers.hero.visual_1_alt') }}" class="for-employers-window-image" />
                            <div class="for-employers-window-label">{{ __('for_employers.hero.visual_1_label') }}</div>
                        </article>
                        <article class="for-employers-window">
                            <img src="{{ asset('assets/employers/hero/onboarding-workflow-1600x900.jpg') }}" alt="{{ __('for_employers.hero.visual_2_alt') }}" class="for-employers-window-image" />
                            <div class="for-employers-window-label">{{ __('for_employers.hero.visual_2_label') }}</div>
                        </article>
                        <article class="for-employers-window">
                            <img src="{{ asset('assets/employers/hero/candidate-pipeline-1400x900.jpg') }}" alt="{{ __('for_employers.hero.visual_3_alt') }}" class="for-employers-window-image" />
                            <div class="for-employers-window-label">{{ __('for_employers.hero.visual_3_label') }}</div>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cw-section">
        <div class="cw-container">
            <div class="grid grid-cols-1 xl:grid-cols-[1.02fr_1fr] gap-6 items-start">
                <article class="cw-surface rounded-3xl p-6 md:p-8">
                    <p class="cw-kicker text-violet-700">{{ __('for_employers.struggle.eyebrow') }}</p>
                    <h2 class="cw-display text-3xl md:text-5xl mt-2">{{ __('for_employers.struggle.headline') }}</h2>
                    <p class="mt-4 text-slate-700 leading-relaxed">{{ __('for_employers.struggle.body_1') }}</p>
                    <p class="mt-4 text-slate-700 leading-relaxed">{{ __('for_employers.struggle.body_2') }}</p>
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach(__('for_employers.struggle.problems') as $problem)
                            <div class="for-employers-problem-card">
                                <svg viewBox="0 0 20 20" fill="none" class="h-5 w-5 text-violet-600 flex-shrink-0" aria-hidden="true">
                                    <circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5"></circle>
                                    <path d="M6 10.5L8.8 13.2L14 7.8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                                <span>{{ $problem }}</span>
                            </div>
                        @endforeach
                    </div>
                </article>

                <aside class="for-employers-layer-stack">
                    <article class="for-employers-layer-card">
                        <h3 class="text-xl font-semibold text-slate-900">{{ __('for_employers.struggle.solution_title') }}</h3>
                        <p class="mt-2 text-slate-600 leading-relaxed">{{ __('for_employers.struggle.solution_text') }}</p>
                        <div class="mt-5 space-y-2">
                            @foreach(__('for_employers.struggle.solution_points') as $point)
                                <p class="text-sm text-slate-700">{{ $point }}</p>
                            @endforeach
                        </div>
                    </article>
                    <article class="for-employers-layer-card for-employers-layer-card-shift">
                        <img src="{{ asset('assets/employers/workflow/hiring-workflow-1600x900.jpg') }}" alt="{{ __('for_employers.struggle.visual_alt') }}" class="h-full w-full object-cover" />
                    </article>
                </aside>
            </div>
        </div>
    </section>

    <section class="cw-section">
        <div class="cw-container">
            <h2 class="cw-display text-3xl md:text-5xl mb-6">{{ __('for_employers.capabilities.headline') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach(__('for_employers.capabilities.items') as $item)
                    <article class="for-employers-feature-card group">
                        <div class="for-employers-feature-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" class="h-6 w-6">
                                <path d="M4 12.5H20" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                                <path d="M7.5 7.5L12 3L16.5 7.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M7.5 16.5L12 21L16.5 16.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <h3 class="mt-4 text-xl font-semibold text-slate-900">{{ $item['title'] }}</h3>
                        <p class="mt-3 text-slate-600 leading-relaxed">{{ $item['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="cw-section">
        <div class="cw-container">
            <div class="grid grid-cols-1 lg:grid-cols-[1.05fr_1fr] gap-6 items-stretch">
                <article class="cw-surface rounded-3xl p-6 md:p-8">
                    <h2 class="cw-display text-3xl md:text-5xl">{{ __('for_employers.experience.headline') }}</h2>
                    <p class="mt-4 text-slate-700 leading-relaxed">{{ __('for_employers.experience.body') }}</p>
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach(__('for_employers.experience.benefits') as $benefit)
                            <div class="rounded-2xl border border-slate-200 bg-white p-4 text-slate-700">{{ $benefit }}</div>
                        @endforeach
                    </div>
                </article>

                <article class="for-employers-window h-full">
                    <img src="{{ asset('assets/employers/onboarding/onboarding-tools-1400x900.jpg') }}" alt="{{ __('for_employers.experience.visual_alt') }}" class="for-employers-window-image" />
                </article>
            </div>
        </div>
    </section>

    <section class="cw-section">
        <div class="cw-container">
            <div class="cw-surface rounded-3xl p-6 md:p-8">
                <h2 class="cw-display text-3xl md:text-5xl">{{ __('for_employers.workflow.headline') }}</h2>
                <p class="mt-3 text-slate-600 max-w-3xl">{{ __('for_employers.workflow.subtitle') }}</p>

                <ol class="for-employers-timeline mt-6">
                    @foreach(__('for_employers.workflow.steps') as $index => $step)
                        <li class="for-employers-timeline-item">
                            <div class="for-employers-step-badge">{{ $index + 1 }}</div>
                            <div class="for-employers-step-card">
                                <div class="flex items-start justify-between gap-3">
                                    <h3 class="text-lg md:text-xl font-semibold text-slate-900">{{ $step['title'] }}</h3>
                                    <svg viewBox="0 0 20 20" fill="none" class="h-5 w-5 text-violet-600" aria-hidden="true">
                                        <path d="M5.5 10H14.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                        <path d="M11.8 6.8L15 10L11.8 13.2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <p class="mt-2 text-slate-600 leading-relaxed">{{ $step['text'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </section>

    <section class="cw-section">
        <div class="cw-container">
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.1fr] gap-6 items-stretch">
                <article class="cw-surface rounded-3xl p-6 md:p-8">
                    <h2 class="cw-display text-3xl md:text-5xl">{{ __('for_employers.branding.headline') }}</h2>
                    <p class="mt-3 text-slate-600">{{ __('for_employers.branding.subtitle') }}</p>

                    <div class="mt-6 space-y-3">
                        @foreach(__('for_employers.branding.points') as $point)
                            <div class="rounded-2xl border border-slate-200 bg-white p-4 text-slate-700">{{ $point }}</div>
                        @endforeach
                    </div>
                </article>

                <article class="for-employers-branding-preview">
                    <div class="for-employers-job-card">
                        <div class="for-employers-job-logo" aria-hidden="true">
                            <img src="{{ asset('assets/employers/branding/employer-branding-1400x900.jpg') }}" alt="{{ __('for_employers.branding.logo_alt') }}" class="h-full w-full object-cover" />
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.08em] text-violet-700">{{ __('for_employers.branding.card_label') }}</p>
                            <h3 class="mt-1 text-xl font-semibold text-slate-900">{{ __('for_employers.branding.card_title') }}</h3>
                            <p class="mt-2 text-slate-600">{{ __('for_employers.branding.card_text') }}</p>
                        </div>
                    </div>

                    <div class="for-employers-square-upload mt-4">
                        <h4 class="text-base font-semibold text-slate-900">{{ __('for_employers.branding.settings_title') }}</h4>
                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach(__('for_employers.branding.settings_points') as $setting)
                                <p class="rounded-xl border border-slate-200 bg-white p-3 text-sm text-slate-700">{{ $setting }}</p>
                            @endforeach
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section id="platform-previews" class="cw-section">
        <div class="cw-container">
            <h2 class="cw-display text-3xl md:text-5xl mb-3">{{ __('for_employers.previews.headline') }}</h2>
            <p class="text-slate-600 max-w-3xl">{{ __('for_employers.previews.subtitle') }}</p>

            <div class="mt-6 md:hidden -mx-1 overflow-x-auto pb-2 snap-x snap-mandatory">
                <div class="flex gap-4 px-1">
                    @foreach(__('for_employers.previews.items') as $index => $item)
                        <article class="for-employers-window snap-start min-w-[84%]">
                            <img src="{{ $previewImages[$index] }}" alt="{{ $item['alt'] }}" class="for-employers-window-image" />
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-slate-900">{{ $item['title'] }}</h3>
                                <p class="mt-1 text-sm text-slate-600">{{ $item['text'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <div class="hidden md:grid mt-6 grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach(__('for_employers.previews.items') as $index => $item)
                    <article class="for-employers-window {{ $index === 0 ? 'xl:col-span-2' : '' }}">
                        <img src="{{ $previewImages[$index] }}" alt="{{ $item['alt'] }}" class="for-employers-window-image" />
                        <div class="p-4 md:p-5">
                            <h3 class="text-lg md:text-xl font-semibold text-slate-900">{{ $item['title'] }}</h3>
                            <p class="mt-2 text-slate-600">{{ $item['text'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="cw-section">
        <div class="cw-container">
            <div class="cw-surface rounded-3xl p-6 md:p-8">
                <h2 class="cw-display text-3xl md:text-4xl">{{ __('for_employers.trust.headline') }}</h2>
                <p class="mt-3 text-slate-600 max-w-3xl">{{ __('for_employers.trust.subtitle') }}</p>
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                    @foreach(__('for_employers.trust.items') as $item)
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 text-slate-700">{{ $item }}</div>
                    @endforeach
                </div>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ url('/employer/register') }}" class="cw-button-accent for-employers-primary-cta" data-cw-track-click="post_job_click" data-cw-item-type="cta">{{ __('for_employers.trust.cta_primary') }}</a>
                    <a href="{{ route('contact') }}" class="cw-button-secondary" data-cw-track-click="contact_submit">{{ __('for_employers.trust.cta_secondary') }}</a>
                </div>
            </div>
        </div>
    </section>
    </div>

    @push('styles')
        <style>
            .for-employers-hero {
                background: linear-gradient(150deg, rgba(255, 255, 255, 0.94), rgba(244, 247, 255, 0.9));
                box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08);
                backdrop-filter: blur(6px);
                border-radius: 1.5rem !important;
            }

            .for-employers-highlight {
                color: #6d28d9;
            }

            .for-employers-hero-glow {
                position: absolute;
                border-radius: 999px;
                filter: blur(42px);
                opacity: 0.8;
                pointer-events: none;
                animation: forEmployersFloat 9s ease-in-out infinite;
            }

            .for-employers-hero-glow-a {
                width: 280px;
                height: 280px;
                right: -90px;
                top: -50px;
                background: radial-gradient(circle, rgba(124, 58, 237, 0.28), rgba(124, 58, 237, 0));
            }

            .for-employers-hero-glow-b {
                width: 300px;
                height: 300px;
                left: -120px;
                bottom: -120px;
                background: radial-gradient(circle, rgba(56, 189, 248, 0.24), rgba(56, 189, 248, 0));
                animation-delay: 1.6s;
            }

            .for-employers-window {
                position: relative;
                overflow: hidden;
                border-radius: 1.5rem;
                border: 1px solid rgba(148, 163, 184, 0.28);
                background: rgba(255, 255, 255, 0.92);
                box-shadow: 0 16px 44px rgba(15, 23, 42, 0.08);
                transition: transform 0.28s ease, box-shadow 0.28s ease;
            }

            .for-employers-window:hover {
                transform: translateY(-3px);
                box-shadow: 0 24px 56px rgba(15, 23, 42, 0.12);
            }

            .for-employers-window-image {
                width: 100%;
                height: 100%;
                min-height: 200px;
                object-fit: cover;
                display: block;
            }

            .for-employers-window-label {
                position: absolute;
                top: 0.8rem;
                left: 0.8rem;
                border-radius: 999px;
                background: rgba(15, 23, 42, 0.68);
                color: rgba(255, 255, 255, 0.96);
                font-size: 0.72rem;
                letter-spacing: 0.06em;
                text-transform: uppercase;
                padding: 0.35rem 0.6rem;
                backdrop-filter: blur(4px);
            }

            .for-employers-problem-card {
                display: flex;
                align-items: center;
                gap: 0.65rem;
                border: 1px solid #e2e8f0;
                border-radius: 0.9rem;
                background: rgba(255, 255, 255, 0.9);
                padding: 0.7rem 0.8rem;
                color: #334155;
            }

            .for-employers-layer-stack {
                position: relative;
                min-height: 420px;
            }

            .for-employers-layer-card {
                border-radius: 1.5rem;
                border: 1px solid rgba(148, 163, 184, 0.3);
                background: rgba(255, 255, 255, 0.9);
                padding: 1.35rem;
                box-shadow: 0 16px 42px rgba(15, 23, 42, 0.08);
            }

            .for-employers-layer-card-shift {
                margin-top: 1rem;
                overflow: hidden;
                padding: 0;
                min-height: 240px;
                animation: forEmployersPulse 8s ease-in-out infinite;
            }

            .for-employers-feature-card {
                position: relative;
                overflow: hidden;
                border-radius: 1.4rem;
                border: 1px solid rgba(148, 163, 184, 0.28);
                background: linear-gradient(165deg, rgba(255, 255, 255, 0.95), rgba(247, 250, 255, 0.9));
                padding: 1.3rem;
                transition: transform 0.24s ease, box-shadow 0.24s ease;
                box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
            }

            .for-employers-feature-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 18px 34px rgba(15, 23, 42, 0.1);
            }

            .for-employers-feature-card::after {
                content: '';
                position: absolute;
                width: 120px;
                height: 120px;
                right: -34px;
                top: -44px;
                border-radius: 999px;
                background: radial-gradient(circle, rgba(196, 181, 253, 0.45), rgba(196, 181, 253, 0));
                pointer-events: none;
            }

            .for-employers-feature-icon {
                height: 2.4rem;
                width: 2.4rem;
                border-radius: 0.8rem;
                background: rgba(237, 233, 254, 0.9);
                color: #7c3aed;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .for-employers-timeline {
                position: relative;
                display: grid;
                gap: 1rem;
            }

            .for-employers-timeline-item {
                display: grid;
                grid-template-columns: auto 1fr;
                gap: 0.8rem;
                align-items: start;
            }

            .for-employers-step-badge {
                width: 2rem;
                height: 2rem;
                border-radius: 999px;
                background: #7c3aed;
                color: #ffffff;
                font-weight: 700;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 8px 16px rgba(124, 58, 237, 0.3);
            }

            .for-employers-step-card {
                border: 1px solid #e2e8f0;
                border-radius: 1rem;
                padding: 0.9rem 1rem;
                background: #ffffff;
            }

            .for-employers-branding-preview {
                border-radius: 1.5rem;
                border: 1px solid rgba(148, 163, 184, 0.28);
                background: linear-gradient(160deg, rgba(255, 255, 255, 0.95), rgba(244, 247, 255, 0.9));
                padding: 1.15rem;
                box-shadow: 0 16px 42px rgba(15, 23, 42, 0.08);
            }

            .for-employers-job-card {
                display: grid;
                grid-template-columns: auto 1fr;
                gap: 0.95rem;
                align-items: center;
                border: 1px solid #e2e8f0;
                border-radius: 1rem;
                background: #ffffff;
                padding: 0.9rem;
            }

            .for-employers-job-logo {
                width: 4rem;
                height: 4rem;
                border-radius: 999px;
                overflow: hidden;
                border: 2px solid #e2e8f0;
                flex-shrink: 0;
            }

            .for-employers-square-upload {
                border: 1px solid #e2e8f0;
                border-radius: 1rem;
                background: rgba(255, 255, 255, 0.95);
                padding: 0.85rem;
            }

            .dark .for-employers-page .for-employers-hero {
                background: linear-gradient(150deg, rgba(15, 23, 42, 0.82), rgba(30, 41, 59, 0.76));
                border-color: rgba(148, 163, 184, 0.3);
                box-shadow: 0 22px 60px rgba(0, 0, 0, 0.45);
            }

            .dark .for-employers-page .for-employers-window,
            .dark .for-employers-page .for-employers-layer-card,
            .dark .for-employers-page .for-employers-feature-card,
            .dark .for-employers-page .for-employers-step-card,
            .dark .for-employers-page .for-employers-branding-preview,
            .dark .for-employers-page .for-employers-job-card,
            .dark .for-employers-page .for-employers-square-upload,
            .dark .for-employers-page .for-employers-problem-card {
                background: rgba(15, 23, 42, 0.78);
                border-color: rgba(100, 116, 139, 0.52);
                box-shadow: 0 14px 32px rgba(0, 0, 0, 0.34);
                color: #dbeafe;
            }

            .dark .for-employers-page .cw-surface {
                background: linear-gradient(165deg, rgba(15, 23, 42, 0.94), rgba(17, 24, 39, 0.9));
                border-color: rgba(100, 116, 139, 0.5);
                box-shadow: 0 16px 36px rgba(0, 0, 0, 0.36);
                color: #dbeafe;
            }

            .dark .for-employers-page .for-employers-feature-card::after {
                background: radial-gradient(circle, rgba(99, 102, 241, 0.26), rgba(99, 102, 241, 0));
            }

            .dark .for-employers-page .for-employers-feature-icon {
                background: rgba(55, 48, 163, 0.34);
                color: #c4b5fd;
            }

            .dark .for-employers-page .for-employers-step-badge {
                background: #8b5cf6;
                box-shadow: 0 10px 20px rgba(139, 92, 246, 0.35);
            }

            .dark .for-employers-page .for-employers-job-logo {
                border-color: rgba(148, 163, 184, 0.52);
            }

            .dark .for-employers-page .bg-white,
            .dark .for-employers-page .bg-white\/75,
            .dark .for-employers-page .bg-white\/95 {
                background: rgba(15, 23, 42, 0.78) !important;
            }

            .dark .for-employers-page .border-slate-200 {
                border-color: rgba(100, 116, 139, 0.5) !important;
            }

            .dark .for-employers-page .text-slate-900 {
                color: #f8fafc !important;
            }

            .dark .for-employers-page .text-slate-700,
            .dark .for-employers-page .text-slate-600 {
                color: #cbd5e1 !important;
            }

            .dark .for-employers-page .for-employers-primary-cta {
                color: #111827;
            }


            @keyframes forEmployersFloat {
                0%,
                100% {
                    transform: translate3d(0, 0, 0);
                }

                50% {
                    transform: translate3d(0, -12px, 0);
                }
            }

            @keyframes forEmployersPulse {
                0%,
                100% {
                    transform: translateY(0);
                }

                50% {
                    transform: translateY(-4px);
                }
            }

            @media (max-width: 767px) {
                .for-employers-layer-stack {
                    min-height: 0;
                }

                .for-employers-window-image {
                    min-height: 190px;
                }
            }

            @media (min-width: 768px) {
                .for-employers-window-image {
                    min-height: 230px;
                }

                .for-employers-layer-card-shift {
                    min-height: 300px;
                }
            }
        </style>
    @endpush
</x-app-layout>
