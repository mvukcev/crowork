<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_holds', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('target_type', 191)->nullable();
            $table->string('target_id', 64)->nullable();
            $table->string('reason', 191);
            $table->string('status', 20)->default('active');
            $table->foreignId('placed_by_admin_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('released_by_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('placed_at');
            $table->timestamp('released_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'user_id']);
            $table->index(['target_type', 'target_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_holds');
    }
};
