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

            </article>

            <div class="mt-6 text-center">
                <a href="{{ route('home') }}" class="text-sm text-blue-600 hover:underline">
                    ← Back to home
                </a>
            </div>
        </div>
    </section>
</x-app-layout>
