<?php

namespace App\Notifications;

use App\Models\JobApplication;
use App\Services\NotificationPreferenceService;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewJobApplicationReceived extends Notification implements ShouldQueue
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
        // Check deduplication - don't send if same notification sent recently
        if (!\App\Services\DataIntegrityService::shouldSendNotification(
            $notifiable,
            self::class,
            "new_application_{$this->application->id}"
        )) {
            return []; // Skip notification if duplicate
        }

        return app(NotificationPreferenceService::class)->channelsFor(
            $notifiable,
            NotificationPreferenceService::CATEGORY_EMPLOYER_NEW_APPLICATION,
            ['mail', 'database']
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        $job = $this->application->job;
        $worker = $this->application->worker;
        $locale = $notifiable->communication_language ?? app()->getLocale();

        return app(EmailTemplateService::class)->toMailMessage(
            'employer_new_application',
            $locale,
            [
                'name' => $notifiable->name,
                'worker_name' => $worker?->name ?? 'Candidate',
                'job_title' => $job->title,
                'application_status' => ucfirst((string) $this->application->status),
                'action_url' => url('/employer/job-applications'),
            ],
            'Review applications',
            url('/employer/job-applications')
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $job = $this->application->job;
        $worker = $this->application->worker;

        return [
            'title' => 'New job application',
            'message' => ($worker?->name ?? 'A worker').' applied for '.$job->title.'.',
            'url' => url('/employer/job-applications'),
            'category' => 'employer_application_alert',
            'importance' => 'normal',
            'application_id' => $this->application->id,
            'job_id' => $this->application->job_id,
            'worker_id' => $this->application->worker_id,
        ];
    }
}
