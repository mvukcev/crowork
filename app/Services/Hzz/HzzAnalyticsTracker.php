<?php

namespace App\Services\Hzz;

use App\Models\HzzJobAnalyticsEvent;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;

class HzzAnalyticsTracker
{
    public function trackView(Job $job, Request $request): void
    {
        if (! $job->isHzzOfficial()) {
            return;
        }

        $sessionId = $request->session()->getId();
        $eventAt = now();

        $isUnique = ! HzzJobAnalyticsEvent::query()
            ->where('job_id', $job->id)
            ->where('event_type', HzzJobAnalyticsEvent::EVENT_VIEW)
            ->where('session_id', $sessionId)
            ->whereDate('event_at', $eventAt->toDateString())
            ->exists();

        $this->createEvent($job, $request->user(), HzzJobAnalyticsEvent::EVENT_VIEW, $request, [
            'is_unique_view' => $isUnique,
        ], $isUnique);
    }

    public function trackCtaClick(Job $job, Request $request): void
    {
        $this->createEvent($job, $request->user(), HzzJobAnalyticsEvent::EVENT_CTA_CLICK, $request);
    }

    public function trackExternalOpen(Job $job, Request $request): void
    {
        $this->createEvent($job, $request->user(), HzzJobAnalyticsEvent::EVENT_EXTERNAL_OPEN, $request);
    }

    public function trackApplicationSent(Job $job, Request $request, bool $success, array $context = []): void
    {
        $meta = array_merge($context, [
            'success' => $success,
        ]);

        $this->createEvent($job, $request->user(), HzzJobAnalyticsEvent::EVENT_APPLICATION_SENT, $request, $meta);
    }

    private function createEvent(
        Job $job,
        ?User $user,
        string $eventType,
        Request $request,
        array $meta = [],
        bool $isUniqueView = false
    ): void {
        if (! $job->isHzzOfficial()) {
            return;
        }

        HzzJobAnalyticsEvent::query()->create([
            'job_id' => $job->id,
            'user_id' => $user?->id,
            'session_id' => $request->session()->getId(),
            'event_type' => $eventType,
            'is_unique_view' => $isUniqueView,
            'event_at' => now(),
            'meta' => array_merge($meta, [
                'path' => $request->path(),
                'referer' => $request->headers->get('referer'),
            ]),
        ]);
    }
}
