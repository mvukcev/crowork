<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\MarketingImages;
use App\Http\Middleware\AdminAccessMiddleware;
use App\Http\Middleware\AdminModuleAccessMiddleware;
use App\Http\Middleware\EnsureLatestLegalConsentAccepted;
use App\Http\Middleware\EnsureAdminPanelSessionIsPrivileged;
use App\Http\Middleware\LogAdminWriteActions;
use App\Models\User;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
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

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('CroWork')
            ->brandLogo(asset('assets/branding/CW-Logo-Dark.svg'))
            ->darkModeBrandLogo(asset('assets/branding/CW-Logo-Light.svg'))
            ->brandLogoHeight('1.45rem')
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
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
                MarketingImages::class,
            ])
            ->navigationGroups([
                NavigationGroup::make('Applications')->label('Applications'),
                NavigationGroup::make('Job Management')->label('Job Management'),
                NavigationGroup::make('Educations Management')->label('Educations Management'),
                NavigationGroup::make('Content')->label('Content'),
                NavigationGroup::make('User Management')->label('User Management'),
                NavigationGroup::make('Settings')->label('Settings'),
                NavigationGroup::make('GDPR')->label('GDPR'),
            ])
            ->navigationItems([
                NavigationItem::make(__('gdpr_admin.menu'))
                    ->icon('heroicon-o-shield-check')
                    ->group('GDPR')
                    ->sort(80)
                    ->url(url('/admin/gdpr'))
                    ->visible(fn (): bool => auth()->check() && auth()->user()?->isAdmin() && auth()->user()?->canAccessAdminModule('gdpr')),
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
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
                fn (): string => view('filament.partials.view-site-link')->render()
            )
            ->renderHook(
                PanelsRenderHook::USER_MENU_BEFORE,
                fn (): string => view('filament.partials.notification-center-dropdown')->render()
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                EnsureAdminPanelSessionIsPrivileged::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                LogAdminWriteActions::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                AdminAccessMiddleware::class,
                AdminModuleAccessMiddleware::class,
                EnsureLatestLegalConsentAccepted::class,
            ]);
    }
}
