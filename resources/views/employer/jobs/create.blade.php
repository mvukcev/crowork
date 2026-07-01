<x-app-layout>
    <x-slot name="title">{{ __('ui.jobs.create') }}</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-4xl">
            <h1 class="cw-display text-4xl md:text-6xl mb-6">{{ __('ui.jobs.create_heading') }}</h1>

            <form method="POST" action="{{ route('employer.jobs.store') }}" enctype="multipart/form-data" class="cw-surface p-6 space-y-4" data-cw-track-submit="employer_job_create">
                @csrf

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                    <p class="font-semibold text-slate-900">{{ __('employer.job_form.required_fields_notice') }}</p>
                    <p class="mt-1">{{ __('employer.job_form.submission_policy_notice') }}</p>
                </div>

                <div>
                    <label class="cw-label" for="title">{{ __('ui.jobs.title') }} <span class="text-rose-600" aria-hidden="true">*</span></label>
                    <input id="title" name="title" class="cw-field" value="{{ old('title') }}" required>
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <div>
                    <label class="cw-label" for="company_name">{{ __('ui.employer.company_name') }} <span class="text-rose-600" aria-hidden="true">*</span></label>
                    <input id="company_name" name="company_name" class="cw-field" value="{{ old('company_name') }}" required>
                    <x-input-error :messages="$errors->get('company_name')" class="mt-1" />
                </div>

                <div>
                    <label class="cw-label" for="description">{{ __('ui.jobs.description') }} <span class="text-rose-600" aria-hidden="true">*</span></label>
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
                        <label class="cw-label" for="location">{{ __('ui.jobs.location') }} <span class="text-rose-600" aria-hidden="true">*</span></label>
                        <input id="location" name="location" class="cw-field" value="{{ old('location') }}" required>
                        <x-input-error :messages="$errors->get('location')" class="mt-1" />
                    </div>
                    <div>
                        <label class="cw-label" for="job_type">{{ __('ui.jobs.type') }} <span class="text-rose-600" aria-hidden="true">*</span></label>
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
                        <label class="cw-label" for="salary_min">{{ __('employer.job_form.minimum_salary') }}</label>
                        <input id="salary_min" type="number" name="salary_min" class="cw-field" value="{{ old('salary_min') }}">
                    </div>
                    <div>
                        <label class="cw-label" for="salary_max">{{ __('employer.job_form.maximum_salary') }}</label>
                        <input id="salary_max" type="number" name="salary_max" class="cw-field" value="{{ old('salary_max') }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="experience_level">{{ __('employer.job_form.experience_level') }}</label>
                        <select id="experience_level" name="experience_level" class="cw-field">
                            <option value="">{{ __('employer.job_form.select_level') }}</option>
                            <option value="entry" @selected(old('experience_level') === 'entry')>{{ __('employer.job_form.level_entry') }}</option>
                            <option value="junior" @selected(old('experience_level') === 'junior')>{{ __('employer.job_form.level_junior') }}</option>
                            <option value="mid" @selected(old('experience_level') === 'mid')>{{ __('employer.job_form.level_mid') }}</option>
                            <option value="senior" @selected(old('experience_level') === 'senior')>{{ __('employer.job_form.level_senior') }}</option>
                            <option value="lead" @selected(old('experience_level') === 'lead')>{{ __('employer.job_form.level_lead') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="cw-label" for="education_required">{{ __('employer.job_form.education_required') }}</label>
                        <input id="education_required" name="education_required" class="cw-field" value="{{ old('education_required') }}" placeholder="{{ __('employer.job_form.education_required_placeholder') }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="contract_duration">{{ __('employer.job_form.contract_duration') }}</label>
                        <input id="contract_duration" name="contract_duration" class="cw-field" value="{{ old('contract_duration') }}" placeholder="{{ __('employer.job_form.contract_duration_placeholder') }}">
                    </div>
                    <div>
                        <label class="cw-label" for="start_date">{{ __('employer.job_form.start_date') }}</label>
                        <input id="start_date" type="date" name="start_date" class="cw-field" value="{{ old('start_date') }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="start_flexibility">{{ __('employer.job_form.start_flexibility') }}</label>
                        <input id="start_flexibility" name="start_flexibility" class="cw-field" value="{{ old('start_flexibility') }}" placeholder="{{ __('employer.job_form.start_flexibility_placeholder') }}">
                    </div>
                    <div>
                        <label class="cw-label" for="positions_available">{{ __('employer.job_form.number_of_positions') }}</label>
                        <input id="positions_available" type="number" min="1" name="positions_available" class="cw-field" value="{{ old('positions_available', 1) }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="working_hours">{{ __('employer.job_form.working_hours') }}</label>
                        <input id="working_hours" name="working_hours" class="cw-field" value="{{ old('working_hours') }}" placeholder="{{ __('employer.job_form.working_hours_placeholder') }}">
                    </div>
                    <div>
                        <label class="cw-label" for="shift_details">{{ __('employer.job_form.shift_details') }}</label>
                        <textarea id="shift_details" name="shift_details" rows="3" class="cw-field">{{ old('shift_details') }}</textarea>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="accommodation_details">{{ __('employer.job_form.accommodation_details') }}</label>
                        <textarea id="accommodation_details" name="accommodation_details" rows="3" class="cw-field">{{ old('accommodation_details') }}</textarea>
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700 mt-2">
                            <input type="checkbox" name="accommodation_provided" value="1" class="rounded border-slate-300" @checked(old('accommodation_provided'))>
                            {{ __('employer.job_form.accommodation_provided') }}
                        </label>
                    </div>
                    <div>
                        <label class="cw-label" for="visa_support_details">{{ __('employer.job_form.visa_support_details') }}</label>
                        <textarea id="visa_support_details" name="visa_support_details" rows="3" class="cw-field">{{ old('visa_support_details') }}</textarea>
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700 mt-2">
                            <input type="checkbox" name="visa_support" value="1" class="rounded border-slate-300" @checked(old('visa_support'))>
                            {{ __('employer.job_form.visa_support') }}
                        </label>
                    </div>
                </div>

                <div>
                    <label class="cw-label" for="application_instructions">{{ __('employer.job_form.application_instructions') }}</label>
                    <textarea id="application_instructions" name="application_instructions" rows="4" class="cw-field">{{ old('application_instructions') }}</textarea>
                </div>

                <div class="space-y-3">
                    <label class="cw-label" for="cover_image">{{ __('employer.job_form.cover_image') }}</label>
                    <input id="cover_image" type="file" name="cover_image" accept="image/jpeg,image/png,image/webp" class="cw-field" data-job-cover-file>
                    <p class="text-xs text-slate-500">{{ __('employer.job_form.cover_image_help') }}</p>

                    <input type="hidden" name="cover_crop_zoom" value="1" data-job-cover-zoom-input>
                    <input type="hidden" name="cover_crop_x" value="0" data-job-cover-x-input>
                    <input type="hidden" name="cover_crop_y" value="0" data-job-cover-y-input>

                    <div class="aspect-[2/1] overflow-hidden rounded-xl border border-slate-200 bg-slate-100" data-job-cover-preview>
                        <div class="h-full w-full grid place-items-center text-sm text-slate-500" data-job-cover-fallback>{{ __('employer.job_form.cover_placeholder') }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <label class="text-xs text-slate-500">{{ __('employer.settings.zoom') }}
                            <input type="range" min="1" max="3" step="0.05" value="1" class="w-full" data-job-cover-zoom>
                        </label>
                        <label class="text-xs text-slate-500">{{ __('employer.settings.horizontal_position') }}
                            <input type="range" min="-100" max="100" step="1" value="0" class="w-full" data-job-cover-x>
                        </label>
                        <label class="text-xs text-slate-500">{{ __('employer.settings.vertical_position') }}
                            <input type="range" min="-100" max="100" step="1" value="0" class="w-full" data-job-cover-y>
                        </label>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="cw-button-primary">{{ __('employer.job_form.create_job') }}</button>
                    <a href="{{ route('employer.jobs.index') }}" class="cw-button-secondary">{{ __('common.cancel') }}</a>
                </div>
            </form>
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const fileInput = document.querySelector('[data-job-cover-file]');
                const preview = document.querySelector('[data-job-cover-preview]');
                const zoomRange = document.querySelector('[data-job-cover-zoom]');
                const xRange = document.querySelector('[data-job-cover-x]');
                const yRange = document.querySelector('[data-job-cover-y]');
                const zoomInput = document.querySelector('[data-job-cover-zoom-input]');
                const xInput = document.querySelector('[data-job-cover-x-input]');
                const yInput = document.querySelector('[data-job-cover-y-input]');

                if (!fileInput || !preview || !zoomRange || !xRange || !yRange) {
                    return;
                }

                const applyTransform = function () {
                    const image = preview.querySelector('img');
                    if (!image) {
                        return;
                    }

                    const zoom = Number(zoomRange.value || 1);
                    const x = Number(xRange.value || 0);
                    const y = Number(yRange.value || 0);

                    const panFactor = zoom > 1 ? ((zoom - 1) / zoom) : 0;
                    const translateX = x * panFactor;
                    const translateY = y * panFactor;

                    preview.style.position = 'relative';
                    image.style.position = 'absolute';
                    image.style.inset = '0';
                    image.style.transform = 'translate(' + translateX + '%, ' + translateY + '%) scale(' + zoom + ')';
                    image.style.transformOrigin = 'center center';

                    if (zoomInput) zoomInput.value = String(zoom);
                    if (xInput) xInput.value = String(x);
                    if (yInput) yInput.value = String(y);
                };

                fileInput.addEventListener('change', function (event) {
                    const file = event.target.files?.[0];
                    if (!file) {
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function (loadEvent) {
                        const oldImage = preview.querySelector('img');
                        const fallback = preview.querySelector('[data-job-cover-fallback]');
                        if (oldImage) oldImage.remove();
                        if (fallback) fallback.remove();

                        const image = document.createElement('img');
                        image.src = String(loadEvent.target?.result || '');
                        image.alt = 'Job cover preview';
                        image.className = 'h-full w-full object-cover block';
                        preview.appendChild(image);

                        zoomRange.value = '1';
                        xRange.value = '0';
                        yRange.value = '0';
                        applyTransform();
                    };
                    reader.readAsDataURL(file);
                });

                zoomRange.addEventListener('input', applyTransform);
                xRange.addEventListener('input', applyTransform);
                yRange.addEventListener('input', applyTransform);
            });
        </script>
    @endpush
</x-app-layout>
