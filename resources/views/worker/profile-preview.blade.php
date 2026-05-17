<x-app-layout>
    <x-slot name="title">Pregled životopisa</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-4xl">
            <div class="flex items-start justify-between gap-3 mb-5">
                <div>
                    <p class="cw-kicker mb-1">Standardizirani životopis</p>
                    <h1 class="cw-display text-4xl md:text-6xl">Pregled životopisa</h1>
                    <p class="text-slate-600 mt-2">Ovo je prikaz profila koji će poslodavci vidjeti u vašim prijavama.</p>
                </div>
                <a href="{{ route('worker.profile.edit') }}" class="cw-button-secondary">Uredi profil</a>
            </div>

            <div class="cw-surface p-5 mb-5">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-slate-700">Kompletnost</span>
                    <span class="text-sm font-semibold text-slate-900">{{ $completeness }}%</span>
                </div>
                <div class="h-2 rounded-full bg-slate-100 overflow-hidden mb-3">
                    <div class="h-full bg-emerald-500" style="width: {{ $completeness }}%"></div>
                </div>
                @if(count($missingChecklist) > 0)
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($missingChecklist as $item)
                            <span class="cw-chip">{{ $item }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            <article class="cw-surface p-6 space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-2xl font-semibold text-slate-900">{{ trim(($profile->first_name ?? '') . ' ' . ($profile->last_name ?? '')) ?: 'Neimenovani radnik' }}</h2>
                        <p class="text-sm text-slate-600">{{ $profile->job_title ?: 'Naziv radnog mjesta nije unesen' }}</p>
                    </div>
                    @if($profile->photo_path)
                        <img src="{{ $profile->photoUrl() }}" alt="Fotografija radnika" class="h-20 w-20 rounded-full object-cover border border-slate-200">
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <p><strong>E-pošta:</strong> {{ $profile->email ?: 'N/A' }}</p>
                    <p><strong>Telefon:</strong> {{ $profile->phone ?: 'N/A' }}</p>
                    <p><strong>Trenutna lokacija:</strong> {{ trim(($profile->current_city ?? '') . ', ' . ($profile->current_country ?? ''), ', ') ?: 'N/A' }}</p>
                    <p><strong>Željena lokacija:</strong> {{ $profile->desired_city ?: 'N/A' }}</p>
                    <p><strong>Nacionalnost:</strong> {{ $profile->nationality_country_code ?: 'N/A' }}</p>
                    <p><strong>Dostupnost:</strong> {{ $profile->availability_date?->format('d.m.Y.') ?: 'N/A' }}</p>
                    <p><strong>Očekivana plaća:</strong> {{ $profile->salary_expectation ? number_format($profile->salary_expectation) . ' EUR' : 'N/A' }}</p>
                    <p><strong>Potreban smještaj:</strong> {{ is_null($profile->accommodation_needed) ? 'N/A' : ($profile->accommodation_needed ? 'Da' : 'Ne') }}</p>
                    <p><strong>Viza/radna dozvola:</strong> {{ $profile->visa_work_permit_status ?: 'N/A' }}</p>
                    <p><strong>Vidljivost profila:</strong> {{ \App\Models\WorkerProfile::visibilityOptions()[$profile->profile_visibility ?? \App\Models\WorkerProfile::VISIBILITY_EMPLOYERS] ?? 'Poslodavci' }}</p>
                </div>

                @if($profile->professional_summary)
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">Stručni sažetak</h3>
                        <p class="text-sm text-slate-700 whitespace-pre-line">{{ $profile->professional_summary }}</p>
                    </div>
                @endif

                @if($profile->education_summary)
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">Sažetak obrazovanja</h3>
                        <p class="text-sm text-slate-700 whitespace-pre-line">{{ $profile->education_summary }}</p>
                    </div>
                @endif

                @if($profile->work_experience)
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">Radno iskustvo</h3>
                        <p class="text-sm text-slate-700 whitespace-pre-line">{{ $profile->work_experience }}</p>
                    </div>
                @endif

                @if(is_array($profile->skills) && count($profile->skills) > 0)
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">Vještine</h3>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($profile->skills as $skill)
                                <span class="cw-chip">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(is_array($profile->languages) && count($profile->languages) > 0)
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">Jezici</h3>
                        <ul class="text-sm text-slate-700 space-y-1">
                            @foreach($profile->languages as $language)
                                @if(!empty($language['language']))
                                    <li>{{ $language['language'] }}{{ !empty($language['level']) ? ' (' . $language['level'] . ')' : '' }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($profile->certifications)
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">Certifikati</h3>
                        <p class="text-sm text-slate-700 whitespace-pre-line">{{ $profile->certifications }}</p>
                    </div>
                @endif

                @if(is_array($profile->desired_roles) && count($profile->desired_roles) > 0)
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">Željene uloge</h3>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($profile->desired_roles as $role)
                                <span class="cw-chip">{{ $role }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($profile->recommendations)
                    <div>
                        <h3 class="text-sm font-semibold text-slate-900 mb-1">Preporuke</h3>
                        <p class="text-sm text-slate-700 whitespace-pre-line">{{ $profile->recommendations }}</p>
                    </div>
                @endif
            </article>
        </div>
    </section>
</x-app-layout>
