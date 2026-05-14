<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add to users table
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (! Schema::hasColumn('users', 'communication_language')) {
                    $table->string('communication_language', 10)->default('en')->after('role');
                }
            });
        }

        // Add to worker_profiles table
        if (Schema::hasTable('worker_profiles')) {
            Schema::table('worker_profiles', function (Blueprint $table) {
                if (! Schema::hasColumn('worker_profiles', 'communication_language')) {
                    // Check if profile_visibility column exists before using ->after()
                    if (Schema::hasColumn('worker_profiles', 'profile_visibility')) {
                        $table->string('communication_language', 10)->default('en')->after('profile_visibility');
                    } else {
                        $table->string('communication_language', 10)->default('en');
                    }
                }
            });
        }

        // Add to employers table
        if (Schema::hasTable('employers')) {
            Schema::table('employers', function (Blueprint $table) {
                if (! Schema::hasColumn('employers', 'communication_language')) {
                    // Check if public_profile_enabled column exists before using ->after()
                    if (Schema::hasColumn('employers', 'public_profile_enabled')) {
                        $table->string('communication_language', 10)->default('en')->after('public_profile_enabled');
                    } elseif (Schema::hasColumn('employers', 'approved_at')) {
                        // Fallback to approved_at if public_profile_enabled doesn't exist
                        $table->string('communication_language', 10)->default('en')->after('approved_at');
                    } else {
                        // If neither exists, just add the column without position constraint
                        $table->string('communication_language', 10)->default('en');
                    }
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'communication_language')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('communication_language');
            });
        }

        if (Schema::hasTable('worker_profiles') && Schema::hasColumn('worker_profiles', 'communication_language')) {
            Schema::table('worker_profiles', function (Blueprint $table) {
                $table->dropColumn('communication_language');
            });
        }

        if (Schema::hasTable('employers') && Schema::hasColumn('employers', 'communication_language')) {
            Schema::table('employers', function (Blueprint $table) {
                $table->dropColumn('communication_language');
            });
        }
    }
};
