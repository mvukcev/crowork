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
        Schema::create('education_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('education_id')->constrained('educations')->onDelete('cascade');
            $table->foreignId('worker_id')->constrained('users')->onDelete('cascade');
            $table->json('profile_snapshot');
            $table->text('message')->nullable();
            $table->string('status')->default('new');
            $table->timestamps();
            
            $table->unique(['education_id', 'worker_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education_applications');
    }
};
