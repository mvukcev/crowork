<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_postings', function (Blueprint $table): void {
            if (! Schema::hasColumn('job_postings', 'cover_image_path')) {
                $table->string('cover_image_path', 2048)->nullable()->after('hzz_legal_notice');
            }
        });
    }

    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table): void {
            if (Schema::hasColumn('job_postings', 'cover_image_path')) {
                $table->dropColumn('cover_image_path');
            }
        });
    }
};
