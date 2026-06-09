<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('resource_posts', function (Blueprint $table) {
            $table->dropUnique('resource_posts_slug_unique');
            $table->unique(['slug', 'locale'], 'resource_posts_slug_locale_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resource_posts', function (Blueprint $table) {
            $table->dropUnique('resource_posts_slug_locale_unique');
            $table->unique('slug', 'resource_posts_slug_unique');
        });
    }
};
