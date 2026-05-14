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
            'coming_soon_enabled' => false,
            'registration_enabled' => true,
            'worker_registration_enabled' => true,
            'employer_registration_enabled' => true,
            'job_approval_required' => true,
            'employer_approval_required' => true,
            'education_approval_required' => true,
            'application_visibility_mode' => 'limited',
            'employer_export_allowed' => false,
            'default_job_expiry_days' => 30,
            'auto_expire_jobs_enabled' => true,
            'admin_notification_email' => null,
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
            'coming_soon_enabled',
            'registration_enabled',
            'worker_registration_enabled',
            'employer_registration_enabled',
            'job_approval_required',
            'employer_approval_required',
            'education_approval_required',
            'application_visibility_mode',
            'employer_export_allowed',
            'default_job_expiry_days',
            'auto_expire_jobs_enabled',
            'admin_notification_email',
        ])->delete();
    }
};
