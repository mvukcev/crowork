<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gdpr_export_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('requested_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('requested_by_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('export_type', 80);
            $table->string('status', 40)->default('pending');
            $table->string('file_path', 255)->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('downloaded_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->index(['status', 'generated_at']);
            $table->index(['user_id', 'export_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gdpr_export_logs');
    }
};
