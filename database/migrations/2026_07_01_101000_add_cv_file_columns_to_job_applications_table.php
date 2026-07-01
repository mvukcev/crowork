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
            if (! Schema::hasColumn('job_applications', 'cv_file_path')) {
                $table->string('cv_file_path', 255)->nullable()->after('cv_snapshot');
            }

            if (! Schema::hasColumn('job_applications', 'cover_letter_template_key')) {
                $table->string('cover_letter_template_key', 60)->nullable()->after('cover_letter_text');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('job_applications')) {
            return;
        }

        Schema::table('job_applications', function (Blueprint $table): void {
            $table->dropColumn([
                'cv_file_path',
                'cover_letter_template_key',
            ]);
        });
    }
};
