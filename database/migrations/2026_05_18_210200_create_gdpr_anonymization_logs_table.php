<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gdpr_anonymization_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('target_type', 191);
            $table->string('target_id', 64)->nullable();
            $table->string('action', 120);
            $table->string('reason', 191)->nullable();
            $table->string('triggered_by', 80);
            $table->foreignId('triggered_by_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 40)->default('started');
            $table->json('summary_json')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->index(['status', 'started_at']);
            $table->index(['target_type', 'target_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gdpr_anonymization_logs');
    }
};
