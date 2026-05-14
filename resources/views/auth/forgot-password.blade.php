<x-guest-layout>
    <div class="cw-surface p-7 md:p-8">
        <p class="cw-kicker mb-2">Password reset</p>
        <h1 class="text-2xl font-semibold text-slate-900 mb-2">Forgot your password?</h1>
        <p class="text-sm text-slate-600 mb-6">Enter your email and we will send you a reset link.</p>

        @if (session('status'))
            <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl p-3">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf
            <div>
                <label class="cw-label" for="email">Email</label>
                <input id="email" class="cw-field" type="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="cw-button-primary">Email reset link</button>
        </form>
    </div>
</x-guest-layout>
