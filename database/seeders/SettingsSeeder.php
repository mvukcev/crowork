<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // Platform Access
            'coming_soon_enabled' => false,
            'registration_enabled' => true,
            'worker_registration_enabled' => true,
            'employer_registration_enabled' => true,
            'demo_preview_enabled' => false,

            // Approvals
            'job_approval_required' => true,
            'employer_approval_required' => true,
            'education_approval_required' => true,

            // Applications
            'application_visibility_mode' => 'limited',
            'employer_export_allowed' => false,
            'employer_visible_fields' => [
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

            // Jobs Lifecycle
            'default_job_expiry_days' => 30,
            'max_active_jobs_per_employer' => 0,
            'auto_expire_jobs_enabled' => true,

            // Email & SMTP
            'mail_mailer' => 'log',
            'mail_host' => null,
            'mail_port' => 587,
            'mail_username' => null,
            'mail_password' => null,
            'mail_encryption' => 'tls',
            'mail_from_address' => 'noreply@crowork.local',
            'mail_from_name' => 'CroWork',

            // Notifications
            'admin_notification_email' => null,
            'notify_admin_new_employer' => true,
            'notify_admin_new_report' => true,
            'notify_employer_new_application' => true,
            'notify_worker_status_changed' => true,

            // Localization
            'default_platform_locale' => 'en',
            'enabled_locales' => ['en', 'hr'],
            'default_timezone' => 'Europe/Zagreb',
            'default_currency' => 'EUR',

            // Analytics
            'analytics_enabled' => false,
            'google_tag_manager_id' => null,
            'google_tag_id' => null,
            'analytics_debug_mode' => false,

            // Meta Pixel & CAPI
            'meta_tracking_enabled' => false,
            'meta_pixel_id' => null,
            'meta_conversions_api_access_token' => null,
            'meta_test_event_code' => null,
            'meta_dataset_id' => null,
            'meta_api_version' => 'v18.0',
            'meta_debug_mode' => false,

            // Consent & Privacy
            'cookie_banner_enabled' => true,
            'consent_required' => true,
            'cookie_statement_url' => null,

            // Security & Audit
            'audit_log_enabled' => true,
            'audit_log_retention_days' => 90,

            // File Uploads
            'upload_max_file_size_mb' => 10,
            'allowed_upload_extensions' => ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'],

            // Legacy keys kept for backward compatibility
            'jobs_require_approval' => true,
            'educations_require_approval' => true,
            'employer_application_visibility' => 'limited',
            'employer_can_export_applications' => false,

            // Admin Features
            'admin_impersonation_enabled' => true,
            'dark_mode_enabled' => true,
            'legal_pages_managed_from_admin' => true,
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
