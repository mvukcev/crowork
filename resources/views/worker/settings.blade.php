<x-app-layout>
    <x-slot name="title">Worker Settings</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-4xl space-y-5">
            <p class="cw-kicker mb-2">Worker settings</p>

            <div class="cw-surface p-6">
                <h2 class="text-xl font-semibold text-slate-900 mb-3">Account details</h2>
                <form method="POST" action="{{ route('worker.settings.profile') }}" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="cw-label" for="name">Name</label>
                        <input id="name" name="name" class="cw-field" value="{{ old('name', $user->name) }}" required>
                        @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="cw-label" for="email">Email</label>
                        <input id="email" type="email" name="email" class="cw-field" value="{{ old('email', $user->email) }}" required>
                        @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="cw-button-primary">Save account</button>
                </form>
            </div>

            <div class="cw-surface p-6">
                <h2 class="text-xl font-semibold text-slate-900 mb-3">Password</h2>
                <form method="POST" action="{{ route('worker.settings.password') }}" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="cw-label" for="current_password">Current password</label>
                        <input id="current_password" type="password" name="current_password" class="cw-field" required>
                        @error('current_password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="cw-label" for="password">New password</label>
                        <input id="password" type="password" name="password" class="cw-field" required>
                        @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="cw-label" for="password_confirmation">Confirm password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="cw-field" required>
                    </div>
                    <button type="submit" class="cw-button-primary">Update password</button>
                </form>
            </div>

            <a href="{{ route('worker.profile.edit') }}" class="cw-button-secondary">Edit worker profile</a>
        </div>
    </section>
</x-app-layout>
