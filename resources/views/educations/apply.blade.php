<x-app-layout>
    <x-slot name="title">Apply - {{ $education->title }}</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-3xl">
            <div class="mb-6 text-sm text-slate-500">
                <a href="{{ route('educations.index') }}" class="hover:text-slate-900">Educations</a>
                <span class="mx-1">/</span>
                <a href="{{ route('educations.show', $education) }}" class="hover:text-slate-900">{{ $education->title }}</a>
                <span class="mx-1">/</span>
                <span class="text-slate-700">Apply</span>
            </div>

            @if($alreadyApplied)
                <div class="cw-surface p-6 text-center">
                    <h1 class="text-2xl font-semibold text-slate-900 mb-2">Application already sent</h1>
                    <p class="text-slate-600 mb-4">You have already applied{{ $existingApplication?->created_at ? ' on '.$existingApplication->created_at->format('M j, Y') : '' }}.</p>
                    <div class="flex flex-wrap justify-center gap-2">
                        <a href="{{ route('worker.education-applications.index') }}" class="cw-button-primary">View my applications</a>
                        <a href="{{ route('educations.show', $education) }}" class="cw-button-secondary">Back to program</a>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                    <div class="lg:col-span-2 cw-surface p-6 md:p-7">
                        <h1 class="cw-display text-3xl md:text-5xl mb-3">Apply for {{ $education->title }}</h1>
                        <p class="text-slate-600 mb-2">Send your application and include a short motivation message.</p>
                        <p class="text-sm text-slate-500 mb-6">You can follow updates from providers in your education applications list.</p>

                        <form method="POST" action="{{ route('educations.apply.store', $education) }}" class="space-y-4" data-cw-track-submit="education_application_submit">
                            @csrf
                            <div>
                                <label class="cw-label" for="message">Motivation message (optional)</label>
                                <textarea id="message" name="message" rows="6" class="cw-field" placeholder="Share your goals and readiness.">{{ old('message') }}</textarea>
                                @error('message')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <label class="flex items-start gap-2 text-sm text-slate-700">
                                <input type="checkbox" name="consent" value="1" class="mt-1 rounded border-slate-300" @checked(old('consent'))>
                                <span>I confirm this application uses my current profile snapshot and I consent to share it with the education provider.</span>
                            </label>
                            @error('consent')<p class="text-xs text-red-600">{{ $message }}</p>@enderror

                            <button type="submit" class="cw-button-primary">Submit application</button>
                        </form>
                    </div>

                    <aside class="cw-surface p-5">
                        <h2 class="text-lg font-semibold text-slate-900 mb-3">Application snapshot preview</h2>
                        <p class="text-sm text-slate-700 mb-1"><strong>Name:</strong> {{ data_get($profileSnapshot, 'first_name') }} {{ data_get($profileSnapshot, 'last_name') }}</p>
                        <p class="text-sm text-slate-700 mb-1"><strong>Nationality:</strong> {{ data_get($profileSnapshot, 'nationality_country_code', 'N/A') }}</p>
                        <p class="text-sm text-slate-700 mb-1"><strong>Current city:</strong> {{ data_get($profileSnapshot, 'current_city', 'N/A') }}</p>
                        <p class="text-sm text-slate-700 mb-3"><strong>Desired city:</strong> {{ data_get($profileSnapshot, 'desired_city', 'N/A') }}</p>

                        @if(is_array(data_get($profileSnapshot, 'skills')) && count(data_get($profileSnapshot, 'skills')) > 0)
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Skills</p>
                            <div class="flex flex-wrap gap-1.5 mb-3">
                                @foreach(data_get($profileSnapshot, 'skills') as $skill)
                                    <span class="cw-chip">{{ $skill }}</span>
                                @endforeach
                            </div>
                        @endif

                        <a href="{{ route('worker.profile.edit') }}" class="cw-button-secondary w-full">Update profile</a>
                    </aside>
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
