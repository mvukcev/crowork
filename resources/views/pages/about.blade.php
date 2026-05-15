<x-app-layout>
    <x-slot name="title">About CroWork</x-slot>
    <x-slot name="description">Learn how CroWork connects workers, employers, and education providers in Croatia.</x-slot>

    <section class="cw-section">
        <div class="cw-container">
            <div class="cw-content-wide">
            <p class="cw-kicker mb-3">About</p>
            <h1 class="cw-display text-3xl md:text-5xl mb-4">A clearer migration experience for everyone involved.</h1>
            <p class="text-base text-slate-600 leading-relaxed cw-measure-md">CroWork is built to remove uncertainty from international hiring and relocation workflows by connecting jobs, readiness, and education in one system.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-10">
                <article class="cw-surface p-5"><h2 class="text-lg font-semibold text-slate-900 mb-2">Workers</h2><p class="text-slate-600">Track progress and expectations in one place.</p></article>
                <article class="cw-surface p-5"><h2 class="text-lg font-semibold text-slate-900 mb-2">Employers</h2><p class="text-slate-600">Keep hiring pipelines structured and transparent.</p></article>
                <article class="cw-surface p-5"><h2 class="text-lg font-semibold text-slate-900 mb-2">Educators</h2><p class="text-slate-600">Align skills training with live role demand.</p></article>
            </div>

            <div class="mt-8">
                <a href="{{ route('contact') }}" class="cw-button-primary" data-cw-track-click="contact_submit">Contact us</a>
            </div>
            </div>
        </div>
    </section>
</x-app-layout>
