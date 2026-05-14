<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('translation_overrides', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 10); // e.g., 'en', 'hr'
            $table->string('group', 191); // e.g., 'auth', 'dashboard'
            $table->string('key', 191); // e.g., 'welcome', 'submit_button'
            $table->text('value'); // The translated text
            $table->timestamps();

            $table->unique(['locale', 'group', 'key']);
            $table->index('locale');
            $table->index('group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translation_overrides');
    }
};
