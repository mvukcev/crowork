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
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
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
            ->brandName('CroWork Employer')
            ->colors([
                'primary' => Color::Green,
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
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                EmployerAccessMiddleware::class,
            ]);
    }
}
