<x-app-layout>
    <x-slot name="title">{{ __('ui.jobs.create') }}</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-4xl">
            <h1 class="cw-display text-4xl md:text-6xl mb-6">{{ __('ui.jobs.create_heading') }}</h1>

            <form method="POST" action="{{ route('employer.jobs.store') }}" class="cw-surface p-6 space-y-4">
                @csrf

                <div>
                    <label class="cw-label" for="title">{{ __('ui.jobs.title') }}</label>
                    <input id="title" name="title" class="cw-field" value="{{ old('title') }}" required>
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <div>
                    <label class="cw-label" for="company_name">{{ __('ui.employer.company_name') }}</label>
                    <input id="company_name" name="company_name" class="cw-field" value="{{ old('company_name') }}" required>
                    <x-input-error :messages="$errors->get('company_name')" class="mt-1" />
                </div>

                <div>
                    <label class="cw-label" for="description">{{ __('ui.jobs.description') }}</label>
                    <textarea id="description" name="description" rows="7" class="cw-field" required>{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <div>
                    <label class="cw-label" for="responsibilities">{{ __('ui.jobs.responsibilities') }}</label>
                    <textarea id="responsibilities" name="responsibilities" rows="5" class="cw-field">{{ old('responsibilities') }}</textarea>
                </div>

                <div>
                    <label class="cw-label" for="requirements">{{ __('ui.jobs.requirements') }}</label>
                    <textarea id="requirements" name="requirements" rows="5" class="cw-field">{{ old('requirements') }}</textarea>
                </div>

                <div>
                    <label class="cw-label" for="benefits">{{ __('ui.jobs.benefits') }}</label>
                    <textarea id="benefits" name="benefits" rows="5" class="cw-field">{{ old('benefits') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="location">{{ __('ui.jobs.location') }}</label>
                        <input id="location" name="location" class="cw-field" value="{{ old('location') }}" required>
                        <x-input-error :messages="$errors->get('location')" class="mt-1" />
                    </div>
                    <div>
                        <label class="cw-label" for="job_type">{{ __('ui.jobs.type') }}</label>
                        <select id="job_type" name="job_type" class="cw-field" required>
                            <option value="full_time" @selected(old('job_type') === 'full_time')>{{ __('ui.jobs.full_time') }}</option>
                            <option value="part_time" @selected(old('job_type') === 'part_time')>{{ __('ui.jobs.part_time') }}</option>
                            <option value="contract" @selected(old('job_type') === 'contract')>{{ __('ui.jobs.contract') }}</option>
                            <option value="seasonal" @selected(old('job_type') === 'seasonal')>{{ __('ui.jobs.seasonal') }}</option>
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="experience_level">Experience level</label>
                        <select id="experience_level" name="experience_level" class="cw-field">
                            <option value="">Select level</option>
                            <option value="entry" @selected(old('experience_level') === 'entry')>Entry level</option>
                            <option value="junior" @selected(old('experience_level') === 'junior')>Junior</option>
                            <option value="mid" @selected(old('experience_level') === 'mid')>Mid</option>
                            <option value="senior" @selected(old('experience_level') === 'senior')>Senior</option>
                            <option value="lead" @selected(old('experience_level') === 'lead')>Lead</option>
                        </select>
                    </div>
                    <div>
                        <label class="cw-label" for="education_required">Education required</label>
                        <input id="education_required" name="education_required" class="cw-field" value="{{ old('education_required') }}" placeholder="e.g. High school, Diploma, Bachelor">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="contract_duration">Contract duration</label>
                        <input id="contract_duration" name="contract_duration" class="cw-field" value="{{ old('contract_duration') }}" placeholder="e.g. 6 months, permanent">
                    </div>
                    <div>
                        <label class="cw-label" for="start_date">Start date</label>
                        <input id="start_date" type="date" name="start_date" class="cw-field" value="{{ old('start_date') }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="start_flexibility">Start flexibility</label>
                        <input id="start_flexibility" name="start_flexibility" class="cw-field" value="{{ old('start_flexibility') }}" placeholder="Immediate, within 2 weeks, negotiable">
                    </div>
                    <div>
                        <label class="cw-label" for="positions_available">Number of positions</label>
                        <input id="positions_available" type="number" min="1" name="positions_available" class="cw-field" value="{{ old('positions_available', 1) }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="working_hours">Working hours</label>
                        <input id="working_hours" name="working_hours" class="cw-field" value="{{ old('working_hours') }}" placeholder="e.g. 40h/week, shifts">
                    </div>
                    <div>
                        <label class="cw-label" for="shift_details">Shift details</label>
                        <textarea id="shift_details" name="shift_details" rows="3" class="cw-field">{{ old('shift_details') }}</textarea>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="accommodation_details">Accommodation details</label>
                        <textarea id="accommodation_details" name="accommodation_details" rows="3" class="cw-field">{{ old('accommodation_details') }}</textarea>
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700 mt-2">
                            <input type="checkbox" name="accommodation_provided" value="1" class="rounded border-slate-300" @checked(old('accommodation_provided'))>
                            Accommodation provided
                        </label>
                    </div>
                    <div>
                        <label class="cw-label" for="visa_support_details">Visa/work permit details</label>
                        <textarea id="visa_support_details" name="visa_support_details" rows="3" class="cw-field">{{ old('visa_support_details') }}</textarea>
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700 mt-2">
                            <input type="checkbox" name="visa_support" value="1" class="rounded border-slate-300" @checked(old('visa_support'))>
                            Visa/work permit support
                        </label>
                    </div>
                </div>

                <div>
                    <label class="cw-label" for="application_instructions">Application instructions</label>
                    <textarea id="application_instructions" name="application_instructions" rows="4" class="cw-field">{{ old('application_instructions') }}</textarea>
                </div>

                <div class="flex flex-wrap gap-4">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="is_featured" value="1" class="rounded border-slate-300" @checked(old('is_featured'))>
                        Featured job
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="is_urgent" value="1" class="rounded border-slate-300" @checked(old('is_urgent'))>
                        Urgent job
                    </label>
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
