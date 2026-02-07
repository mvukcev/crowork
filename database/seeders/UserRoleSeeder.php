<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employer;
use App\Models\WorkerProfile;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@crowork.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
            ]
        );

        // Create or update employer user with employer profile
        $employerUser = User::updateOrCreate(
            ['email' => 'employer@crowork.com'],
            [
                'name' => 'Employer User',
                'password' => Hash::make('password'),
                'role' => User::ROLE_EMPLOYER,
                'email_verified_at' => now(),
            ]
        );

        Employer::updateOrCreate(
            ['user_id' => $employerUser->id],
            [
                'company_name' => 'Tech Solutions Ltd',
                'city' => 'Zagreb',
                'approved_at' => now(),
            ]
        );

        // Create or update worker user with worker profile
        $workerUser = User::updateOrCreate(
            ['email' => 'worker@crowork.com'],
            [
                'name' => 'Worker User',
                'password' => Hash::make('password'),
                'role' => User::ROLE_WORKER,
                'email_verified_at' => now(),
            ]
        );

        WorkerProfile::updateOrCreate(
            ['user_id' => $workerUser->id],
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'nationality_country_code' => 'HR',
                'birth_year' => 1990,
                'education_summary' => 'Bachelor of Computer Science',
                'work_experience' => 'Software Developer at ABC Company (2015-2020)',
                'skills' => ['PHP', 'Laravel', 'JavaScript', 'Vue.js'],
                'recommendations' => 'Excellent team player and problem solver.',
                'photo_path' => null,
            ]
        );
    }
}


