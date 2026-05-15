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
                    'name' => 'Resources',
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
                <a href="{{ route('home') }}" class="hover:text-slate-900">Home</a>
                <span class="mx-1">/</span>
                <a href="{{ route('resources.index') }}" class="hover:text-slate-900">Resources</a>
                <span class="mx-1">/</span>
                <span class="text-slate-700">{{ $resource['title'] }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_320px] gap-6 items-start">
                <div class="space-y-6">
                    <article class="cw-surface p-6 md:p-8">
                        <p class="cw-kicker mb-3">{{ $resource['kicker'] }}</p>
                        <h1 class="cw-display text-4xl md:text-6xl mb-4">{{ $resource['title'] }}</h1>
                        <p class="text-base text-slate-600 leading-relaxed cw-measure-md">{{ $resource['intro'] }}</p>
                    </article>

                    @foreach($resource['sections'] as $section)
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">{{ $section['title'] }}</h2>
                            <div class="space-y-3 text-slate-700">
                                @foreach($section['body'] as $paragraph)
                                    <p>{{ $paragraph }}</p>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>

                <aside class="space-y-4 lg:sticky lg:top-24">
                    <div class="cw-surface p-5">
                        <h2 class="text-lg font-semibold text-slate-900 mb-3">Guide topics</h2>
                        <div class="flex flex-col gap-2">
                            @foreach($resources as $navResource)
                                <a
                                    href="{{ route('resources.show', $navResource['slug']) }}"
                                    @class([
                                        'cw-button-secondary text-left' => true,
                                        'border-slate-900 text-slate-900' => $navResource['slug'] === $resource['slug'],
                                    ])
                                >
                                    {{ $navResource['title'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="cw-surface p-5">
                        <h2 class="text-lg font-semibold text-slate-900 mb-3">Next steps</h2>
                        <div class="space-y-3 text-sm text-slate-700">
                            <p>Review live roles and compare the employer promises in each listing.</p>
                            <a href="{{ route('jobs.index') }}" class="cw-button-primary w-full text-center">Browse jobs</a>
                            <a href="{{ route('contact') }}" class="cw-button-secondary w-full text-center">Contact CroWork</a>
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