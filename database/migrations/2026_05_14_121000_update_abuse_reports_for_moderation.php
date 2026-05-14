<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('abuse_reports')) {
            return;
        }

        Schema::table('abuse_reports', function (Blueprint $table) {
            if (! Schema::hasColumn('abuse_reports', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('message');
            }
        });

        DB::table('abuse_reports')->where('status', 'new')->update(['status' => 'open']);
    }

    public function down(): void
    {
        if (! Schema::hasTable('abuse_reports')) {
            return;
        }

        Schema::table('abuse_reports', function (Blueprint $table) {
            if (Schema::hasColumn('abuse_reports', 'admin_notes')) {
                $table->dropColumn('admin_notes');
            }
        });

        DB::table('abuse_reports')->where('status', 'open')->update(['status' => 'new']);
    }
};
