<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // New cockpit keys
            'coming_soon_enabled' => false,
            'registration_enabled' => true,
            'worker_registration_enabled' => true,
            'employer_registration_enabled' => true,
            'job_approval_required' => true,
            'employer_approval_required' => true,
            'education_approval_required' => true,
            'application_visibility_mode' => 'limited', // full|limited|anonymous
            'employer_export_allowed' => false,
            'default_job_expiry_days' => 30,
            'auto_expire_jobs_enabled' => true,
            'admin_notification_email' => null,

            // Legacy keys kept for backward compatibility
            'jobs_require_approval' => true,
            'educations_require_approval' => true,
            'employer_application_visibility' => 'limited', // full|limited|anonymous
            'employer_can_export_applications' => false,
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
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}