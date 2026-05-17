<?php

namespace Database\Seeders;

use App\Models\Employer;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use App\Models\WorkerProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class CroworkDemoCvSeeder extends Seeder
{
    public function run(): void
    {
        $forced = (bool) env('CROWORK_DEMO_SEED_FORCE', false);

        if (! app()->environment(['local', 'testing']) && ! $forced) {
            $this->command?->warn('CroworkDemoCvSeeder skipped outside local/testing. Set CROWORK_DEMO_SEED_FORCE=true to force.');

            return;
        }

        DB::transaction(function (): void {
            $worker = User::updateOrCreate(
                ['email' => 'demo.worker@crowork.local'],
                [
                    'name' => 'Demo Worker',
                    'password' => Hash::make('password'),
                    'role' => User::ROLE_WORKER,
                    'email_verified_at' => now(),
                    'communication_language' => 'en',
                ]
            );

            $profile = WorkerProfile::updateOrCreate(
                ['user_id' => $worker->id],
                [
                    'first_name' => 'Demo',
                    'last_name' => 'Worker',
                    'nationality_country_code' => 'HR',
                    'birth_year' => 1996,
                    'current_country' => 'Croatia',
                    'current_city' => 'Zagreb',
                    'desired_city' => 'Split',
                    'availability_date' => now()->addWeeks(2)->toDateString(),
                    'professional_summary' => 'Reliable hospitality worker with 5+ years of seasonal and hotel operations experience.',
                    'salary_expectation' => 1500,
                    'accommodation_needed' => true,
                    'visa_work_permit_status' => 'EU citizen',
                    'skills' => ['Housekeeping', 'Guest relations', 'Food prep'],
                    'languages' => [
                        ['language' => 'Croatian', 'level' => 'C2'],
                        ['language' => 'English', 'level' => 'B2'],
                    ],
                    'desired_roles' => ['Housekeeper', 'Kitchen assistant'],
                    'profile_visibility' => WorkerProfile::VISIBILITY_EMPLOYERS,
                    'communication_language' => 'en',
                ]
            );

            if (Schema::hasTable('worker_educations')) {
                DB::table('worker_educations')->where('worker_profile_id', $profile->id)->delete();
                DB::table('worker_educations')->insert([
                    [
                        'worker_profile_id' => $profile->id,
                        'institution' => 'Hospitality School Zagreb',
                        'degree' => 'Vocational diploma',
                        'field_of_study' => 'Hospitality services',
                        'country' => 'Croatia',
                        'city' => 'Zagreb',
                        'start_date' => '2012-09-01',
                        'end_date' => '2016-06-30',
                        'description' => 'Hospitality operations and guest service fundamentals.',
                        'sort_order' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'worker_profile_id' => $profile->id,
                        'institution' => 'Adriatic Food Safety Center',
                        'degree' => 'Certificate',
                        'field_of_study' => 'Food safety and hygiene',
                        'country' => 'Croatia',
                        'city' => 'Split',
                        'start_date' => '2018-03-01',
                        'end_date' => '2018-05-15',
                        'description' => 'HACCP and kitchen safety compliance.',
                        'sort_order' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'worker_profile_id' => $profile->id,
                        'institution' => 'Tourism Academy Rijeka',
                        'degree' => 'Short program',
                        'field_of_study' => 'Customer communication',
                        'country' => 'Croatia',
                        'city' => 'Rijeka',
                        'start_date' => '2021-01-10',
                        'end_date' => '2021-02-10',
                        'description' => 'Conflict resolution and guest communication.',
                        'sort_order' => 2,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ]);
            }

            if (Schema::hasTable('worker_experiences')) {
                DB::table('worker_experiences')->where('worker_profile_id', $profile->id)->delete();
                DB::table('worker_experiences')->insert([
                    [
                        'worker_profile_id' => $profile->id,
                        'job_title' => 'Senior Housekeeper',
                        'company_name' => 'Blue Coast Resort',
                        'country' => 'Croatia',
                        'city' => 'Makarska',
                        'start_date' => '2022-04-01',
                        'end_date' => null,
                        'is_current' => true,
                        'description' => 'Team lead for floor operations and room readiness.',
                        'sort_order' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ]);
            }

            if (Schema::hasTable('worker_skills')) {
                DB::table('worker_skills')->where('worker_profile_id', $profile->id)->delete();
                DB::table('worker_skills')->insert([
                    ['worker_profile_id' => $profile->id, 'name' => 'Housekeeping', 'level' => 'Advanced', 'sort_order' => 0, 'created_at' => now(), 'updated_at' => now()],
                    ['worker_profile_id' => $profile->id, 'name' => 'Food prep', 'level' => 'Intermediate', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                    ['worker_profile_id' => $profile->id, 'name' => 'Guest communication', 'level' => 'Advanced', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ]);
            }

            if (Schema::hasTable('worker_languages')) {
                DB::table('worker_languages')->where('worker_profile_id', $profile->id)->delete();
                DB::table('worker_languages')->insert([
                    ['worker_profile_id' => $profile->id, 'language' => 'Croatian', 'level' => 'C2', 'sort_order' => 0, 'created_at' => now(), 'updated_at' => now()],
                    ['worker_profile_id' => $profile->id, 'language' => 'English', 'level' => 'B2', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ]);
            }

            $employerUser = User::updateOrCreate(
                ['email' => 'demo.employer@crowork.local'],
                [
                    'name' => 'Demo Employer',
                    'password' => Hash::make('password'),
                    'role' => User::ROLE_EMPLOYER,
                    'email_verified_at' => now(),
                    'communication_language' => 'en',
                ]
            );

            $employer = Employer::updateOrCreate(
                ['user_id' => $employerUser->id],
                [
                    'approved_at' => now(),
                    'company_name' => 'Adriatic Hospitality Group',
                    'company_display_name' => 'Adriatic Hospitality Group',
                    'city' => 'Split',
                    'country' => 'Croatia',
                    'industry' => 'Hospitality',
                    'website' => 'https://example.com',
                    'contact_email' => 'hiring@example.com',
                    'contact_phone' => '+38591111222',
                    'company_address' => 'Riva 1, Split',
                    'description' => 'Seasonal hospitality and hotel operations employer.',
                    'accommodation_support' => true,
                    'relocation_support' => true,
                    'communication_language' => 'en',
                ]
            );

            $job = Job::updateOrCreate(
                ['slug' => 'demo-seasonal-housekeeper-split'],
                [
                    'employer_id' => $employer->id,
                    'created_by_user_id' => $employerUser->id,
                    'title' => 'Seasonal Housekeeper',
                    'salary_min' => 1300,
                    'salary_max' => 1700,
                    'salary_currency' => 'EUR',
                    'salary_period' => 'month',
                    'description' => 'Join our summer team and support daily room turnover and guest readiness.',
                    'location_city' => 'Split',
                    'category' => 'Hospitality',
                    'languages' => ['Croatian', 'English'],
                    'accommodation_provided' => true,
                    'accommodation_details' => 'Shared company apartment near the hotel.',
                    'contract_type' => 'seasonal',
                    'start_date' => now()->addMonth()->toDateString(),
                    'status' => 'published',
                    'published_at' => now(),
                    'expires_at' => now()->addMonths(2),
                ]
            );

            JobApplication::updateOrCreate(
                [
                    'job_id' => $job->id,
                    'worker_id' => $worker->id,
                ],
                [
                    'profile_snapshot' => $profile->fresh()->toSnapshot(),
                    'job_snapshot' => [
                        'title' => $job->title,
                        'location_city' => $job->location_city,
                        'salary_min' => $job->salary_min,
                        'salary_max' => $job->salary_max,
                        'salary_currency' => $job->salary_currency,
                        'salary_period' => $job->salary_period,
                    ],
                    'message' => 'Available from next month and open to relocation within Croatia.',
                    'status' => JobApplication::STATUS_NEW,
                    'status_updated_at' => now(),
                ]
            );
        });

        $this->command?->info('CroworkDemoCvSeeder completed: worker profile, structured CV records, employer, job, and application are ready.');
    }
}
