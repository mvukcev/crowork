<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
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