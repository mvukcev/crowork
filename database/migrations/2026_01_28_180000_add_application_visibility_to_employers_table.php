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
        Schema::table('employers', function (Blueprint $table) {
            // Add visibility and export override columns
            $table->enum('applications_visibility_override', ['full', 'limited', 'anonymous'])->nullable()->after('city');
            $table->boolean('can_export_applications_override')->nullable()->after('applications_visibility_override');
            $table->json('visible_fields_override')->nullable()->after('can_export_applications_override');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employers', function (Blueprint $table) {
            $table->dropColumn(['applications_visibility_override', 'can_export_applications_override', 'visible_fields_override']);
        });
    }
};
