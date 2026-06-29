<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => setting('aws_access_key_id', env('AWS_ACCESS_KEY_ID')),
        'secret' => setting('aws_secret_access_key', env('AWS_SECRET_ACCESS_KEY')),
        'region' => setting('aws_default_region', env('AWS_DEFAULT_REGION', 'us-east-1')),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Optional error monitoring placeholders.
    // Add Sentry package separately when enabling runtime integration.
    'sentry' => [
        'dsn' => env('SENTRY_LARAVEL_DSN'),
        'traces_sample_rate' => env('SENTRY_TRACES_SAMPLE_RATE'),
        'environment' => env('SENTRY_ENVIRONMENT', env('APP_ENV')),
    ],

    'meta' => [
        'enabled' => setting('meta_tracking_enabled', env('META_ENABLED', false)),
        'pixel_id' => setting('meta_pixel_id', env('META_PIXEL_ID')),
        'dataset_id' => setting('meta_dataset_id', env('META_DATASET_ID')),
    ],

    'hzz' => [
        'feed_url' => env('HZZ_FEED_URL', 'https://burzarada.hzz.hr/rss/0xADAA044C9A86446096022A136750DD8F.xml?AspxAutoDetectCookieSupport=1'),
        'logo_url' => env('HZZ_LOGO_URL', 'https://www.hzz.hr/app/uploads/2022/11/logo_hzz.svg'),
    ],

];
