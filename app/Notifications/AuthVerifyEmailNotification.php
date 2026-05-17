<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AuthVerifyEmailNotification extends VerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        $verifyUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject(__('emails.verify_subject'))
            ->line(__('emails.verify_line_1'))
            ->action(__('emails.verify_action'), $verifyUrl)
            ->line(__('emails.verify_line_2'));
    }
}
