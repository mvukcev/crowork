<x-app-layout>
    <x-slot name="title">Pricing</x-slot>
    <x-slot name="description">Simple plans for workers and employers using CroWork.</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-6xl">
            <p class="cw-kicker mb-3">Pricing</p>
            <h1 class="cw-display text-4xl md:text-6xl mb-4">Clear pricing for clear workflows.</h1>
            <p class="text-base text-slate-600 mb-8">Choose a plan based on hiring volume and support needs.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <article class="cw-surface p-6">
                    <h2 class="text-xl font-semibold text-slate-900 mb-2">Starter</h2>
                    <p class="text-3xl font-semibold text-slate-900 mb-3">EUR 0</p>
                    <p class="text-slate-600 mb-4">For early exploration and pilot workflows.</p>
                    <a href="{{ url('/employer/register') }}" class="cw-button-secondary w-full">Get started</a>
                </article>
                <article class="cw-surface p-6 border-amber-300">
                    <h2 class="text-xl font-semibold text-slate-900 mb-2">Growth</h2>
                    <p class="text-3xl font-semibold text-slate-900 mb-3">EUR 149/mo</p>
                    <p class="text-slate-600 mb-4">For active teams hiring across multiple roles.</p>
                    <a href="{{ url('/contact') }}" class="cw-button-accent w-full">Talk to sales</a>
                </article>
                <article class="cw-surface p-6">
                    <h2 class="text-xl font-semibold text-slate-900 mb-2">Enterprise</h2>
                    <p class="text-3xl font-semibold text-slate-900 mb-3">Custom</p>
                    <p class="text-slate-600 mb-4">For high-volume recruitment and integrations.</p>
                    <a href="{{ url('/contact') }}" class="cw-button-secondary w-full">Contact us</a>
                </article>
            </div>
        </div>
    </section>
</x-app-layout>
