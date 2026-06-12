<?php

namespace App\Notifications;

use App\Models\BugReport;
use App\Services\EmailTemplateService;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNewBugReport extends Notification
{
    public function __construct(private readonly BugReport $bugReport)
    {
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
        $reportUrl = url('/admin/bugs/' . $this->bugReport->id . '/edit');
        $locale = method_exists($notifiable, 'preferredLocale')
            ? (string) $notifiable->preferredLocale()
            : app()->getLocale();

        return app(EmailTemplateService::class)->toMailMessage(
            'admin_new_bug_report',
            $locale,
            [
                'name' => $notifiable->name ?? 'Admin',
                'reporter_email' => $this->bugReport->reporter_email ?: 'anonymous',
                'reported_at' => optional($this->bugReport->reported_at)->format('Y-m-d H:i:s') ?: now()->format('Y-m-d H:i:s'),
                'page_uri' => (string) $this->bugReport->page_uri,
                'error_logs_count' => (string) ((int) $this->bugReport->error_logs_count),
                'description' => (string) $this->bugReport->description,
                'action_url' => $reportUrl,
            ],
            $locale === 'hr' ? 'Otvori bug prijavu' : 'Open bug report',
            $reportUrl
        );
    }
}
