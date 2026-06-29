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
            if (! Schema::hasColumn('job_postings', 'source_type')) {
                $table->string('source_type', 40)->nullable()->after('created_by_user_id');
            }

            if (! Schema::hasColumn('job_postings', 'source_external_id')) {
                $table->string('source_external_id', 120)->nullable()->after('source_type');
            }

            if (! Schema::hasColumn('job_postings', 'source_url')) {
                $table->text('source_url')->nullable()->after('source_external_id');
            }

            if (! Schema::hasColumn('job_postings', 'source_logo_url')) {
                $table->text('source_logo_url')->nullable()->after('source_url');
            }

            if (! Schema::hasColumn('job_postings', 'source_imported_at')) {
                $table->timestamp('source_imported_at')->nullable()->after('published_at');
            }

            if (! Schema::hasColumn('job_postings', 'external_company_name')) {
                $table->string('external_company_name')->nullable()->after('source_imported_at');
            }

            $table->index(['source_type', 'source_external_id'], 'job_postings_source_lookup_index');
            $table->index('source_imported_at', 'job_postings_source_imported_at_index');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('job_postings')) {
            return;
        }

        Schema::table('job_postings', function (Blueprint $table) {
            if (Schema::hasColumn('job_postings', 'source_imported_at')) {
                $table->dropIndex('job_postings_source_imported_at_index');
            }

            if (Schema::hasColumn('job_postings', 'source_external_id')) {
                $table->dropIndex('job_postings_source_lookup_index');
            }

            $dropColumns = [];

            foreach (['source_type', 'source_external_id', 'source_url', 'source_logo_url', 'source_imported_at', 'external_company_name'] as $column) {
                if (Schema::hasColumn('job_postings', $column)) {
                    $dropColumns[] = $column;
                }
            }

            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
