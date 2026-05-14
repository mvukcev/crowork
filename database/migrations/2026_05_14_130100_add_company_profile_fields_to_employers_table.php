<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employers', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('company_name');
            $table->string('industry')->nullable()->after('city');
            $table->string('website')->nullable()->after('industry');
            $table->text('description')->nullable()->after('website');
            $table->boolean('relocation_support')->default(false)->after('description');
            $table->boolean('accommodation_support')->default(false)->after('relocation_support');
        });
    }

    public function down(): void
    {
        Schema::table('employers', function (Blueprint $table) {
            $table->dropColumn([
                'logo_path',
                'industry',
                'website',
                'description',
                'relocation_support',
                'accommodation_support',
            ]);
        });
    }
};
