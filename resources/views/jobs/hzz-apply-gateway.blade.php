<x-app-layout>
    <x-slot name="title">{{ __('ui.jobs_show.apply_via_crowork') }} - {{ $job->localized('title') }}</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-3xl">
            <div class="mb-6 text-sm text-slate-500">
                <a href="{{ route('jobs.index') }}" class="hover:text-slate-900">{{ __('navigation.jobs') }}</a>
                <span class="mx-1">/</span>
                <a href="{{ route('jobs.show', $job) }}" class="hover:text-slate-900">{{ $job->localized('title') }}</a>
                <span class="mx-1">/</span>
                <span class="text-slate-700">{{ __('ui.jobs_show.apply_via_crowork') }}</span>
            </div>

            <article class="cw-surface p-6 md:p-7">
                <p class="text-xs uppercase tracking-[0.08em] text-slate-500 font-semibold mb-2">HZZ</p>
                <h1 class="cw-display text-3xl md:text-5xl mb-3">{{ __('ui.jobs_apply.hzz_external_title') }}</h1>
                <p class="text-slate-700 leading-relaxed mb-4">{{ __('ui.jobs_apply.hzz_external_intro') }}</p>

                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 mb-6">
                    <h2 class="text-sm font-semibold text-slate-900 mb-1">{{ __('ui.jobs_apply.hzz_profile_ready') }}</h2>
                    <p class="text-sm text-slate-700">
                        {{ data_get($profileSnapshot, 'first_name') }} {{ data_get($profileSnapshot, 'last_name') }}
                        @if(data_get($profileSnapshot, 'current_city'))
                            · {{ data_get($profileSnapshot, 'current_city') }}
                        @endif
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('jobs.hzz.open', $job) }}" target="_blank" rel="noopener" class="cw-button-primary text-center">
                        {{ __('ui.jobs_apply.open_application') }}
                    </a>
                    <a href="{{ route('worker.profile.edit') }}" class="cw-button-secondary text-center">
                        {{ __('ui.jobs_apply.update_profile') }}
                    </a>
                </div>
            </article>
        </div>
    </section>
</x-app-layout>
