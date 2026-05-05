<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobApplicationSubmitted extends Notification
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
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $job = $this->application->job;

        return (new MailMessage)
            ->subject('Application received: '.$job->title)
            ->greeting('Hi '.$notifiable->name.',')
            ->line('We received your application for '.$job->title.' at '.$job->employer?->company_name.'.')
            ->line('Your application status is currently: '.ucfirst($this->application->status).'.')
            ->action('View your applications', route('worker.applications.index'))
            ->line('We will keep your application available in your CroWork account.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'job_id' => $this->application->job_id,
            'status' => $this->application->status,
        ];
    }
}
