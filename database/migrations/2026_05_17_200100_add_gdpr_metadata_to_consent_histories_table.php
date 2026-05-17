<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('consent_histories')) {
            return;
        }

        Schema::table('consent_histories', function (Blueprint $table) {
            if (! Schema::hasColumn('consent_histories', 'consent_version')) {
                $table->string('consent_version', 64)->nullable()->after('consent_type');
            }

            if (! Schema::hasColumn('consent_histories', 'source')) {
                $table->string('source', 64)->nullable()->after('consent_version');
            }

            if (! Schema::hasColumn('consent_histories', 'accepted_at')) {
                $table->timestamp('accepted_at')->nullable()->after('given');
            }

            if (! Schema::hasColumn('consent_histories', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('accepted_at');
            }

            if (! Schema::hasColumn('consent_histories', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('consent_histories')) {
            return;
        }

        Schema::table('consent_histories', function (Blueprint $table) {
            $columns = [];
            foreach (['consent_version', 'source', 'accepted_at', 'ip_address', 'user_agent'] as $column) {
                if (Schema::hasColumn('consent_histories', $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
