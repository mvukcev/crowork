<x-app-layout>
    <x-slot name="title">CroWork Resources</x-slot>
    <x-slot name="description">Practical guides for foreign workers preparing to work, relocate, and settle into employment in Croatia.</x-slot>
    <x-slot name="canonical">{{ route('resources.index') }}</x-slot>

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
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

    <section class="cw-section">
        <div class="cw-container">
            <div class="cw-content-wide">
                <p class="cw-kicker mb-3">Croatia Work Guide</p>
                <h1 class="cw-display text-3xl md:text-5xl mb-4">Resources for working and relocating to Croatia.</h1>
                <p class="text-base text-slate-600 leading-relaxed cw-measure-md">Use these guides to prepare documents, understand employer responsibilities, evaluate housing support, and reduce uncertainty before you move.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mt-10">
                    @foreach($resources as $resource)
                        <article class="cw-surface p-5 flex flex-col">
                            <p class="text-xs uppercase tracking-[0.08em] text-slate-500 mb-2">{{ $resource['kicker'] }}</p>
                            <h2 class="text-xl font-semibold text-slate-900 mb-2">{{ $resource['title'] }}</h2>
                            <p class="text-sm text-slate-600 mb-4 flex-1">{{ $resource['description'] }}</p>
                            <a href="{{ route('resources.show', $resource['slug']) }}" class="cw-button-secondary self-start">Open guide</a>
                        </article>
                    @endforeach
                </div>

                <div class="cw-surface p-6 md:p-7 mt-8">
                    <h2 class="text-xl font-semibold text-slate-900 mb-3">How to use this section</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-slate-700">
                        <p><strong>Start with permits:</strong> Review the process first so relocation timing stays realistic.</p>
                        <p><strong>Prepare your documents:</strong> Gather and back up common paperwork before the employer asks for it urgently.</p>
                        <p><strong>Check practical support:</strong> Compare accommodation, onboarding, and employer obligations before accepting a move.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                window.cwTrack?.('page_view', {
                    page_type: 'resources_index',
                    resource_count: {{ count($resources) }}
                });
            });
        </script>
    @endpush
</x-app-layout>