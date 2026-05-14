<?php

namespace App\Notifications;

use App\Models\Job;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobStatusChanged extends Notification
{
    use Queueable;

    public function __construct(public Job $job, public string $status)
    {
        $this->job->loadMissing('employer');
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isPublished = $this->status === 'published';
        $locale = $notifiable->communication_language ?? app()->getLocale();
        $actionUrl = $isPublished
            ? route('jobs.show', $this->job)
            : url('/employer');
        $actionLabel = $isPublished
            ? 'View job listing'
            : 'Go to employer dashboard';

        return app(EmailTemplateService::class)->toMailMessage(
            'job_status_changed',
            $locale,
            [
                'name' => $notifiable->name,
                'job_title' => $this->job->title,
                'job_status' => $isPublished ? 'approved and visible to workers' : 'not publicly visible',
                'action_url' => $actionUrl,
            ],
            $actionLabel,
            $actionUrl
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $isPublished = $this->status === 'published';

        return [
            'title' => $isPublished ? 'Job approved' : 'Job no longer public',
            'message' => $isPublished
                ? 'Your job listing "'.$this->job->title.'" is approved and visible to workers.'
                : 'Your job listing "'.$this->job->title.'" is no longer publicly visible.',
            'url' => $isPublished ? route('jobs.show', $this->job) : url('/employer'),
            'category' => 'important_system_notice',
            'importance' => $isPublished ? 'normal' : 'high',
            'job_id' => $this->job->id,
            'status' => $this->status,
        ];
    }
}
