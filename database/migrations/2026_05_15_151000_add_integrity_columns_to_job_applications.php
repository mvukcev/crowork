<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add soft deletes to job_applications if not already present
        if (Schema::hasTable('job_applications') && !Schema::hasColumn('job_applications', 'deleted_at')) {
            Schema::table('job_applications', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add status_updated_at timestamp if not already present
        if (Schema::hasTable('job_applications') && !Schema::hasColumn('job_applications', 'status_updated_at')) {
            Schema::table('job_applications', function (Blueprint $table) {
                $table->timestamp('status_updated_at')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('job_applications')) {
            Schema::table('job_applications', function (Blueprint $table) {
                $table->dropSoftDeletes();
                $table->dropColumn('status_updated_at');
            });
        }
    }
};
