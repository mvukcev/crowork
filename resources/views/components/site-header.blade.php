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

    $activeLocaleIndex = array_search($activeLocale, $enabledLocales, true);
    if ($activeLocaleIndex === false) {
        $activeLocaleIndex = 0;
    }
    $nextLocale = $enabledLocales[($activeLocaleIndex + 1) % max(count($enabledLocales), 1)];

    $themeOrder = ['system', 'light', 'dark'];
    $themeIndex = array_search($themePreference, $themeOrder, true);
    if ($themeIndex === false) {
        $themeIndex = 0;
    }
    $nextThemePreference = $themeOrder[($themeIndex + 1) % count($themeOrder)];
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
                <form method="POST" action="{{ route('preferences.locale') }}" data-cw-track-click="language_change">
                    @csrf
                    <input type="hidden" name="redirect" value="{{ $currentUrl }}">
                    <input type="hidden" name="locale" value="{{ $nextLocale }}">
                    <button type="submit" class="cw-icon-control cw-nav-control" title="Language {{ $localeLabels[$activeLocale] ?? strtoupper($activeLocale) }}" aria-label="Switch language to {{ $localeLabels[$nextLocale] ?? strtoupper($nextLocale) }}">
                        <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18M12 3a15.3 15.3 0 014 9 15.3 15.3 0 01-4 9 15.3 15.3 0 01-4-9 15.3 15.3 0 014-9zM5 7h14M5 17h14" />
                        </svg>
                    </button>
                </form>
                <form method="POST" action="{{ route('preferences.theme') }}" data-cw-track-click="theme_change">
                    @csrf
                    <input type="hidden" name="redirect" value="{{ $currentUrl }}">
                    <input type="hidden" name="theme" value="{{ $nextThemePreference }}">
                    <button type="submit" class="cw-icon-control cw-nav-control" title="Theme {{ ucfirst($themePreference) }}" aria-label="Switch theme to {{ ucfirst($nextThemePreference) }}" onclick="window.cwTheme?.setPreference('{{ $nextThemePreference }}')">
                        @if($themePreference === 'light')
                            <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                <circle cx="12" cy="12" r="4" />
                                <path stroke-linecap="round" d="M12 2v2.2M12 19.8V22M4.9 4.9l1.6 1.6M17.5 17.5l1.6 1.6M2 12h2.2M19.8 12H22M4.9 19.1l1.6-1.6M17.5 6.5l1.6-1.6" />
                            </svg>
                        @elseif($themePreference === 'dark')
                            <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3c.1 0 .2 0 .29.01A7 7 0 0021 12.79z" />
                            </svg>
                        @else
                            <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                <rect x="3" y="4" width="18" height="13" rx="2" />
                                <path stroke-linecap="round" d="M8 20h8" />
                            </svg>
                        @endif
                    </button>
                </form>
                @auth
                    @include('components.notification-dropdown')
                    @if(auth()->user()->isAdmin() || auth()->user()->isMod())
                        <a href="{{ url('/admin') }}" class="cw-button-secondary cw-nav-control" data-cw-track-click="navigation_click">Admin</a>
                    @endif
                    @if(auth()->user()->isEmployer())
                        <a href="{{ url('/employer') }}" class="cw-button-secondary cw-nav-control" data-cw-track-click="navigation_click">Dashboard</a>
                    @endif
                    @if(auth()->user()->isWorker())
                        <a href="{{ route('worker.applications.index') }}" class="cw-button-secondary cw-nav-control" data-cw-track-click="navigation_click">Dashboard</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" data-cw-track-submit="logout">
                        @csrf
                        <button class="cw-button-secondary cw-nav-control">Logout</button>
                    </form>
                @else
                    <a href="{{ route('access.show') }}" class="cw-button-primary cw-nav-control" data-cw-track-click="navigation_click">Get started</a>
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
                <form method="POST" action="{{ route('preferences.locale') }}" class="col-span-1" data-cw-track-click="language_change">
                    @csrf
                    <input type="hidden" name="redirect" value="{{ $currentUrl }}">
                    <input type="hidden" name="locale" value="{{ $nextLocale }}">
                    <button type="submit" class="cw-icon-control cw-nav-control w-full" title="Language {{ $localeLabels[$activeLocale] ?? strtoupper($activeLocale) }}" aria-label="Switch language to {{ $localeLabels[$nextLocale] ?? strtoupper($nextLocale) }}">
                        <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18M12 3a15.3 15.3 0 014 9 15.3 15.3 0 01-4 9 15.3 15.3 0 01-4-9 15.3 15.3 0 014-9zM5 7h14M5 17h14" />
                        </svg>
                    </button>
                </form>
                <form method="POST" action="{{ route('preferences.theme') }}" class="col-span-1" data-cw-track-click="theme_change">
                    @csrf
                    <input type="hidden" name="redirect" value="{{ $currentUrl }}">
                    <input type="hidden" name="theme" value="{{ $nextThemePreference }}">
                    <button type="submit" class="cw-icon-control cw-nav-control w-full" title="Theme {{ ucfirst($themePreference) }}" aria-label="Switch theme to {{ ucfirst($nextThemePreference) }}" onclick="window.cwTheme?.setPreference('{{ $nextThemePreference }}')">
                        @if($themePreference === 'light')
                            <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                <circle cx="12" cy="12" r="4" />
                                <path stroke-linecap="round" d="M12 2v2.2M12 19.8V22M4.9 4.9l1.6 1.6M17.5 17.5l1.6 1.6M2 12h2.2M19.8 12H22M4.9 19.1l1.6-1.6M17.5 6.5l1.6-1.6" />
                            </svg>
                        @elseif($themePreference === 'dark')
                            <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3c.1 0 .2 0 .29.01A7 7 0 0021 12.79z" />
                            </svg>
                        @else
                            <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                <rect x="3" y="4" width="18" height="13" rx="2" />
                                <path stroke-linecap="round" d="M8 20h8" />
                            </svg>
                        @endif
                    </button>
                </form>
            </div>
            <div class="mt-3 grid grid-cols-1 gap-2">
                @auth
                    <a href="{{ route('notifications.index') }}" class="cw-button-secondary text-center cw-nav-control">
                        Notifications
                        @if(auth()->user()->unreadNotifications()->count() > 0)
                            ({{ auth()->user()->unreadNotifications()->count() }})
                        @endif
                    </a>
                    @if(auth()->user()->isAdmin() || auth()->user()->isMod())
                        <a href="{{ url('/admin') }}" class="cw-button-secondary text-center cw-nav-control">Admin</a>
                    @elseif(auth()->user()->isEmployer())
                        <a href="{{ url('/employer') }}" class="cw-button-secondary text-center cw-nav-control">Dashboard</a>
                    @else
                        <a href="{{ route('worker.applications.index') }}" class="cw-button-secondary text-center cw-nav-control">Dashboard</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="cw-button-secondary w-full cw-nav-control">Logout</button>
                    </form>
                @else
                    <a href="{{ route('access.show') }}" class="cw-button-primary text-center cw-nav-control">Get started</a>
                @endauth
            </div>
        </div>
    </div>
</header>
