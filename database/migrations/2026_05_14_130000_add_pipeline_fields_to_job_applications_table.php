<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->text('internal_note')->nullable()->after('message');
            $table->unsignedTinyInteger('score')->nullable()->after('internal_note');
            $table->timestamp('interview_at')->nullable()->after('score');
            $table->timestamp('status_updated_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn([
                'internal_note',
                'score',
                'interview_at',
                'status_updated_at',
            ]);
        });
    }
};
