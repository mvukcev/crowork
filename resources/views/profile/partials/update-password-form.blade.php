<section>
    <h2 class="text-xl font-semibold text-slate-900 mb-1">Update password</h2>
    <p class="text-sm text-slate-600 mb-4">Use a strong password to protect your account.</p>

    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('put')

        <div>
            <label class="cw-label" for="update_password_current_password">Current password</label>
            <input id="update_password_current_password" name="current_password" type="password" class="cw-field" autocomplete="current-password">
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
        </div>

        <div>
            <label class="cw-label" for="update_password_password">New password</label>
            <input id="update_password_password" name="password" type="password" class="cw-field" autocomplete="new-password">
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
        </div>

        <div>
            <label class="cw-label" for="update_password_password_confirmation">Confirm password</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="cw-field" autocomplete="new-password">
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
        </div>

        <button type="submit" class="cw-button-primary">Update password</button>

        @if (session('status') === 'password-updated')
            <p class="text-sm text-emerald-700">Saved.</p>
        @endif
    </form>
</section>
