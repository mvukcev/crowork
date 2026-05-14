<x-app-layout>
    <x-slot name="title">Jobs in Croatia</x-slot>
    <x-slot name="description">Browse verified jobs and migration opportunities in Croatia.</x-slot>

    <section class="cw-section">
        <div class="cw-container">
            <div class="cw-content-wide mb-8">
                <p class="cw-kicker mb-2">Jobs</p>
                <h1 class="cw-display text-4xl md:text-6xl mb-3">Find your next role in Croatia.</h1>
                <p class="text-base text-slate-600">Search roles by city, category, salary, accommodation, and language requirements.</p>
            </div>

            <form method="GET" action="{{ route('jobs.index') }}" class="cw-surface p-4 md:p-5 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <div>
                        <label class="cw-label" for="q">Search</label>
                        <input id="q" name="q" value="{{ $filters['q'] ?? request('q') }}" class="cw-field" placeholder="Role, company, skill">
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
                        <label class="cw-label" for="category">Category</label>
                        <select id="category" name="category" class="cw-field">
                            <option value="">All categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" @selected(($filters['category'] ?? request('category')) === $category)>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="cw-label" for="salary_min">Minimum salary</label>
                        <input id="salary_min" name="salary_min" value="{{ $filters['salary_min'] ?? request('salary_min') }}" class="cw-field" type="number" min="0" placeholder="EUR">
                    </div>
                    <div>
                        <label class="cw-label" for="language">Language</label>
                        <select id="language" name="language" class="cw-field">
                            <option value="">Any language</option>
                            @php($languages = ['en' => 'English', 'hr' => 'Croatian', 'de' => 'German', 'it' => 'Italian'])
                            @foreach($languages as $code => $name)
                                <option value="{{ $code }}" @selected(($filters['language'] ?? request('language')) === $code)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700 mb-2">
                            <input type="checkbox" name="accommodation" value="1" class="rounded border-slate-300" @checked(($filters['accommodation'] ?? request('accommodation')))> 
                            Accommodation provided
                        </label>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 mt-4">
                    <button class="cw-button-primary" type="submit">Apply filters</button>
                    <a href="{{ route('jobs.index') }}" class="cw-button-secondary">Reset</a>
                </div>
            </form>

            @include('jobs._results', ['jobs' => $jobs])
        </div>
    </section>
</x-app-layout>
