<?php

namespace App\Notifications;

use App\Models\Employer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNewEmployerPending extends Notification
{
    use Queueable;

    public function __construct(public Employer $employer)
    {
        $this->employer->loadMissing('user');
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
            ->subject('CroWork admin: new employer pending approval')
            ->greeting('Hi '.$notifiable->name.',')
            ->line('A new employer account is pending review.')
            ->line('Company: '.$this->employer->company_name)
            ->line('Contact email: '.$this->employer->user?->email)
            ->action('Review employer queue', url('/admin/employers'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'employer_id' => $this->employer->id,
            'company_name' => $this->employer->company_name,
        ];
    }
}