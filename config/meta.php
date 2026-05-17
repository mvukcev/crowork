<?php

return [
    'enabled' => (bool) setting('meta_tracking_enabled', env('META_ENABLED', false)),

    'browser_enabled' => (bool) setting('meta_browser_enabled', env('META_BROWSER_ENABLED', true)),
    'capi_enabled' => (bool) setting('meta_capi_enabled', env('META_CAPI_ENABLED', true)),

    'pixel_id' => setting('meta_pixel_id', env('META_PIXEL_ID')),
    'dataset_id' => setting('meta_dataset_id', env('META_DATASET_ID')),
    'access_token' => setting('meta_conversions_api_access_token', env('META_ACCESS_TOKEN')),
    'api_version' => setting('meta_api_version', env('META_API_VERSION', 'v20.0')),
    'test_event_code' => setting('meta_test_event_code', env('META_TEST_EVENT_CODE')),

    'timeout_seconds' => (int) setting('meta_timeout_seconds', env('META_TIMEOUT_SECONDS', 10)),
    'queue' => (string) setting('meta_queue', env('META_QUEUE', 'default')),
    'log_channel' => (string) setting('meta_log_channel', env('META_LOG_CHANNEL', 'meta')),

    'send_from_local' => (bool) setting('meta_send_from_local', env('META_SEND_FROM_LOCAL', false)),
];