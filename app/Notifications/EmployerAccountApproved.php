<?php

namespace App\Notifications;

use App\Models\Employer;
use App\Services\EmailTemplateService;
use App\Services\NotificationPreferenceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmployerAccountApproved extends Notification implements ShouldQueue
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
                'account_status' => 'approved',
                'status_message' => 'You can now access employer tools and manage listings in CroWork.',
                'action_url' => url('/employer'),
            ],
            'Go to employer dashboard',
            url('/employer')
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Employer account approved',
            'message' => 'Your employer account for '.$this->employer->company_name.' is approved.',
            'url' => url('/employer'),
            'category' => 'important_system_notice',
            'importance' => 'high',
            'employer_id' => $this->employer->id,
            'approved_at' => $this->employer->approved_at?->toIso8601String(),
        ];
    }
}
