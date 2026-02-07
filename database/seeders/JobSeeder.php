<?php

namespace Database\Seeders;

use App\Models\Job;
use App\Models\Employer;
use App\Models\User;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create an employer
        $employer = Employer::with('user')->first();
        
        if (!$employer) {
            $employerUser = User::where('role', User::ROLE_EMPLOYER)->first();
            if ($employerUser && $employerUser->employer) {
                $employer = $employerUser->employer;
            }
        }

        if (!$employer) {
            $this->command->warn('No employer found. Please run UserRoleSeeder first.');
            return;
        }

        $jobs = [
            [
                'title' => 'Senior Full-Stack Developer',
                'employer_id' => $employer->id,
                'created_by_user_id' => $employer->user_id,
                'description' => 'We are seeking an experienced full-stack developer to join our growing team in Zagreb. You will work on exciting projects using modern technologies including Laravel, Vue.js, and React.',
                'location_city' => 'Zagreb',
                'category' => 'IT & Software',
                'salary_min' => 3500,
                'salary_max' => 5500,
                'salary_currency' => 'EUR',
                'salary_period' => 'month',
                'languages' => ['EN', 'HR'],
                'accommodation_provided' => false,
                'contract_type' => 'full-time',
                'status' => 'published',
                'published_at' => now()->subDays(2),
            ],
            [
                'title' => 'Hotel Manager',
                'employer_id' => $employer->id,
                'created_by_user_id' => $employer->user_id,
                'description' => 'Luxury hotel on the Adriatic coast seeks experienced hotel manager. Accommodation and meals included. Perfect for international professionals.',
                'location_city' => 'Split',
                'category' => 'Hospitality',
                'salary_min' => 2500,
                'salary_max' => 4000,
                'salary_currency' => 'EUR',
                'salary_period' => 'month',
                'languages' => ['EN', 'HR', 'DE', 'IT'],
                'accommodation_provided' => true,
                'accommodation_details' => 'Private apartment near the hotel',
                'contract_type' => 'full-time',
                'status' => 'published',
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => 'Marketing Specialist',
                'employer_id' => $employer->id,
                'created_by_user_id' => $employer->user_id,
                'description' => 'Dynamic marketing team looking for a creative specialist to manage our digital campaigns. Experience with social media and content marketing required.',
                'location_city' => 'Zagreb',
                'category' => 'Marketing',
                'salary_min' => 2000,
                'salary_max' => 3500,
                'salary_currency' => 'EUR',
                'salary_period' => 'month',
                'languages' => ['EN'],
                'accommodation_provided' => false,
                'contract_type' => 'full-time',
                'status' => 'published',
                'published_at' => now()->subHours(8),
            ],
            [
                'title' => 'Chef de Cuisine',
                'employer_id' => $employer->id,
                'created_by_user_id' => $employer->user_id,
                'description' => 'Fine dining restaurant in Dubrovnik seeks talented chef. Mediterranean cuisine focus. Accommodation provided.',
                'location_city' => 'Dubrovnik',
                'category' => 'Hospitality',
                'salary_min' => 2200,
                'salary_max' => 3800,
                'salary_currency' => 'EUR',
                'salary_period' => 'month',
                'languages' => ['EN', 'HR'],
                'accommodation_provided' => true,
                'contract_type' => 'full-time',
                'status' => 'published',
                'published_at' => now()->subDays(1),
            ],
            [
                'title' => 'Frontend Developer',
                'employer_id' => $employer->id,
                'created_by_user_id' => $employer->user_id,
                'description' => 'Join our tech team to build modern web applications. React and TypeScript experience required. Remote work possible.',
                'location_city' => 'Rijeka',
                'category' => 'IT & Software',
                'salary_min' => 2800,
                'salary_max' => 4500,
                'salary_currency' => 'EUR',
                'salary_period' => 'month',
                'languages' => ['EN'],
                'accommodation_provided' => false,
                'contract_type' => 'full-time',
                'status' => 'published',
                'published_at' => now()->subHours(3),
            ],
            [
                'title' => 'Accountant',
                'employer_id' => $employer->id,
                'created_by_user_id' => $employer->user_id,
                'description' => 'Growing company seeks experienced accountant. Knowledge of Croatian tax law preferred but not required.',
                'location_city' => 'Zagreb',
                'category' => 'Finance',
                'salary_min' => 2200,
                'salary_max' => 3200,
                'salary_currency' => 'EUR',
                'salary_period' => 'month',
                'languages' => ['EN', 'HR'],
                'accommodation_provided' => false,
                'contract_type' => 'full-time',
                'status' => 'published',
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Tourism Guide',
                'employer_id' => $employer->id,
                'created_by_user_id' => $employer->user_id,
                'description' => 'Tour company seeks multilingual guides for summer season. Must be energetic and knowledgeable about Croatian history.',
                'location_city' => 'Zadar',
                'category' => 'Tourism',
                'salary_min' => 1500,
                'salary_max' => 2500,
                'salary_currency' => 'EUR',
                'salary_period' => 'month',
                'languages' => ['EN', 'DE', 'FR', 'ES', 'IT'],
                'accommodation_provided' => true,
                'contract_type' => 'seasonal',
                'status' => 'published',
                'published_at' => now()->subDays(4),
            ],
            [
                'title' => 'DevOps Engineer',
                'employer_id' => $employer->id,
                'created_by_user_id' => $employer->user_id,
                'description' => 'We need a DevOps engineer to manage our cloud infrastructure. AWS experience required. Competitive salary.',
                'location_city' => 'Split',
                'category' => 'IT & Software',
                'salary_min' => 4000,
                'salary_max' => 6000,
                'salary_currency' => 'EUR',
                'salary_period' => 'month',
                'languages' => ['EN'],
                'accommodation_provided' => false,
                'contract_type' => 'full-time',
                'status' => 'published',
                'published_at' => now()->subHours(12),
            ],
            [
                'title' => 'Graphic Designer',
                'employer_id' => $employer->id,
                'created_by_user_id' => $employer->user_id,
                'description' => 'Creative studio looking for talented graphic designer. Adobe Creative Suite experience required.',
                'location_city' => 'Zagreb',
                'category' => 'Design',
                'salary_min' => 1800,
                'salary_max' => 2800,
                'salary_currency' => 'EUR',
                'salary_period' => 'month',
                'languages' => ['EN', 'HR'],
                'accommodation_provided' => false,
                'contract_type' => 'full-time',
                'status' => 'published',
                'published_at' => now()->subDays(6),
            ],
            [
                'title' => 'Restaurant Server',
                'employer_id' => $employer->id,
                'created_by_user_id' => $employer->user_id,
                'description' => 'Beachfront restaurant seeks friendly servers for summer season. Accommodation provided.',
                'location_city' => 'Hvar',
                'category' => 'Hospitality',
                'salary_min' => 1200,
                'salary_max' => 2000,
                'salary_currency' => 'EUR',
                'salary_period' => 'month',
                'languages' => ['EN', 'HR'],
                'accommodation_provided' => true,
                'contract_type' => 'seasonal',
                'status' => 'published',
                'published_at' => now()->subDays(7),
            ],
            [
                'title' => 'Data Analyst',
                'employer_id' => $employer->id,
                'created_by_user_id' => $employer->user_id,
                'description' => 'E-commerce company needs data analyst to help drive business decisions. SQL and Python skills required.',
                'location_city' => 'Zagreb',
                'category' => 'IT & Software',
                'salary_min' => 3000,
                'salary_max' => 4500,
                'salary_currency' => 'EUR',
                'salary_period' => 'month',
                'languages' => ['EN'],
                'accommodation_provided' => false,
                'contract_type' => 'full-time',
                'status' => 'published',
                'published_at' => now()->subHours(6),
            ],
            [
                'title' => 'English Teacher',
                'employer_id' => $employer->id,
                'created_by_user_id' => $employer->user_id,
                'description' => 'Language school seeks native English speaker to teach adults and children. TEFL certification preferred.',
                'location_city' => 'Osijek',
                'category' => 'Education',
                'salary_min' => 1500,
                'salary_max' => 2200,
                'salary_currency' => 'EUR',
                'salary_period' => 'month',
                'languages' => ['EN'],
                'accommodation_provided' => false,
                'contract_type' => 'full-time',
                'status' => 'published',
                'published_at' => now()->subDays(10),
            ],
        ];

        foreach ($jobs as $jobData) {
            Job::updateOrCreate(
                [
                    'title' => $jobData['title'],
                    'employer_id' => $jobData['employer_id'],
                ],
                $jobData
            );
        }

        $this->command->info('Created ' . count($jobs) . ' sample jobs.');
    }
}
