<?php

namespace Database\Seeders;

use App\Models\JobListing;
use App\Models\User;
use Illuminate\Database\Seeder;

class JobListingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create an employer
        $employer = User::where('role', 'employer')->first();
        
        if (!$employer) {
            $employer = User::factory()->create([
                'name' => 'Employer Demo',
                'email' => 'employer@example.com',
                'role' => 'employer',
            ]);
        }

        $jobs = [
            [
                'title' => 'Senior PHP Developer',
                'description' => 'We are looking for an experienced PHP developer to join our team in Zagreb. Must have 5+ years of experience with Laravel and modern PHP practices.',
                'location' => 'Zagreb, Croatia',
                'job_type' => 'full-time',
                'salary_min' => 3000,
                'salary_max' => 5000,
                'company_name' => 'Tech Solutions Croatia',
                'is_active' => true,
            ],
            [
                'title' => 'Frontend Developer - React',
                'description' => 'Join our dynamic team in Split as a Frontend Developer. Experience with React, TypeScript, and modern CSS frameworks required.',
                'location' => 'Split, Croatia',
                'job_type' => 'full-time',
                'salary_min' => 2500,
                'salary_max' => 4000,
                'company_name' => 'Digital Agency Croatia',
                'is_active' => true,
            ],
            [
                'title' => 'DevOps Engineer',
                'description' => 'We need a DevOps engineer with experience in AWS, Docker, and Kubernetes. Remote work available.',
                'location' => 'Remote',
                'job_type' => 'full-time',
                'salary_min' => 3500,
                'salary_max' => 5500,
                'company_name' => 'Cloud Services Croatia',
                'is_active' => true,
            ],
            [
                'title' => 'Junior Web Developer',
                'description' => 'Great opportunity for a junior developer to learn and grow. Basic knowledge of HTML, CSS, JavaScript required.',
                'location' => 'Rijeka, Croatia',
                'job_type' => 'full-time',
                'salary_min' => 1500,
                'salary_max' => 2500,
                'company_name' => 'Web Studio Rijeka',
                'is_active' => true,
            ],
            [
                'title' => 'Project Manager - IT',
                'description' => 'Looking for an experienced IT Project Manager to lead our development team. PMP certification preferred.',
                'location' => 'Zagreb, Croatia',
                'job_type' => 'full-time',
                'salary_min' => 4000,
                'salary_max' => 6000,
                'company_name' => 'Enterprise Solutions',
                'is_active' => true,
            ],
        ];

        foreach ($jobs as $jobData) {
            JobListing::updateOrCreate(
                ['title' => $jobData['title'], 'employer_id' => $employer->id],
                array_merge($jobData, ['employer_id' => $employer->id])
            );
        }
    }
}
