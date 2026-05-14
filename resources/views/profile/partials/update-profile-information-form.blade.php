<section>
    <h2 class="text-xl font-semibold text-slate-900 mb-1">Profile information</h2>
    <p class="text-sm text-slate-600 mb-4">Update your account name and email.</p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        <div>
            <label class="cw-label" for="name">Name</label>
            <input id="name" name="name" type="text" class="cw-field" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            <x-input-error class="mt-1" :messages="$errors->get('name')" />
        </div>

        <div>
            <label class="cw-label" for="email">Email</label>
            <input id="email" name="email" type="email" class="cw-field" value="{{ old('email', $user->email) }}" required autocomplete="username">
            <x-input-error class="mt-1" :messages="$errors->get('email')" />
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <p class="text-sm text-slate-600">
                Your email address is unverified.
                <button form="send-verification" class="underline text-slate-900">Click here to re-send verification.</button>
            </p>

            @if (session('status') === 'verification-link-sent')
                <p class="text-sm text-emerald-700">A new verification link has been sent.</p>
            @endif
        @endif

        <button type="submit" class="cw-button-primary">Save</button>
    </form>
</section>
