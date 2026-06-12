<?php

namespace App\Notifications;

use App\Services\EmailTemplateService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class AuthResetPasswordNotification extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);
        $locale = $notifiable->communication_language ?? app()->getLocale();
        $expires = (string) config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return app(EmailTemplateService::class)->toMailMessage(
            'auth_reset_password',
            $locale,
            [
                'name' => $notifiable->name ?? trans('emails.recipient_fallback', locale: $locale),
                'count' => $expires,
                'expire_minutes' => $expires,
                'action_url' => $url,
            ],
            trans('emails.reset_action', locale: $locale),
            $url,
        );
    }
}
