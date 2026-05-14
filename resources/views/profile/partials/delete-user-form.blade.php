<section>
    <h2 class="text-xl font-semibold text-slate-900 mb-1">Delete account</h2>
    <p class="text-sm text-slate-600 mb-4">This action permanently removes your account and all associated resources.</p>

    <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
        @csrf
        @method('delete')

        <div>
            <label class="cw-label" for="delete_password">Confirm password</label>
            <input id="delete_password" name="password" type="password" class="cw-field" autocomplete="current-password">
            <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1" />
        </div>

        <button type="submit" class="cw-button-secondary text-red-700 border-red-200 bg-red-50">Delete account</button>
    </form>
</section>
