<?php

namespace App\Services\Hzz;

use App\Models\HzzJobAnalyticsEvent;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class HzzAnalyticsService
{
    public function overview(Carbon $from, Carbon $to): array
    {
        $jobIds = $this->hzzJobsQuery()->pluck('id');

        $events = HzzJobAnalyticsEvent::query()
            ->whereIn('job_id', $jobIds)
            ->whereBetween('event_at', [$from, $to]);

        $views = (clone $events)->where('event_type', HzzJobAnalyticsEvent::EVENT_VIEW)->count();
        $uniqueViews = (clone $events)->where('event_type', HzzJobAnalyticsEvent::EVENT_VIEW)->where('is_unique_view', true)->count();
        $ctaClicks = (clone $events)->where('event_type', HzzJobAnalyticsEvent::EVENT_CTA_CLICK)->count();
        $externalOpens = (clone $events)->where('event_type', HzzJobAnalyticsEvent::EVENT_EXTERNAL_OPEN)->count();

        $sentApplications = JobApplication::query()
            ->whereIn('job_id', $jobIds)
            ->where('apply_channel', 'hzz_email')
            ->where('submission_status', 'sent')
            ->whereBetween('submitted_at', [$from, $to])
            ->count();

        return [
            'total_hzz_jobs' => $jobIds->count(),
            'total_views' => $views,
            'unique_views' => $uniqueViews,
            'cta_clicks' => $ctaClicks,
            'external_opens' => $externalOpens,
            'applications_sent' => $sentApplications,
            'ctr_percent' => $views > 0 ? round(($ctaClicks / $views) * 100, 2) : 0,
        ];
    }

    public function viewsByDay(Carbon $from, Carbon $to): Collection
    {
        $jobIds = $this->hzzJobsQuery()->pluck('id');

        return HzzJobAnalyticsEvent::query()
            ->selectRaw('DATE(event_at) as day, COUNT(*) as total_views, SUM(CASE WHEN is_unique_view = 1 THEN 1 ELSE 0 END) as unique_views')
            ->whereIn('job_id', $jobIds)
            ->where('event_type', HzzJobAnalyticsEvent::EVENT_VIEW)
            ->whereBetween('event_at', [$from, $to])
            ->groupByRaw('DATE(event_at)')
            ->orderByRaw('DATE(event_at) ASC')
            ->get();
    }

    public function perJobStats(Carbon $from, Carbon $to): Collection
    {
        $jobIds = $this->hzzJobsQuery()->pluck('id');

        $events = HzzJobAnalyticsEvent::query()
            ->selectRaw('job_id,
                SUM(CASE WHEN event_type = ? THEN 1 ELSE 0 END) as views,
                SUM(CASE WHEN event_type = ? AND is_unique_view = 1 THEN 1 ELSE 0 END) as unique_views,
                SUM(CASE WHEN event_type = ? THEN 1 ELSE 0 END) as cta_clicks,
                SUM(CASE WHEN event_type = ? THEN 1 ELSE 0 END) as external_opens', [
                HzzJobAnalyticsEvent::EVENT_VIEW,
                HzzJobAnalyticsEvent::EVENT_VIEW,
                HzzJobAnalyticsEvent::EVENT_CTA_CLICK,
                HzzJobAnalyticsEvent::EVENT_EXTERNAL_OPEN,
            ])
            ->whereIn('job_id', $jobIds)
            ->whereBetween('event_at', [$from, $to])
            ->groupBy('job_id')
            ->get()
            ->keyBy('job_id');

        $applications = JobApplication::query()
            ->selectRaw('job_id, COUNT(*) as sent_count')
            ->whereIn('job_id', $jobIds)
            ->where('apply_channel', 'hzz_email')
            ->where('submission_status', 'sent')
            ->whereBetween('submitted_at', [$from, $to])
            ->groupBy('job_id')
            ->get()
            ->keyBy('job_id');

        return Job::query()
            ->whereIn('id', $jobIds)
            ->orderByDesc('published_at')
            ->get(['id', 'title', 'slug'])
            ->map(function (Job $job) use ($events, $applications) {
                $event = $events->get($job->id);
                $views = (int) ($event->views ?? 0);
                $clicks = (int) ($event->cta_clicks ?? 0);

                return [
                    'job_id' => $job->id,
                    'title' => $job->title,
                    'slug' => $job->slug,
                    'views' => $views,
                    'unique_views' => (int) ($event->unique_views ?? 0),
                    'cta_clicks' => $clicks,
                    'external_opens' => (int) ($event->external_opens ?? 0),
                    'applications_sent' => (int) ($applications->get($job->id)->sent_count ?? 0),
                    'ctr_percent' => $views > 0 ? round(($clicks / $views) * 100, 2) : 0,
                ];
            });
    }

    public function monthlyReportRows(Carbon $month): Collection
    {
        $from = $month->copy()->startOfMonth();
        $to = $month->copy()->endOfMonth();

        return $this->perJobStats($from, $to)->map(function (array $row) use ($month) {
            return [
                'month' => $month->format('Y-m'),
                'job_id' => $row['job_id'],
                'job_title' => $row['title'],
                'job_slug' => $row['slug'],
                'views' => $row['views'],
                'unique_views' => $row['unique_views'],
                'cta_clicks' => $row['cta_clicks'],
                'ctr_percent' => $row['ctr_percent'],
                'applications_sent' => $row['applications_sent'],
                'external_opens' => $row['external_opens'],
            ];
        });
    }

    public function monthlyDetailedRows(Carbon $month): Collection
    {
        $from = $month->copy()->startOfMonth();
        $to = $month->copy()->endOfMonth();
        $jobIds = $this->hzzJobsQuery()->pluck('id');

        $base = HzzJobAnalyticsEvent::query()
            ->selectRaw('job_id, DATE(event_at) as event_date, MIN(event_at) as first_view_at, MAX(event_at) as last_view_at')
            ->whereIn('job_id', $jobIds)
            ->whereBetween('event_at', [$from, $to])
            ->where('event_type', HzzJobAnalyticsEvent::EVENT_VIEW)
            ->groupBy('job_id')
            ->groupByRaw('DATE(event_at)')
            ->get();

        $jobMap = Job::query()->whereIn('id', $jobIds)->get(['id', 'title', 'slug'])->keyBy('id');

        return $base->map(function ($row) use ($jobMap) {
            $jobId = (int) ($row->job_id ?? 0);
            $job = $jobMap->get($jobId);

            $dayStart = Carbon::parse((string) $row->event_date)->startOfDay();
            $dayEnd = Carbon::parse((string) $row->event_date)->endOfDay();

            $views = HzzJobAnalyticsEvent::query()
                ->where('job_id', $jobId)
                ->where('event_type', HzzJobAnalyticsEvent::EVENT_VIEW)
                ->whereBetween('event_at', [$dayStart, $dayEnd])
                ->count();

            $uniqueViews = HzzJobAnalyticsEvent::query()
                ->where('job_id', $jobId)
                ->where('event_type', HzzJobAnalyticsEvent::EVENT_VIEW)
                ->where('is_unique_view', true)
                ->whereBetween('event_at', [$dayStart, $dayEnd])
                ->count();

            $ctaClicks = HzzJobAnalyticsEvent::query()
                ->where('job_id', $jobId)
                ->where('event_type', HzzJobAnalyticsEvent::EVENT_CTA_CLICK)
                ->whereBetween('event_at', [$dayStart, $dayEnd])
                ->count();

            $externalOpens = HzzJobAnalyticsEvent::query()
                ->where('job_id', $jobId)
                ->where('event_type', HzzJobAnalyticsEvent::EVENT_EXTERNAL_OPEN)
                ->whereBetween('event_at', [$dayStart, $dayEnd])
                ->count();

            $applicationsSent = JobApplication::query()
                ->where('job_id', $jobId)
                ->where('apply_channel', JobApplication::CHANNEL_HZZ_EMAIL)
                ->where('submission_status', JobApplication::SUBMISSION_SENT)
                ->whereBetween('submitted_at', [$dayStart, $dayEnd])
                ->count();

            return [
                'job_id' => $jobId,
                'job_title' => $job?->title,
                'job_slug' => $job?->slug,
                'date' => (string) $row->event_date,
                'first_view_time' => $row->first_view_at ? Carbon::parse((string) $row->first_view_at)->format('H:i:s') : null,
                'last_view_time' => $row->last_view_at ? Carbon::parse((string) $row->last_view_at)->format('H:i:s') : null,
                'total_views' => $views,
                'unique_views' => $uniqueViews,
                'cta_clicks' => $ctaClicks,
                'external_opens' => $externalOpens,
                'applications_sent_via_crowork' => $applicationsSent,
                'ctr_percent' => $views > 0 ? round(($ctaClicks / $views) * 100, 2) : 0,
            ];
        })->values();
    }

    private function hzzJobsQuery(): Builder
    {
        return Job::query()->where(function (Builder $query): void {
            $query->where('hzz_is_official', true)
                ->orWhere('source_system', 'hzz');
        });
    }
}
