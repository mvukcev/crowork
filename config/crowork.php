<?php

return [
    'coming_soon' => [
        'mode' => (string) env('COMING_SOON_MODE', 'env'),
        'enabled' => (bool) env('COMING_SOON_ENABLED', false),
        'demo_username' => (string) env('COMING_SOON_DEMO_USERNAME', 'demo'),
        'demo_password' => (string) env('COMING_SOON_DEMO_PASSWORD', 'demo123'),
        'session_key' => 'coming_soon_preview',
    ],
];
