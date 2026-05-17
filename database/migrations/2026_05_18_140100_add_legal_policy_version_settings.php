<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('settings')) {
            return;
        }

        $defaults = [
            'terms_version' => '2026-05-terms-v1',
            'terms_hash' => null,
            'privacy_policy_version' => '2026-05-privacy-v1',
            'privacy_policy_hash' => null,
        ];

        foreach ($defaults as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                [
                    'value' => json_encode(['value' => $value], JSON_UNESCAPED_UNICODE),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('settings')) {
            return;
        }

        DB::table('settings')->whereIn('key', [
            'terms_version',
            'terms_hash',
            'privacy_policy_version',
            'privacy_policy_hash',
        ])->delete();
    }
};
