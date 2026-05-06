<x-app-layout>
    <x-slot name="title">Worker Settings</x-slot>
    <x-slot name="description">Manage your account details and password.</x-slot>

    <div class="section-spacing-tight bg-background min-h-screen">
        <div class="container-base max-w-3xl">
            <div class="mb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                <div>
                    <p class="text-body-sm font-semibold uppercase tracking-wide text-primary mb-2">Worker dashboard</p>
                    <h1 class="text-title-1 font-semibold text-text-primary mb-1">Settings</h1>
                    <p class="text-body text-text-secondary mb-0">Update your account name and password.</p>
                </div>
                <x-button href="{{ route('worker.profile.edit') }}" variant="outline">Back to Profile</x-button>
            </div>

            <div class="grid grid-cols-1 gap-5">
                <x-card class="border border-border/70 shadow-elevation-1">
                    <h2 class="text-subtitle font-semibold text-text-primary mb-4">Account</h2>
                    <form method="POST" action="{{ route('worker.settings.profile') }}" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-body-sm font-semibold text-text-primary mb-2">Email</label>
                            <input type="email" value="{{ $user->email }}" disabled class="w-full rounded-xl border border-border bg-surface px-4 py-3 text-text-secondary">
                        </div>

                        <div>
                            <label for="name" class="block text-body-sm font-semibold text-text-primary mb-2">Display Name</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required maxlength="120" class="w-full rounded-xl border border-border bg-white px-4 py-3 text-text-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                            @error('name')
                                <p class="mt-2 text-body-sm text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <x-button type="submit" variant="primary">Save Account Changes</x-button>
                    </form>
                </x-card>

                <x-card class="border border-border/70 shadow-elevation-1">
                    <h2 class="text-subtitle font-semibold text-text-primary mb-4">Password</h2>
                    <form method="POST" action="{{ route('worker.settings.password') }}" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="current_password" class="block text-body-sm font-semibold text-text-primary mb-2">Current Password</label>
                            <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="w-full rounded-xl border border-border bg-white px-4 py-3 text-text-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                            @error('current_password')
                                <p class="mt-2 text-body-sm text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-body-sm font-semibold text-text-primary mb-2">New Password</label>
                            <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password" class="w-full rounded-xl border border-border bg-white px-4 py-3 text-text-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                            @error('password')
                                <p class="mt-2 text-body-sm text-danger">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-body-sm font-semibold text-text-primary mb-2">Confirm New Password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="w-full rounded-xl border border-border bg-white px-4 py-3 text-text-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                        </div>

                        <x-button type="submit" variant="primary">Update Password</x-button>
                    </form>
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>
