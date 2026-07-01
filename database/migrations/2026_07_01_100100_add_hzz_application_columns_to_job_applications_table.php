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
            if (! Schema::hasColumn('job_applications', 'apply_channel')) {
                $table->string('apply_channel', 40)->default('internal')->after('job_id');
            }

            if (! Schema::hasColumn('job_applications', 'cv_snapshot')) {
                $table->json('cv_snapshot')->nullable()->after('job_snapshot');
            }

            if (! Schema::hasColumn('job_applications', 'cover_letter_text')) {
                $table->text('cover_letter_text')->nullable()->after('message');
            }

            if (! Schema::hasColumn('job_applications', 'submitted_to_email')) {
                $table->string('submitted_to_email', 190)->nullable()->after('cover_letter_text');
            }

            if (! Schema::hasColumn('job_applications', 'submission_status')) {
                $table->string('submission_status', 40)->default('pending')->after('submitted_to_email');
            }

            if (! Schema::hasColumn('job_applications', 'submission_log')) {
                $table->text('submission_log')->nullable()->after('submission_status');
            }

            if (! Schema::hasColumn('job_applications', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('submission_log');
            }
        });

        Schema::table('job_applications', function (Blueprint $table): void {
            $table->index(['apply_channel', 'created_at'], 'job_applications_apply_channel_created_index');
            $table->index(['submission_status', 'submitted_at'], 'job_applications_submission_status_submitted_index');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('job_applications')) {
            return;
        }

        Schema::table('job_applications', function (Blueprint $table): void {
            $table->dropIndex('job_applications_apply_channel_created_index');
            $table->dropIndex('job_applications_submission_status_submitted_index');
        });

        Schema::table('job_applications', function (Blueprint $table): void {
            $table->dropColumn([
                'apply_channel',
                'cv_snapshot',
                'cover_letter_text',
                'submitted_to_email',
                'submission_status',
                'submission_log',
                'submitted_at',
            ]);
        });
    }
};
