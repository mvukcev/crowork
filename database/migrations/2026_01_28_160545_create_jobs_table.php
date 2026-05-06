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
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->nullable()->constrained('employers')->onDelete('set null');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->string('slug', 191)->unique();
            $table->integer('salary_min')->nullable();
            $table->integer('salary_max')->nullable();
            $table->string('salary_currency', 3)->default('EUR');
            $table->string('salary_period')->default('month');
            $table->longText('description');
            $table->string('location_city');
            $table->string('category');
            $table->json('languages')->nullable();
            $table->boolean('accommodation_provided')->default(false);
            $table->text('accommodation_details')->nullable();
            $table->string('contract_type')->nullable();
            $table->date('start_date')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};
