<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('job_id')->constrained('job_postings')->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('source_locale', 10)->default('hr');
            $table->string('provider', 40)->default('azure');
            $table->string('status', 20)->default('pending');
            $table->char('source_hash', 64);
            $table->json('content')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamp('translated_at')->nullable();
            $table->timestamps();

            $table->unique(['job_id', 'locale']);
            $table->index(['locale', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_translations');
    }
};
