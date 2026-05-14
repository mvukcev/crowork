<?php

namespace App\Notifications;

use App\Models\Employer;
use App\Services\EmailTemplateService;
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
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locale = $notifiable->communication_language ?? app()->getLocale();

        return app(EmailTemplateService::class)->toMailMessage(
            'admin_new_employer_pending',
            $locale,
            [
                'name' => $notifiable->name,
                'company_name' => $this->employer->company_name,
                'contact_email' => $this->employer->user?->email ?? '-',
                'action_url' => url('/admin/employers'),
            ],
            'Review employer queue',
            url('/admin/employers')
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New employer pending approval',
            'message' => 'Company '.$this->employer->company_name.' is waiting for review.',
            'url' => url('/admin/employers'),
            'category' => 'employer_application_alert',
            'importance' => 'high',
            'employer_id' => $this->employer->id,
            'company_name' => $this->employer->company_name,
        ];
    }
}