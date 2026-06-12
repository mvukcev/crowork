<?php

namespace App\Services;

use App\Models\EmailTemplate;
use Illuminate\Notifications\Messages\MailMessage;

class EmailTemplateService
{
    /**
     * @return array<string, array{label: string, trigger: string, source: string, variables: array<int, string>}>
     */
    public static function catalog(): array
    {
        return [
            'verification_code' => [
                'label' => 'Verification Code',
                'trigger' => 'Access flow after entering new email address',
                'source' => 'App\\Mail\\VerificationCodeMail',
                'variables' => ['name', 'code'],
            ],
            'auth_reset_password' => [
                'label' => 'Auth Reset Password',
                'trigger' => 'Password reset email notification',
                'source' => 'App\\Notifications\\AuthResetPasswordNotification',
                'variables' => ['name', 'reset_url', 'expire_minutes'],
            ],
            'worker_application_confirmation' => [
                'label' => 'Worker Application Confirmation',
                'trigger' => 'Worker submits job application',
                'source' => 'App\\Notifications\\JobApplicationSubmitted',
                'variables' => ['name', 'job_title', 'company_name', 'application_status', 'action_url'],
            ],
            'employer_new_application' => [
                'label' => 'Employer New Application',
                'trigger' => 'Employer receives a new candidate application',
                'source' => 'App\\Notifications\\NewJobApplicationReceived',
                'variables' => ['name', 'worker_name', 'job_title', 'application_status', 'action_url'],
            ],
            'application_status_changed' => [
                'label' => 'Application Status Changed',
                'trigger' => 'Employer updates worker application status',
                'source' => 'App\\Notifications\\JobApplicationStatusChanged',
                'variables' => ['name', 'job_title', 'new_status', 'previous_status', 'company_name', 'action_url'],
            ],
            'employer_account_status' => [
                'label' => 'Employer Approved/Rejected',
                'trigger' => 'Admin approves or rejects employer account',
                'source' => 'App\\Notifications\\EmployerAccountApproved, App\\Notifications\\EmployerAccountRejected',
                'variables' => ['name', 'company_name', 'account_status', 'status_message', 'action_url'],
            ],
            'job_status_changed' => [
                'label' => 'Job Approved/Rejected',
                'trigger' => 'Admin moderates employer job listing status',
                'source' => 'App\\Notifications\\JobStatusChanged',
                'variables' => ['name', 'job_title', 'job_status', 'action_url'],
            ],
            'education_application_confirmation' => [
                'label' => 'Education Application Confirmation',
                'trigger' => 'Worker submits education application',
                'source' => 'App\\Notifications\\EducationApplicationSubmitted',
                'variables' => ['name', 'education_title', 'application_status', 'action_url'],
            ],
            'admin_new_employer_pending' => [
                'label' => 'Admin New Employer Pending',
                'trigger' => 'New employer registration requires moderation',
                'source' => 'App\\Notifications\\AdminNewEmployerPending',
                'variables' => ['name', 'company_name', 'contact_email', 'action_url'],
            ],
            'admin_new_abuse_report' => [
                'label' => 'Admin New Abuse Report',
                'trigger' => 'Worker reports abuse/issue that needs moderation',
                'source' => 'App\\Notifications\\AdminNewAbuseReport',
                'variables' => ['name', 'report_type', 'report_reason', 'report_id', 'action_url'],
            ],
            'admin_new_bug_report' => [
                'label' => 'Admin New Bug Report',
                'trigger' => 'User submits a bug report from frontend bug-report widget',
                'source' => 'App\\Notifications\\AdminNewBugReport',
                'variables' => ['name', 'reporter_email', 'reported_at', 'page_uri', 'error_logs_count', 'description', 'action_url'],
            ],
            'important_system_notice' => [
                'label' => 'Important System Notice',
                'trigger' => 'System raises an important account/platform notice with email delivery enabled',
                'source' => 'App\\Notifications\\ImportantSystemNotice',
                'variables' => ['name', 'title', 'message', 'action_url'],
            ],
        ];
    }

    /**
     * @return array<string, array{label: string, trigger: string, source: string, subject: string, body: string, variables: array<int, string>}>
     */
    public static function definitions(?string $locale = null): array
    {
        $normalizedLocale = strtolower((string) ($locale ?: app()->getLocale()));
        $defaults = self::localizedDefaults($normalizedLocale === 'hr' ? 'hr' : 'en');

        $definitions = [];
        foreach (self::catalog() as $key => $meta) {
            $localized = $defaults[$key] ?? $defaults['verification_code'];

            $definitions[$key] = [
                'label' => $meta['label'],
                'trigger' => $meta['trigger'],
                'source' => $meta['source'],
                'subject' => $localized['subject'],
                'body' => $localized['body'],
                'variables' => $meta['variables'],
            ];
        }

        return $definitions;
    }

    /**
     * @param array<int, string>|null $locales
     * @return array{created: int, updated: int, skipped: int, deleted: int}
     */
    public function syncDefaultTemplates(?array $locales = null, bool $overwrite = false): array
    {
        $locales = $locales ?: ['en', 'hr'];

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $deleted = 0;

        $catalogKeys = array_keys(self::catalog());

        if ($catalogKeys !== []) {
            $deleted = EmailTemplate::query()
                ->whereIn('locale', $locales)
                ->whereNotIn('key', $catalogKeys)
                ->delete();
        }

        foreach ($locales as $locale) {
            $locale = strtolower(trim((string) $locale));
            if ($locale === '') {
                continue;
            }

            $definitions = self::definitions($locale);

            foreach ($definitions as $key => $definition) {
                $row = EmailTemplate::query()
                    ->where('key', $key)
                    ->where('locale', $locale)
                    ->first();

                if (! $row) {
                    EmailTemplate::query()->create([
                        'key' => $key,
                        'locale' => $locale,
                        'subject' => $definition['subject'],
                        'body' => $definition['body'],
                        'variables_preview' => $this->defaultPreviewVariables($definition['variables']),
                    ]);
                    $created++;
                    continue;
                }

                if ($overwrite) {
                    $row->forceFill([
                        'subject' => $definition['subject'],
                        'body' => $definition['body'],
                        'variables_preview' => $row->variables_preview ?: $this->defaultPreviewVariables($definition['variables']),
                    ])->save();
                    $updated++;
                    continue;
                }

                if (empty($row->variables_preview)) {
                    $row->forceFill([
                        'variables_preview' => $this->defaultPreviewVariables($definition['variables']),
                    ])->save();
                    $updated++;
                    continue;
                }

                $skipped++;
            }
        }

        return [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'deleted' => $deleted,
        ];
    }

    /**
     * @return array{key: string, locale: string, subject: string, body: string, variables: array<int, string>, used_default: bool}
     */
    public function resolveTemplate(string $key, ?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();
        $definitions = self::definitions($locale);
        $default = $definitions[$key] ?? null;

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

            if (
                $index === 0
                && preg_match('/^(hi\b|hello\b|dear\b|pozdrav\b)/i', $line) === 1
            ) {
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

    /**
     * @param array<int, string> $variables
     * @return array<string, string>
     */
    private function defaultPreviewVariables(array $variables): array
    {
        $seed = [
            'name' => 'Test User',
            'code' => '123456',
            'job_title' => 'Warehouse Associate',
            'company_name' => 'Example Company',
            'application_status' => 'Submitted',
            'worker_name' => 'Worker Name',
            'new_status' => 'Interview',
            'previous_status' => 'Applied',
            'account_status' => 'Approved',
            'status_message' => 'Your account is now active.',
            'job_status' => 'approved and visible to workers',
            'education_title' => 'Hospitality Training Program',
            'contact_email' => 'hr@example.com',
            'report_type' => 'JOB',
            'report_reason' => 'Spam content',
            'report_id' => '42',
            'reporter_email' => 'reporter@example.com',
            'reported_at' => '2026-06-12 12:34:56',
            'page_uri' => 'https://example.com/jobs',
            'error_logs_count' => '5',
            'description' => 'Clicking retry on failed jobs showed a 404 page.',
            'reset_url' => 'https://example.com/reset-password',
            'expire_minutes' => '60',
            'action_url' => 'https://example.com/dashboard',
        ];

        $preview = [];
        foreach ($variables as $variable) {
            $preview[$variable] = $seed[$variable] ?? 'sample';
        }

        return $preview;
    }

    /**
     * @return array<string, array{subject: string, body: string}>
     */
    private static function localizedDefaults(string $locale): array
    {
        if ($locale === 'hr') {
            return [
                'verification_code' => [
                    'subject' => 'Vas verifikacijski kod za CroWork',
                    'body' => "Pozdrav,\n\nVas verifikacijski kod glasi: {{code}}\n\nKod vrijedi 10 minuta. Ako niste zatrazili ovaj kod, slobodno zanemarite ovu poruku.",
                ],
                'auth_reset_password' => [
                    'subject' => 'Zahtjev za reset lozinke',
                    'body' => "Pozdrav {{name}},\n\nPrimili smo zahtjev za reset lozinke na vasem racunu.\nPoveznica vrijedi {{expire_minutes}} minuta.\n\nAko niste vi zatrazili reset, slobodno zanemarite ovu poruku.",
                ],
                'worker_application_confirmation' => [
                    'subject' => 'Prijava zaprimljena: {{job_title}}',
                    'body' => "Pozdrav {{name}},\n\nVasa prijava za poziciju {{job_title}} kod poslodavca {{company_name}} je uspjesno zaprimljena.\nTrenutni status: {{application_status}}.\n\nPrijavu mozete pratiti u svom CroWork korisnickom racunu.",
                ],
                'employer_new_application' => [
                    'subject' => 'Nova prijava: {{job_title}}',
                    'body' => "Pozdrav {{name}},\n\nKandidat {{worker_name}} poslao je novu prijavu za poziciju {{job_title}}.\nTrenutni status: {{application_status}}.\n\nPrijavite se u CroWork kako biste pregledali profil kandidata i poruku.",
                ],
                'application_status_changed' => [
                    'subject' => 'CroWork obavijest: promjena statusa prijave',
                    'body' => "Pozdrav {{name}},\n\nStatus vase prijave za poziciju {{job_title}} je promijenjen u: {{new_status}}.\nPoslodavac: {{company_name}}.\n\nOvo je automatizirana obavijest sustava CroWork.",
                ],
                'employer_account_status' => [
                    'subject' => 'CroWork: promjena statusa employer racuna',
                    'body' => "Pozdrav {{name}},\n\nStatus vaseg employer racuna za tvrtku {{company_name}} je: {{account_status}}.\n{{status_message}}",
                ],
                'job_status_changed' => [
                    'subject' => 'CroWork: promjena statusa oglasa',
                    'body' => "Pozdrav {{name}},\n\nStatus vaseg oglasa \"{{job_title}}\" je azuriran: {{job_status}}.\n\nOtvorite CroWork za detalje.",
                ],
                'education_application_confirmation' => [
                    'subject' => 'CroWork: prijava na edukaciju zaprimljena',
                    'body' => "Pozdrav {{name}},\n\nVasa prijava za edukaciju \"{{education_title}}\" je zaprimljena.\nTrenutni status: {{application_status}}.\n\nHvala sto koristite CroWork.",
                ],
                'admin_new_employer_pending' => [
                    'subject' => 'CroWork admin: novi employer ceka odobrenje',
                    'body' => "Pozdrav {{name}},\n\nNovi employer racun ceka administratorski pregled.\nTvrtka: {{company_name}}\nKontakt e-mail: {{contact_email}}",
                ],
                'admin_new_abuse_report' => [
                    'subject' => 'CroWork admin: nova abuse prijava',
                    'body' => "Pozdrav {{name}},\n\nZaprimljena je nova abuse prijava.\nTip: {{report_type}}\nRazlog: {{report_reason}}\nID prijave: {{report_id}}",
                ],
                'admin_new_bug_report' => [
                    'subject' => 'CroWork admin: nova bug prijava',
                    'body' => "Pozdrav {{name}},\n\nZaprimljena je nova bug prijava iz beta sucelja.\nVrijeme: {{reported_at}}\nStranica: {{page_uri}}\nPrijavitelj: {{reporter_email}}\nBroj logova (20 min): {{error_logs_count}}\n\nOpis:\n{{description}}",
                ],
                'important_system_notice' => [
                    'subject' => '{{title}}',
                    'body' => "Pozdrav {{name}},\n\n{{message}}",
                ],
            ];
        }

        return [
            'verification_code' => [
                'subject' => 'Your CroWork verification code',
                'body' => "Hi {{name}},\n\nYour verification code is: {{code}}\n\nThis code expires soon. If you did not request this, ignore this email.",
            ],
            'auth_reset_password' => [
                'subject' => 'Reset password notification',
                'body' => "Hi {{name}},\n\nYou are receiving this email because we received a password reset request for your account.\nThis reset link will expire in {{expire_minutes}} minutes.\n\nIf you did not request a password reset, no further action is required.",
            ],
            'worker_application_confirmation' => [
                'subject' => 'Application received: {{job_title}}',
                'body' => "Hi {{name}},\n\nWe received your application for {{job_title}} at {{company_name}}.\nCurrent status: {{application_status}}.\n\nYou can review your applications in your CroWork account.",
            ],
            'employer_new_application' => [
                'subject' => 'New application: {{job_title}}',
                'body' => "Hi {{name}},\n\n{{worker_name}} submitted a new application for {{job_title}}.\nCurrent status: {{application_status}}.\n\nSign in to CroWork to review the candidate profile snapshot and message.",
            ],
            'application_status_changed' => [
                'subject' => 'CroWork update: application status changed',
                'body' => "Hi {{name}},\n\nYour application for {{job_title}} has a new status: {{new_status}}.\nEmployer: {{company_name}}.\n\nThis is an automated update from CroWork.",
            ],
            'employer_account_status' => [
                'subject' => 'CroWork: employer account update',
                'body' => "Hi {{name}},\n\nYour employer account for {{company_name}} is now {{account_status}}.\n{{status_message}}",
            ],
            'job_status_changed' => [
                'subject' => 'CroWork: job status updated',
                'body' => "Hi {{name}},\n\nYour job listing \"{{job_title}}\" is now {{job_status}}.\n\nOpen CroWork to review details.",
            ],
            'education_application_confirmation' => [
                'subject' => 'CroWork: education application received',
                'body' => "Hi {{name}},\n\nWe received your application for \"{{education_title}}\".\nCurrent status: {{application_status}}.\n\nThanks for applying through CroWork.",
            ],
            'admin_new_employer_pending' => [
                'subject' => 'CroWork admin: new employer pending approval',
                'body' => "Hi {{name}},\n\nA new employer account is pending review.\nCompany: {{company_name}}\nContact email: {{contact_email}}",
            ],
            'admin_new_abuse_report' => [
                'subject' => 'CroWork admin: new abuse report',
                'body' => "Hi {{name}},\n\nA new abuse report was submitted.\nType: {{report_type}}\nReason: {{report_reason}}\nReport ID: {{report_id}}",
            ],
            'admin_new_bug_report' => [
                'subject' => 'CroWork admin: new bug report',
                'body' => "Hi {{name}},\n\nA new bug report was submitted from the beta interface.\nReported at: {{reported_at}}\nPage: {{page_uri}}\nReporter: {{reporter_email}}\nCaptured logs (20 min): {{error_logs_count}}\n\nDescription:\n{{description}}",
            ],
            'important_system_notice' => [
                'subject' => '{{title}}',
                'body' => "Hi {{name}},\n\n{{message}}",
            ],
        ];
    }
}
