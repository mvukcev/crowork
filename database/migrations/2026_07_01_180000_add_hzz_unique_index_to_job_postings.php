<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const INDEX_NAME = 'job_postings_source_system_source_reference_unique';

    public function up(): void
    {
        if (! Schema::hasTable('job_postings')) {
            return;
        }

        if (! Schema::hasColumn('job_postings', 'source_system') || ! Schema::hasColumn('job_postings', 'source_reference')) {
            return;
        }

        $hasDuplicates = DB::table('job_postings')
            ->select('source_system', 'source_reference')
            ->whereNotNull('source_system')
            ->whereNotNull('source_reference')
            ->groupBy('source_system', 'source_reference')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($hasDuplicates) {
            Log::warning('Skipping HZZ unique index creation because duplicate source keys exist.', [
                'index' => self::INDEX_NAME,
            ]);

            return;
        }

        try {
            Schema::table('job_postings', function (Blueprint $table): void {
                $table->unique(['source_system', 'source_reference'], self::INDEX_NAME);
            });
        } catch (\Throwable $exception) {
            Log::warning('HZZ unique index creation skipped.', [
                'index' => self::INDEX_NAME,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('job_postings')) {
            return;
        }

        try {
            Schema::table('job_postings', function (Blueprint $table): void {
                $table->dropUnique(self::INDEX_NAME);
            });
        } catch (\Throwable) {
            // Ignore environments where the unique index was not created.
        }
    }
};