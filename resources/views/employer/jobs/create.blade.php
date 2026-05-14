<x-app-layout>
    <x-slot name="title">Create Job</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-4xl">
            <h1 class="cw-display text-4xl md:text-6xl mb-6">Create a job listing</h1>

            <form method="POST" action="{{ route('employer.jobs.store') }}" class="cw-surface p-6 space-y-4">
                @csrf

                <div>
                    <label class="cw-label" for="title">Job title</label>
                    <input id="title" name="title" class="cw-field" value="{{ old('title') }}" required>
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <div>
                    <label class="cw-label" for="company_name">Company name</label>
                    <input id="company_name" name="company_name" class="cw-field" value="{{ old('company_name') }}" required>
                    <x-input-error :messages="$errors->get('company_name')" class="mt-1" />
                </div>

                <div>
                    <label class="cw-label" for="description">Description</label>
                    <textarea id="description" name="description" rows="7" class="cw-field" required>{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="location">Location</label>
                        <input id="location" name="location" class="cw-field" value="{{ old('location') }}" required>
                        <x-input-error :messages="$errors->get('location')" class="mt-1" />
                    </div>
                    <div>
                        <label class="cw-label" for="job_type">Job type</label>
                        <select id="job_type" name="job_type" class="cw-field" required>
                            <option value="full_time" @selected(old('job_type') === 'full_time')>Full-time</option>
                            <option value="part_time" @selected(old('job_type') === 'part_time')>Part-time</option>
                            <option value="contract" @selected(old('job_type') === 'contract')>Contract</option>
                            <option value="seasonal" @selected(old('job_type') === 'seasonal')>Seasonal</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="salary_min">Minimum salary</label>
                        <input id="salary_min" type="number" name="salary_min" class="cw-field" value="{{ old('salary_min') }}">
                    </div>
                    <div>
                        <label class="cw-label" for="salary_max">Maximum salary</label>
                        <input id="salary_max" type="number" name="salary_max" class="cw-field" value="{{ old('salary_max') }}">
                    </div>
                </div>

                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300" @checked(old('is_active', true))>
                    Publish immediately
                </label>

                <div class="flex gap-2">
                    <button type="submit" class="cw-button-primary">Create job</button>
                    <a href="{{ route('employer.jobs.index') }}" class="cw-button-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </section>
</x-app-layout>
