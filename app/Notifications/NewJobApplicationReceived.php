<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewJobApplicationReceived extends Notification
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
        $worker = $this->application->worker;

        return (new MailMessage)
            ->subject('New application: '.$job->title)
            ->greeting('Hi '.$notifiable->name.',')
            ->line($worker->name.' submitted a new application for '.$job->title.'.')
            ->line('Application status: '.ucfirst($this->application->status).'.')
            ->action('Review applications', url('/employer/job-applications'))
            ->line('Sign in to CroWork to review the candidate profile snapshot and message.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'job_id' => $this->application->job_id,
            'worker_id' => $this->application->worker_id,
        ];
    }
}
