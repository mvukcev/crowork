<section>
    <h2 class="text-xl font-semibold text-slate-900 mb-1">{{ __('auth.profile_information') }}</h2>
    <p class="text-sm text-slate-600 mb-4">{{ __('auth.update_profile_information') }}</p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        <div>
            <label class="cw-label" for="name">{{ __('auth.name') }}</label>
            <input id="name" name="name" type="text" class="cw-field w-full" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            <x-input-error class="mt-1" :messages="$errors->get('name')" />
        </div>

        <div>
            <label class="cw-label" for="email">{{ __('auth.email') }}</label>
            <input id="email" name="email" type="email" class="cw-field w-full" value="{{ old('email', $user->email) }}" required autocomplete="username">
            <x-input-error class="mt-1" :messages="$errors->get('email')" />
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <p class="text-sm text-slate-600">
                {{ __('auth.email_unverified') }}
                <button form="send-verification" class="underline text-slate-900">{{ __('auth.click_to_resend_verification') }}</button>
            </p>

            @if (session('status') === 'verification-link-sent')
                <p class="text-sm text-emerald-700">{{ __('auth.new_verification_link_sent') }}</p>
            @endif
        @endif

        <button type="submit" class="cw-button-primary">{{ __('auth.save') }}</button>
    </form>
</section>
