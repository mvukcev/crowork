<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('job_postings')) {
            return;
        }

        Schema::table('job_postings', function (Blueprint $table) {
            $table->text('responsibilities')->nullable()->after('description');
            $table->text('requirements')->nullable()->after('responsibilities');
            $table->text('benefits')->nullable()->after('requirements');

            $table->boolean('visa_support')->default(false)->after('accommodation_details');
            $table->text('visa_support_details')->nullable()->after('visa_support');

            $table->string('experience_level', 80)->nullable()->after('contract_type');
            $table->string('education_required', 120)->nullable()->after('experience_level');
            $table->string('contract_duration', 120)->nullable()->after('education_required');
            $table->string('start_flexibility', 120)->nullable()->after('start_date');
            $table->unsignedInteger('positions_available')->nullable()->after('start_flexibility');
            $table->string('working_hours', 120)->nullable()->after('positions_available');
            $table->text('shift_details')->nullable()->after('working_hours');
            $table->text('application_instructions')->nullable()->after('shift_details');

            $table->boolean('is_featured')->default(false)->after('application_instructions');
            $table->boolean('is_urgent')->default(false)->after('is_featured');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('job_postings')) {
            return;
        }

        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropColumn([
                'responsibilities',
                'requirements',
                'benefits',
                'visa_support',
                'visa_support_details',
                'experience_level',
                'education_required',
                'contract_duration',
                'start_flexibility',
                'positions_available',
                'working_hours',
                'shift_details',
                'application_instructions',
                'is_featured',
                'is_urgent',
            ]);
        });
    }
};
