<x-app-layout>
    <x-slot name="title">Notification Preferences</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-4xl space-y-5">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="cw-kicker mb-2">Notifications</p>
                    <h1 class="text-2xl font-semibold text-slate-900">Notification Preferences</h1>
                </div>
                <a href="{{ route('notifications.index') }}" class="cw-button-secondary">Back to notifications</a>
            </div>

            @if (session('success'))
                <x-alert type="success" :message="session('success')" />
            @endif

            <div class="cw-surface p-6">
                <form method="POST" action="{{ route('notifications.preferences.update') }}" class="space-y-5" data-cw-track-submit="notification_preferences_save">
                    @csrf
                    @method('PATCH')

                    @foreach($categoryLabels as $category => $label)
                        @php
                            $pref = $preferences[$category] ?? ['email_enabled' => true, 'database_enabled' => true, 'digest_frequency' => 'none'];
                        @endphp

                        <div class="border border-slate-200 rounded-xl p-4">
                            <h2 class="text-base font-semibold text-slate-900">{{ $label }}</h2>
                            <div class="mt-3 grid gap-3 md:grid-cols-3">
                                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                    <input type="hidden" name="preferences[{{ $category }}][email_enabled]" value="0">
                                    <input type="checkbox" name="preferences[{{ $category }}][email_enabled]" value="1" @checked($pref['email_enabled'])>
                                    Email
                                </label>

                                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                    <input type="hidden" name="preferences[{{ $category }}][database_enabled]" value="0">
                                    <input type="checkbox" name="preferences[{{ $category }}][database_enabled]" value="1" @checked($pref['database_enabled'])>
                                    In-app notifications
                                </label>

                                <label class="text-sm text-slate-700">
                                    Digest frequency
                                    <select name="preferences[{{ $category }}][digest_frequency]" class="cw-field mt-1">
                                        <option value="none" @selected($pref['digest_frequency'] === 'none')>None</option>
                                        <option value="daily" @selected($pref['digest_frequency'] === 'daily')>Daily</option>
                                        <option value="weekly" @selected($pref['digest_frequency'] === 'weekly')>Weekly</option>
                                    </select>
                                </label>
                            </div>
                        </div>
                    @endforeach

                    <div class="flex items-center gap-3">
                        <button type="submit" class="cw-button-primary">Save preferences</button>
                        <a href="{{ route('notifications.index') }}" class="cw-button-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>
