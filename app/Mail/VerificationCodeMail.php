<?php

namespace App\Mail;

use App\Services\EmailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $code,
        public readonly ?string $mailLocale = null,
        public readonly ?string $name = null,
    ) {}

    public function envelope(): Envelope
    {
        $rendered = app(EmailTemplateService::class)->render('verification_code', $this->mailLocale, [
            'name' => $this->name ?: 'there',
            'code' => $this->code,
        ]);

        return new Envelope(
            subject: $rendered['subject'],
        );
    }

    public function content(): Content
    {
        $rendered = app(EmailTemplateService::class)->render('verification_code', $this->mailLocale, [
            'name' => $this->name ?: 'there',
            'code' => $this->code,
        ]);

        return new Content(
            view: 'mail.dynamic-template',
            with: [
                'body' => $rendered['body'],
            ],
        );
    }
}
