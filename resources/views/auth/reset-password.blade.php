<x-guest-layout>
    <div class="cw-surface p-7 md:p-8">
        <p class="cw-kicker mb-2">Reset password</p>
        <h1 class="text-2xl font-semibold text-slate-900 mb-6">Choose a new password</h1>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <label class="cw-label" for="email">Email</label>
                <input id="email" class="cw-field" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
                @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="cw-label" for="password">Password</label>
                <input id="password" class="cw-field" type="password" name="password" required autocomplete="new-password">
                @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="cw-label" for="password_confirmation">Confirm password</label>
                <input id="password_confirmation" class="cw-field" type="password" name="password_confirmation" required autocomplete="new-password">
                @error('password_confirmation')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <button type="submit" class="cw-button-primary">Reset password</button>
        </form>
    </div>
</x-guest-layout>
