<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('account_deletion_requests')) {
            return;
        }

        Schema::table('account_deletion_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('account_deletion_requests', 'reason')) {
                $table->text('reason')->nullable()->after('status');
            }

            if (! Schema::hasColumn('account_deletion_requests', 'requested_at')) {
                $table->timestamp('requested_at')->nullable()->after('reason');
            }

            if (! Schema::hasColumn('account_deletion_requests', 'anonymization_scheduled_at')) {
                $table->timestamp('anonymization_scheduled_at')->nullable()->after('requested_at');
            }

            if (! Schema::hasColumn('account_deletion_requests', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('anonymization_scheduled_at');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('account_deletion_requests')) {
            return;
        }

        Schema::table('account_deletion_requests', function (Blueprint $table) {
            $columns = [];
            foreach (['reason', 'requested_at', 'anonymization_scheduled_at', 'completed_at'] as $column) {
                if (Schema::hasColumn('account_deletion_requests', $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
