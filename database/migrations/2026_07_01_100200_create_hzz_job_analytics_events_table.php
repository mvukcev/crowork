<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hzz_job_analytics_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('job_id')->constrained('job_postings')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('session_id', 120)->nullable();
            $table->string('event_type', 40);
            $table->boolean('is_unique_view')->default(false);
            $table->timestamp('event_at');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['job_id', 'event_type', 'event_at'], 'hzz_events_job_type_time_index');
            $table->index(['event_type', 'event_at'], 'hzz_events_type_time_index');
            $table->index(['session_id', 'job_id', 'event_type'], 'hzz_events_session_job_type_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hzz_job_analytics_events');
    }
};
