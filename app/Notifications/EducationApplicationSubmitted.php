<?php

namespace App\Notifications;

use App\Models\EducationApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EducationApplicationSubmitted extends Notification
{
    use Queueable;

    public function __construct(public EducationApplication $application)
    {
        $this->application->loadMissing('education');
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
        return (new MailMessage)
            ->subject('CroWork: education application received')
            ->greeting('Hi '.$notifiable->name.',')
            ->line('We received your application for the program "'.$this->application->education?->title.'".')
            ->line('Your application status is currently: New.')
            ->action('View your education applications', route('worker.education-applications.index'))
            ->line('Thanks for applying through CroWork.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'education_application_id' => $this->application->id,
            'education_id' => $this->application->education_id,
            'status' => $this->application->status,
        ];
    }
}