<?php

namespace App\Notifications;

use App\Models\JobApplication;
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
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $job = $this->application->job;
        $newStatus = Str::headline($this->application->status);

        return (new MailMessage)
            ->subject('CroWork update: application status changed')
            ->greeting('Hi '.$notifiable->name.',')
            ->line('Your application for "'.$job->title.'" has a new status: '.$newStatus.'.')
            ->line('Employer: '.($job->employer?->company_name ?? 'Employer'))
            ->action('View your applications', route('worker.applications.index'))
            ->line('This is an automated update from CroWork.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'job_id' => $this->application->job_id,
            'previous_status' => $this->previousStatus,
            'status' => $this->application->status,
        ];
    }
}