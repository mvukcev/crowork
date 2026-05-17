<section>
    <h2 class="text-xl font-semibold text-slate-900 mb-1">{{ __('auth.update_password') }}</h2>
    <p class="text-sm text-slate-600 mb-4">{{ __('auth.update_password_help') }}</p>

    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('put')

        <div>
            <label class="cw-label" for="update_password_current_password">{{ __('auth.current_password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" class="cw-field w-full" autocomplete="current-password">
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
        </div>

        <div>
            <label class="cw-label" for="update_password_password">{{ __('auth.new_password') }}</label>
            <input id="update_password_password" name="password" type="password" class="cw-field w-full" autocomplete="new-password">
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
        </div>

        <div>
            <label class="cw-label" for="update_password_password_confirmation">{{ __('auth.confirm_password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="cw-field w-full" autocomplete="new-password">
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
        </div>

        <button type="submit" class="cw-button-primary">{{ __('auth.save_password') }}</button>

        @if (session('status') === 'password-updated')
            <p class="text-sm text-emerald-700">{{ __('auth.saved') }}</p>
        @endif
    </form>
</section>
