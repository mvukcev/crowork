<x-guest-layout>
    <div class="cw-surface p-6 md:p-8">
        <p class="cw-kicker mb-2">Sign in</p>
        <h1 class="text-3xl md:text-4xl font-semibold text-slate-900 mb-2">Welcome back</h1>
        <p class="text-sm text-slate-600 mb-6">Continue your CroWork journey in two calm steps.</p>

        @if (session('status'))
            <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl p-3">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ step: {{ $errors->has('password') ? 2 : 1 }} }">
            @csrf

            <div class="flex items-center justify-between text-xs uppercase tracking-[0.08em] text-slate-500">
                <span :class="step === 1 ? 'text-slate-900 font-semibold' : ''">Step 1 · Identity</span>
                <span :class="step === 2 ? 'text-slate-900 font-semibold' : ''">Step 2 · Access</span>
            </div>

            <div x-show="step === 1" x-transition.opacity.duration.150ms>
                <label for="email" class="cw-label">Email</label>
                <input id="email" class="cw-field" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="alex@example.com" />
                @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror

                <button type="button" class="cw-button-primary mt-4" @click="step = 2">Continue</button>
            </div>

            <div x-show="step === 2" x-transition.opacity.duration.150ms x-cloak>
                <div class="mb-3 text-xs text-slate-500">Signing in as <span class="font-medium text-slate-700">{{ old('email') ?: 'your account' }}</span></div>

                <label for="password" class="cw-label">Password</label>
                <input id="password" class="cw-field" type="password" name="password" required autocomplete="current-password" />
                @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror

                <label class="inline-flex items-center gap-2 text-sm text-slate-700 mt-3">
                    <input type="checkbox" name="remember" class="rounded border-slate-300" />
                    Remember me
                </label>

                <div class="flex flex-wrap items-center justify-between gap-2 mt-4">
                    <button type="button" class="cw-button-secondary" @click="step = 1">Back</button>
                    @if (Route::has('password.request'))
                        <a class="text-sm text-slate-600 hover:text-slate-900" href="{{ route('password.request') }}">Forgot password?</a>
                    @endif
                    <button type="submit" class="cw-button-primary">Sign in</button>
                </div>
            </div>
        </form>

        <p class="text-sm text-slate-600 mt-5">No account? <a href="{{ route('register') }}" class="text-slate-900 font-medium">Create one</a></p>
    </div>
</x-guest-layout>
