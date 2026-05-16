<x-app-layout>
    <x-slot name="title">{{ $title }}</x-slot>
    <x-slot name="description">{{ $metaDescription ?? $title }}</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-3xl">
            <article class="cw-surface p-8 prose prose-sm max-w-none">
                <h1 class="text-3xl font-semibold text-slate-900 mb-4">{{ $title }}</h1>

                <div class="mt-6 text-slate-700">
                    {!! $body !!}
                </div>

                @if(! $fromDatabase)
                    <div class="mt-8 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                        <p class="text-sm text-amber-800">
                            This is a default placeholder.
                            @if(auth()->user()?->isAdmin())
                                <a href="{{ route('filament.admin.resources.content-pages.index') }}" class="font-semibold underline">Edit in admin</a>
                            @else
                                Please check back soon for the full content.
                            @endif
                        </p>
                    </div>
                @endif
            </article>

            <div class="mt-6 text-center">
                <a href="{{ route('home') }}" class="text-sm text-blue-600 hover:underline">
                    ← Back to home
                </a>
            </div>
        </div>
    </section>
</x-app-layout>
