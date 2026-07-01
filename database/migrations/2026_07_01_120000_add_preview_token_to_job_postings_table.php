<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('job_postings', 'preview_token')) {
            Schema::table('job_postings', function (Blueprint $table): void {
                $table->string('preview_token', 64)->nullable()->unique()->after('slug');
            });
        }

        DB::table('job_postings')
            ->select(['id'])
            ->whereNull('preview_token')
            ->orderBy('id')
            ->chunkById(200, function ($rows): void {
                foreach ($rows as $row) {
                    do {
                        $token = Str::random(64);
                    } while (DB::table('job_postings')->where('preview_token', $token)->exists());

                    DB::table('job_postings')
                        ->where('id', $row->id)
                        ->update(['preview_token' => $token]);
                }
            });
    }

    public function down(): void
    {
        if (Schema::hasColumn('job_postings', 'preview_token')) {
            Schema::table('job_postings', function (Blueprint $table): void {
                $table->dropUnique('job_postings_preview_token_unique');
                $table->dropColumn('preview_token');
            });
        }
    }
};
