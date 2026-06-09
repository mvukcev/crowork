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
            $table->unsignedInteger('featured_image_focus_x')->nullable()->after('featured_image_path');
            $table->unsignedInteger('featured_image_focus_y')->nullable()->after('featured_image_focus_x');
            $table->string('author_name_with_title', 255)->nullable()->after('body');
            $table->string('author_specialty', 255)->nullable()->after('author_name_with_title');
            $table->string('author_external_url', 500)->nullable()->after('author_specialty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resource_posts', function (Blueprint $table) {
            $table->dropColumn([
                'featured_image_focus_x',
                'featured_image_focus_y',
                'author_name_with_title',
                'author_specialty',
                'author_external_url',
            ]);
        });
    }
};
