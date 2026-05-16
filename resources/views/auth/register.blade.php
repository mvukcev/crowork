<x-guest-layout>
    <div class="cw-surface p-6 md:p-8">
        <p class="cw-kicker mb-2">Create worker account</p>
        <h1 class="text-3xl md:text-4xl font-semibold text-slate-900 mb-2">Your path to Croatia starts here</h1>
        <p class="text-sm text-slate-600 mb-6">Create your account in two quick steps. Apply to jobs and find opportunities in just minutes.</p>

        <form method="POST" action="{{ route('register') }}" class="space-y-4" x-data="{ step: {{ ($errors->has('password') || $errors->has('password_confirmation') || $errors->has('accept_terms') || $errors->has('accept_privacy')) ? 2 : 1 }} }">
            @csrf

            <div class="flex items-center justify-between text-xs uppercase tracking-[0.08em] text-slate-500 mb-6">
                <span :class="step === 1 ? 'text-slate-900 font-semibold' : ''">Step 1 · Basic Info</span>
                <div class="flex-1 mx-2 h-px bg-slate-200"></div>
                <span :class="step === 2 ? 'text-slate-900 font-semibold' : ''">Step 2 · Security</span>
            </div>

            <div x-show="step === 1" x-transition.opacity.duration.150ms>
                <div>
                    <label class="cw-label" for="name">Your full name</label>
                    <input id="name" class="cw-field" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="e.g., Ana Horvat">
                    @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mt-4">
                    <label class="cw-label" for="email">Email address</label>
                    <input id="email" class="cw-field" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="ana@example.com">
                    @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mt-4">
                    <label class="cw-label" for="role">Account type</label>
                    <select id="role" name="role" class="cw-field" required>
                        <option value="worker" @selected(old('role', 'worker') === 'worker')>Worker (Looking for jobs in Croatia)</option>
                        <option value="employer" @selected(old('role') === 'employer')>Employer (Hiring talent)</option>
                    </select>
                    @error('role')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <button type="button" class="cw-button-primary w-full mt-6" @click="step = 2">Continue to security setup</button>
                
                <p class="text-xs text-slate-500 mt-4 text-center">We'll keep your data safe and secure.</p>
            </div>

            <div x-show="step === 2" x-transition.opacity.duration.150ms x-cloak>
                <p class="text-sm text-slate-600 mb-4">Create a strong password to protect your account.</p>
                
                <div>
                    <label class="cw-label" for="password">Password</label>
                    <input id="password" class="cw-field" type="password" name="password" required autocomplete="new-password" placeholder="Minimum 8 characters">
                    @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mt-4">
                    <label class="cw-label" for="password_confirmation">Confirm password</label>
                    <input id="password_confirmation" class="cw-field" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your password">
                </div>

                <div class="mt-6 space-y-3">
                    <label class="inline-flex items-start gap-3 text-sm text-slate-700">
                        <input type="checkbox" name="accept_terms" value="1" class="mt-1 rounded border-slate-300" @checked(old('accept_terms')) required>
                        <span>I agree to the <a href="{{ route('terms') }}" target="_blank" class="font-medium text-slate-900 underline hover:text-slate-700">Terms of Use</a>.</span>
                    </label>
                    @error('accept_terms')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror

                    <label class="inline-flex items-start gap-3 text-sm text-slate-700">
                        <input type="checkbox" name="accept_privacy" value="1" class="mt-1 rounded border-slate-300" @checked(old('accept_privacy')) required>
                        <span>I agree to the <a href="{{ route('privacy') }}" target="_blank" class="font-medium text-slate-900 underline hover:text-slate-700">Privacy Policy</a>.</span>
                    </label>
                    @error('accept_privacy')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center gap-3 mt-6">
                    <button type="button" class="cw-button-secondary flex-1" @click="step = 1">Back</button>
                    <button type="submit" class="cw-button-primary flex-1">Create account</button>
                </div>
            </div>
        </form>

        <p class="text-sm text-slate-600 mt-6 text-center">Already have an account? <a href="{{ route('login') }}" class="text-slate-900 font-semibold hover:underline">Sign in</a></p>
    </div>
</x-guest-layout>
