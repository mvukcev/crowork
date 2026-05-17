<x-app-layout>
    <x-slot name="title">{{ __('ui.jobs.edit') }}</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-4xl">
            <h1 class="cw-display text-4xl md:text-6xl mb-6">{{ __('ui.jobs.edit_heading') }}</h1>

            <form method="POST" action="{{ route('employer.jobs.update', $job) }}" class="cw-surface p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="cw-label" for="title">{{ __('ui.jobs.title') }}</label>
                    <input id="title" name="title" class="cw-field" value="{{ old('title', $job->title) }}" required>
                </div>

                <div>
                    <label class="cw-label" for="company_name">{{ __('ui.employer.company_name') }}</label>
                    <input id="company_name" name="company_name" class="cw-field" value="{{ old('company_name', $job->company_name) }}" required>
                </div>

                <div>
                    <label class="cw-label" for="description">{{ __('ui.jobs.description') }}</label>
                    <textarea id="description" name="description" rows="7" class="cw-field" required>{{ old('description', $job->description) }}</textarea>
                </div>

                <div>
                    <label class="cw-label" for="responsibilities">{{ __('ui.jobs.responsibilities') }}</label>
                    <textarea id="responsibilities" name="responsibilities" rows="5" class="cw-field">{{ old('responsibilities', $job->responsibilities) }}</textarea>
                </div>

                <div>
                    <label class="cw-label" for="requirements">{{ __('ui.jobs.requirements') }}</label>
                    <textarea id="requirements" name="requirements" rows="5" class="cw-field">{{ old('requirements', $job->requirements) }}</textarea>
                </div>

                <div>
                    <label class="cw-label" for="benefits">{{ __('ui.jobs.benefits') }}</label>
                    <textarea id="benefits" name="benefits" rows="5" class="cw-field">{{ old('benefits', $job->benefits) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="location">{{ __('ui.jobs.location') }}</label>
                        <input id="location" name="location" class="cw-field" value="{{ old('location', $job->location) }}" required>
                    </div>
                    <div>
                        <label class="cw-label" for="job_type">{{ __('ui.jobs.type') }}</label>
                        <select id="job_type" name="job_type" class="cw-field" required>
                            <option value="full_time" @selected(old('job_type', $job->job_type) === 'full_time')>{{ __('ui.jobs.full_time') }}</option>
                            <option value="part_time" @selected(old('job_type', $job->job_type) === 'part_time')>{{ __('ui.jobs.part_time') }}</option>
                            <option value="contract" @selected(old('job_type', $job->job_type) === 'contract')>{{ __('ui.jobs.contract') }}</option>
                            <option value="seasonal" @selected(old('job_type', $job->job_type) === 'seasonal')>{{ __('ui.jobs.seasonal') }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="cw-label" for="salary_min">{{ __('employer.job_form.minimum_salary') }}</label><input id="salary_min" type="number" name="salary_min" class="cw-field" value="{{ old('salary_min', $job->salary_min) }}"></div>
                    <div><label class="cw-label" for="salary_max">{{ __('employer.job_form.maximum_salary') }}</label><input id="salary_max" type="number" name="salary_max" class="cw-field" value="{{ old('salary_max', $job->salary_max) }}"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="experience_level">{{ __('employer.job_form.experience_level') }}</label>
                        <select id="experience_level" name="experience_level" class="cw-field">
                            <option value="">{{ __('employer.job_form.select_level') }}</option>
                            <option value="entry" @selected(old('experience_level', $job->experience_level) === 'entry')>{{ __('employer.job_form.level_entry') }}</option>
                            <option value="junior" @selected(old('experience_level', $job->experience_level) === 'junior')>{{ __('employer.job_form.level_junior') }}</option>
                            <option value="mid" @selected(old('experience_level', $job->experience_level) === 'mid')>{{ __('employer.job_form.level_mid') }}</option>
                            <option value="senior" @selected(old('experience_level', $job->experience_level) === 'senior')>{{ __('employer.job_form.level_senior') }}</option>
                            <option value="lead" @selected(old('experience_level', $job->experience_level) === 'lead')>{{ __('employer.job_form.level_lead') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="cw-label" for="education_required">{{ __('employer.job_form.education_required') }}</label>
                        <input id="education_required" name="education_required" class="cw-field" value="{{ old('education_required', $job->education_required) }}" placeholder="{{ __('employer.job_form.education_required_placeholder') }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="contract_duration">{{ __('employer.job_form.contract_duration') }}</label>
                        <input id="contract_duration" name="contract_duration" class="cw-field" value="{{ old('contract_duration', $job->contract_duration) }}" placeholder="{{ __('employer.job_form.contract_duration_placeholder') }}">
                    </div>
                    <div>
                        <label class="cw-label" for="start_date">{{ __('employer.job_form.start_date') }}</label>
                        <input id="start_date" type="date" name="start_date" class="cw-field" value="{{ old('start_date', optional($job->start_date)->format('Y-m-d')) }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="start_flexibility">{{ __('employer.job_form.start_flexibility') }}</label>
                        <input id="start_flexibility" name="start_flexibility" class="cw-field" value="{{ old('start_flexibility', $job->start_flexibility) }}" placeholder="{{ __('employer.job_form.start_flexibility_placeholder') }}">
                    </div>
                    <div>
                        <label class="cw-label" for="positions_available">{{ __('employer.job_form.number_of_positions') }}</label>
                        <input id="positions_available" type="number" min="1" name="positions_available" class="cw-field" value="{{ old('positions_available', $job->positions_available ?? 1) }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="working_hours">{{ __('employer.job_form.working_hours') }}</label>
                        <input id="working_hours" name="working_hours" class="cw-field" value="{{ old('working_hours', $job->working_hours) }}" placeholder="{{ __('employer.job_form.working_hours_placeholder') }}">
                    </div>
                    <div>
                        <label class="cw-label" for="shift_details">{{ __('employer.job_form.shift_details') }}</label>
                        <textarea id="shift_details" name="shift_details" rows="3" class="cw-field">{{ old('shift_details', $job->shift_details) }}</textarea>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="accommodation_details">{{ __('employer.job_form.accommodation_details') }}</label>
                        <textarea id="accommodation_details" name="accommodation_details" rows="3" class="cw-field">{{ old('accommodation_details', $job->accommodation_details) }}</textarea>
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700 mt-2">
                            <input type="checkbox" name="accommodation_provided" value="1" class="rounded border-slate-300" @checked(old('accommodation_provided', $job->accommodation_provided))>
                            {{ __('employer.job_form.accommodation_provided') }}
                        </label>
                    </div>
                    <div>
                        <label class="cw-label" for="visa_support_details">{{ __('employer.job_form.visa_support_details') }}</label>
                        <textarea id="visa_support_details" name="visa_support_details" rows="3" class="cw-field">{{ old('visa_support_details', $job->visa_support_details) }}</textarea>
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700 mt-2">
                            <input type="checkbox" name="visa_support" value="1" class="rounded border-slate-300" @checked(old('visa_support', $job->visa_support))>
                            {{ __('employer.job_form.visa_support') }}
                        </label>
                    </div>
                </div>

                <div>
                    <label class="cw-label" for="application_instructions">{{ __('employer.job_form.application_instructions') }}</label>
                    <textarea id="application_instructions" name="application_instructions" rows="4" class="cw-field">{{ old('application_instructions', $job->application_instructions) }}</textarea>
                </div>

                <div class="flex flex-wrap gap-4">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="is_featured" value="1" class="rounded border-slate-300" @checked(old('is_featured', $job->is_featured))>
                        {{ __('employer.job_form.featured_job') }}
                    </label>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="is_urgent" value="1" class="rounded border-slate-300" @checked(old('is_urgent', $job->is_urgent))>
                        {{ __('employer.job_form.urgent_job') }}
                    </label>
                </div>

                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="is_active" value="1" class="rounded border-slate-300" @checked(old('is_active', $job->is_active))>
                    {{ __('employer.job_form.publish_immediately') }}
                </label>

                <div class="flex gap-2">
                    <button type="submit" class="cw-button-primary">{{ __('employer.job_form.save_changes') }}</button>
                    <a href="{{ route('employer.jobs.index') }}" class="cw-button-secondary">{{ __('common.cancel') }}</a>
                </div>
            </form>
        </div>
    </section>
</x-app-layout>
