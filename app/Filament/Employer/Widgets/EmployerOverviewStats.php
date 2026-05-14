<?php

namespace App\Filament\Employer\Widgets;

use App\Models\Job;
use App\Models\JobApplication;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmployerOverviewStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $employerId = auth()->user()?->employer?->id;

        $jobsQuery = Job::query()->where('employer_id', $employerId);

        $activeJobs = (clone $jobsQuery)
            ->where('status', 'published')
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->count();

        $pendingJobs = (clone $jobsQuery)->where('status', 'pending')->count();

        $expiredJobs = (clone $jobsQuery)
            ->where(function ($query) {
                $query->where('status', 'expired')
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereNotNull('expires_at')->where('expires_at', '<=', now());
                    });
            })
            ->count();

        $applicationsQuery = JobApplication::query()
            ->whereHas('job', fn ($query) => $query->where('employer_id', $employerId));

        $totalApplications = (clone $applicationsQuery)->count();
        $newApplications = (clone $applicationsQuery)->where('status', JobApplication::STATUS_NEW)->count();
        $shortlisted = (clone $applicationsQuery)->where('status', JobApplication::STATUS_SHORTLISTED)->count();
        $interviews = (clone $applicationsQuery)->where('status', JobApplication::STATUS_INTERVIEW)->count();

        $expiringSoon = (clone $jobsQuery)
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [now(), now()->addDays(7)])
            ->count();

        return [
            Stat::make('Active Jobs', number_format($activeJobs))->color('success'),
            Stat::make('Pending Jobs', number_format($pendingJobs))->color('warning'),
            Stat::make('Expired Jobs', number_format($expiredJobs))->color('gray'),
            Stat::make('Total Applications', number_format($totalApplications)),
            Stat::make('New Applications', number_format($newApplications))->color('warning'),
            Stat::make('Shortlisted', number_format($shortlisted))->color('success'),
            Stat::make('Interviews', number_format($interviews))->color('info'),
            Stat::make('Expiring Soon (7d)', number_format($expiringSoon))->color('danger'),
        ];
    }
}
