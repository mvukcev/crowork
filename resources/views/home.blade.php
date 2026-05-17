
<x-app-layout>
    <x-slot name="title">The Modern Labor Market</x-slot>
    <x-slot name="description">Find your next career opportunity or hire top talent on Croatia's leading employment platform. CroWork connects people, employers, and opportunities through transparent, modern employment.</x-slot>
    <x-slot name="canonical">{{ route('home') }}</x-slot>

    <div class="overflow-x-hidden">
        {{-- HERO SECTION --}}
        <section class="cw-hero cw-section-atmosphere">
            <div class="cw-hero-glow" aria-hidden="true"></div>
            <span class="cw-orb cw-orb-cyan hidden md:block" style="width: 260px; height: 260px; left: -80px; top: 220px;"></span>
            <span class="cw-orb cw-orb-violet hidden md:block" style="width: 290px; height: 290px; right: -90px; top: 140px;"></span>
            <div class="cw-container cw-hero-inner">
                <div class="cw-content-wide mx-auto text-center scroll-fade-in">
                    <h1 class="cw-display text-[clamp(2.65rem,7.4vw,5.75rem)] leading-[0.94] tracking-[-0.018em] text-slate-900 mb-4 text-balance max-w-[16.9ch] mx-auto">
                        {!! __('ui.homepage.hero_headline') !!}
                    </h1>
                    <p class="text-base md:text-[1.06rem] text-slate-600 cw-measure-sm mx-auto mb-8 leading-relaxed">
                        {{ __('ui.homepage.hero_subheadline') }}
                    </p>
                    
                    {{-- Search Box --}}
                    <div class="flex flex-col items-center gap-3 mb-12 max-w-2xl mx-auto">
                        <form action="{{ route('jobs.index') }}" method="GET" class="w-full flex flex-col sm:flex-row gap-3">
                            <input 
                                type="text" 
                                name="q" 
                                placeholder="{{ __('ui.homepage.hero_search_placeholder') }}" 
                                class="flex-1 px-6 py-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white/80 dark:bg-slate-800/80 backdrop-blur text-slate-900 dark:text-slate-100 placeholder-slate-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-900 dark:focus:ring-slate-100"
                            >
                            <button 
                                type="submit" 
                                class="cw-button-primary whitespace-nowrap cw-press"
                            >
                                {{ __('ui.homepage.hero_search_button') }}
                            </button>
                        </form>
                        <a href="{{ route('for-employers') }}" class="text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 text-sm font-medium">
                            {{ __('ui.homepage.hero_employer_cta') }}
                        </a>
                    </div>
                    
                    {{-- Hero Visual --}}
                    <div class="flex justify-center items-center w-full mt-12">
                        <div class="w-full max-w-4xl rounded-2xl overflow-hidden shadow-2xl">
                            <img src="/assets/pages/home/hero-dashboard-preview-1200x900.jpg" alt="{{ __('ui.homepage.dashboard_preview_alt') }}" class="w-full h-auto object-cover" loading="lazy">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- TRUST BAR --}}
        <section class="cw-section py-6 md:py-8 bg-white/80 dark:bg-black border-b border-slate-100 dark:border-white/10">
            <div class="cw-container flex flex-wrap justify-center gap-6 md:gap-10 items-center text-center">
                <div class="flex flex-col items-center">
                    <span class="cw-trust-icon mb-2"><i class="fa fa-eye"></i></span>
                    <span class="font-medium text-slate-800 dark:text-slate-100">{{ __('ui.homepage.trust_bar.transparent_jobs') }}</span>
                </div>
                <div class="flex flex-col items-center">
                    <span class="cw-trust-icon mb-2"><i class="fa fa-bolt"></i></span>
                    <span class="font-medium text-slate-800 dark:text-slate-100">{{ __('ui.homepage.trust_bar.fast_applications') }}</span>
                </div>
                <div class="flex flex-col items-center">
                    <span class="cw-trust-icon mb-2"><i class="fa fa-language"></i></span>
                    <span class="font-medium text-slate-800 dark:text-slate-100">{{ __('ui.homepage.trust_bar.multilingual') }}</span>
                </div>
                <div class="flex flex-col items-center">
                    <span class="cw-trust-icon mb-2"><i class="fa fa-rocket"></i></span>
                    <span class="font-medium text-slate-800 dark:text-slate-100">{{ __('ui.homepage.trust_bar.modern_hiring') }}</span>
                </div>
            </div>
        </section>

        {{-- EDITORIAL SECTION: More than a job board --}}
        <section class="cw-section py-16 md:py-24 bg-transparent">
            <div class="cw-container">
                <h2 class="cw-display text-3xl md:text-5xl text-center mb-12">{{ __('ui.homepage.editorial_title') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="cw-editorial-card p-8 rounded-2xl bg-white/70 dark:bg-black shadow hover:shadow-lg transition-all cw-hover-lift text-center border border-transparent dark:border-white/10">
                        <span class="cw-editorial-icon mb-4"><i class="fa fa-eye"></i></span>
                        <h3 class="text-xl font-semibold mb-2">{{ __('ui.homepage.editorial.transparent_hiring') }}</h3>
                        <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">{{ __('ui.homepage.editorial.transparent_hiring_desc') }}</p>
                    </div>
                    <div class="cw-editorial-card p-8 rounded-2xl bg-white/70 dark:bg-black shadow hover:shadow-lg transition-all cw-hover-lift text-center border border-transparent dark:border-white/10">
                        <span class="cw-editorial-icon mb-4"><i class="fa fa-users"></i></span>
                        <h3 class="text-xl font-semibold mb-2">{{ __('ui.homepage.editorial.better_integration') }}</h3>
                        <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">{{ __('ui.homepage.editorial.better_integration_desc') }}</p>
                    </div>
                    <div class="cw-editorial-card p-8 rounded-2xl bg-white/70 dark:bg-black shadow hover:shadow-lg transition-all cw-hover-lift text-center border border-transparent dark:border-white/10">
                        <span class="cw-editorial-icon mb-4"><i class="fa fa-star"></i></span>
                        <h3 class="text-xl font-semibold mb-2">{{ __('ui.homepage.editorial.modern_experience') }}</h3>
                        <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">{{ __('ui.homepage.editorial.modern_experience_desc') }}</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- FEATURED JOBS --}}
        <section class="cw-section py-16 md:py-24 cw-home-alt-section">
            <div class="cw-container">
                <h2 class="cw-display text-3xl md:text-5xl text-center mb-12">{{ __('ui.homepage.featured_jobs_title') }}</h2>
                {{-- Featured jobs component/partial goes here --}}
                @include('components.featured-jobs')
            </div>
        </section>

        {{-- EMPLOYER SECTION --}}
        <section class="cw-section py-16 md:py-24 bg-transparent">
            <div class="cw-container grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="cw-display text-2xl md:text-4xl mb-4">{{ __('ui.homepage.employer_section.headline') }}</h2>
                    <p class="text-slate-600 dark:text-slate-300 mb-6 leading-relaxed">{{ __('ui.homepage.employer_section.intro') }}</p>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start gap-3">
                            <span class="cw-benefit-icon flex-shrink-0 mt-1"><i class="fa fa-check"></i></span>
                            <span class="text-slate-700 dark:text-slate-200">{{ __('ui.homepage.employer_section.benefits.clarity') }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="cw-benefit-icon flex-shrink-0 mt-1"><i class="fa fa-check"></i></span>
                            <span class="text-slate-700 dark:text-slate-200">{{ __('ui.homepage.employer_section.benefits.management') }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="cw-benefit-icon flex-shrink-0 mt-1"><i class="fa fa-check"></i></span>
                            <span class="text-slate-700 dark:text-slate-200">{{ __('ui.homepage.employer_section.benefits.onboarding') }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="cw-benefit-icon flex-shrink-0 mt-1"><i class="fa fa-check"></i></span>
                            <span class="text-slate-700 dark:text-slate-200">{{ __('ui.homepage.employer_section.benefits.stability') }}</span>
                        </li>
                    </ul>
                    <a href="{{ route('for-employers') }}" class="cw-button-primary cw-press">{{ __('ui.homepage.employer_section.cta_post_job') }}</a>
                </div>
                <div class="rounded-2xl overflow-hidden shadow-2xl">
                    <img src="/assets/pages/home/employer-workflow-1200x800.jpg" alt="Employer workflow" class="w-full h-auto object-cover" loading="lazy">
                </div>
            </div>
        </section>

        {{-- CANDIDATE SECTION --}}
        <section class="cw-section py-16 md:py-24 cw-home-alt-section">
            <div class="cw-container grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="rounded-2xl overflow-hidden shadow-2xl order-2 md:order-1">
                    <img src="/assets/pages/home/candidate-opportunity-1200x800.jpg" alt="Candidate opportunity" class="w-full h-auto object-cover" loading="lazy">
                </div>
                <div class="order-1 md:order-2">
                    <h2 class="cw-display text-2xl md:text-4xl mb-4">{{ __('ui.homepage.candidate_section.headline') }}</h2>
                    <p class="text-slate-600 dark:text-slate-300 mb-6 leading-relaxed">{{ __('ui.homepage.candidate_section.intro') }}</p>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start gap-3">
                            <span class="cw-benefit-icon flex-shrink-0 mt-1"><i class="fa fa-check"></i></span>
                            <span class="text-slate-700 dark:text-slate-200">{{ __('ui.homepage.candidate_section.benefits.transparent') }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="cw-benefit-icon flex-shrink-0 mt-1"><i class="fa fa-check"></i></span>
                            <span class="text-slate-700 dark:text-slate-200">{{ __('ui.homepage.candidate_section.benefits.simpler') }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="cw-benefit-icon flex-shrink-0 mt-1"><i class="fa fa-check"></i></span>
                            <span class="text-slate-700 dark:text-slate-200">{{ __('ui.homepage.candidate_section.benefits.tracking') }}</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="cw-benefit-icon flex-shrink-0 mt-1"><i class="fa fa-check"></i></span>
                            <span class="text-slate-700 dark:text-slate-200">{{ __('ui.homepage.candidate_section.benefits.support') }}</span>
                        </li>
                    </ul>
                    <a href="{{ route('jobs.index') }}" class="cw-button-primary cw-press">{{ __('ui.homepage.candidate_section.cta_explore_jobs') }}</a>
                </div>
            </div>
        </section>

        {{-- HOW IT WORKS --}}
        <section class="cw-section py-16 md:py-24 bg-transparent">
            <div class="cw-container">
                <div class="text-center mb-12">
                    <h2 class="cw-display text-3xl md:text-5xl mb-4">{{ __('ui.homepage.how_it_works.title') }}</h2>
                    <p class="text-lg text-slate-600 dark:text-slate-300">{{ __('ui.homepage.how_it_works.subtitle') }}</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="cw-how-card p-8 rounded-2xl bg-white/70 dark:bg-black shadow hover:shadow-lg transition-all cw-hover-lift text-center border border-transparent dark:border-white/10">
                        <span class="cw-how-icon mb-4"><i class="fa fa-user-plus"></i></span>
                        <h3 class="text-xl font-semibold mb-3">{{ __('ui.homepage.how_it_works.steps.profile') }}</h3>
                        <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">{{ __('ui.homepage.how_it_works.steps.profile_desc') }}</p>
                    </div>
                    <div class="cw-how-card p-8 rounded-2xl bg-white/70 dark:bg-black shadow hover:shadow-lg transition-all cw-hover-lift text-center border border-transparent dark:border-white/10">
                        <span class="cw-how-icon mb-4"><i class="fa fa-search"></i></span>
                        <h3 class="text-xl font-semibold mb-3">{{ __('ui.homepage.how_it_works.steps.discover') }}</h3>
                        <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">{{ __('ui.homepage.how_it_works.steps.discover_desc') }}</p>
                    </div>
                    <div class="cw-how-card p-8 rounded-2xl bg-white/70 dark:bg-black shadow hover:shadow-lg transition-all cw-hover-lift text-center border border-transparent dark:border-white/10">
                        <span class="cw-how-icon mb-4"><i class="fa fa-bolt"></i></span>
                        <h3 class="text-xl font-semibold mb-3">{{ __('ui.homepage.how_it_works.steps.connect') }}</h3>
                        <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">{{ __('ui.homepage.how_it_works.steps.connect_desc') }}</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- RESOURCES / INSIGHTS SECTION --}}
        <section class="cw-section py-16 md:py-24 cw-home-alt-section">
            <div class="cw-container">
                <h2 class="cw-display text-3xl md:text-5xl text-center mb-12">{{ __('ui.homepage.resources_title') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                    <div class="cw-resource-card p-6 rounded-2xl bg-white/80 dark:bg-black shadow hover:shadow-lg transition-all cw-hover-lift text-center border border-transparent dark:border-white/10">
                        <span class="cw-resource-icon mb-3"><i class="fa fa-user-plus"></i></span>
                        <h4 class="font-semibold mb-2">{{ __('ui.homepage.resources_highlights.onboarding') }}</h4>
                        <p class="text-slate-600 dark:text-slate-300 text-xs leading-relaxed">{{ __('ui.homepage.resources_highlights.onboarding_desc') }}</p>
                    </div>
                    <div class="cw-resource-card p-6 rounded-2xl bg-white/80 dark:bg-black shadow hover:shadow-lg transition-all cw-hover-lift text-center border border-transparent dark:border-white/10">
                        <span class="cw-resource-icon mb-3"><i class="fa fa-chart-line"></i></span>
                        <h4 class="font-semibold mb-2">{{ __('ui.homepage.resources_highlights.trends') }}</h4>
                        <p class="text-slate-600 dark:text-slate-300 text-xs leading-relaxed">{{ __('ui.homepage.resources_highlights.trends_desc') }}</p>
                    </div>
                    <div class="cw-resource-card p-6 rounded-2xl bg-white/80 dark:bg-black shadow hover:shadow-lg transition-all cw-hover-lift text-center border border-transparent dark:border-white/10">
                        <span class="cw-resource-icon mb-3"><i class="fa fa-bullhorn"></i></span>
                        <h4 class="font-semibold mb-2">{{ __('ui.homepage.resources_highlights.branding') }}</h4>
                        <p class="text-slate-600 dark:text-slate-300 text-xs leading-relaxed">{{ __('ui.homepage.resources_highlights.branding_desc') }}</p>
                    </div>
                    <div class="cw-resource-card p-6 rounded-2xl bg-white/80 dark:bg-black shadow hover:shadow-lg transition-all cw-hover-lift text-center border border-transparent dark:border-white/10">
                        <span class="cw-resource-icon mb-3"><i class="fa fa-users"></i></span>
                        <h4 class="font-semibold mb-2">{{ __('ui.homepage.resources_highlights.integration') }}</h4>
                        <p class="text-slate-600 dark:text-slate-300 text-xs leading-relaxed">{{ __('ui.homepage.resources_highlights.integration_desc') }}</p>
                    </div>
                    <div class="cw-resource-card p-6 rounded-2xl bg-white/80 dark:bg-black shadow hover:shadow-lg transition-all cw-hover-lift text-center border border-transparent dark:border-white/10">
                        <span class="cw-resource-icon mb-3"><i class="fa fa-balance-scale"></i></span>
                        <h4 class="font-semibold mb-2">{{ __('ui.homepage.resources_highlights.rights') }}</h4>
                        <p class="text-slate-600 dark:text-slate-300 text-xs leading-relaxed">{{ __('ui.homepage.resources_highlights.rights_desc') }}</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- FINAL CTA --}}
        <section class="cw-section py-20 md:py-32 bg-gradient-to-b from-white/90 dark:from-black to-slate-50 dark:to-black">
            <div class="cw-container text-center">
                <h2 class="cw-display text-3xl md:text-5xl mb-4">{{ __('ui.homepage.final_cta.headline') }}</h2>
                <p class="text-lg text-slate-600 dark:text-slate-300 mb-8">{{ __('ui.homepage.final_cta.subheadline') }}</p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('jobs.index') }}" class="cw-button-primary cw-press text-lg px-8 py-4">{{ __('ui.homepage.final_cta.cta_explore_jobs') }}</a>
                    <a href="{{ route('for-employers') }}" class="cw-button-accent cw-press text-lg px-8 py-4">{{ __('ui.homepage.final_cta.cta_hire_faster') }}</a>
                </div>
            </div>
        </section>
    </div>

    @push('styles')
        <style>
            .cw-home-alt-section {
                background: #f7f7fb;
            }

            .cw-theme-dark .cw-home-alt-section {
                background: #000000 !important;
            }
        </style>
    @endpush
</x-app-layout>
