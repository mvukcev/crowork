@php
    $isHome = request()->routeIs('home');

    $enabledLocales = collect(setting('enabled_locales', ['en', 'hr']))
        ->filter(fn ($locale) => is_string($locale) && $locale !== '')
        ->map(fn ($locale) => strtolower(trim((string) $locale)))
        ->values()
        ->all();

    if ($enabledLocales === []) {
        $enabledLocales = ['en'];
    }

    $activeLocale = strtolower((string) app()->getLocale());
    if (! in_array($activeLocale, $enabledLocales, true)) {
        $activeLocale = $enabledLocales[0];
    }

    $themePreference = strtolower((string) (session('theme') ?? request()->cookie('cw_theme') ?? 'system'));
    if (! in_array($themePreference, ['light', 'dark', 'system'], true)) {
        $themePreference = 'system';
    }

    $currentUrl = request()->fullUrl();
    $localeLabels = ['en' => 'EN', 'hr' => 'HR'];
@endphp

<header @class([
    'fixed inset-x-0 top-0 z-50 backdrop-blur-md' => true,
    'bg-transparent border-transparent' => $isHome,
    'border-b border-slate-200/80 bg-white/90' => ! $isHome,
]) x-data="{ mobileOpen: false }">
    <div class="cw-container h-16 md:h-[72px]">
        <nav class="h-full flex items-center justify-between gap-4">
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="flex items-center">
                    <img
                        src="{{ asset('assets/branding/CW-Logo-Dark.svg') }}"
                        alt="CroWork"
                        class="h-6 w-auto cw-logo-on-light"
                        onerror="this.style.display='none';"
                    >
                    <img
                        src="{{ asset('assets/branding/CW-Logo-Light.svg') }}"
                        alt="CroWork"
                        class="h-6 w-auto cw-logo-on-dark"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';"
                    >
                    <span class="hidden text-[15px] font-semibold text-slate-900">CroWork</span>
                </a>

                <div class="hidden lg:flex items-center gap-8">
                    <a href="{{ route('jobs.index') }}" data-cw-track-click="navigation_click" @class(['cw-nav-link', 'cw-nav-link-active' => request()->routeIs('jobs.*')])>Jobs</a>
                    <a href="{{ route('educations.index') }}" data-cw-track-click="navigation_click" @class(['cw-nav-link', 'cw-nav-link-active' => request()->routeIs('educations.*')])>Educations</a>
                    <a href="{{ route('resources.index') }}" data-cw-track-click="navigation_click" @class(['cw-nav-link', 'cw-nav-link-active' => request()->routeIs('resources.*')])>Resources</a>
                    <a href="{{ route('about') }}" data-cw-track-click="navigation_click" @class(['cw-nav-link', 'cw-nav-link-active' => request()->routeIs('about')])>About</a>
                    <a href="{{ route('for-employers') }}" data-cw-track-click="navigation_click" @class(['cw-nav-link', 'cw-nav-link-active' => request()->routeIs('for-employers')])>For Employers</a>
                </div>
            </div>

            <div class="hidden md:flex items-center justify-end gap-2">
                <form method="POST" action="{{ route('preferences.locale') }}">
                    @csrf
                    <input type="hidden" name="redirect" value="{{ $currentUrl }}">
                    <label class="sr-only" for="desktop-locale-switch">Language</label>
                    <select id="desktop-locale-switch" name="locale" class="cw-control-select" onchange="this.form.submit()">
                        @foreach($enabledLocales as $locale)
                            <option value="{{ $locale }}" @selected($activeLocale === $locale)>{{ $localeLabels[$locale] ?? strtoupper($locale) }}</option>
                        @endforeach
                    </select>
                </form>
                <form method="POST" action="{{ route('preferences.theme') }}">
                    @csrf
                    <input type="hidden" name="redirect" value="{{ $currentUrl }}">
                    <label class="sr-only" for="desktop-theme-switch">Theme</label>
                    <select id="desktop-theme-switch" name="theme" class="cw-control-select" data-cw-theme-switcher onchange="window.cwTheme?.setPreference(this.value); this.form.submit()">
                        <option value="system" @selected($themePreference === 'system')>System</option>
                        <option value="light" @selected($themePreference === 'light')>Light</option>
                        <option value="dark" @selected($themePreference === 'dark')>Dark</option>
                    </select>
                </form>
                @auth
                    @include('components.notification-dropdown')
                    @if(auth()->user()->isAdmin() || auth()->user()->isMod())
                        <a href="{{ url('/admin') }}" class="cw-button-secondary" data-cw-track-click="navigation_click">Admin</a>
                    @endif
                    @if(auth()->user()->isEmployer())
                        <a href="{{ url('/employer') }}" class="cw-button-secondary" data-cw-track-click="navigation_click">Dashboard</a>
                    @endif
                    @if(auth()->user()->isWorker())
                        <a href="{{ route('worker.applications.index') }}" class="cw-button-secondary" data-cw-track-click="navigation_click">Dashboard</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" data-cw-track-submit="logout">
                        @csrf
                        <button class="cw-button-secondary">Logout</button>
                    </form>
                @else
                    <a href="{{ route('access.show') }}" class="cw-button-primary" data-cw-track-click="navigation_click">Get started</a>
                @endauth
            </div>

            <button
                type="button"
                class="md:hidden inline-flex items-center justify-center justify-self-end h-10 w-10 rounded-xl border border-slate-200 bg-white text-slate-900"
                @click="mobileOpen = !mobileOpen"
                aria-label="Toggle navigation"
            >
                <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h16" /></svg>
                <svg x-show="mobileOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </nav>

        <div x-show="mobileOpen" x-cloak x-transition.opacity.duration.200ms class="md:hidden pb-4 border-t border-slate-200 bg-white">
            <div class="pt-3 space-y-2">
                <a href="{{ route('jobs.index') }}" class="block cw-button-secondary text-center" data-cw-track-click="navigation_click">Jobs</a>
                <a href="{{ route('educations.index') }}" class="block cw-button-secondary text-center" data-cw-track-click="navigation_click">Educations</a>
                <a href="{{ route('resources.index') }}" class="block cw-button-secondary text-center" data-cw-track-click="navigation_click">Resources</a>
                <a href="{{ route('about') }}" class="block cw-button-secondary text-center" data-cw-track-click="navigation_click">About</a>
                <a href="{{ route('for-employers') }}" class="block cw-button-secondary text-center" data-cw-track-click="navigation_click">For Employers</a>
            </div>
            <div class="mt-3 grid grid-cols-2 gap-2">
                <form method="POST" action="{{ route('preferences.locale') }}" class="col-span-1">
                    @csrf
                    <input type="hidden" name="redirect" value="{{ $currentUrl }}">
                    <label class="sr-only" for="mobile-locale-switch">Language</label>
                    <select id="mobile-locale-switch" name="locale" class="cw-control-select w-full" onchange="this.form.submit()">
                        @foreach($enabledLocales as $locale)
                            <option value="{{ $locale }}" @selected($activeLocale === $locale)>{{ $localeLabels[$locale] ?? strtoupper($locale) }}</option>
                        @endforeach
                    </select>
                </form>
                <form method="POST" action="{{ route('preferences.theme') }}" class="col-span-1">
                    @csrf
                    <input type="hidden" name="redirect" value="{{ $currentUrl }}">
                    <label class="sr-only" for="mobile-theme-switch">Theme</label>
                    <select id="mobile-theme-switch" name="theme" class="cw-control-select w-full" data-cw-theme-switcher onchange="window.cwTheme?.setPreference(this.value); this.form.submit()">
                        <option value="system" @selected($themePreference === 'system')>System</option>
                        <option value="light" @selected($themePreference === 'light')>Light</option>
                        <option value="dark" @selected($themePreference === 'dark')>Dark</option>
                    </select>
                </form>
            </div>
            <div class="mt-3 grid grid-cols-1 gap-2">
                @auth
                    <a href="{{ route('notifications.index') }}" class="cw-button-secondary text-center">
                        Notifications
                        @if(auth()->user()->unreadNotifications()->count() > 0)
                            ({{ auth()->user()->unreadNotifications()->count() }})
                        @endif
                    </a>
                    @if(auth()->user()->isAdmin() || auth()->user()->isMod())
                        <a href="{{ url('/admin') }}" class="cw-button-secondary text-center">Admin</a>
                    @elseif(auth()->user()->isEmployer())
                        <a href="{{ url('/employer') }}" class="cw-button-secondary text-center">Dashboard</a>
                    @else
                        <a href="{{ route('worker.applications.index') }}" class="cw-button-secondary text-center">Dashboard</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="cw-button-secondary w-full">Logout</button>
                    </form>
                @else
                    <a href="{{ route('access.show') }}" class="cw-button-primary text-center">Get started</a>
                @endauth
            </div>
        </div>
    </div>
</header>
