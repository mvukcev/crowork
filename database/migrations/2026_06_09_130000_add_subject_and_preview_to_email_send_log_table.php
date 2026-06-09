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
        Schema::table('email_send_log', function (Blueprint $table) {
            $table->string('subject', 255)->nullable()->after('template');
            $table->text('body_preview')->nullable()->after('subject');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_send_log', function (Blueprint $table) {
            $table->dropColumn(['subject', 'body_preview']);
        });
    }
};
