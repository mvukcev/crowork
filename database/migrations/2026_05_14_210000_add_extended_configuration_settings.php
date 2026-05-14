<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $defaults = [
            // Email & SMTP
            'mail_mailer' => 'log',
            'mail_host' => null,
            'mail_port' => 587,
            'mail_username' => null,
            'mail_password' => null,
            'mail_encryption' => 'tls',
            'mail_from_address' => 'noreply@crowork.local',
            'mail_from_name' => 'CroWork',

            // Additional notifications
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

            // Additional settings
            'demo_preview_enabled' => false,
            'max_active_jobs_per_employer' => 0,
        ];

        foreach ($defaults as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                [
                    'value' => json_encode($value),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        DB::table('settings')->whereIn('key', [
            'mail_mailer',
            'mail_host',
            'mail_port',
            'mail_username',
            'mail_password',
            'mail_encryption',
            'mail_from_address',
            'mail_from_name',
            'notify_admin_new_employer',
            'notify_admin_new_report',
            'notify_employer_new_application',
            'notify_worker_status_changed',
            'default_platform_locale',
            'enabled_locales',
            'default_timezone',
            'default_currency',
            'analytics_enabled',
            'google_tag_manager_id',
            'google_tag_id',
            'analytics_debug_mode',
            'meta_tracking_enabled',
            'meta_pixel_id',
            'meta_conversions_api_access_token',
            'meta_test_event_code',
            'meta_dataset_id',
            'meta_api_version',
            'meta_debug_mode',
            'cookie_banner_enabled',
            'consent_required',
            'cookie_statement_url',
            'audit_log_enabled',
            'audit_log_retention_days',
            'upload_max_file_size_mb',
            'allowed_upload_extensions',
            'demo_preview_enabled',
            'max_active_jobs_per_employer',
        ])->delete();
    }
};
