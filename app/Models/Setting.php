<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public const DEFINITIONS = [
        // Platform Access
        'coming_soon_enabled' => [
            'label' => 'Coming Soon Enabled',
            'group' => 'Platform Access',
            'type' => 'boolean',
            'default' => false,
        ],
        'registration_enabled' => [
            'label' => 'Registration Enabled',
            'group' => 'Platform Access',
            'type' => 'boolean',
            'default' => true,
        ],
        'worker_registration_enabled' => [
            'label' => 'Worker Registration Enabled',
            'group' => 'Platform Access',
            'type' => 'boolean',
            'default' => true,
        ],
        'employer_registration_enabled' => [
            'label' => 'Employer Registration Enabled',
            'group' => 'Platform Access',
            'type' => 'boolean',
            'default' => true,
        ],
        'demo_preview_enabled' => [
            'label' => 'Demo Preview Enabled',
            'group' => 'Platform Access',
            'type' => 'boolean',
            'default' => false,
        ],

        // Approvals
        'job_approval_required' => [
            'label' => 'Job Approval Required',
            'group' => 'Approvals',
            'type' => 'boolean',
            'default' => true,
        ],
        'employer_approval_required' => [
            'label' => 'Employer Approval Required',
            'group' => 'Approvals',
            'type' => 'boolean',
            'default' => true,
        ],
        'education_approval_required' => [
            'label' => 'Education Approval Required',
            'group' => 'Approvals',
            'type' => 'boolean',
            'default' => true,
        ],

        // Applications
        'application_visibility_mode' => [
            'label' => 'Application Visibility Mode',
            'group' => 'Applications',
            'type' => 'select',
            'default' => 'limited',
            'options' => [
                'full' => 'Full',
                'limited' => 'Limited',
                'anonymous' => 'Anonymous',
            ],
        ],
        'employer_export_allowed' => [
            'label' => 'Employer Export Allowed',
            'group' => 'Applications',
            'type' => 'boolean',
            'default' => false,
        ],
        'employer_visible_fields' => [
            'label' => 'Employer Visible Fields (Limited Mode)',
            'group' => 'Applications',
            'type' => 'array',
            'default' => [
                'first_name',
                'last_name',
                'nationality_country_code',
                'birth_year',
                'education_summary',
                'work_experience',
                'skills',
                'recommendations',
                'photo_path',
            ],
        ],

        // Jobs Lifecycle
        'default_job_expiry_days' => [
            'label' => 'Default Job Expiry Days',
            'group' => 'Jobs Lifecycle',
            'type' => 'integer',
            'default' => 30,
        ],
        'max_active_jobs_per_employer' => [
            'label' => 'Max Active Jobs Per Employer',
            'group' => 'Jobs Lifecycle',
            'type' => 'integer',
            'default' => 0, // 0 = unlimited
        ],
        'auto_expire_jobs_enabled' => [
            'label' => 'Auto-expire Jobs Enabled',
            'group' => 'Jobs Lifecycle',
            'type' => 'boolean',
            'default' => true,
        ],

        // Email & SMTP
        'mail_mailer' => [
            'label' => 'Mail Driver',
            'group' => 'Email & SMTP',
            'type' => 'select',
            'default' => 'smtp',
            'options' => [
                'smtp' => 'SMTP',
                'log' => 'Log (Development)',
                'mailgun' => 'Mailgun',
                'postmark' => 'Postmark',
                'sendmail' => 'Sendmail',
            ],
        ],
        'mail_host' => [
            'label' => 'Mail Host',
            'group' => 'Email & SMTP',
            'type' => 'text',
            'default' => null,
        ],
        'mail_port' => [
            'label' => 'Mail Port',
            'group' => 'Email & SMTP',
            'type' => 'integer',
            'default' => 587,
        ],
        'mail_username' => [
            'label' => 'Mail Username',
            'group' => 'Email & SMTP',
            'type' => 'text',
            'default' => null,
        ],
        'mail_password' => [
            'label' => 'Mail Password',
            'group' => 'Email & SMTP',
            'type' => 'password',
            'default' => null,
            'secret' => true,
        ],
        'mail_encryption' => [
            'label' => 'Mail Encryption',
            'group' => 'Email & SMTP',
            'type' => 'select',
            'default' => 'tls',
            'options' => [
                'tls' => 'TLS',
                'ssl' => 'SSL',
                'null' => 'None',
            ],
        ],
        'mail_from_address' => [
            'label' => 'Mail From Address',
            'group' => 'Email & SMTP',
            'type' => 'email',
            'default' => 'noreply@crowork.local',
        ],
        'mail_from_name' => [
            'label' => 'Mail From Name',
            'group' => 'Email & SMTP',
            'type' => 'text',
            'default' => 'CroWork',
        ],

        // Notifications
        'admin_notification_email' => [
            'label' => 'Admin Notification Email',
            'group' => 'Notifications',
            'type' => 'email',
            'default' => null,
        ],
        'notify_admin_new_employer' => [
            'label' => 'Notify Admin on New Employer Registration',
            'group' => 'Notifications',
            'type' => 'boolean',
            'default' => true,
        ],
        'notify_admin_new_report' => [
            'label' => 'Notify Admin on Abuse Report',
            'group' => 'Notifications',
            'type' => 'boolean',
            'default' => true,
        ],
        'notify_employer_new_application' => [
            'label' => 'Notify Employer on New Application',
            'group' => 'Notifications',
            'type' => 'boolean',
            'default' => true,
        ],
        'notify_worker_status_changed' => [
            'label' => 'Notify Worker on Application Status Change',
            'group' => 'Notifications',
            'type' => 'boolean',
            'default' => true,
        ],

        // Localization
        'default_platform_locale' => [
            'label' => 'Default Platform Locale',
            'group' => 'Localization',
            'type' => 'select',
            'default' => 'en',
            'options' => [
                'en' => 'English',
                'hr' => 'Croatian',
                'de' => 'German',
            ],
        ],
        'enabled_locales' => [
            'label' => 'Enabled Locales',
            'group' => 'Localization',
            'type' => 'array',
            'default' => ['en', 'hr'],
        ],
        'default_timezone' => [
            'label' => 'Default Timezone',
            'group' => 'Localization',
            'type' => 'text',
            'default' => 'Europe/Zagreb',
        ],
        'default_currency' => [
            'label' => 'Default Currency',
            'group' => 'Localization',
            'type' => 'select',
            'default' => 'EUR',
            'options' => [
                'EUR' => 'EUR (€)',
                'USD' => 'USD ($)',
                'GBP' => 'GBP (£)',
            ],
        ],

        // Analytics & Tracking
        'analytics_enabled' => [
            'label' => 'Analytics Enabled',
            'group' => 'Analytics',
            'type' => 'boolean',
            'default' => false,
        ],
        'google_tag_manager_id' => [
            'label' => 'Google Tag Manager ID (GTM-XXXXXXX)',
            'group' => 'Analytics',
            'type' => 'text',
            'default' => null,
        ],
        'google_tag_id' => [
            'label' => 'Google Analytics 4 Measurement ID (G-XXXXXXXXXX)',
            'group' => 'Analytics',
            'type' => 'text',
            'default' => null,
        ],
        'google_search_console_verification' => [
            'label' => 'Google Search Console Verification Token',
            'group' => 'Analytics',
            'type' => 'text',
            'default' => null,
        ],
        'analytics_debug_mode' => [
            'label' => 'Analytics Debug Mode',
            'group' => 'Analytics',
            'type' => 'boolean',
            'default' => false,
        ],

        // Meta Pixel & CAPI
        'meta_tracking_enabled' => [
            'label' => 'Meta Tracking Enabled',
            'group' => 'Meta Pixel & CAPI',
            'type' => 'boolean',
            'default' => false,
        ],
        'meta_pixel_id' => [
            'label' => 'Meta Pixel ID',
            'group' => 'Meta Pixel & CAPI',
            'type' => 'text',
            'default' => null,
        ],
        'meta_conversions_api_access_token' => [
            'label' => 'Meta Conversions API Access Token',
            'group' => 'Meta Pixel & CAPI',
            'type' => 'password',
            'default' => null,
            'secret' => true,
        ],
        'meta_test_event_code' => [
            'label' => 'Meta Test Event Code',
            'group' => 'Meta Pixel & CAPI',
            'type' => 'text',
            'default' => null,
        ],
        'meta_dataset_id' => [
            'label' => 'Meta Dataset ID',
            'group' => 'Meta Pixel & CAPI',
            'type' => 'text',
            'default' => null,
        ],
        'meta_api_version' => [
            'label' => 'Meta API Version',
            'group' => 'Meta Pixel & CAPI',
            'type' => 'text',
            'default' => 'v18.0',
        ],
        'meta_debug_mode' => [
            'label' => 'Meta Debug Mode',
            'group' => 'Meta Pixel & CAPI',
            'type' => 'boolean',
            'default' => false,
        ],
        'meta_browser_enabled' => [
            'label' => 'Meta Browser Pixel Enabled',
            'group' => 'Meta Pixel & CAPI',
            'type' => 'boolean',
            'default' => true,
        ],
        'meta_capi_enabled' => [
            'label' => 'Meta Conversions API Enabled',
            'group' => 'Meta Pixel & CAPI',
            'type' => 'boolean',
            'default' => true,
        ],
        'meta_timeout_seconds' => [
            'label' => 'Meta API Timeout (Seconds)',
            'group' => 'Meta Pixel & CAPI',
            'type' => 'integer',
            'default' => 10,
        ],
        'meta_queue' => [
            'label' => 'Meta Queue Name',
            'group' => 'Meta Pixel & CAPI',
            'type' => 'text',
            'default' => 'default',
        ],
        'meta_log_channel' => [
            'label' => 'Meta Log Channel',
            'group' => 'Meta Pixel & CAPI',
            'type' => 'text',
            'default' => 'meta',
        ],
        'meta_send_from_local' => [
            'label' => 'Allow Meta CAPI from Local Environment',
            'group' => 'Meta Pixel & CAPI',
            'type' => 'boolean',
            'default' => false,
        ],

        // AWS
        'aws_access_key_id' => [
            'label' => 'AWS Access Key ID',
            'group' => 'AWS',
            'type' => 'text',
            'default' => null,
        ],
        'aws_secret_access_key' => [
            'label' => 'AWS Secret Access Key',
            'group' => 'AWS',
            'type' => 'password',
            'default' => null,
            'secret' => true,
        ],
        'aws_default_region' => [
            'label' => 'AWS Default Region',
            'group' => 'AWS',
            'type' => 'text',
            'default' => 'us-east-1',
        ],
        'aws_bucket' => [
            'label' => 'AWS Bucket',
            'group' => 'AWS',
            'type' => 'text',
            'default' => null,
        ],
        'aws_url' => [
            'label' => 'AWS URL',
            'group' => 'AWS',
            'type' => 'text',
            'default' => null,
        ],
        'aws_endpoint' => [
            'label' => 'AWS Endpoint',
            'group' => 'AWS',
            'type' => 'text',
            'default' => null,
        ],
        'aws_use_path_style_endpoint' => [
            'label' => 'AWS Use Path Style Endpoint',
            'group' => 'AWS',
            'type' => 'boolean',
            'default' => false,
        ],

        // Consent & Privacy
        'cookie_banner_enabled' => [
            'label' => 'Cookie Banner Enabled',
            'group' => 'Consent & Privacy',
            'type' => 'boolean',
            'default' => true,
        ],
        'consent_required' => [
            'label' => 'Require Consent for Analytics & Marketing',
            'group' => 'Consent & Privacy',
            'type' => 'boolean',
            'default' => true,
        ],
        'cookie_statement_url' => [
            'label' => 'Cookie Statement URL',
            'group' => 'Consent & Privacy',
            'type' => 'text',
            'default' => null,
        ],
        'terms_version' => [
            'label' => 'Current Terms Version',
            'group' => 'Consent & Privacy',
            'type' => 'text',
            'default' => '2026-05-terms-v1',
        ],
        'terms_hash' => [
            'label' => 'Current Terms Version Hash',
            'group' => 'Consent & Privacy',
            'type' => 'text',
            'default' => null,
        ],
        'privacy_policy_version' => [
            'label' => 'Current Privacy Policy Version',
            'group' => 'Consent & Privacy',
            'type' => 'text',
            'default' => '2026-05-privacy-v1',
        ],
        'privacy_policy_hash' => [
            'label' => 'Current Privacy Policy Version Hash',
            'group' => 'Consent & Privacy',
            'type' => 'text',
            'default' => null,
        ],

        // Privacy Retention
        'enable_retention_automation' => [
            'label' => 'Enable Retention Automation',
            'group' => 'Privacy Retention',
            'type' => 'boolean',
            'default' => false,
        ],
        'dry_run_mode' => [
            'label' => 'Retention Dry-Run Mode',
            'group' => 'Privacy Retention',
            'type' => 'boolean',
            'default' => true,
        ],
        'rejected_applications_retention_months' => [
            'label' => 'Rejected Applications Retention (Months)',
            'group' => 'Privacy Retention',
            'type' => 'integer',
            'default' => 6,
        ],
        'inactive_worker_retention_months' => [
            'label' => 'Inactive Worker Retention (Months)',
            'group' => 'Privacy Retention',
            'type' => 'integer',
            'default' => 24,
        ],
        'inactive_employer_retention_months' => [
            'label' => 'Inactive Employer Retention (Months)',
            'group' => 'Privacy Retention',
            'type' => 'integer',
            'default' => 36,
        ],
        'notification_retention_months' => [
            'label' => 'Notification Retention (Months)',
            'group' => 'Privacy Retention',
            'type' => 'integer',
            'default' => 12,
        ],

        // Security & Audit
        'audit_log_enabled' => [
            'label' => 'Audit Log Enabled',
            'group' => 'Security & Audit',
            'type' => 'boolean',
            'default' => true,
        ],
        'audit_log_retention_days' => [
            'label' => 'Audit Log Retention Days',
            'group' => 'Security & Audit',
            'type' => 'integer',
            'default' => 90,
        ],

        // File Uploads
        'upload_max_file_size_mb' => [
            'label' => 'Max Upload File Size (MB)',
            'group' => 'File Uploads',
            'type' => 'integer',
            'default' => 10,
        ],
        'allowed_upload_extensions' => [
            'label' => 'Allowed Upload Extensions',
            'group' => 'File Uploads',
            'type' => 'array',
            'default' => ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'],
        ],

        // Admin Features
        'admin_impersonation_enabled' => [
            'label' => 'Admin Impersonation Enabled',
            'group' => 'Admin Features',
            'type' => 'boolean',
            'default' => true,
        ],
        'dark_mode_enabled' => [
            'label' => 'Dark Mode Enabled',
            'group' => 'Admin Features',
            'type' => 'boolean',
            'default' => true,
        ],
        'legal_pages_managed_from_admin' => [
            'label' => 'Manage Legal Pages from Admin',
            'group' => 'Admin Features',
            'type' => 'boolean',
            'default' => true,
        ],
    ];

    protected $fillable = ['key', 'value'];

    protected $casts = [
        'value' => 'array',
    ];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $record = static::query()->where('key', $key)->first();

        if (! $record) {
            return $default;
        }

        return static::unwrapValue($record->value, $default);
    }

    public static function getBool(string $key, bool $default = false): bool
    {
        $value = static::getValue($key, $default);

        if (is_bool($value)) {
            return $value;
        }

        return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? $default;
    }

    public static function getInt(string $key, int $default = 0): int
    {
        $value = static::getValue($key, $default);
        return is_numeric($value) ? (int) $value : $default;
    }

    public static function getString(string $key, ?string $default = null): ?string
    {
        $value = static::getValue($key, $default);
        return is_string($value) ? $value : $default;
    }

    public static function getArray(string $key, array $default = []): array
    {
        $value = static::getValue($key, $default);
        return is_array($value) ? $value : $default;
    }

    public static function setValue(string $key, mixed $value): self
    {
        return static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );
    }

    public static function definition(string $key): ?array
    {
        return static::DEFINITIONS[$key] ?? null;
    }

    public static function adminManagedKeys(): array
    {
        return array_keys(static::DEFINITIONS);
    }

    public static function defaultFor(string $key): mixed
    {
        return static::definition($key)['default'] ?? null;
    }

    public static function unwrapValue(mixed $value, mixed $default = null): mixed
    {
        if ($value === null) {
            return $default;
        }

        // Legacy values may be stored as {"value": ...}.
        if (is_array($value) && array_key_exists('value', $value) && count($value) === 1) {
            return $value['value'];
        }

        return $value;
    }
}