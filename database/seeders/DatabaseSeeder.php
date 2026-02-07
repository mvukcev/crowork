<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed role-based users
        $this->call(UserRoleSeeder::class);

        // Seed default settings
        $this->call(SettingsSeeder::class);

        // Seed jobs (legacy)
        $this->call(JobListingSeeder::class);
        
        // Seed new job postings
        $this->call(JobSeeder::class);
    }
}
