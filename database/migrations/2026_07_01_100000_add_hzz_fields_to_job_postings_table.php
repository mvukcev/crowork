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

        Schema::table('job_postings', function (Blueprint $table): void {
            if (! Schema::hasColumn('job_postings', 'source_system')) {
                $table->string('source_system', 40)->nullable()->after('created_by_user_id');
            }

            if (! Schema::hasColumn('job_postings', 'source_reference')) {
                $table->string('source_reference', 190)->nullable()->after('source_system');
            }

            if (! Schema::hasColumn('job_postings', 'source_url')) {
                $table->text('source_url')->nullable()->after('source_reference');
            }

            if (! Schema::hasColumn('job_postings', 'source_payload')) {
                $table->json('source_payload')->nullable()->after('source_url');
            }

            if (! Schema::hasColumn('job_postings', 'source_imported_at')) {
                $table->timestamp('source_imported_at')->nullable()->after('source_payload');
            }

            if (! Schema::hasColumn('job_postings', 'hzz_is_official')) {
                $table->boolean('hzz_is_official')->default(false)->after('source_imported_at');
            }

            if (! Schema::hasColumn('job_postings', 'hzz_apply_email')) {
                $table->string('hzz_apply_email', 190)->nullable()->after('hzz_is_official');
            }

            if (! Schema::hasColumn('job_postings', 'hzz_apply_contact_type')) {
                $table->string('hzz_apply_contact_type', 40)->default('unknown')->after('hzz_apply_email');
            }

            if (! Schema::hasColumn('job_postings', 'hzz_apply_contact_raw')) {
                $table->text('hzz_apply_contact_raw')->nullable()->after('hzz_apply_contact_type');
            }

            if (! Schema::hasColumn('job_postings', 'hzz_apply_url')) {
                $table->text('hzz_apply_url')->nullable()->after('hzz_apply_contact_raw');
            }

            if (! Schema::hasColumn('job_postings', 'hzz_apply_method_available')) {
                $table->boolean('hzz_apply_method_available')->default(false)->after('hzz_apply_url');
            }

            if (! Schema::hasColumn('job_postings', 'hzz_legal_notice')) {
                $table->text('hzz_legal_notice')->nullable()->after('hzz_apply_method_available');
            }
        });

        Schema::table('job_postings', function (Blueprint $table): void {
            $table->index(['source_system', 'status'], 'job_postings_source_system_status_index');
            $table->index(['hzz_is_official', 'published_at'], 'job_postings_hzz_official_published_index');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('job_postings')) {
            return;
        }

        Schema::table('job_postings', function (Blueprint $table): void {
            $table->dropIndex('job_postings_source_system_status_index');
            $table->dropIndex('job_postings_hzz_official_published_index');
        });

        Schema::table('job_postings', function (Blueprint $table): void {
            $table->dropColumn([
                'source_system',
                'source_reference',
                'source_url',
                'source_payload',
                'source_imported_at',
                'hzz_is_official',
                'hzz_apply_email',
                'hzz_apply_contact_type',
                'hzz_apply_contact_raw',
                'hzz_apply_url',
                'hzz_apply_method_available',
                'hzz_legal_notice',
            ]);
        });
    }
};
