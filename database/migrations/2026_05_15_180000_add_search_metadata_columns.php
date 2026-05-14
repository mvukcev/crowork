<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('jobs_listing', function (Blueprint $table) {
            $table->json('search_metadata')->nullable();
        });

        Schema::table('educations', function (Blueprint $table) {
            $table->json('search_metadata')->nullable();
        });

        Schema::table('employers', function (Blueprint $table) {
            $table->json('search_metadata')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('jobs_listing', function (Blueprint $table) {
            $table->dropColumn('search_metadata');
        });

        Schema::table('educations', function (Blueprint $table) {
            $table->dropColumn('search_metadata');
        });

        Schema::table('employers', function (Blueprint $table) {
            $table->dropColumn('search_metadata');
        });
    }
};