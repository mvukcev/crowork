<x-app-layout>
    <x-slot name="title">CroWork - Product Platform for Work in Croatia</x-slot>
    <x-slot name="description">A premium product platform connecting workers, employers, and education pathways in Croatia.</x-slot>
    <x-slot name="canonical">{{ route('home') }}</x-slot>

    <section class="cw-hero cw-section-atmosphere">
        <div class="cw-hero-glow" aria-hidden="true"></div>
        <span class="cw-orb cw-orb-cyan hidden md:block" style="width: 260px; height: 260px; left: -80px; top: 220px;"></span>
        <span class="cw-orb cw-orb-violet hidden md:block" style="width: 290px; height: 290px; right: -90px; top: 140px;"></span>
        <div class="cw-container cw-hero-inner">
            <div class="cw-content-wide mx-auto text-center scroll-fade-in">
                <p class="cw-kicker mb-4">CroWork platform</p>
                <h1 class="cw-display text-[clamp(2.6rem,7vw,5.7rem)] text-slate-900 mb-4 text-balance">
                    Every migration journey
                    deserves <span class="cw-highlight">a clear story.</span>
                </h1>
                <form action="{{ route('jobs.index') }}" method="GET" class="cw-measure-md mx-auto mt-6 mb-8">
                    <div class="rounded-2xl border border-slate-200 bg-white p-2 flex flex-col sm:flex-row gap-2 shadow-soft">
                        <input type="text" name="q" value="{{ request('q') }}" class="cw-field border-0 shadow-none focus:ring-0" placeholder="Search jobs, employers, or cities" aria-label="Search jobs" />
                        <button type="submit" class="cw-button-primary whitespace-nowrap">Search jobs</button>
                    </div>
                </form>
                <p class="text-base md:text-[1.06rem] text-slate-600 cw-measure-sm mx-auto mb-8 leading-relaxed">
                    CroWork unifies jobs, employer pipelines, and education pathways in one calm workflow from first search to signed contract.
                </p>
                <div class="flex flex-wrap justify-center gap-3 items-center">
                    <a href="{{ route('jobs.index') }}" class="cw-button-primary cw-press">Explore jobs</a>
                    <a href="{{ route('for-employers') }}" class="cw-button-accent cw-press">For employers</a>
                </div>
            </div>
        </div>
    </section>

    <section class="cw-section bg-white cw-section-atmosphere">
        <span class="cw-orb cw-orb-blue hidden md:block" style="width: 280px; height: 280px; right: -110px; top: 220px;"></span>
        <div class="cw-container space-y-12 md:space-y-14">
            <article class="grid grid-cols-1 lg:grid-cols-2 gap-7 items-center scroll-fade-in">
                <div class="space-y-4">
                    <p class="cw-kicker">Story 1 · Workers <span class="cw-chip cw-chip-blue ml-1.5">Blue status flow</span></p>
                    <h2 class="cw-display text-3xl md:text-5xl">Communicate each step effectively.</h2>
                    <p class="text-base text-slate-600 leading-relaxed">From first role discovery to relocation prep, workers can track all events and responsibilities in one clear view.</p>
                    <a href="{{ route('access.show') }}" class="cw-button-primary cw-press">Start as worker</a>
                </div>
                <div class="cw-story-panel cw-story-accent cw-story-blue">
                    <div class="space-y-2.5">
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm text-slate-600">Profile review completed</div>
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm text-slate-600">Interview scheduled with employer</div>
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm text-slate-600">Relocation checklist activated</div>
                    </div>
                </div>
            </article>

            <article class="grid grid-cols-1 lg:grid-cols-2 gap-7 items-center scroll-fade-in">
                <div class="cw-story-panel cw-story-accent cw-story-orange order-2 lg:order-1">
                    <div class="grid grid-cols-3 gap-2">
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                            <p class="text-xs text-slate-500 mb-1">New</p>
                            <p class="text-base font-semibold text-slate-900 mb-0">14</p>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                            <p class="text-xs text-slate-500 mb-1">Reviewed</p>
                            <p class="text-base font-semibold text-slate-900 mb-0">12</p>
                        </div>
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                            <p class="text-xs text-slate-500 mb-1">Interview</p>
                            <p class="text-base font-semibold text-slate-900 mb-0">8</p>
                        </div>
                    </div>
                    <div class="mt-3 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2">
                        <p class="text-xs font-medium text-amber-800 mb-0">Orange highlight: priority candidates ready for interview</p>
                    </div>
                </div>
                <div class="space-y-4 order-1 lg:order-2">
                    <p class="cw-kicker">Story 2 · Employers <span class="cw-chip cw-chip-orange ml-1.5">Orange hiring moments</span></p>
                    <h2 class="cw-display text-3xl md:text-5xl">Keep hiring decisions structured and fast.</h2>
                    <p class="text-base text-slate-600 leading-relaxed">Employers can triage candidates, manage interview waves, and maintain decision momentum with less operational drag.</p>
                    <a href="{{ route('for-employers') }}" class="cw-button-accent cw-press">Learn employer flow</a>
                </div>
            </article>

            <article class="cw-content-wide mx-auto text-center space-y-5 scroll-fade-in cw-soft-reveal">
                <p class="cw-kicker">Story 3 · Education + relocation</p>
                <h2 class="cw-display text-3xl md:text-5xl">Support readiness beyond the job offer.</h2>
                <p class="text-base text-slate-600 leading-relaxed">Education pathways, language modules, and relocation tasks stay connected so workers and employers always operate on shared context.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-left">
                    <article class="cw-mini-card cw-hover-lift">
                        <p class="text-xs uppercase tracking-[0.08em] text-slate-500 mb-2">Program</p>
                        <p class="text-sm font-semibold text-slate-900 mb-1">Croatian A2 Sprint</p>
                        <p class="text-sm text-slate-600 mb-0">Language readiness for frontline roles.</p>
                    </article>
                    <article class="cw-mini-card cw-hover-lift">
                        <p class="text-xs uppercase tracking-[0.08em] text-slate-500 mb-2">Program</p>
                        <p class="text-sm font-semibold text-slate-900 mb-1">Hospitality Onboarding</p>
                        <p class="text-sm text-slate-600 mb-0">Role-specific standards and expectations.</p>
                    </article>
                    <article class="cw-mini-card cw-hover-lift">
                        <p class="text-xs uppercase tracking-[0.08em] text-slate-500 mb-2">Program</p>
                        <p class="text-sm font-semibold text-slate-900 mb-1">Relocation Prep</p>
                        <p class="text-sm text-slate-600 mb-0">Documents, housing, and onboarding coordination.</p>
                    </article>
                </div>
            </article>
        </div>
    </section>

    <section class="cw-section bg-slate-50 cw-section-atmosphere">
        <span class="cw-orb cw-orb-orange hidden md:block" style="width: 250px; height: 250px; left: -80px; top: 26px;"></span>
        <div class="cw-container">
            <div class="cw-surface p-7 md:p-10 text-center scroll-fade-in">
                <p class="cw-kicker mb-2">Start now</p>
                <h2 class="cw-display text-3xl md:text-5xl mb-3">Move your migration workflow forward.</h2>
                <p class="text-base text-slate-600 cw-measure-sm mx-auto mb-6">Create your account and run applications, employer decisions, and relocation planning from one clear product flow.</p>
                <div class="flex flex-wrap justify-center gap-2.5">
                    <a href="{{ route('access.show') }}" class="cw-button-primary cw-press">Create account</a>
                    <a href="{{ route('for-employers') }}" class="cw-button-accent cw-press">For employers</a>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
