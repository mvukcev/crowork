<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('worker_profiles', function (Blueprint $table) {
            $table->string('current_country')->nullable()->after('nationality_country_code');
            $table->string('current_city')->nullable()->after('current_country');
            $table->string('desired_city')->nullable()->after('current_city');
            $table->date('availability_date')->nullable()->after('desired_city');
            $table->json('languages')->nullable()->after('availability_date');
            $table->text('certifications')->nullable()->after('work_experience');
            $table->json('desired_roles')->nullable()->after('certifications');
            $table->unsignedInteger('salary_expectation')->nullable()->after('desired_roles');
            $table->boolean('accommodation_needed')->nullable()->after('salary_expectation');
            $table->string('visa_work_permit_status')->nullable()->after('accommodation_needed');
            $table->text('professional_summary')->nullable()->after('visa_work_permit_status');
            $table->string('profile_visibility', 20)->default('employers')->after('professional_summary');
        });
    }

    public function down(): void
    {
        Schema::table('worker_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'current_country',
                'current_city',
                'desired_city',
                'availability_date',
                'languages',
                'certifications',
                'desired_roles',
                'salary_expectation',
                'accommodation_needed',
                'visa_work_permit_status',
                'professional_summary',
                'profile_visibility',
            ]);
        });
    }
};
