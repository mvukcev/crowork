<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 191); // privacy, terms, cookies
            $table->string('locale', 10)->default('en'); // en, hr, etc
            $table->string('title');
            $table->text('body');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_published')->default(true);
            $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['slug', 'locale']);
            $table->index('slug');
            $table->index('locale');
            $table->index('is_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_pages');
    }
};
