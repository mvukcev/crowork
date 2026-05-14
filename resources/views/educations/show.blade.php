<x-app-layout>
    <x-slot name="title">{{ $education->title }}</x-slot>
    <x-slot name="description">{{ \Illuminate\Support\Str::limit(strip_tags($education->description), 150) }}</x-slot>

    <section class="cw-section">
        <div class="cw-container">
            <div class="mb-6 text-sm text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-slate-900">Home</a>
                <span class="mx-1">/</span>
                <a href="{{ route('educations.index') }}" class="hover:text-slate-900">Educations</a>
                <span class="mx-1">/</span>
                <span class="text-slate-700">{{ $education->title }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <article class="lg:col-span-2 cw-surface p-6 md:p-7">
                    <p class="text-xs text-slate-500 mb-1">{{ $provider ?? ($education->createdByUser?->name ?? 'Education provider') }}</p>
                    <h1 class="cw-display text-4xl md:text-6xl mb-3">{{ $education->title }}</h1>
                    <p class="text-sm text-slate-500 mb-4">Posted {{ $postedDisplay ?? ($education->published_at?->diffForHumans() ?? $education->created_at?->diffForHumans()) }}</p>
                    <div class="prose max-w-none text-slate-700 leading-relaxed">{!! nl2br(e($education->description)) !!}</div>
                </article>

                <aside class="cw-surface p-5">
                    <h2 class="text-lg font-semibold text-slate-900 mb-3">Program details</h2>
                    <p class="text-sm text-slate-700 mb-2"><strong>Location:</strong> {{ $locationDisplay ?? ($education->is_online ? 'Online' : ($education->city ?: 'Croatia')) }}</p>
                    <p class="text-sm text-slate-700 mb-2"><strong>Price:</strong> {{ $priceDisplay ?? (($education->currency ?? 'EUR') . ' ' . number_format(($education->price_cents ?? 0) / 100, 2)) }}</p>
                    @if($education->start_date)
                        <p class="text-sm text-slate-700 mb-2"><strong>Start:</strong> {{ $startDateDisplay ?? $education->start_date->format('M j, Y') }}</p>
                    @endif
                    @if($education->capacity)
                        <p class="text-sm text-slate-700 mb-2"><strong>Capacity:</strong> {{ $education->capacity }}</p>
                    @endif

                    <a href="{{ route('educations.apply', $education) }}" class="cw-button-primary w-full mt-3">Apply now</a>
                </aside>
            </div>
        </div>
    </section>
</x-app-layout>
