<x-guest-layout>
    <div class="cw-surface p-7 md:p-8">
        <p class="cw-kicker mb-2">Security check</p>
        <h1 class="text-2xl font-semibold text-slate-900 mb-2">Confirm your password</h1>
        <p class="text-sm text-slate-600 mb-6">Please confirm your password before continuing.</p>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
            @csrf
            <div>
                <label class="cw-label" for="password">Password</label>
                <input id="password" class="cw-field" type="password" name="password" required autocomplete="current-password">
                @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="cw-button-primary">Confirm</button>
        </form>
    </div>
</x-guest-layout>
