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
            // Add column to link to the related post in another language
            // Even if slugs are different (e.g., 'working-in-croatia' EN linked to 'rad-u-hrvatske' HR)
            $table->foreignId('related_post_id')
                ->nullable()
                ->constrained('resource_posts')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resource_posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('related_post_id');
        });
    }
};
