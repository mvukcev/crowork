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
            // Add override for requiring approval
            // If null, uses global setting; if set, uses this value
            $table->boolean('require_approval_override')
                ->nullable()
                ->after('id')
                ->comment('Null = use global setting, true = require approval, false = auto-publish');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employers', function (Blueprint $table) {
            $table->dropColumn('require_approval_override');
        });
    }
};
