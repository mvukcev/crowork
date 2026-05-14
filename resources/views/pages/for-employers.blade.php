<x-app-layout>
    <x-slot name="title">For Employers</x-slot>
    <x-slot name="description">Recruit and manage migration-ready talent in Croatia with one clear workflow.</x-slot>
    <x-slot name="canonical">{{ route('for-employers') }}</x-slot>

    <section class="cw-section">
        <div class="cw-container">
            <div class="cw-content-wide">
            <p class="cw-kicker mb-3">For employers</p>
            <h1 class="cw-display text-4xl md:text-6xl mb-4">Hire with less friction and more confidence.</h1>
            <p class="text-base text-slate-600 leading-relaxed cw-measure-md">CroWork helps teams shortlist, interview, and onboard workers while keeping readiness and relocation context visible.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-10">
                <article class="cw-surface p-5"><h2 class="text-lg font-semibold text-slate-900 mb-2">Structured pipeline</h2><p class="text-slate-600">Move candidates from screening to offer with clear states.</p></article>
                <article class="cw-surface p-5"><h2 class="text-lg font-semibold text-slate-900 mb-2">Relocation visibility</h2><p class="text-slate-600">Track onboarding and documentation milestones in context.</p></article>
                <article class="cw-surface p-5"><h2 class="text-lg font-semibold text-slate-900 mb-2">Education alignment</h2><p class="text-slate-600">Connect program readiness to open role requirements.</p></article>
                <article class="cw-surface p-5"><h2 class="text-lg font-semibold text-slate-900 mb-2">Faster decisions</h2><p class="text-slate-600">Reduce waiting loops with better status clarity.</p></article>
            </div>

            <div class="flex flex-wrap gap-2 mt-8">
                <a href="{{ url('/employer/register') }}" class="cw-button-primary" data-cw-track-click="employer_cta_click">Create employer account</a>
                <a href="{{ url('/contact') }}" class="cw-button-secondary">Talk to CroWork</a>
            </div>
            </div>
        </div>
    </section>
</x-app-layout>
