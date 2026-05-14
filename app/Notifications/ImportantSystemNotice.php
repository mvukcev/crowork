<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ImportantSystemNotice extends Notification
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

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title)
            ->greeting('Hi '.$notifiable->name.',')
            ->line($this->message);

        if ($this->url) {
            $mail->action('Open notification', $this->url);
        }

        return $mail;
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
