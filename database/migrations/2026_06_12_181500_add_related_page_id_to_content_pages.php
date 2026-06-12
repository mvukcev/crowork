<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_pages', function (Blueprint $table) {
            $table->foreignId('related_page_id')
                ->nullable()
                ->after('locale')
                ->constrained('content_pages')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('content_pages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('related_page_id');
        });
    }
};
