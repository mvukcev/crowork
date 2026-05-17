<x-app-layout>
    <x-slot name="title">Privacy & Data</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-4xl space-y-5">
            <p class="cw-kicker mb-2">Privacy controls</p>

            @if (session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                    {{ session('success') }}
                </div>
            @endif

            <div class="cw-surface p-6 space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">Export your personal data</h2>
                <p class="text-sm text-slate-600">Download a machine-readable JSON file with your account profile, applications, notifications, and consent history.</p>
                <a href="{{ route('user.export') }}" class="cw-button-primary inline-flex">Download export</a>
            </div>

            <div class="cw-surface p-6 space-y-3">
                <h2 class="text-xl font-semibold text-slate-900">Notification preferences</h2>
                <p class="text-sm text-slate-600">Control email and in-app notification categories.</p>
                <a href="{{ route('notifications.preferences') }}" class="cw-button-secondary inline-flex">Manage notification preferences</a>
            </div>

            <div class="cw-surface p-6 space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">Legal consent status</h2>
                <p class="text-sm text-slate-600">Current required policy versions and your acceptance status.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                        <p class="font-semibold text-slate-900">Terms of Service</p>
                        <p class="text-slate-600">Version: {{ $currentLegalVersions['terms_version'] }}</p>
                        <p class="mt-1 {{ ($legalStatus['terms'] ?? false) ? 'text-emerald-700' : 'text-amber-700' }}">
                            {{ ($legalStatus['terms'] ?? false) ? 'Current' : 'Outdated' }}
                        </p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                        <p class="font-semibold text-slate-900">Privacy Policy</p>
                        <p class="text-slate-600">Version: {{ $currentLegalVersions['privacy_policy_version'] }}</p>
                        <p class="mt-1 {{ ($legalStatus['privacy_policy'] ?? false) ? 'text-emerald-700' : 'text-amber-700' }}">
                            {{ ($legalStatus['privacy_policy'] ?? false) ? 'Current' : 'Outdated' }}
                        </p>
                    </div>
                </div>

                @if(!($legalStatus['terms'] ?? true) || !($legalStatus['privacy_policy'] ?? true))
                    <a href="{{ route('legal.reaccept.show') }}" class="cw-button-primary inline-flex">Re-accept updated policies</a>
                @endif

                @if($legalConsentHistory->count() > 0)
                    <div class="rounded-lg border border-slate-200 p-3">
                        <p class="text-sm font-semibold text-slate-900 mb-2">Recent legal consent history</p>
                        <ul class="space-y-1 text-xs text-slate-600">
                            @foreach($legalConsentHistory as $entry)
                                <li>
                                    {{ $entry->consent_type }} · v{{ $entry->consent_version ?? '-' }} ·
                                    {{ $entry->given ? 'accepted' : 'withdrawn' }} ·
                                    {{ $entry->accepted_at?->format('Y-m-d H:i') ?? '-' }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <div class="cw-surface p-6 space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">Cookie and tracking preferences</h2>
                <p class="text-sm text-slate-600">Control optional analytics and marketing cookies. Required cookies stay enabled to keep account, security, and session features working.</p>

                <form method="POST" action="{{ route('worker.privacy.consent') }}" class="space-y-3">
                    @csrf
                    @method('PATCH')

                    <label class="flex items-start gap-2 text-sm text-slate-700">
                        <input
                            type="checkbox"
                            name="consent_analytics"
                            value="1"
                            class="mt-1 rounded border-slate-300"
                            @checked(($trackingConsent['analytics'] ?? false) === true)
                        >
                        <span>Allow analytics cookies (traffic and feature usage insights).</span>
                    </label>

                    <label class="flex items-start gap-2 text-sm text-slate-700">
                        <input
                            type="checkbox"
                            name="consent_marketing"
                            value="1"
                            class="mt-1 rounded border-slate-300"
                            @checked(($trackingConsent['marketing'] ?? false) === true)
                        >
                        <span>Allow marketing cookies (Meta Pixel and related conversion tracking).</span>
                    </label>

                    <p class="text-xs text-slate-500">
                        You can review policy details in the <a href="{{ route('cookies') }}" class="underline text-slate-700">Cookie Statement</a>.
                    </p>

                    <button type="submit" class="cw-button-primary">Save tracking preferences</button>
                </form>
            </div>

            <div class="cw-surface p-6 space-y-4">
                <h2 class="text-xl font-semibold text-slate-900">Profile visibility</h2>
                <p class="text-sm text-slate-600">Choose how much information employers can see from your worker profile snapshot.</p>

                <form method="POST" action="{{ route('worker.privacy.visibility') }}" class="space-y-3">
                    @csrf
                    @method('PATCH')
                    <label class="cw-label" for="profile_visibility">Visibility mode</label>
                    <select id="profile_visibility" name="profile_visibility" class="cw-field w-full">
                        @foreach ($visibilityOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('profile_visibility', $profile->profile_visibility) === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('profile_visibility')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    <button type="submit" class="cw-button-primary">Save visibility</button>
                </form>
            </div>

            <div class="cw-surface p-6 space-y-4 border border-rose-200 bg-rose-50/40">
                <h2 class="text-xl font-semibold text-rose-900">Delete account</h2>
                <p class="text-sm text-rose-800">Submitting this request starts a 14-day grace period. After that, your personal data is anonymized and your account is deactivated.</p>

                @if ($user->pending_deletion)
                    <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                        Deletion request is pending.
                        @if ($latestDeletionRequest?->anonymization_scheduled_at)
                            Scheduled anonymization date: {{ $latestDeletionRequest->anonymization_scheduled_at->format('Y-m-d H:i') }}.
                        @endif
                    </div>
                @else
                    <form method="POST" action="{{ route('worker.privacy.request-deletion') }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="cw-label" for="reason">Reason (optional)</label>
                            <textarea id="reason" name="reason" rows="3" class="cw-field w-full">{{ old('reason') }}</textarea>
                            @error('reason')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="cw-label" for="password">Confirm current password</label>
                            <input id="password" name="password" type="password" class="cw-field w-full" required>
                            @error('password')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="inline-flex items-center rounded-lg bg-rose-700 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-800">Request account deletion</button>
                    </form>
                @endif
            </div>
        </div>
    </section>
</x-app-layout>
