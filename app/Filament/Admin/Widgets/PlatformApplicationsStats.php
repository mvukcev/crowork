<?php

namespace App\Filament\Admin\Widgets;

use App\Models\AbuseReport;
use App\Models\Education;
use App\Models\EducationApplication;
use App\Models\JobApplication;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformApplicationsStats extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $jobApplicationsTotal = JobApplication::query()->count();
        $jobApplicationsWeek = JobApplication::query()->where('created_at', '>=', now()->startOfWeek())->count();
        $jobApplicationsMonth = JobApplication::query()->where('created_at', '>=', now()->startOfMonth())->count();

        $educationsTotal = Education::query()->count();
        $educationApplicationsTotal = EducationApplication::query()->count();

        $abuseReportsTotal = AbuseReport::query()->count();
        $jobReportsTotal = AbuseReport::query()->where('type', 'job')->count();
        $openReports = AbuseReport::query()->whereIn('status', ['new', 'open'])->count();

        return [
            Stat::make('Job Applications', number_format($jobApplicationsTotal))
                ->description('All-time total'),
            Stat::make('Applications This Week', number_format($jobApplicationsWeek))
                ->description('Current week'),
            Stat::make('Applications This Month', number_format($jobApplicationsMonth))
                ->description('Current month'),
            Stat::make('Education Listings', number_format($educationsTotal))
                ->description('All education entries'),
            Stat::make('Education Applications', number_format($educationApplicationsTotal))
                ->description('All-time total'),
            Stat::make('Reported Jobs', number_format($jobReportsTotal))
                ->description('Abuse reports with type=job')
                ->color('warning'),
            Stat::make('Abuse Reports (Total)', number_format($abuseReportsTotal))
                ->description('All report types'),
            Stat::make('Open Abuse Reports', number_format($openReports))
                ->description('Need moderator action')
                ->color('danger'),
        ];
    }
}
