
<x-app-layout>
    <x-slot name="title">{{ $resource['title'] }}</x-slot>
    <x-slot name="description">{{ $resource['description'] }}</x-slot>
    <x-slot name="canonical">{{ route('resources.show', $resource['slug']) }}</x-slot>

    @push('head')
        <script type="application/ld+json">{!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Home',
                    'item' => route('home'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => __('resources.headline'),
                    'item' => route('resources.index'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $resource['title'],
                    'item' => route('resources.show', $resource['slug']),
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

    <section class="cw-section">
        <div class="cw-container">
            <div class="mb-6 text-sm text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-slate-900">{{ __('ui.navigation.home') }}</a>
                <span class="mx-1">/</span>
                <a href="{{ route('resources.index') }}" class="hover:text-slate-900">{{ __('resources.headline') }}</a>
                <span class="mx-1">/</span>
                <span class="text-slate-700">{{ $resource['title'] }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_340px] gap-10 items-start">
                <div class="space-y-8">
                    <article class="cw-surface p-8 rounded-2xl shadow-md bg-white/90">
                        <p class="cw-kicker mb-3 text-violet-600 font-semibold">{{ $resource['kicker'] }}</p>
                        <h1 class="cw-display text-4xl md:text-6xl mb-4 font-bold">{{ $resource['title'] }}</h1>
                        <p class="text-lg text-slate-700 leading-relaxed mb-6">{{ $resource['intro'] }}</p>
                    </article>

                    @foreach($resource['sections'] as $section)
                        <article class="cw-surface p-7 rounded-xl bg-white/80 shadow border-l-4 border-violet-200">
                            <h2 class="text-2xl font-semibold text-violet-700 mb-3">{{ $section['title'] }}</h2>
                            <div class="space-y-3 text-slate-700">
                                @foreach($section['body'] as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>

                <aside class="mt-8 lg:mt-0 space-y-6 lg:sticky lg:top-24">
                    <div class="cw-surface p-6 rounded-xl bg-white/90 shadow">
                        <h2 class="text-lg font-semibold text-violet-700 mb-3">{{ __('resources.show.guide_topics') }}</h2>
                        <div class="flex flex-col gap-2">
                            @foreach($resources as $navResource)
                                <a
                                    href="{{ route('resources.show', $navResource['slug']) }}"
                                    @class([
                                        'cw-button-secondary text-left' => true,
                                        'border-violet-700 text-violet-700 font-bold' => $navResource['slug'] === $resource['slug'],
                                    ])
                                >
                                    {{ $navResource['title'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="cw-surface p-6 rounded-xl bg-gradient-to-r from-violet-50 to-white shadow">
                        <h2 class="text-lg font-semibold text-violet-700 mb-3">{{ __('resources.show.next_steps') }}</h2>
                        <div class="space-y-3 text-sm text-slate-700">
                            <p>{{ __('resources.show.next_steps_copy') }}</p>
                            <a href="{{ route('jobs.index') }}" class="cw-button-primary w-full text-center">{{ __('resources.cta.browse_jobs') }}</a>
                            <a href="{{ route('contact') }}" class="cw-button-secondary w-full text-center">{{ __('resources.cta.contact') }}</a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                window.cwTrack?.('page_view', {
                    page_type: 'resource_detail',
                    resource_slug: @json($resource['slug'])
                });
            });
        </script>
    @endpush
</x-app-layout>