<?php

namespace App\Notifications;

use App\Models\Employer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployerAccountApproved extends Notification
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
            ->subject('Your CroWork employer account is approved')
            ->greeting('Hi '.$notifiable->name.',')
            ->line('Your employer account for '.$this->employer->company_name.' has been approved.')
            ->line('You can now access employer tools and manage your listings in CroWork.')
            ->action('Go to employer dashboard', url('/employer'));
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
