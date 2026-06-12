<x-app-layout>
    <x-slot name="title">{{ $post->title }}</x-slot>
    <x-slot name="description">{{ $post->excerpt ?: str(strip_tags($post->body))->limit(155) }}</x-slot>
    <x-slot name="canonical">{{ route('resources.show', $post->slug) }}</x-slot>

    @php
        $featuredImageUrl = $post->featured_image_path
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($post->featured_image_path)
            : null;

        $articleSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post->title,
            'description' => $post->excerpt,
            'inLanguage' => $post->locale,
            'mainEntityOfPage' => route('resources.show', $post->slug),
            'author' => $post->author_name_with_title
                ? [
                    '@type' => 'Person',
                    'name' => $post->author_name_with_title,
                    'description' => $post->author_specialty,
                    'url' => $post->author_external_url,
                ]
                : [
                    '@type' => 'Organization',
                    'name' => config('app.name', 'CroWork'),
                ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name', 'CroWork'),
            ],
            'datePublished' => optional($post->published_at)->toAtomString(),
            'articleBody' => strip_tags((string) $post->body),
        ];
    @endphp

    @push('head')
        <script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

    <section class="cw-section">
        <div class="cw-container">
            <div class="mb-6 text-sm text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-slate-900">{{ __('ui.navigation.home') }}</a>
                <span class="mx-1">/</span>
                <a href="{{ route('resources.index') }}" class="hover:text-slate-900">{{ __('resources.headline') }}</a>
                <span class="mx-1">/</span>
                <span class="text-slate-700">{{ $post->title }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_340px] gap-10 items-start">
                <article class="cw-surface p-8 rounded-2xl shadow-md bg-white/90">
                    @if($featuredImageUrl)
                        <img src="{{ $featuredImageUrl }}" alt="{{ $post->title }}" class="mb-6 aspect-[2/1] w-full rounded-2xl object-cover" loading="eager" decoding="async" width="1600" height="800" />
                    @endif
                    <p class="cw-kicker mb-3 text-brand-violet font-semibold">{{ ucfirst($post->type) }}</p>
                    <h1 class="cw-display text-4xl md:text-6xl mb-4 font-bold">{{ $post->title }}</h1>
                    @if($post->excerpt)
                        <p class="text-lg text-slate-700 leading-relaxed mb-6">{{ $post->excerpt }}</p>
                    @endif

                    @if($post->author_name_with_title || $post->author_specialty || $post->author_external_url)
                        <div class="mb-6 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <p class="text-xs uppercase tracking-[0.08em] text-slate-500">Author</p>
                            @if($post->author_name_with_title)
                                <p class="mt-1 text-sm font-semibold text-slate-900">{{ $post->author_name_with_title }}</p>
                            @endif
                            @if($post->author_specialty)
                                <p class="text-sm text-slate-600">{{ $post->author_specialty }}</p>
                            @endif
                            @if($post->author_external_url)
                                <a href="{{ $post->author_external_url }}" target="_blank" rel="noopener noreferrer" class="mt-1 inline-flex text-sm font-medium text-brand-violet hover:underline">
                                    View profile
                                </a>
                            @endif
                        </div>
                    @endif

                    <div class="prose prose-slate max-w-none">
                        {!! $post->body !!}
                    </div>
                </article>

                <aside class="mt-8 lg:mt-0 space-y-6 lg:sticky lg:top-24">
                    @if(!empty($alternateLocales))
                        <div class="cw-surface p-4 rounded-xl bg-white/90 shadow border-l-4 border-brand-violet">
                            <p class="text-xs uppercase tracking-[0.08em] text-slate-500 font-semibold mb-2">{{ __('ui.language') }}</p>
                            <div class="space-y-2">
                                <span class="block text-sm font-medium text-slate-700 px-3 py-2 bg-slate-100 rounded-lg">
                                    @if($post->locale === 'en')
                                        English
                                    @else
                                        Hrvatski
                                    @endif
                                </span>
                                @foreach($alternateLocales as $altLocale => $altSlug)
                                    <form method="POST" action="{{ route('preferences.locale') }}" class="w-full">
                                        @csrf
                                        <input type="hidden" name="locale" value="{{ $altLocale }}">
                                        <input type="hidden" name="redirect" value="{{ route('resources.show', $altSlug) }}">
                                        <button type="submit" class="w-full text-sm font-medium text-slate-700 px-3 py-2 bg-slate-50 rounded-lg hover:bg-brand-violet hover:text-white transition-colors text-left">
                                            @if($altLocale === 'en')
                                                English
                                            @else
                                                Hrvatski
                                            @endif
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="cw-surface p-6 rounded-xl bg-white/90 shadow">
                        <h2 class="text-lg font-semibold text-brand-violet mb-3">{{ __('resources.show.guide_topics') }}</h2>
                        <div class="flex flex-col gap-2">
                            @foreach($resources as $navResource)
                                <a
                                    href="{{ route('resources.show', $navResource['slug']) }}"
                                    @class([
                                        'cw-button-secondary text-left' => true,
                                        'border-brand-violet text-brand-violet font-bold' => $navResource['slug'] === $post->slug,
                                    ])
                                >
                                    {{ $navResource['title'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</x-app-layout>
