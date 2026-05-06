<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Suppress E_DEPRECATED notices from vendor code on PHP 8.5+ (e.g. PDO::MYSQL_ATTR_SSL_CA).
// App-level deprecations (outside vendor/) still surface normally.
set_error_handler(static function (int $errno, string $errstr, string $errfile): bool {
    if ($errno === E_DEPRECATED && str_contains($errfile, DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR)) {
        return true;
    }
    return false;
}, E_DEPRECATED);

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
