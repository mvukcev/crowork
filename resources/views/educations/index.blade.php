<x-app-layout>
    <x-slot name="title">Education Pathways</x-slot>
    <x-slot name="description">Explore language and certification pathways for work in Croatia.</x-slot>

    <section class="cw-section">
        <div class="cw-container">
            <div class="cw-content-wide mb-8">
                <p class="cw-kicker mb-2">Education</p>
                <h1 class="cw-display text-4xl md:text-6xl mb-3">Build skills for relocation and work.</h1>
                <p class="text-base text-slate-600">Find programs by city, online format, dates, and price.</p>
            </div>

            <form method="GET" action="{{ route('educations.index') }}" class="cw-surface p-4 md:p-5 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <div>
                        <label class="cw-label" for="q">Search</label>
                        <input id="q" name="q" value="{{ $filters['q'] ?? request('q') }}" class="cw-field" placeholder="Program, provider, topic">
                    </div>
                    <div>
                        <label class="cw-label" for="city">City</label>
                        <select id="city" name="city" class="cw-field">
                            <option value="">All cities</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}" @selected(($filters['city'] ?? request('city')) === $city)>{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="cw-label" for="price_max">Max price (EUR)</label>
                        <input id="price_max" name="price_max" value="{{ $filters['price_max'] ?? request('price_max') }}" class="cw-field" type="number" min="0" step="1">
                    </div>
                    <div>
                        <label class="cw-label" for="start_from">Start from</label>
                        <input id="start_from" name="start_from" value="{{ $filters['start_from'] ?? request('start_from') }}" class="cw-field" type="date">
                    </div>
                    <div class="flex items-end">
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700 mb-2">
                            <input type="checkbox" name="is_online" value="1" class="rounded border-slate-300" @checked(($filters['is_online'] ?? request('is_online')))>
                            Online only
                        </label>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 mt-4">
                    <button class="cw-button-primary" type="submit">Apply filters</button>
                    <a href="{{ route('educations.index') }}" class="cw-button-secondary">Reset</a>
                </div>
            </form>

            @include('educations._results', ['educations' => $educations])
        </div>
    </section>
</x-app-layout>
