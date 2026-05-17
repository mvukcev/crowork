<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('consent_histories')) {
            return;
        }

        Schema::table('consent_histories', function (Blueprint $table): void {
            if (! Schema::hasColumn('consent_histories', 'consent_version_hash')) {
                $table->string('consent_version_hash', 64)
                    ->nullable()
                    ->after('consent_version');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('consent_histories') || ! Schema::hasColumn('consent_histories', 'consent_version_hash')) {
            return;
        }

        Schema::table('consent_histories', function (Blueprint $table): void {
            $table->dropColumn('consent_version_hash');
        });
    }
};
