<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test users
        User::factory()->create([
            'name' => 'Worker Demo',
            'email' => 'worker@example.com',
            'role' => 'worker',
        ]);

        User::factory()->create([
            'name' => 'Employer Demo',
            'email' => 'employer@example.com',
            'role' => 'employer',
        ]);

        // Seed jobs
        $this->call(JobListingSeeder::class);
    }
}
