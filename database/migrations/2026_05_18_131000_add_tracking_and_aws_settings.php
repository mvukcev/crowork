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
            'google_search_console_verification' => null,
            'meta_browser_enabled' => true,
            'meta_capi_enabled' => true,
            'meta_timeout_seconds' => 10,
            'meta_queue' => 'default',
            'meta_log_channel' => 'meta',
            'meta_send_from_local' => false,
            'aws_access_key_id' => null,
            'aws_secret_access_key' => null,
            'aws_default_region' => 'us-east-1',
            'aws_bucket' => null,
            'aws_url' => null,
            'aws_endpoint' => null,
            'aws_use_path_style_endpoint' => false,
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
            'google_search_console_verification',
            'meta_browser_enabled',
            'meta_capi_enabled',
            'meta_timeout_seconds',
            'meta_queue',
            'meta_log_channel',
            'meta_send_from_local',
            'aws_access_key_id',
            'aws_secret_access_key',
            'aws_default_region',
            'aws_bucket',
            'aws_url',
            'aws_endpoint',
            'aws_use_path_style_endpoint',
        ])->delete();
    }
};
