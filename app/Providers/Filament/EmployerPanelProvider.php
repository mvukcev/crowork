<?php

namespace App\Providers\Filament;

use App\Filament\Employer\Pages\Dashboard;
use App\Filament\Employer\Resources\EmployerProfileResource;
use App\Filament\Employer\Resources\JobApplicationResource;
use App\Filament\Employer\Widgets\EmployerApplicationStatusChart;
use App\Filament\Employer\Widgets\EmployerApplicationsByJobChart;
use App\Filament\Employer\Widgets\EmployerJobsByStatusChart;
use App\Filament\Employer\Widgets\EmployerOverviewStats;
use App\Filament\Employer\Widgets\ExpiringJobsTable;
use App\Http\Middleware\EmployerAccessMiddleware;
use App\Http\Middleware\EnsureLatestLegalConsentAccepted;
use App\Http\Middleware\PreventImpersonatedWrites;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class EmployerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('employer')
            ->path('employer')
            ->login()
            ->brandName('CroWork')
            ->brandLogo(asset('assets/branding/CW-Logo-Dark.svg'))
            ->darkModeBrandLogo(asset('assets/branding/CW-Logo-Light.svg'))
            ->favicon(asset('assets/CW-Favicon.png'))
            ->brandLogoHeight('2rem')
            ->colors([
                'primary' => [
                    50 => '#fff4ec',
                    100 => '#ffe5d6',
                    200 => '#ffc4a7',
                    300 => '#ff9f76',
                    400 => '#ff7641',
                    500 => '#fe5000',
                    600 => '#db4300',
                    700 => '#b53800',
                    800 => '#902d00',
                    900 => '#732400',
                    950 => '#4a1700',
                ],
                'gray' => [
                    50 => '#f4f7fa',
                    100 => '#e8eef4',
                    200 => '#dde5ed',
                    300 => '#bcc9d7',
                    400 => '#8fa1b6',
                    500 => '#5e728d',
                    600 => '#425570',
                    700 => '#2c3f5a',
                    800 => '#1b2f4b',
                    900 => '#0c2340',
                    950 => '#06182d',
                ],
            ])
            ->resources([
                EmployerProfileResource::class,
                JobApplicationResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Employer/Pages'), for: 'App\\Filament\\Employer\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Employer/Widgets'), for: 'App\\Filament\\Employer\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                EmployerOverviewStats::class,
                EmployerJobsByStatusChart::class,
                EmployerApplicationStatusChart::class,
                EmployerApplicationsByJobChart::class,
                ExpiringJobsTable::class,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_START,
                fn (): string => view('components.theme-init')->render()
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => view('filament.partials.topbar-overlay-fix')->render()
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => view('filament.partials.brand-theme')->render()
            )
            ->renderHook(
                PanelsRenderHook::TOPBAR_START,
                fn (): string => view('filament.partials.impersonation-banner')->render()
            )
            ->renderHook(
                PanelsRenderHook::TOPBAR_START,
                fn (): string => view('filament.partials.view-site-link')->render()
            )
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_AFTER,
                fn (): string => view('filament.partials.notification-center-dropdown')->render()
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                PreventImpersonatedWrites::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                EmployerAccessMiddleware::class,
                EnsureLatestLegalConsentAccepted::class,
            ]);
    }
}
