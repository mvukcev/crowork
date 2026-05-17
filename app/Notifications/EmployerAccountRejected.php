<?php

namespace App\Notifications;

use App\Models\Employer;
use App\Services\EmailTemplateService;
use App\Services\NotificationPreferenceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployerAccountRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Employer $employer) {}

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
            'employer_account_status',
            $locale,
            [
                'name' => $notifiable->name,
                'company_name' => $this->employer->company_name,
                'account_status' => trans('notifications.employer_account_status_rejected', locale: $locale),
                'status_message' => trans('notifications.employer_account_status_rejected_message', locale: $locale),
                'action_url' => url('/'),
            ],
            trans('notifications.employer_go_to_platform', locale: $locale),
            url('/')
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $locale = $notifiable->communication_language ?? app()->getLocale();

        return [
            'title' => trans('notifications.employer_account_rejected_title', locale: $locale),
            'message' => trans('notifications.employer_account_rejected_message', ['company' => $this->employer->company_name], locale: $locale),
            'url' => url('/'),
            'category' => 'important_system_notice',
            'importance' => 'high',
            'employer_id' => $this->employer->id,
            'approved_at' => $this->employer->approved_at?->toIso8601String(),
        ];
    }
}