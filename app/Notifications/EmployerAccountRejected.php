<?php

namespace App\Notifications;

use App\Models\Employer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployerAccountRejected extends Notification
{
    use Queueable;

    public function __construct(public Employer $employer) {}

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
            ->subject('CroWork: employer account update')
            ->greeting('Hi '.$notifiable->name.',')
            ->line('Your employer account for '.$this->employer->company_name.' is currently not approved.')
            ->line('If you believe this is a mistake, contact the CroWork support team for clarification.')
            ->action('Go to CroWork', url('/'))
            ->line('This message does not include sensitive internal moderation details.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'employer_id' => $this->employer->id,
            'approved_at' => $this->employer->approved_at?->toIso8601String(),
        ];
    }
}