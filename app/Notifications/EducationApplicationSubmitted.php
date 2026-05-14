<?php

namespace App\Notifications;

use App\Models\EducationApplication;
use App\Services\EmailTemplateService;
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
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locale = $notifiable->communication_language ?? app()->getLocale();

        return app(EmailTemplateService::class)->toMailMessage(
            'education_application_confirmation',
            $locale,
            [
                'name' => $notifiable->name,
                'education_title' => $this->application->education?->title ?? 'Education program',
                'application_status' => ucfirst((string) ($this->application->status ?: 'new')),
                'action_url' => route('worker.education-applications.index'),
            ],
            'View your education applications',
            route('worker.education-applications.index')
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Education application received',
            'message' => 'We received your application for '.$this->application->education?->title.'.',
            'url' => route('worker.education-applications.index'),
            'category' => 'application_status_update',
            'importance' => 'normal',
            'education_application_id' => $this->application->id,
            'education_id' => $this->application->education_id,
            'status' => $this->application->status,
        ];
    }
}