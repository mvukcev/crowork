<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Employer;
use App\Models\Job;
use App\Models\WorkerProfile;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformOverviewStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalWorkers = WorkerProfile::query()->count();
        $totalEmployers = Employer::query()->count();
        $pendingEmployers = Employer::query()->whereNull('approved_at')->count();

        $activeJobs = Job::query()
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->count();

        $pendingJobs = Job::query()->where('status', 'pending')->count();

        $expiredJobs = Job::query()
            ->where(function ($query) {
                $query->where('status', 'expired')
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereNotNull('expires_at')->where('expires_at', '<=', now());
                    });
            })
            ->count();

        return [
            Stat::make('Total Workers', number_format($totalWorkers))
                ->description('Worker profiles'),
            Stat::make('Total Employers', number_format($totalEmployers))
                ->description('All employer accounts'),
            Stat::make('Pending Employers', number_format($pendingEmployers))
                ->description('Awaiting approval')
                ->color('warning'),
            Stat::make('Active Jobs', number_format($activeJobs))
                ->description('Published and not expired')
                ->color('success'),
            Stat::make('Pending Jobs', number_format($pendingJobs))
                ->description('Awaiting moderation')
                ->color('warning'),
            Stat::make('Expired Jobs', number_format($expiredJobs))
                ->description('Need review or renew')
                ->color('gray'),
        ];
    }
}
