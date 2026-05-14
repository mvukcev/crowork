<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key', 120);
            $table->string('locale', 10)->default('en');
            $table->string('subject');
            $table->longText('body');
            $table->json('variables_preview')->nullable();
            $table->timestamps();

            $table->unique(['key', 'locale']);
            $table->index('key');
            $table->index('locale');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
