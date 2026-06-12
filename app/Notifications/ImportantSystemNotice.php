<?php

namespace App\Notifications;

use App\Services\EmailTemplateService;
use App\Services\NotificationPreferenceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ImportantSystemNotice extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public ?string $url = null,
        public bool $sendMail = false,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($this->sendMail) {
            $channels[] = 'mail';
        }

        return app(NotificationPreferenceService::class)->channelsFor(
            $notifiable,
            NotificationPreferenceService::CATEGORY_SYSTEM_NOTICES,
            $channels
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locale = $notifiable->communication_language ?? app()->getLocale();
        $actionLabel = $locale === 'hr' ? 'Otvori obavijest' : 'Open notification';

        return app(EmailTemplateService::class)->toMailMessage(
            'important_system_notice',
            $locale,
            [
                'name' => $notifiable->name ?? trans('emails.recipient_fallback', locale: $locale),
                'title' => $this->title,
                'message' => $this->message,
                'action_url' => $this->url ?: route('notifications.index'),
            ],
            $actionLabel,
            $this->url ?: route('notifications.index'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url ?: route('notifications.index'),
            'category' => 'important_system_notice',
            'importance' => 'high',
        ];
    }
}
