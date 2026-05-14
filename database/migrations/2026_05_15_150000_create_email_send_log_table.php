<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_send_log', function (Blueprint $table) {
            $table->id();
            $table->string('to_address', 254);
            $table->string('template', 191);
            $table->string('context_hash', 64)->nullable();
            $table->string('message_id', 255)->nullable();
            $table->timestamp('sent_at')->index();
            $table->timestamps();

            // Composite index for deduplication check
            $table->index(['to_address', 'template', 'context_hash', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_send_log');
    }
};
