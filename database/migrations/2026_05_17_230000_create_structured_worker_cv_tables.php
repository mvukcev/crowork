<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('worker_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_profile_id')->constrained()->cascadeOnDelete();
            $table->string('job_title');
            $table->string('company_name')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['worker_profile_id', 'sort_order']);
        });

        Schema::create('worker_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_profile_id')->constrained()->cascadeOnDelete();
            $table->string('institution');
            $table->string('degree')->nullable();
            $table->string('field_of_study')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['worker_profile_id', 'sort_order']);
        });

        Schema::create('worker_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_profile_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('issuer')->nullable();
            $table->date('issued_on')->nullable();
            $table->date('expires_on')->nullable();
            $table->string('credential_id')->nullable();
            $table->string('credential_url')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['worker_profile_id', 'sort_order']);
        });

        Schema::create('worker_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_profile_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('position')->nullable();
            $table->string('company')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['worker_profile_id', 'sort_order']);
        });

        Schema::create('worker_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_profile_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('level')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['worker_profile_id', 'sort_order']);
        });

        Schema::create('worker_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_profile_id')->constrained()->cascadeOnDelete();
            $table->string('language');
            $table->string('level')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['worker_profile_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_languages');
        Schema::dropIfExists('worker_skills');
        Schema::dropIfExists('worker_references');
        Schema::dropIfExists('worker_certifications');
        Schema::dropIfExists('worker_educations');
        Schema::dropIfExists('worker_experiences');
    }
};
