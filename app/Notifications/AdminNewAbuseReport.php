<?php

namespace App\Notifications;

use App\Models\AbuseReport;
use App\Services\EmailTemplateService;
use App\Services\NotificationPreferenceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNewAbuseReport extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public AbuseReport $report) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return app(NotificationPreferenceService::class)->channelsFor(
            $notifiable,
            NotificationPreferenceService::CATEGORY_ADMIN_MODERATION,
            ['mail', 'database']
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locale = $notifiable->communication_language ?? app()->getLocale();

        return app(EmailTemplateService::class)->toMailMessage(
            'admin_new_abuse_report',
            $locale,
            [
                'name' => $notifiable->name,
                'report_type' => strtoupper((string) $this->report->type),
                'report_reason' => (string) $this->report->reason,
                'report_id' => (string) $this->report->id,
                'action_url' => url('/admin/abuse-reports'),
            ],
            'Open moderation queue',
            url('/admin/abuse-reports')
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New abuse report',
            'message' => 'A new abuse report #'.$this->report->id.' was submitted.',
            'url' => url('/admin/abuse-reports'),
            'category' => 'important_system_notice',
            'importance' => 'high',
            'report_id' => $this->report->id,
            'type' => $this->report->type,
            'reason' => $this->report->reason,
        ];
    }
}