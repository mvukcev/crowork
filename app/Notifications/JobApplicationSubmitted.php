<?php

namespace App\Notifications;

use App\Models\JobApplication;
use App\Services\EmailTemplateService;
use App\Services\NotificationPreferenceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobApplicationSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public JobApplication $application)
    {
        $this->application->loadMissing('job.employer', 'worker');
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return app(NotificationPreferenceService::class)->channelsFor(
            $notifiable,
            NotificationPreferenceService::CATEGORY_WORKER_APPLICATION_STATUS,
            ['mail', 'database']
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        $job = $this->application->job;
        $locale = $notifiable->communication_language ?? app()->getLocale();

        return app(EmailTemplateService::class)->toMailMessage(
            'worker_application_confirmation',
            $locale,
            [
                'name' => $notifiable->name,
                'job_title' => $job->title,
                'company_name' => $job->employer?->company_name ?? 'Employer',
                'application_status' => ucfirst((string) $this->application->status),
                'action_url' => route('worker.applications.index'),
            ],
            'View your applications',
            route('worker.applications.index')
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $job = $this->application->job;

        return [
            'title' => 'Application received',
            'message' => 'We received your application for '.$job->title.'.',
            'url' => route('worker.applications.index'),
            'category' => 'application_status_update',
            'importance' => 'normal',
            'application_id' => $this->application->id,
            'job_id' => $this->application->job_id,
            'status' => $this->application->status,
        ];
    }
}
