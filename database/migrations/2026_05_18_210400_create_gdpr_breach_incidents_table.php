<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gdpr_breach_incidents', function (Blueprint $table): void {
            $table->id();
            $table->string('title', 255);
            $table->string('severity', 20)->default('low');
            $table->string('status', 30)->default('open');
            $table->timestamp('detected_at');
            $table->timestamp('reported_at')->nullable();
            $table->text('summary');
            $table->json('affected_data_categories')->nullable();
            $table->unsignedInteger('affected_user_count')->nullable();
            $table->boolean('authority_notification_required')->default(false);
            $table->boolean('users_notification_required')->default(false);
            $table->foreignId('owner_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('internal_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'severity']);
            $table->index(['detected_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gdpr_breach_incidents');
    }
};
