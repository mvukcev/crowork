<x-app-layout>
    <x-slot name="title">{{ __('ui.jobs_apply.page_title', ['title' => $job->localized('title')]) }}</x-slot>

    @php
        $isHzzOfficial = $job->isHzzOfficial();
    @endphp

    <section class="cw-section">
        <div class="cw-container max-w-4xl">
            <div class="mb-6 text-sm text-slate-500">
                <a href="{{ route('jobs.index') }}" class="hover:text-slate-900">{{ __('ui.navigation.jobs') }}</a>
                <span class="mx-1">/</span>
                <a href="{{ route('jobs.show', $job) }}" class="hover:text-slate-900">{{ $job->localized('title') }}</a>
                <span class="mx-1">/</span>
                <span class="text-slate-700">{{ __('ui.jobs_apply.apply') }}</span>
            </div>

            @if($alreadyApplied)
                <div class="cw-surface p-6 text-center">
                    <h1 class="text-2xl font-semibold text-slate-900 mb-2">{{ __('ui.jobs_apply.already_sent_title') }}</h1>
                    @if($existingApplication?->created_at)
                        <p class="text-slate-600 mb-4">{{ __('ui.jobs_apply.already_sent_with_date', ['date' => $existingApplication->created_at->translatedFormat('j M Y')]) }}</p>
                    @else
                        <p class="text-slate-600 mb-4">{{ __('ui.jobs_apply.already_sent_without_date') }}</p>
                    @endif
                    <div class="flex flex-wrap justify-center gap-2">
                        <a href="{{ route('jobs.show', $job) }}" class="cw-button-secondary">{{ __('ui.jobs_apply.back_to_job') }}</a>
                        <a href="{{ route('worker.applications.index') }}" class="cw-button-primary">{{ __('ui.jobs_apply.track_my_applications') }}</a>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                    <div class="lg:col-span-2 cw-surface p-6">
                        <h1 class="cw-display text-3xl md:text-5xl mb-3">{{ __('ui.jobs_apply.apply_for_role', ['title' => $job->localized('title')]) }}</h1>
                        @if($isHzzOfficial)
                            <p class="text-sm font-medium text-slate-800 mb-2">{{ __('ui.jobs_show.apply_via_crowork') }}</p>
                        @endif
                        <p class="text-slate-600 mb-2">{{ __('ui.jobs_apply.intro_line') }}</p>
                        <p class="text-sm text-slate-500 mb-6">{{ __('ui.jobs_apply.updates_line') }}</p>

                        <form method="POST" action="{{ route('jobs.apply.store', $job) }}" class="space-y-4" data-cw-track-submit="job_application_submit" enctype="multipart/form-data" data-apply-wizard>
                            @csrf

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-2 text-xs">
                                <button type="button" class="cw-button-secondary" data-step-trigger="1">1. Profil</button>
                                <button type="button" class="cw-button-secondary" data-step-trigger="2">2. CV</button>
                                <button type="button" class="cw-button-secondary" data-step-trigger="3">3. Motivacijsko</button>
                                <button type="button" class="cw-button-secondary" data-step-trigger="4">4. Pregled</button>
                            </div>

                            <div data-step="1" class="space-y-3">
                                <p class="text-sm text-slate-700">Provjeri podatke profila u desnom panelu. Ako nešto nedostaje, prvo ažuriraj profil.</p>
                                <input type="hidden" name="message" value="{{ old('message') }}">
                            </div>

                            <div data-step="2" class="space-y-3 hidden">
                                <p class="text-sm font-medium text-slate-900">Odaberi CV za ovu prijavu</p>
                                <label class="flex items-start gap-2 text-sm text-slate-700">
                                    <input type="radio" name="cv_choice" value="profile" class="mt-1" @checked(old('cv_choice', 'profile') === 'profile')>
                                    <span>Koristi CroWork profil kao CV (preporučeno)</span>
                                </label>
                                <label class="flex items-start gap-2 text-sm text-slate-700">
                                    <input type="radio" name="cv_choice" value="upload" class="mt-1" @checked(old('cv_choice') === 'upload')>
                                    <span>Upload PDF/DOC CV-a</span>
                                </label>
                                <div data-upload-cv class="hidden">
                                    <label class="cw-label" for="cv_file">Upload CV</label>
                                    <input id="cv_file" type="file" name="cv_file" class="cw-field" accept=".pdf,.doc,.docx">
                                    @error('cv_file')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div data-step="3" class="space-y-3 hidden">
                                <p class="text-sm font-medium text-slate-900">Motivacijsko pismo</p>
                                <label class="flex items-start gap-2 text-sm text-slate-700">
                                    <input type="radio" name="cover_letter_mode" value="none" class="mt-1" @checked(old('cover_letter_mode', 'preset') === 'none')>
                                    <span>Bez motivacijskog pisma</span>
                                </label>
                                <label class="flex items-start gap-2 text-sm text-slate-700">
                                    <input type="radio" name="cover_letter_mode" value="preset" class="mt-1" @checked(old('cover_letter_mode', 'preset') === 'preset')>
                                    <span>Koristi CroWork predložak</span>
                                </label>
                                <label class="flex items-start gap-2 text-sm text-slate-700">
                                    <input type="radio" name="cover_letter_mode" value="custom" class="mt-1" @checked(old('cover_letter_mode') === 'custom')>
                                    <span>Napiši vlastito motivacijsko pismo</span>
                                </label>

                                <div data-cover-preset>
                                    <label class="cw-label" for="cover_letter_preset">Predložak</label>
                                    <select id="cover_letter_preset" name="cover_letter_preset" class="cw-field">
                                        <option value="short" @selected(old('cover_letter_preset') === 'short')>Kratki</option>
                                        <option value="standard" @selected(old('cover_letter_preset', 'standard') === 'standard')>Standard</option>
                                        <option value="detailed" @selected(old('cover_letter_preset') === 'detailed')>Detaljni</option>
                                    </select>
                                </div>

                                <div data-cover-custom class="hidden">
                                    <label class="cw-label" for="cover_letter_text">{{ __('ui.jobs_apply.message_label') }}</label>
                                    <textarea id="cover_letter_text" name="cover_letter_text" rows="6" class="cw-field" placeholder="{{ __('ui.jobs_apply.message_placeholder') }}">{{ old('cover_letter_text') }}</textarea>
                                    @error('cover_letter_text')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div data-step="4" class="space-y-3 hidden">
                                <p class="text-sm font-medium text-slate-900">Pregled prijave prije slanja</p>
                                <ul class="text-sm text-slate-700 space-y-1 list-disc pl-5">
                                    <li>Profil kandidata: spreman</li>
                                    <li>CV: odabran prema izboru u koraku 2</li>
                                    <li>Motivacijsko pismo: odabrano u koraku 3</li>
                                    <li>Nakon slanja prijava će biti evidentirana u Moje prijave</li>
                                </ul>
                            </div>

                            <label class="flex items-start gap-2 text-sm text-slate-700">
                                <input type="checkbox" name="consent" value="1" class="mt-1 rounded border-slate-300" @checked(old('consent'))>
                                <span>{{ __('ui.jobs_apply.consent_text') }}</span>
                            </label>
                            @error('consent')<p class="text-xs text-red-600">{{ $message }}</p>@enderror

                            <div class="flex flex-wrap gap-2">
                                <button type="button" class="cw-button-secondary" data-step-prev>Prethodni korak</button>
                                <button type="button" class="cw-button-secondary" data-step-next>Sljedeći korak</button>
                                <button type="submit" class="cw-button-primary">{{ $isHzzOfficial ? __('ui.jobs_show.apply_via_crowork') : __('ui.jobs_apply.submit_application') }}</button>
                            </div>
                        </form>
                    </div>

                    <aside class="cw-surface p-5">
                        <h2 class="text-lg font-semibold text-slate-900 mb-3">{{ __('ui.jobs_apply.snapshot_heading') }}</h2>
                        <p class="text-sm text-slate-700 mb-1"><strong>{{ __('ui.jobs_apply.name_label') }}</strong> {{ data_get($profileSnapshot, 'first_name') }} {{ data_get($profileSnapshot, 'last_name') }}</p>
                        <p class="text-sm text-slate-700 mb-1"><strong>{{ __('ui.jobs_apply.nationality_label') }}</strong> {{ data_get($profileSnapshot, 'nationality_country_code', __('ui.jobs_apply.not_available')) }}</p>
                        <p class="text-sm text-slate-700 mb-1"><strong>{{ __('ui.jobs_apply.current_city_label') }}</strong> {{ data_get($profileSnapshot, 'current_city', __('ui.jobs_apply.not_available')) }}</p>
                        <p class="text-sm text-slate-700 mb-1"><strong>{{ __('ui.jobs_apply.desired_city_label') }}</strong> {{ data_get($profileSnapshot, 'desired_city', __('ui.jobs_apply.not_available')) }}</p>
                        <p class="text-sm text-slate-700 mb-3"><strong>{{ __('ui.jobs_apply.availability_label') }}</strong> {{ data_get($profileSnapshot, 'availability_date', __('ui.jobs_apply.not_available')) }}</p>

                        @if(!empty($profileSkills))
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">{{ __('ui.jobs_apply.skills_label') }}</p>
                            <div class="flex flex-wrap gap-1.5 mb-3">
                                @foreach($profileSkills as $skill)
                                    <span class="cw-chip">{{ $skill }}</span>
                                @endforeach
                            </div>
                        @endif

                        @if(is_array(data_get($profileSnapshot, 'languages')) && count(data_get($profileSnapshot, 'languages')) > 0)
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">{{ __('ui.jobs_apply.languages_label') }}</p>
                            <ul class="text-sm text-slate-700 mb-3 space-y-1">
                                @foreach(data_get($profileSnapshot, 'languages') as $language)
                                    @if(!empty($language['language']))
                                        <li>{{ $language['language'] }}{{ !empty($language['level']) ? ' (' . $language['level'] . ')' : '' }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif

                        <a href="{{ route('worker.profile.edit') }}" class="cw-button-secondary w-full">{{ __('ui.jobs_apply.update_profile') }}</a>
                    </aside>
                </div>
            @endif
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.querySelector('[data-apply-wizard]');
                if (!form) {
                    return;
                }

                const steps = Array.from(form.querySelectorAll('[data-step]'));
                const stepButtons = Array.from(form.querySelectorAll('[data-step-trigger]'));
                const prevBtn = form.querySelector('[data-step-prev]');
                const nextBtn = form.querySelector('[data-step-next]');
                let current = 1;

                const updateModes = () => {
                    const cvChoice = form.querySelector('input[name="cv_choice"]:checked')?.value || 'profile';
                    const cvUpload = form.querySelector('[data-upload-cv]');
                    if (cvUpload) {
                        cvUpload.classList.toggle('hidden', cvChoice !== 'upload');
                    }

                    const coverMode = form.querySelector('input[name="cover_letter_mode"]:checked')?.value || 'preset';
                    const preset = form.querySelector('[data-cover-preset]');
                    const custom = form.querySelector('[data-cover-custom]');
                    if (preset) {
                        preset.classList.toggle('hidden', coverMode !== 'preset');
                    }
                    if (custom) {
                        custom.classList.toggle('hidden', coverMode !== 'custom');
                    }
                };

                const activate = (step) => {
                    current = Math.min(4, Math.max(1, step));
                    steps.forEach((el) => el.classList.toggle('hidden', Number(el.dataset.step) !== current));
                    stepButtons.forEach((btn) => {
                        const active = Number(btn.dataset.stepTrigger) === current;
                        btn.classList.toggle('cw-button-primary', active);
                        btn.classList.toggle('cw-button-secondary', !active);
                    });
                    if (prevBtn) prevBtn.disabled = current === 1;
                    if (nextBtn) nextBtn.disabled = current === 4;
                    updateModes();
                };

                stepButtons.forEach((btn) => btn.addEventListener('click', () => activate(Number(btn.dataset.stepTrigger))));
                prevBtn?.addEventListener('click', () => activate(current - 1));
                nextBtn?.addEventListener('click', () => activate(current + 1));
                form.querySelectorAll('input[name="cv_choice"], input[name="cover_letter_mode"]').forEach((el) => {
                    el.addEventListener('change', updateModes);
                });

                activate(1);
            });
        </script>
    @endpush
</x-app-layout>
