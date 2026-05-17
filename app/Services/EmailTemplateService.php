<?php

namespace App\Services;

use App\Models\EmailTemplate;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Arr;

class EmailTemplateService
{
    /**
     * @return array<string, array{label: string, subject: string, body: string, variables: array<int, string>}>
     */
    public static function definitions(): array
    {
        return [
            'verification_code' => [
                'label' => 'Verification Code',
                'subject' => 'Your CroWork verification code',
                'body' => "Hi {{name}},\n\nYour verification code is: {{code}}\n\nThis code expires soon. If you did not request this, ignore this email.",
                'variables' => ['name', 'code'],
            ],
            'worker_application_confirmation' => [
                'label' => 'Worker Application Confirmation',
                'subject' => 'Application received: {{job_title}}',
                'body' => "Hi {{name}},\n\nWe received your application for {{job_title}} at {{company_name}}.\nCurrent status: {{application_status}}.\n\nYou can review your applications in your CroWork account.",
                'variables' => ['name', 'job_title', 'company_name', 'application_status', 'action_url'],
            ],
            'employer_new_application' => [
                'label' => 'Employer New Application',
                'subject' => 'New application: {{job_title}}',
                'body' => "Hi {{name}},\n\n{{worker_name}} submitted a new application for {{job_title}}.\nCurrent status: {{application_status}}.\n\nSign in to CroWork to review the candidate profile snapshot and message.",
                'variables' => ['name', 'worker_name', 'job_title', 'application_status', 'action_url'],
            ],
            'application_status_changed' => [
                'label' => 'Application Status Changed',
                'subject' => 'CroWork update: application status changed',
                'body' => "Hi {{name}},\n\nYour application for {{job_title}} has a new status: {{new_status}}.\nEmployer: {{company_name}}.\n\nThis is an automated update from CroWork.",
                'variables' => ['name', 'job_title', 'new_status', 'previous_status', 'company_name', 'action_url'],
            ],
            'employer_account_status' => [
                'label' => 'Employer Approved/Rejected',
                'subject' => 'CroWork: employer account update',
                'body' => "Hi {{name}},\n\nYour employer account for {{company_name}} is now {{account_status}}.\n{{status_message}}",
                'variables' => ['name', 'company_name', 'account_status', 'status_message', 'action_url'],
            ],
            'job_status_changed' => [
                'label' => 'Job Approved/Rejected',
                'subject' => 'CroWork: job status updated',
                'body' => "Hi {{name}},\n\nYour job listing \"{{job_title}}\" is now {{job_status}}.\n\nOpen CroWork to review details.",
                'variables' => ['name', 'job_title', 'job_status', 'action_url'],
            ],
            'education_application_confirmation' => [
                'label' => 'Education Application Confirmation',
                'subject' => 'CroWork: education application received',
                'body' => "Hi {{name}},\n\nWe received your application for \"{{education_title}}\".\nCurrent status: {{application_status}}.\n\nThanks for applying through CroWork.",
                'variables' => ['name', 'education_title', 'application_status', 'action_url'],
            ],
            'admin_new_employer_pending' => [
                'label' => 'Admin New Employer Pending',
                'subject' => 'CroWork admin: new employer pending approval',
                'body' => "Hi {{name}},\n\nA new employer account is pending review.\nCompany: {{company_name}}\nContact email: {{contact_email}}",
                'variables' => ['name', 'company_name', 'contact_email', 'action_url'],
            ],
            'admin_new_abuse_report' => [
                'label' => 'Admin New Abuse Report',
                'subject' => 'CroWork admin: new abuse report',
                'body' => "Hi {{name}},\n\nA new abuse report was submitted.\nType: {{report_type}}\nReason: {{report_reason}}\nReport ID: {{report_id}}",
                'variables' => ['name', 'report_type', 'report_reason', 'report_id', 'action_url'],
            ],
        ];
    }

    /**
     * @return array{key: string, locale: string, subject: string, body: string, variables: array<int, string>, used_default: bool}
     */
    public function resolveTemplate(string $key, ?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();
        $definitions = self::definitions();
        $default = Arr::get($definitions, $key);

        if ($key === 'verification_code' && is_array($default)) {
            $default['subject'] = trans('emails.verification_code_subject', locale: $locale);
            $default['body'] = implode("\n\n", [
                trans('emails.verification_code_greeting', ['name' => '{{name}}'], locale: $locale),
                trans('emails.verification_code_body', ['code' => '{{code}}'], locale: $locale),
                trans('emails.verification_code_fallback', locale: $locale),
            ]);
        }

        if (! is_array($default)) {
            return [
                'key' => $key,
                'locale' => $locale,
                'subject' => $key,
                'body' => '',
                'variables' => [],
                'used_default' => true,
            ];
        }

        $row = EmailTemplate::query()
            ->where('key', $key)
            ->where('locale', $locale)
            ->first();

        if (! $row && $locale !== 'en') {
            $row = EmailTemplate::query()
                ->where('key', $key)
                ->where('locale', 'en')
                ->first();
        }

        return [
            'key' => $key,
            'locale' => $locale,
            'subject' => $row?->subject ?: $default['subject'],
            'body' => $row?->body ?: $default['body'],
            'variables' => $default['variables'],
            'used_default' => ! $row,
        ];
    }

    /**
     * @param array<string, mixed> $variables
     * @return array{subject: string, body: string, used_default: bool, variables: array<int, string>}
     */
    public function render(string $key, ?string $locale, array $variables): array
    {
        $template = $this->resolveTemplate($key, $locale);

        $subject = $this->interpolate($template['subject'], $variables);
        $body = $this->interpolate($template['body'], $variables);

        return [
            'subject' => $subject,
            'body' => $body,
            'used_default' => $template['used_default'],
            'variables' => $template['variables'],
        ];
    }

    /**
     * @param array<string, mixed> $variables
     */
    public function toMailMessage(
        string $key,
        ?string $locale,
        array $variables,
        ?string $actionLabel = null,
        ?string $actionUrl = null
    ): MailMessage {
        $rendered = $this->render($key, $locale, $variables);

        $message = (new MailMessage)->subject($rendered['subject']);

        $lines = preg_split('/\r\n|\r|\n/', trim($rendered['body'])) ?: [];

        foreach ($lines as $index => $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            if ($index === 0 && str_starts_with($line, 'Hi ')) {
                $message->greeting($line);
                continue;
            }

            $message->line($line);
        }

        if ($actionLabel && $actionUrl) {
            $message->action($actionLabel, $actionUrl);
        }

        return $message;
    }

    /**
     * @param array<string, mixed> $variables
     */
    protected function interpolate(string $text, array $variables): string
    {
        foreach ($variables as $name => $value) {
            $text = str_replace('{{'.$name.'}}', (string) $value, $text);
        }

        return preg_replace('/\{\{\s*[a-zA-Z0-9_]+\s*\}\}/', '', $text) ?: $text;
    }
}
