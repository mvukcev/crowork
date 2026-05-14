<x-guest-layout>
    <div class="cw-surface p-6 md:p-8">
        <p class="cw-kicker mb-2">Employer signup</p>
        <h1 class="text-3xl md:text-4xl font-semibold text-slate-900 mb-2">Create employer account</h1>
        <p class="text-sm text-slate-600 mb-6">Set up your company profile in two short steps.</p>

        @if(session('status'))
            <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl p-3">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('employer.register') }}" class="space-y-4" x-data="{ step: {{ ($errors->has('password') || $errors->has('password_confirmation') || $errors->has('accept_terms') || $errors->has('accept_privacy')) ? 2 : 1 }} }">
            @csrf

            <div class="flex items-center justify-between text-xs uppercase tracking-[0.08em] text-slate-500">
                <span :class="step === 1 ? 'text-slate-900 font-semibold' : ''">Step 1 · Company</span>
                <span :class="step === 2 ? 'text-slate-900 font-semibold' : ''">Step 2 · Access</span>
            </div>

            <div x-show="step === 1" x-transition.opacity.duration.150ms>
                <div>
                    <label class="cw-label" for="company_name">Company name</label>
                    <input id="company_name" name="company_name" class="cw-field" value="{{ old('company_name') }}" required>
                    @error('company_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mt-4">
                    <label class="cw-label" for="contact_name">Contact name</label>
                    <input id="contact_name" name="contact_name" class="cw-field" value="{{ old('contact_name') }}" required>
                    @error('contact_name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mt-4">
                    <label class="cw-label" for="email">Work email</label>
                    <input id="email" type="email" name="email" class="cw-field" value="{{ old('email') }}" required>
                    @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mt-4">
                    <label class="cw-label" for="city">City</label>
                    <input id="city" name="city" class="cw-field" value="{{ old('city') }}" required>
                    @error('city')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <button type="button" class="cw-button-primary mt-4" @click="step = 2">Continue</button>
            </div>

            <div x-show="step === 2" x-transition.opacity.duration.150ms x-cloak>
                <div>
                    <label class="cw-label" for="password">Password</label>
                    <input id="password" type="password" name="password" class="cw-field" required>
                    @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mt-4">
                    <label class="cw-label" for="password_confirmation">Confirm password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="cw-field" required>
                </div>

                <div class="mt-4 space-y-2">
                    <label class="inline-flex items-start gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="accept_terms" value="1" class="mt-1 rounded border-slate-300" @checked(old('accept_terms')) required>
                        <span>I agree to the <a href="{{ route('terms') }}" class="font-medium text-slate-900 underline">Terms of Use</a>.</span>
                    </label>
                    @error('accept_terms')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror

                    <label class="inline-flex items-start gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="accept_privacy" value="1" class="mt-1 rounded border-slate-300" @checked(old('accept_privacy')) required>
                        <span>I agree to the <a href="{{ route('privacy') }}" class="font-medium text-slate-900 underline">Privacy Policy</a>.</span>
                    </label>
                    @error('accept_privacy')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center gap-2 mt-4">
                    <button type="button" class="cw-button-secondary" @click="step = 1">Back</button>
                    <button type="submit" class="cw-button-primary">Create employer account</button>
                </div>
            </div>
        </form>

        <p class="text-sm text-slate-600 mt-5">Already have an account? <a href="{{ route('login') }}" class="text-slate-900 font-medium">Sign in</a></p>
    </div>
</x-guest-layout>
