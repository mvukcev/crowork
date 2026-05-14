<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action', 191); // e.g., 'settings_updated', 'employer_approved'
            $table->string('subject_type', 191)->nullable(); // e.g., 'Setting', 'Employer'
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('changes')->nullable(); // {'old' => [...], 'new' => [...]}
            $table->string('ip_address', 45)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('action');
            $table->index('subject_type');
            $table->index('created_at');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
