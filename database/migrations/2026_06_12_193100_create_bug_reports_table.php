<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bug_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reporter_email', 255)->nullable();
            $table->string('status', 32)->default('open')->index();
            $table->string('page_uri', 2048);
            $table->text('description');
            $table->string('screenshot_path', 1024)->nullable();
            $table->json('error_logs_snapshot')->nullable();
            $table->unsignedInteger('error_logs_count')->default(0);
            $table->text('admin_notes')->nullable();
            $table->timestamp('reported_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bug_reports');
    }
};
