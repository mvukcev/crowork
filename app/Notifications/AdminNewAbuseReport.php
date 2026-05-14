<?php

namespace App\Notifications;

use App\Models\AbuseReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNewAbuseReport extends Notification
{
    use Queueable;

    public function __construct(public AbuseReport $report) {}

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
            ->subject('CroWork admin: new abuse report')
            ->greeting('Hi '.$notifiable->name.',')
            ->line('A new abuse report was submitted on CroWork.')
            ->line('Type: '.strtoupper($this->report->type))
            ->line('Reason: '.$this->report->reason)
            ->line('Report ID: '.$this->report->id)
            ->action('Open moderation queue', url('/admin/abuse-reports'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'report_id' => $this->report->id,
            'type' => $this->report->type,
            'reason' => $this->report->reason,
        ];
    }
}