<nav x-data="{ open: false }" class="border-b border-slate-200 bg-white">
    <div class="cw-container">
        <div class="flex justify-between h-16 items-center">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <x-application-logo class="block h-8 w-auto fill-current text-slate-900" />
            </a>

            <div class="hidden sm:flex items-center gap-6">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('Dashboard') }}</x-nav-link>
                <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">{{ __('Profile') }}</x-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="cw-button-secondary">{{ __('Log Out') }}</button>
                </form>
            </div>

            <button @click="open = ! open" class="sm:hidden inline-flex items-center justify-center p-2 rounded-lg border border-slate-200 text-slate-700">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-slate-200">
        <div class="cw-container py-3 space-y-2">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('Dashboard') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>
            <form method="POST" action="{{ route('logout') }}">@csrf <button class="cw-button-secondary w-full">{{ __('Log Out') }}</button></form>
        </div>
    </div>
</nav>
