<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('job_applications')) {
            return;
        }

        Schema::table('job_applications', function (Blueprint $table): void {
            if (! Schema::hasColumn('job_applications', 'anonymized_at')) {
                $table->timestamp('anonymized_at')->nullable()->after('status_updated_at');
            }

            if (! Schema::hasColumn('job_applications', 'retention_reason')) {
                $table->string('retention_reason', 120)->nullable()->after('anonymized_at');
            }

            if (! Schema::hasColumn('job_applications', 'retention_processed_at')) {
                $table->timestamp('retention_processed_at')->nullable()->after('retention_reason');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('job_applications')) {
            return;
        }

        Schema::table('job_applications', function (Blueprint $table): void {
            $columns = [];

            foreach (['anonymized_at', 'retention_reason', 'retention_processed_at'] as $column) {
                if (Schema::hasColumn('job_applications', $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
