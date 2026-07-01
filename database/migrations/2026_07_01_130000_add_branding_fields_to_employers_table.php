<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employers', function (Blueprint $table): void {
            if (! Schema::hasColumn('employers', 'brand_color')) {
                $table->string('brand_color', 7)->nullable()->after('logo_path');
            }

            if (! Schema::hasColumn('employers', 'cover_image_path')) {
                $table->string('cover_image_path', 2048)->nullable()->after('brand_color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employers', function (Blueprint $table): void {
            if (Schema::hasColumn('employers', 'cover_image_path')) {
                $table->dropColumn('cover_image_path');
            }

            if (Schema::hasColumn('employers', 'brand_color')) {
                $table->dropColumn('brand_color');
            }
        });
    }
};
