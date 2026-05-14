<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('employers')) {
            return;
        }

        Schema::table('employers', function (Blueprint $table) {
            if (! Schema::hasColumn('employers', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('company_name');
            }

            if (! Schema::hasColumn('employers', 'country')) {
                $table->string('country', 120)->nullable()->after('city');
            }
        });

        DB::table('employers')
            ->select(['id', 'company_name', 'slug'])
            ->orderBy('id')
            ->get()
            ->each(function (object $employer): void {
                if (! empty($employer->slug)) {
                    return;
                }

                $baseSlug = Str::slug((string) $employer->company_name);
                $slug = $baseSlug !== '' ? $baseSlug : 'company-' . $employer->id;
                $counter = 2;

                while (DB::table('employers')->where('slug', $slug)->where('id', '!=', $employer->id)->exists()) {
                    $slug = $baseSlug !== '' ? $baseSlug . '-' . $counter : 'company-' . $employer->id . '-' . $counter;
                    $counter++;
                }

                DB::table('employers')->where('id', $employer->id)->update(['slug' => $slug]);
            });
    }

    public function down(): void
    {
        if (! Schema::hasTable('employers')) {
            return;
        }

        Schema::table('employers', function (Blueprint $table) {
            $dropColumns = [];

            if (Schema::hasColumn('employers', 'slug')) {
                $dropColumns[] = 'slug';
            }

            if (Schema::hasColumn('employers', 'country')) {
                $dropColumns[] = 'country';
            }

            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};