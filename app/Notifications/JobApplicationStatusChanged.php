<?php

namespace App\Notifications;

use App\Models\JobApplication;
use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class JobApplicationStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public JobApplication $application,
        public string $previousStatus
    ) {
        $this->application->loadMissing('job.employer');
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
        $job = $this->application->job;
        $newStatus = Str::headline($this->application->status);
        $locale = $notifiable->communication_language ?? app()->getLocale();

        return app(EmailTemplateService::class)->toMailMessage(
            'application_status_changed',
            $locale,
            [
                'name' => $notifiable->name,
                'job_title' => $job->title,
                'new_status' => $newStatus,
                'previous_status' => Str::headline($this->previousStatus),
                'company_name' => $job->employer?->company_name ?? 'Employer',
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
            'title' => 'Application status changed',
            'message' => 'Your application for '.$job->title.' is now '.Str::headline($this->application->status).'.',
            'url' => route('worker.applications.index'),
            'category' => 'application_status_update',
            'importance' => in_array($this->application->status, ['rejected', 'accepted'], true) ? 'high' : 'normal',
            'application_id' => $this->application->id,
            'job_id' => $this->application->job_id,
            'previous_status' => $this->previousStatus,
            'status' => $this->application->status,
        ];
    }
}