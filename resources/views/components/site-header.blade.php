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
    $localeLabels = ['en' => 'English', 'hr' => 'Hrvatski'];
@endphp

<header @class([
    'fixed inset-x-0 top-0 z-[90] backdrop-blur-md transition-[background-color,border-color,box-shadow,backdrop-filter] duration-300 ease-out' => true,
    'cw-public-nav' => true,
])
style="z-index: 90;"
data-cw-public-nav>
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

            <div class="hidden md:flex items-center justify-end gap-2" data-cw-dropdown-root>
                <form method="POST" action="{{ route('preferences.locale') }}" data-cw-locale-form>
                    @csrf
                    <input type="hidden" name="redirect" value="{{ $currentUrl }}">
                    <input type="hidden" name="locale" data-cw-locale-input value="{{ $activeLocale }}">
                </form>

                <form method="POST" action="{{ route('preferences.theme') }}" data-cw-theme-form>
                    @csrf
                    <input type="hidden" name="redirect" value="{{ $currentUrl }}">
                    <input type="hidden" name="theme" data-cw-theme-input value="{{ $themePreference }}">
                </form>

                <div class="relative">
                    <button
                        type="button"
                        class="cw-header-icon-button cw-nav-control"
                        title="Language"
                        aria-label="Open language menu"
                        aria-expanded="false"
                        aria-controls="cw-header-language-menu"
                        data-cw-dropdown-trigger="cw-header-language-menu"
                    >
                        <svg class="cw-header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"/>
                            <path stroke-linecap="round" d="M3 12h18M12 3c2.2 2.3 3.3 5.3 3.3 9S14.2 18.7 12 21M12 3C9.8 5.3 8.7 8.3 8.7 12s1.1 6.7 3.3 9"/>
                        </svg>
                    </button>
                    <div
                        id="cw-header-language-menu"
                        data-cw-dropdown-panel
                        aria-hidden="true"
                        style="display: none;"
                        class="cw-dropdown-panel absolute right-0 mt-2 z-[110]"
                    >
                        @foreach($enabledLocales as $locale)
                            <button
                                type="button"
                                class="cw-dropdown-item {{ $activeLocale === $locale ? 'cw-dropdown-item-active' : '' }}"
                                data-cw-dropdown-select="locale"
                                data-cw-locale-value="{{ $locale }}"
                                data-cw-language-option="{{ $locale }}"
                            >
                                <span>{{ $localeLabels[$locale] ?? strtoupper($locale) }}</span>
                                @if($activeLocale === $locale)
                                    <span class="text-[11px]">✓</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="relative">
                    <button
                        type="button"
                        class="cw-header-icon-button cw-nav-control"
                        title="Theme"
                        aria-label="Open theme menu"
                        aria-expanded="false"
                        aria-controls="cw-header-theme-menu"
                        data-cw-dropdown-trigger="cw-header-theme-menu"
                    >
                        <svg class="cw-header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.2M12 18.8V21M5.64 5.64l1.56 1.56M16.8 16.8l1.56 1.56M3 12h2.2M18.8 12H21M5.64 18.36l1.56-1.56M16.8 7.2l1.56-1.56"/>
                            <circle cx="12" cy="12" r="3.8"/>
                        </svg>
                    </button>
                    <div
                        id="cw-header-theme-menu"
                        data-cw-dropdown-panel
                        aria-hidden="true"
                        style="display: none;"
                        class="cw-dropdown-panel absolute right-0 mt-2 z-[110]"
                    >
                        @foreach(['system' => 'System', 'light' => 'Light', 'dark' => 'Dark'] as $value => $label)
                            <button
                                type="button"
                                class="cw-dropdown-item {{ $themePreference === $value ? 'cw-dropdown-item-active' : '' }}"
                                data-cw-dropdown-select="theme"
                                data-cw-theme-value="{{ $value }}"
                                data-cw-theme-option="{{ $value }}"
                            >
                                <span>{{ $label }}</span>
                                @if($themePreference === $value)
                                    <span class="text-[11px]">✓</span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>

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
                class="md:hidden cw-header-icon-button justify-self-end text-slate-900"
                aria-label="Toggle navigation"
                aria-expanded="false"
                aria-controls="cw-mobile-nav-panel"
                data-cw-mobile-toggle
            >
                <svg data-cw-mobile-icon-open class="cw-header-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h16" /></svg>
                <svg data-cw-mobile-icon-close class="hidden cw-header-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </nav>

        <div id="cw-mobile-nav-panel" data-cw-mobile-panel hidden style="display: none;" class="md:hidden pb-4 border-t border-slate-200 bg-white">
            <div class="pt-3 space-y-2">
                <a href="{{ route('jobs.index') }}" class="block cw-button-secondary text-center" data-cw-track-click="navigation_click">Jobs</a>
                <a href="{{ route('educations.index') }}" class="block cw-button-secondary text-center" data-cw-track-click="navigation_click">Educations</a>
                <a href="{{ route('resources.index') }}" class="block cw-button-secondary text-center" data-cw-track-click="navigation_click">Resources</a>
                <a href="{{ route('about') }}" class="block cw-button-secondary text-center" data-cw-track-click="navigation_click">About</a>
                <a href="{{ route('for-employers') }}" class="block cw-button-secondary text-center" data-cw-track-click="navigation_click">For Employers</a>
            </div>
            <div class="mt-3 grid grid-cols-1 gap-2">
                <form method="POST" action="{{ route('preferences.locale') }}" class="cw-surface p-2.5 space-y-1.5">
                    @csrf
                    <input type="hidden" name="redirect" value="{{ $currentUrl }}">
                    <p class="text-xs uppercase tracking-[0.08em] text-slate-500 px-1">Language</p>
                    @foreach($enabledLocales as $locale)
                        <button type="submit" name="locale" value="{{ $locale }}" class="cw-dropdown-item {{ $activeLocale === $locale ? 'cw-dropdown-item-active' : '' }}" data-cw-language-option="{{ $locale }}">
                            <span>{{ $localeLabels[$locale] ?? strtoupper($locale) }}</span>
                            @if($activeLocale === $locale)
                                <span class="text-[11px]">✓</span>
                            @endif
                        </button>
                    @endforeach
                </form>

                <form method="POST" action="{{ route('preferences.theme') }}" class="cw-surface p-2.5 space-y-1.5">
                    @csrf
                    <input type="hidden" name="redirect" value="{{ $currentUrl }}">
                    <p class="text-xs uppercase tracking-[0.08em] text-slate-500 px-1">Theme</p>
                    @foreach(['system' => 'System', 'light' => 'Light', 'dark' => 'Dark'] as $value => $label)
                        <button type="submit" name="theme" value="{{ $value }}" class="cw-dropdown-item {{ $themePreference === $value ? 'cw-dropdown-item-active' : '' }}" data-cw-theme-option="{{ $value }}" @click="if (window.cwTheme && typeof window.cwTheme.setPreference === 'function') { window.cwTheme.setPreference('{{ $value }}') }">
                            <span>{{ $label }}</span>
                            @if($themePreference === $value)
                                <span class="text-[11px]">✓</span>
                            @endif
                        </button>
                    @endforeach
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
