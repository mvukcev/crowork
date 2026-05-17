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

    $localeLabels = ['en' => __('settings.locale_en'), 'hr' => __('settings.locale_hr')];
    $themeLabels = [
        'system' => __('settings.theme_system'),
        'light' => __('settings.theme_light'),
        'dark' => __('settings.theme_dark'),
    ];
    $activeLocaleLabel = $localeLabels[$activeLocale] ?? strtoupper($activeLocale);
    $activeThemeLabel = $themeLabels[$themePreference] ?? __('settings.theme_system');

    $currentUrl = request()->fullUrl();

    $mobileProfileAccountLinks = [];
    $mobileProfileWorkLinks = [];

    if (auth()->check()) {
        $user = auth()->user();

        if ($user->isAdmin() || $user->isMod()) {
            if (\Illuminate\Support\Facades\Route::has('filament.admin.pages.dashboard')) {
                $mobileProfileAccountLinks[] = [
                    'label' => __('navigation.admin'),
                    'url' => route('filament.admin.pages.dashboard'),
                    'track' => 'mobile_admin',
                ];
            }

            if (\Illuminate\Support\Facades\Route::has('profile.edit')) {
                $mobileProfileAccountLinks[] = [
                    'label' => __('navigation.account_settings'),
                    'url' => route('profile.edit'),
                    'track' => 'mobile_profile_settings',
                ];
            }

            if (\Illuminate\Support\Facades\Route::has('notifications.index')) {
                $mobileProfileAccountLinks[] = [
                    'label' => __('navigation.notifications'),
                    'url' => route('notifications.index'),
                    'track' => 'mobile_notifications',
                ];
            }
        } elseif ($user->isEmployer()) {
            if (\Illuminate\Support\Facades\Route::has('employer.dashboard')) {
                $mobileProfileAccountLinks[] = [
                    'label' => __('navigation.dashboard'),
                    'url' => route('employer.dashboard'),
                    'track' => 'mobile_dashboard',
                ];
            }

            if (\Illuminate\Support\Facades\Route::has('employer.settings.profile')) {
                $mobileProfileAccountLinks[] = [
                    'label' => __('navigation.account_settings'),
                    'url' => route('employer.settings.profile'),
                    'track' => 'mobile_profile_settings',
                ];
            }

            if (\Illuminate\Support\Facades\Route::has('notifications.index')) {
                $mobileProfileAccountLinks[] = [
                    'label' => __('navigation.notifications'),
                    'url' => route('notifications.index'),
                    'track' => 'mobile_notifications',
                ];
            }

            if (\Illuminate\Support\Facades\Route::has('employer.jobs.index')) {
                $mobileProfileWorkLinks[] = [
                    'label' => __('navigation.my_jobs'),
                    'url' => route('employer.jobs.index'),
                    'track' => 'mobile_employer_jobs',
                ];
            }

            if (\Illuminate\Support\Facades\Route::has('employer.applications.pipeline')) {
                $mobileProfileWorkLinks[] = [
                    'label' => __('navigation.applications'),
                    'url' => route('employer.applications.pipeline'),
                    'track' => 'mobile_employer_applications',
                ];
            }
        } elseif ($user->isWorker()) {
            if (\Illuminate\Support\Facades\Route::has('worker.dashboard')) {
                $mobileProfileAccountLinks[] = [
                    'label' => __('navigation.dashboard'),
                    'url' => route('worker.dashboard'),
                    'track' => 'mobile_dashboard',
                ];
            }

            if (\Illuminate\Support\Facades\Route::has('worker.settings.edit')) {
                $mobileProfileAccountLinks[] = [
                    'label' => __('navigation.account_settings'),
                    'url' => route('worker.settings.edit'),
                    'track' => 'mobile_profile_settings',
                ];
            } elseif (\Illuminate\Support\Facades\Route::has('profile.edit')) {
                $mobileProfileAccountLinks[] = [
                    'label' => __('navigation.account_settings'),
                    'url' => route('profile.edit'),
                    'track' => 'mobile_profile_settings',
                ];
            }

            if (\Illuminate\Support\Facades\Route::has('notifications.index')) {
                $mobileProfileAccountLinks[] = [
                    'label' => __('navigation.notifications'),
                    'url' => route('notifications.index'),
                    'track' => 'mobile_notifications',
                ];
            }

            if (\Illuminate\Support\Facades\Route::has('worker.applications.index')) {
                $mobileProfileWorkLinks[] = [
                    'label' => __('navigation.my_applications'),
                    'url' => route('worker.applications.index'),
                    'track' => 'mobile_worker_applications',
                ];
            }

            if (\Illuminate\Support\Facades\Route::has('saved-jobs.index')) {
                $mobileProfileWorkLinks[] = [
                    'label' => __('navigation.saved_jobs'),
                    'url' => route('saved-jobs.index'),
                    'track' => 'mobile_saved_jobs',
                ];
            }
        } else {
            if (\Illuminate\Support\Facades\Route::has('dashboard')) {
                $mobileProfileAccountLinks[] = [
                    'label' => __('navigation.dashboard'),
                    'url' => route('dashboard'),
                    'track' => 'mobile_dashboard',
                ];
            }

            if (\Illuminate\Support\Facades\Route::has('profile.edit')) {
                $mobileProfileAccountLinks[] = [
                    'label' => __('navigation.account_settings'),
                    'url' => route('profile.edit'),
                    'track' => 'mobile_profile_settings',
                ];
            }

            if (\Illuminate\Support\Facades\Route::has('notifications.index')) {
                $mobileProfileAccountLinks[] = [
                    'label' => __('navigation.notifications'),
                    'url' => route('notifications.index'),
                    'track' => 'mobile_notifications',
                ];
            }
        }
    }
@endphp

<header @class([
    'backdrop-blur-md transition-[background-color,border-color,box-shadow,backdrop-filter] duration-300 ease-out' => true,
    'cw-public-nav' => true,
])
style="position: fixed; inset-inline: 0; top: 0; z-index: 1200;"
data-cw-public-nav>
    <div class="cw-container h-16 md:h-[72px]">
        <nav class="h-full flex items-center gap-4">
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
                    <a href="{{ route('jobs.index') }}" data-cw-track-click="navigation_click" @class(['cw-nav-link', 'cw-nav-link-active' => request()->routeIs('jobs.*')])>{{ __('navigation.jobs') }}</a>
                    <a href="{{ route('educations.index') }}" data-cw-track-click="navigation_click" @class(['cw-nav-link', 'cw-nav-link-active' => request()->routeIs('educations.*')])>{{ __('navigation.educations') }}</a>
                    <a href="{{ route('resources.index') }}" data-cw-track-click="navigation_click" @class(['cw-nav-link', 'cw-nav-link-active' => request()->routeIs('resources.*')])>{{ __('navigation.resources') }}</a>
                    <a href="{{ route('about') }}" data-cw-track-click="navigation_click" @class(['cw-nav-link', 'cw-nav-link-active' => request()->routeIs('about')])>{{ __('navigation.about') }}</a>
                    <a href="{{ route('for-employers') }}" data-cw-track-click="navigation_click" @class(['cw-nav-link', 'cw-nav-link-active' => request()->routeIs('for-employers')])>{{ __('navigation.for_employers') }}</a>
                </div>
            </div>

            <div class="ml-auto hidden md:flex items-center justify-end gap-2" data-cw-dropdown-root>
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
                        title="{{ __('settings.language') }}"
                        aria-label="{{ __('settings.language') }}"
                        aria-expanded="false"
                        aria-controls="cw-header-language-menu"
                        data-cw-dropdown-trigger="cw-header-language-menu"
                    >
                        <svg class="cw-header-icon cw-header-icon-globe" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"/>
                            <path d="M3 12h18"/>
                            <path d="M12 3a13.2 13.2 0 0 1 0 18"/>
                            <path d="M12 3a13.2 13.2 0 0 0 0 18"/>
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
                                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="relative">
                    <button
                        type="button"
                        class="cw-header-icon-button cw-nav-control"
                        title="{{ __('settings.theme') }}"
                        aria-label="{{ __('settings.theme') }}"
                        aria-expanded="false"
                        aria-controls="cw-header-theme-menu"
                        data-cw-dropdown-trigger="cw-header-theme-menu"
                    >
                        <svg class="cw-header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path d="M12 3v2.2M12 18.8V21M5.64 5.64l1.56 1.56M16.8 16.8l1.56 1.56M3 12h2.2M18.8 12H21M5.64 18.36l1.56-1.56M16.8 7.2l1.56-1.56"/>
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
                        @foreach($themeLabels as $value => $label)
                            <button
                                type="button"
                                class="cw-dropdown-item {{ $themePreference === $value ? 'cw-dropdown-item-active' : '' }}"
                                data-cw-dropdown-select="theme"
                                data-cw-theme-value="{{ $value }}"
                                data-cw-theme-option="{{ $value }}"
                            >
                                <span>{{ $label }}</span>
                                @if($themePreference === $value)
                                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                @auth
                    @include('components.notification-dropdown')
                    @if(auth()->user()->isAdmin() || auth()->user()->isMod())
                        <a href="{{ url('/admin') }}" class="cw-button-secondary cw-nav-control" data-cw-track-click="navigation_click">{{ __('navigation.admin') }}</a>
                    @endif
                    @if(auth()->user()->isEmployer())
                        <a href="{{ url('/employer') }}" class="cw-button-secondary cw-nav-control" data-cw-track-click="navigation_click">{{ __('navigation.dashboard') }}</a>
                    @endif
                    @if(auth()->user()->isWorker())
                        <a href="{{ route('worker.applications.index') }}" class="cw-button-secondary cw-nav-control" data-cw-track-click="navigation_click">{{ __('navigation.dashboard') }}</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" data-cw-track-submit="logout">
                        @csrf
                        <button class="cw-button-secondary cw-nav-control">{{ __('navigation.logout') }}</button>
                    </form>
                @else
                    <a href="{{ route('access.show') }}" class="cw-button-accent cw-nav-control" data-cw-track-click="navigation_click">{{ __('navigation.login') }}</a>
                @endauth
            </div>

            <button
                type="button"
                class="lg:hidden cw-header-icon-button cw-mobile-toggle-trigger justify-self-end text-slate-900"
                aria-label="{{ __('settings.toggle_navigation') }}"
                aria-expanded="false"
                aria-controls="cw-mobile-nav-panel"
                data-cw-mobile-toggle
            >
                <svg data-cw-mobile-icon-open class="cw-header-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M4 12h16M4 17h16" /></svg>
                <svg data-cw-mobile-icon-close class="hidden cw-header-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </nav>

        <div id="cw-mobile-nav-panel" data-cw-mobile-panel hidden style="display: none;" class="cw-mobile-nav-overlay fixed inset-0 z-[1280] flex flex-col overflow-hidden" data-cw-mobile-state="main">
            <button type="button" class="absolute inset-0 z-[1270] bg-transparent" data-cw-mobile-backdrop aria-label="{{ __('settings.close_menu') }}"></button>

            <div class="cw-mobile-panel-toolbar flex items-center justify-between px-6 pt-6 pb-2 relative z-[1320]">
                <a href="{{ route('home') }}" class="flex items-center gap-2" data-cw-mobile-panel-main>
                    <img src="{{ asset('assets/branding/CW-Logo-Dark.svg') }}" alt="CroWork" class="h-6 w-auto cw-logo-on-light">
                    <img src="{{ asset('assets/branding/CW-Logo-Light.svg') }}" alt="CroWork" class="h-6 w-auto cw-logo-on-dark">
                </a>
                <button type="button" class="cw-header-icon-button" data-cw-mobile-close aria-label="{{ __('settings.close_menu') }}" data-cw-mobile-panel-main>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="cw-header-icon" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="relative z-[1300] flex-1 overflow-hidden" data-cw-mobile-panels>
                <div class="absolute inset-0 z-[1300] flex flex-col overflow-y-auto" data-cw-mobile-content-main style="transform: translateX(0); opacity: 1; pointer-events: auto;">
                    <div class="flex flex-col items-start justify-start gap-2 px-6 py-6">
                        <a href="{{ route('jobs.index') }}" class="cw-mobile-nav-link">{{ __('navigation.jobs') }}</a>
                        <a href="{{ route('educations.index') }}" class="cw-mobile-nav-link">{{ __('navigation.educations') }}</a>
                        <a href="{{ route('resources.index') }}" class="cw-mobile-nav-link">{{ __('navigation.resources') }}</a>
                        <a href="{{ route('about') }}" class="cw-mobile-nav-link">{{ __('navigation.about') }}</a>
                        <a href="{{ route('for-employers') }}" class="cw-mobile-nav-link">{{ __('navigation.for_employers') }}</a>
                    </div>

                    <div class="flex w-full flex-col gap-2 px-6 pb-6 mt-auto">
                        <div class="cw-mobile-action-grid" aria-label="{{ __('settings.mobile_utility_actions') }}">
                            <button type="button" class="cw-mobile-action-row" data-cw-mobile-language-toggle>
                                <span class="cw-mobile-action-row-label">{{ __('settings.language') }}</span>
                                <span class="cw-mobile-action-row-meta">
                                    <span class="cw-mobile-action-row-value">{{ $activeLocaleLabel }}</span>
                                    <svg class="cw-mobile-action-row-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 6l6 6-6 6" />
                                    </svg>
                                </span>
                            </button>
                            <button type="button" class="cw-mobile-action-row" data-cw-mobile-theme-toggle>
                                <span class="cw-mobile-action-row-label">{{ __('settings.theme') }}</span>
                                <span class="cw-mobile-action-row-meta">
                                    <span class="cw-mobile-action-row-value">{{ $activeThemeLabel }}</span>
                                    <svg class="cw-mobile-action-row-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 6l6 6-6 6" />
                                    </svg>
                                </span>
                            </button>
                            @auth
                                <button type="button" class="cw-mobile-action-row" data-cw-mobile-profile-toggle>
                                    <span class="cw-mobile-action-row-label">{{ __('navigation.profile') }}</span>
                                    <span class="cw-mobile-action-row-meta" aria-hidden="true">
                                        <svg class="cw-mobile-action-row-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 6l6 6-6 6" />
                                        </svg>
                                    </span>
                                </button>
                            @else
                                <a href="{{ route('access.show') }}" class="cw-mobile-action-row cw-mobile-action-row-emphasis" data-cw-track-click="mobile_login">
                                    <span class="cw-mobile-action-row-label">{{ __('navigation.get_started') }}</span>
                                    <span class="cw-mobile-action-row-meta" aria-hidden="true">
                                        <svg class="cw-mobile-action-row-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 6l6 6-6 6" />
                                        </svg>
                                    </span>
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Language Submenu Panel -->
                <div class="absolute inset-0 z-[1310] flex flex-col overflow-y-auto" data-cw-mobile-content-language style="transform: translateX(100%); opacity: 0; pointer-events: none;">
                    <div class="flex-1 flex flex-col gap-4 px-6 py-6">
                        <button type="button" class="cw-mobile-back-control" data-cw-mobile-back aria-label="{{ __('settings.back_to_main_menu') }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-[18px] h-[18px]" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19l-7-7 7-7" /></svg>
                            <span>{{ __('common.back') }}</span>
                        </button>
                        <form method="POST" action="{{ route('preferences.locale') }}" class="flex flex-col gap-1.5" data-cw-mobile-locale-form>
                            @csrf
                            <input type="hidden" name="redirect" value="{{ $currentUrl }}">
                            @foreach($enabledLocales as $locale)
                                <button type="submit" name="locale" value="{{ $locale }}" class="cw-mobile-submenu-item {{ $activeLocale === $locale ? 'active' : '' }}">
                                    <span>{{ $localeLabels[$locale] ?? strtoupper($locale) }}</span>
                                    @if($activeLocale === $locale)
                                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                    @endif
                                </button>
                            @endforeach
                        </form>
                    </div>
                </div>

                <!-- Theme Submenu Panel -->
                <div class="absolute inset-0 z-[1310] flex flex-col overflow-y-auto" data-cw-mobile-content-theme style="transform: translateX(100%); opacity: 0; pointer-events: none;">
                    <div class="flex-1 flex flex-col gap-4 px-6 py-6">
                        <button type="button" class="cw-mobile-back-control" data-cw-mobile-back aria-label="{{ __('settings.back_to_main_menu') }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-[18px] h-[18px]" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19l-7-7 7-7" /></svg>
                            <span>{{ __('common.back') }}</span>
                        </button>
                        <form method="POST" action="{{ route('preferences.theme') }}" class="flex flex-col gap-1.5" data-cw-mobile-theme-form>
                            @csrf
                            <input type="hidden" name="redirect" value="{{ $currentUrl }}">
                            @foreach($themeLabels as $value => $label)
                                <button type="submit" name="theme" value="{{ $value }}" class="cw-mobile-submenu-item {{ $themePreference === $value ? 'active' : '' }}">
                                    <span>{{ $label }}</span>
                                    @if($themePreference === $value)
                                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                    @endif
                                </button>
                            @endforeach
                        </form>
                    </div>
                </div>

                <div class="absolute inset-0 z-[1310] flex flex-col overflow-y-auto" data-cw-mobile-content-profile style="transform: translateX(100%); opacity: 0; pointer-events: none;">
                    <div class="flex-1 flex flex-col gap-4 px-6 py-6">
                        <button type="button" class="cw-mobile-back-control" data-cw-mobile-back aria-label="{{ __('settings.back_to_main_menu') }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-[18px] h-[18px]" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 19l-7-7 7-7" /></svg>
                            <span>{{ __('common.back') }}</span>
                        </button>

                        @auth
                            @if($mobileProfileAccountLinks !== [])
                                <div class="pt-3">
                                    <div class="text-xs font-semibold uppercase tracking-wider mb-2 opacity-60">{{ __('navigation.account') }}</div>
                                    <div class="flex flex-col gap-1.5">
                                        @foreach($mobileProfileAccountLinks as $link)
                                            <a href="{{ $link['url'] }}" class="cw-mobile-submenu-item" data-cw-track-click="{{ $link['track'] }}">
                                                <span>{{ $link['label'] }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($mobileProfileWorkLinks !== [])
                                <div class="pt-3 border-t border-opacity-20">
                                    <div class="text-xs font-semibold uppercase tracking-wider mb-2 opacity-60">{{ __('navigation.work_applications') }}</div>
                                    <div class="flex flex-col gap-1.5">
                                        @foreach($mobileProfileWorkLinks as $link)
                                            <a href="{{ $link['url'] }}" class="cw-mobile-submenu-item" data-cw-track-click="{{ $link['track'] }}">
                                                <span>{{ $link['label'] }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endauth

                    </div>

                    @auth
                        <div class="flex flex-col gap-2 px-6 pb-6">
                            <form method="POST" action="{{ route('logout') }}" data-cw-track-submit="logout" class="w-full">
                                @csrf
                                <button type="submit" class="cw-mobile-submenu-item w-full">
                                    {{ __('navigation.logout') }}
                                </button>
                            </form>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</header>
