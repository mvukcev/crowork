<x-app-layout>
    <x-slot name="title">{{ __('seo.contact.title') }}</x-slot>
    <x-slot name="description">{{ __('seo.contact.description') }}</x-slot>
    <x-slot name="canonical">{{ route('contact') }}</x-slot>

    @push('head')
        <script type="application/ld+json">{!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'ContactPage',
            'name' => __('seo.contact.title'),
            'description' => __('seo.contact.description'),
            'url' => route('contact'),
            'inLanguage' => app()->getLocale(),
            'mainEntity' => [
                '@type' => 'Organization',
                'name' => config('app.name', 'CroWork'),
                'contactPoint' => [
                    [
                        '@type' => 'ContactPoint',
                        'contactType' => 'general support',
                        'email' => 'hello@crowork.app',
                        'availableLanguage' => ['en', 'hr'],
                    ],
                    [
                        '@type' => 'ContactPoint',
                        'contactType' => 'employer support',
                        'email' => 'employers@crowork.app',
                        'availableLanguage' => ['en', 'hr'],
                    ],
                    [
                        '@type' => 'ContactPoint',
                        'contactType' => 'worker support',
                        'email' => 'support@crowork.app',
                        'availableLanguage' => ['en', 'hr'],
                    ],
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

    <section class="cw-section">
        <div class="cw-container max-w-4xl">
            <p class="cw-kicker mb-3">{{ __('seo.contact.kicker') }}</p>
            <h1 class="cw-display text-4xl md:text-6xl mb-4">{{ __('seo.contact.headline') }}</h1>
            <p class="text-base text-slate-600 mb-8">{{ __('seo.contact.subheadline') }}</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <article class="cw-surface p-5">
                    <h2 class="text-lg font-semibold text-slate-900 mb-2">General</h2>
                    <a class="text-slate-700 hover:text-slate-900" href="mailto:hello@crowork.app" data-cw-track-click="contact_submit">hello@crowork.app</a>
                </article>
                <article class="cw-surface p-5">
                    <h2 class="text-lg font-semibold text-slate-900 mb-2">Employer support</h2>
                    <a class="text-slate-700 hover:text-slate-900" href="mailto:employers@crowork.app" data-cw-track-click="contact_submit">employers@crowork.app</a>
                </article>
                <article class="cw-surface p-5">
                    <h2 class="text-lg font-semibold text-slate-900 mb-2">Worker support</h2>
                    <a class="text-slate-700 hover:text-slate-900" href="mailto:support@crowork.app" data-cw-track-click="contact_submit">support@crowork.app</a>
                </article>
                <article class="cw-surface p-5">
                    <h2 class="text-lg font-semibold text-slate-900 mb-2">Phone</h2>
                    <a class="text-slate-700 hover:text-slate-900" href="tel:+38515551234" data-cw-track-click="contact_submit">+385 1 555 1234</a>
                </article>
            </div>
        </div>
    </section>
</x-app-layout>
