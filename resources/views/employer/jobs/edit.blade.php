<x-app-layout>
    <x-slot name="title">Edit Job</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-4xl">
            <h1 class="cw-display text-4xl md:text-6xl mb-6">Edit job listing</h1>

            <form method="POST" action="{{ route('employer.jobs.update', $job->id) }}" class="cw-surface p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="cw-label" for="title">Job title</label>
                    <input id="title" name="title" class="cw-field" value="{{ old('title', $job->title) }}" required>
                </div>

                <div>
                    <label class="cw-label" for="company_name">Company name</label>
                    <input id="company_name" name="company_name" class="cw-field" value="{{ old('company_name', $job->company_name) }}" required>
                </div>

                <div>
                    <label class="cw-label" for="description">Description</label>
                    <textarea id="description" name="description" rows="7" class="cw-field" required>{{ old('description', $job->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="location">Location</label>
                        <input id="location" name="location" class="cw-field" value="{{ old('location', $job->location) }}" required>
                    </div>
                    <div>
                        <label class="cw-label" for="job_type">Job type</label>
                        <select id="job_type" name="job_type" class="cw-field" required>
                            <option value="full_time" @selected(old('job_type', $job->job_type) === 'full_time')>Full-time</option>
                            <option value="part_time" @selected(old('job_type', $job->job_type) === 'part_time')>Part-time</option>
                            <option value="contract" @selected(old('job_type', $job->job_type) === 'contract')>Contract</option>
                            <option value="seasonal" @selected(old('job_type', $job->job_type) === 'seasonal')>Seasonal</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="cw-label" for="salary_min">Minimum salary</label><input id="salary_min" type="number" name="salary_min" class="cw-field" value="{{ old('salary_min', $job->salary_min) }}"></div>
                    <div><label class="cw-label" for="salary_max">Maximum salary</label><input id="salary_max" type="number" name="salary_max" class="cw-field" value="{{ old('salary_max', $job->salary_max) }}"></div>
                </div>

                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300" @checked(old('is_active', $job->is_active))>
                    Job is active
                </label>

                <div class="flex gap-2">
                    <button type="submit" class="cw-button-primary">Save changes</button>
                    <a href="{{ route('employer.jobs.index') }}" class="cw-button-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </section>
</x-app-layout>
