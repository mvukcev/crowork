<x-app-layout>
    <x-slot name="title">{{ __('ui.jobs_apply.page_title', ['title' => $job->title]) }}</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-4xl">
            <div class="mb-6 text-sm text-slate-500">
                <a href="{{ route('jobs.index') }}" class="hover:text-slate-900">{{ __('ui.navigation.jobs') }}</a>
                <span class="mx-1">/</span>
                <a href="{{ route('jobs.show', $job) }}" class="hover:text-slate-900">{{ $job->title }}</a>
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
                        <h1 class="cw-display text-3xl md:text-5xl mb-3">{{ __('ui.jobs_apply.apply_for_role', ['title' => $job->title]) }}</h1>
                        <p class="text-slate-600 mb-2">{{ __('ui.jobs_apply.intro_line') }}</p>
                        <p class="text-sm text-slate-500 mb-6">{{ __('ui.jobs_apply.updates_line') }}</p>

                        <form method="POST" action="{{ route('jobs.apply.store', $job) }}" class="space-y-4" data-cw-track-submit="job_application_submit">
                            @csrf
                            <div>
                                <label class="cw-label" for="message">{{ __('ui.jobs_apply.message_label') }}</label>
                                <textarea id="message" name="message" rows="6" class="cw-field" placeholder="{{ __('ui.jobs_apply.message_placeholder') }}">{{ old('message') }}</textarea>
                                @error('message')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <label class="flex items-start gap-2 text-sm text-slate-700">
                                <input type="checkbox" name="consent" value="1" class="mt-1 rounded border-slate-300" @checked(old('consent'))>
                                <span>{{ __('ui.jobs_apply.consent_text') }}</span>
                            </label>
                            @error('consent')<p class="text-xs text-red-600">{{ $message }}</p>@enderror

                            <button type="submit" class="cw-button-primary">{{ __('ui.jobs_apply.submit_application') }}</button>
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
</x-app-layout>
