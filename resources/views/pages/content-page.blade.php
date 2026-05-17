<x-app-layout>
    <x-slot name="title">{{ $metaTitle ?? $title }}</x-slot>
    <x-slot name="description">{{ $metaDescription ?? $title }}</x-slot>
    <x-slot name="canonical">{{ route($slug) }}</x-slot>

    @php
        $schemaName = $metaTitle ?? $title;
        $schemaDescription = $metaDescription ?? $title;
    @endphp

    @push('head')
        <script type="application/ld+json">{!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $schemaName,
            'description' => $schemaDescription,
            'url' => route($slug),
            'inLanguage' => app()->getLocale(),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        <script type="application/ld+json">{!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => __('navigation.home'),
                    'item' => route('home'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $title,
                    'item' => route($slug),
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

    <section class="cw-section">
        <div class="cw-container max-w-3xl">
            <article class="cw-surface p-8 prose prose-sm max-w-none">
                <h1 class="text-3xl font-semibold text-slate-900 mb-4">{{ $title }}</h1>

                <div class="mt-6 text-slate-700">
                    {!! $body !!}
                </div>

            </article>

            <div class="mt-6 text-center">
                <a href="{{ route('home') }}" class="text-sm text-blue-600 hover:underline">
                    ← Back to home
                </a>
            </div>
        </div>
    </section>
</x-app-layout>
