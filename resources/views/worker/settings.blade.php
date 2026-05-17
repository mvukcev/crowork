<x-app-layout>
    <x-slot name="title">{{ __('settings.worker.page_title') }}</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-4xl space-y-5">
            <p class="cw-kicker mb-2">{{ __('settings.worker.kicker') }}</p>

            <div class="cw-surface p-6">
                <h2 class="text-xl font-semibold text-slate-900 mb-3">{{ __('settings.worker.account_details') }}</h2>
                <form method="POST" action="{{ route('worker.settings.profile') }}" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="cw-label" for="name">{{ __('settings.worker.name') }}</label>
                        <input id="name" name="name" class="cw-field" value="{{ old('name', $user->name) }}" required>
                        @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="cw-label" for="email">{{ __('settings.worker.email') }}</label>
                        <input id="email" type="email" name="email" class="cw-field" value="{{ old('email', $user->email) }}" required>
                        @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="cw-button-primary">{{ __('settings.worker.save_account') }}</button>
                </form>
            </div>

            <div class="cw-surface p-6">
                <h2 class="text-xl font-semibold text-slate-900 mb-3">{{ __('settings.worker.password') }}</h2>
                <form method="POST" action="{{ route('worker.settings.password') }}" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="cw-label" for="current_password">{{ __('settings.worker.current_password') }}</label>
                        <input id="current_password" type="password" name="current_password" class="cw-field" required>
                        @error('current_password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="cw-label" for="password">{{ __('settings.worker.new_password') }}</label>
                        <input id="password" type="password" name="password" class="cw-field" required>
                        @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="cw-label" for="password_confirmation">{{ __('settings.worker.confirm_password') }}</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="cw-field" required>
                    </div>
                    <button type="submit" class="cw-button-primary">{{ __('settings.worker.update_password') }}</button>
                </form>
            </div>

            <a href="{{ route('worker.profile.edit') }}" class="cw-button-secondary">{{ __('settings.worker.edit_profile') }}</a>
        </div>
    </section>
</x-app-layout>
