<x-app-layout>
    <x-slot name="title">{{ __('ui.jobs.edit') }}</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-4xl">
            <h1 class="cw-display text-4xl md:text-6xl mb-6">{{ __('ui.jobs.edit_heading') }}</h1>

            <form method="POST" action="{{ route('employer.jobs.update', $job) }}" enctype="multipart/form-data" class="cw-surface p-6 md:p-8 space-y-6">
                @csrf
                @method('PUT')

                <div class="rounded-xl border border-sky-200 bg-sky-50 p-4 text-sm text-slate-700 space-y-3">
                    <p class="font-semibold text-slate-900">{{ __('employer.job_preview.title') }}</p>
                    <p>{{ __('employer.job_preview.help') }}</p>
                    <div class="flex flex-col md:flex-row gap-2">
                        <input id="persistent-preview-link" type="text" class="cw-field w-full" readonly value="{{ route('jobs.preview.shared', ['token' => $job->preview_token]) }}">
                        <button
                            type="button"
                            class="cw-button-secondary"
                            data-copy-target="persistent-preview-link"
                            data-copy-label-default="{{ __('employer.job_preview.copy') }}"
                            data-copy-label-success="{{ __('employer.job_preview.copied') }}"
                        >{{ __('employer.job_preview.copy') }}</button>
                        <a href="{{ route('jobs.preview.shared', ['token' => $job->preview_token]) }}" target="_blank" rel="noopener" class="cw-button-secondary">{{ __('employer.job_preview.open') }}</a>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700">
                    <p class="font-semibold text-slate-900">{{ __('employer.job_form.required_fields_notice') }}</p>
                    <p class="mt-1">{{ __('employer.job_form.submission_policy_notice') }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="title">{{ __('ui.jobs.title') }} <span class="text-rose-600" aria-hidden="true">*</span></label>
                        <input id="title" name="title" class="cw-field w-full" value="{{ old('title', $job->title) }}" required>
                    </div>

                    <div>
                        <label class="cw-label" for="company_name">{{ __('ui.employer.company_name') }} <span class="text-rose-600" aria-hidden="true">*</span></label>
                        <input id="company_name" name="company_name" class="cw-field w-full" value="{{ old('company_name', $job->company_name) }}" required>
                    </div>
                </div>

                <div>
                    <label class="cw-label" for="description">{{ __('ui.jobs.description') }} <span class="text-rose-600" aria-hidden="true">*</span></label>
                    <textarea id="description" name="description" rows="7" class="cw-field w-full" required>{{ old('description', $job->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="responsibilities">{{ __('ui.jobs.responsibilities') }}</label>
                        <textarea id="responsibilities" name="responsibilities" rows="5" class="cw-field w-full">{{ old('responsibilities', $job->responsibilities) }}</textarea>
                    </div>

                    <div>
                        <label class="cw-label" for="requirements">{{ __('ui.jobs.requirements') }}</label>
                        <textarea id="requirements" name="requirements" rows="5" class="cw-field w-full">{{ old('requirements', $job->requirements) }}</textarea>
                    </div>
                </div>

                <div>
                    <label class="cw-label" for="benefits">{{ __('ui.jobs.benefits') }}</label>
                    <textarea id="benefits" name="benefits" rows="5" class="cw-field w-full">{{ old('benefits', $job->benefits) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="location">{{ __('ui.jobs.location') }} <span class="text-rose-600" aria-hidden="true">*</span></label>
                        <input id="location" name="location" class="cw-field w-full" value="{{ old('location', $job->location) }}" required>
                    </div>
                    <div>
                        <label class="cw-label" for="job_type">{{ __('ui.jobs.type') }} <span class="text-rose-600" aria-hidden="true">*</span></label>
                        <select id="job_type" name="job_type" class="cw-field w-full" required>
                            <option value="full_time" @selected(old('job_type', $job->job_type) === 'full_time')>{{ __('ui.jobs.full_time') }}</option>
                            <option value="part_time" @selected(old('job_type', $job->job_type) === 'part_time')>{{ __('ui.jobs.part_time') }}</option>
                            <option value="contract" @selected(old('job_type', $job->job_type) === 'contract')>{{ __('ui.jobs.contract') }}</option>
                            <option value="seasonal" @selected(old('job_type', $job->job_type) === 'seasonal')>{{ __('ui.jobs.seasonal') }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="cw-label" for="salary_min">{{ __('employer.job_form.minimum_salary') }}</label><input id="salary_min" type="number" name="salary_min" class="cw-field w-full" value="{{ old('salary_min', $job->salary_min) }}"></div>
                    <div><label class="cw-label" for="salary_max">{{ __('employer.job_form.maximum_salary') }}</label><input id="salary_max" type="number" name="salary_max" class="cw-field w-full" value="{{ old('salary_max', $job->salary_max) }}"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="experience_level">{{ __('employer.job_form.experience_level') }}</label>
                        <select id="experience_level" name="experience_level" class="cw-field w-full">
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
                        <input id="education_required" name="education_required" class="cw-field w-full" value="{{ old('education_required', $job->education_required) }}" placeholder="{{ __('employer.job_form.education_required_placeholder') }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="contract_duration">{{ __('employer.job_form.contract_duration') }}</label>
                        <input id="contract_duration" name="contract_duration" class="cw-field w-full" value="{{ old('contract_duration', $job->contract_duration) }}" placeholder="{{ __('employer.job_form.contract_duration_placeholder') }}">
                    </div>
                    <div>
                        <label class="cw-label" for="start_date">{{ __('employer.job_form.start_date') }}</label>
                        <input id="start_date" type="date" name="start_date" class="cw-field w-full" value="{{ old('start_date', optional($job->start_date)->format('Y-m-d')) }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="start_flexibility">{{ __('employer.job_form.start_flexibility') }}</label>
                        <input id="start_flexibility" name="start_flexibility" class="cw-field w-full" value="{{ old('start_flexibility', $job->start_flexibility) }}" placeholder="{{ __('employer.job_form.start_flexibility_placeholder') }}">
                    </div>
                    <div>
                        <label class="cw-label" for="positions_available">{{ __('employer.job_form.number_of_positions') }}</label>
                        <input id="positions_available" type="number" min="1" name="positions_available" class="cw-field w-full" value="{{ old('positions_available', $job->positions_available ?? 1) }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="working_hours">{{ __('employer.job_form.working_hours') }}</label>
                        <input id="working_hours" name="working_hours" class="cw-field w-full" value="{{ old('working_hours', $job->working_hours) }}" placeholder="{{ __('employer.job_form.working_hours_placeholder') }}">
                    </div>
                    <div>
                        <label class="cw-label" for="shift_details">{{ __('employer.job_form.shift_details') }}</label>
                        <textarea id="shift_details" name="shift_details" rows="3" class="cw-field w-full">{{ old('shift_details', $job->shift_details) }}</textarea>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="cw-label" for="accommodation_details">{{ __('employer.job_form.accommodation_details') }}</label>
                        <textarea id="accommodation_details" name="accommodation_details" rows="3" class="cw-field w-full">{{ old('accommodation_details', $job->accommodation_details) }}</textarea>
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700 mt-2">
                            <input type="checkbox" name="accommodation_provided" value="1" class="rounded border-slate-300" @checked(old('accommodation_provided', $job->accommodation_provided))>
                            {{ __('employer.job_form.accommodation_provided') }}
                        </label>
                    </div>
                    <div>
                        <label class="cw-label" for="visa_support_details">{{ __('employer.job_form.visa_support_details') }}</label>
                        <textarea id="visa_support_details" name="visa_support_details" rows="3" class="cw-field w-full">{{ old('visa_support_details', $job->visa_support_details) }}</textarea>
                        <label class="inline-flex items-center gap-2 text-sm text-slate-700 mt-2">
                            <input type="checkbox" name="visa_support" value="1" class="rounded border-slate-300" @checked(old('visa_support', $job->visa_support))>
                            {{ __('employer.job_form.visa_support') }}
                        </label>
                    </div>
                </div>

                <div>
                    <label class="cw-label" for="application_instructions">{{ __('employer.job_form.application_instructions') }}</label>
                    <textarea id="application_instructions" name="application_instructions" rows="4" class="cw-field w-full">{{ old('application_instructions', $job->application_instructions) }}</textarea>
                </div>

                <div class="space-y-3">
                    <label class="cw-label" for="cover_image">{{ __('employer.job_form.cover_image') }}</label>
                    <input id="cover_image" type="file" name="cover_image" accept="image/jpeg,image/png,image/webp" class="cw-field" data-job-cover-file>
                    <p class="text-xs text-slate-500">{{ __('employer.job_form.cover_image_help') }}</p>

                    <input type="hidden" name="cover_crop_zoom" value="1" data-job-cover-zoom-input>
                    <input type="hidden" name="cover_crop_x" value="0" data-job-cover-x-input>
                    <input type="hidden" name="cover_crop_y" value="0" data-job-cover-y-input>

                    <div class="aspect-[2/1] overflow-hidden rounded-xl border border-slate-200 bg-slate-100" data-job-cover-preview>
                        @if($job->cover_image_path)
                            <img src="{{ asset('storage/' . $job->cover_image_path) }}" alt="{{ $job->title }} cover" class="h-full w-full object-cover" data-job-cover-image>
                        @else
                            <div class="h-full w-full grid place-items-center text-sm text-slate-500" data-job-cover-fallback>{{ __('employer.job_form.cover_placeholder') }}</div>
                        @endif
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
                    <button type="submit" class="cw-button-primary">{{ __('employer.job_form.save_changes') }}</button>
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

                const applyCoverTransform = function () {
                    const image = preview?.querySelector('img');
                    if (!image) {
                        return;
                    }

                    const zoom = Number(zoomRange?.value || 1);
                    const x = Number(xRange?.value || 0);
                    const y = Number(yRange?.value || 0);

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

                if (fileInput && preview && zoomRange && xRange && yRange) {
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
                            applyCoverTransform();
                        };
                        reader.readAsDataURL(file);
                    });

                    zoomRange.addEventListener('input', applyCoverTransform);
                    xRange.addEventListener('input', applyCoverTransform);
                    yRange.addEventListener('input', applyCoverTransform);
                }

                document.querySelectorAll('[data-copy-target]').forEach(function (button) {
                    button.addEventListener('click', function () {
                        const input = document.getElementById(button.dataset.copyTarget);
                        if (!input) {
                            return;
                        }

                        input.focus();
                        input.select();

                        const copiedLabel = button.dataset.copyLabelSuccess || 'Copied';
                        const defaultLabel = button.dataset.copyLabelDefault || button.textContent;

                        const onSuccess = function () {
                            button.textContent = copiedLabel;
                            setTimeout(function () {
                                button.textContent = defaultLabel;
                            }, 1400);
                        };

                        if (navigator.clipboard && window.isSecureContext) {
                            navigator.clipboard.writeText(input.value).then(onSuccess).catch(function () {
                                if (document.execCommand('copy')) {
                                    onSuccess();
                                }
                            });
                            return;
                        }

                        if (document.execCommand('copy')) {
                            onSuccess();
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
