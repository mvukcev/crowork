<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gdpr_data_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('requester_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('requester_email', 255)->nullable();
            $table->string('request_type', 60);
            $table->string('status', 40)->default('open');
            $table->string('priority', 20)->default('normal');
            $table->timestamp('due_at')->nullable();
            $table->foreignId('assigned_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('internal_notes')->nullable();
            $table->text('resolution_summary')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'due_at']);
            $table->index(['request_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gdpr_data_requests');
    }
};
