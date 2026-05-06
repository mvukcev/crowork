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
        Schema::create('educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title');
            $table->string('slug', 191)->unique();
            $table->longText('description');
            $table->string('city')->nullable();
            $table->boolean('is_online')->default(false);
            $table->date('start_date')->nullable();
            $table->integer('price_cents')->nullable();
            $table->string('currency', 3)->default('EUR');
            $table->integer('capacity')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('educations');
    }
};
