<?php

declare(strict_types=1);

// Vercel serverless filesystem is read-only except /tmp.
// Prepare writable locations before bootstrapping Laravel.
$vercelTmp = '/tmp';
$directories = [
    $vercelTmp . '/storage',
    $vercelTmp . '/storage/app',
    $vercelTmp . '/storage/app/private',
    $vercelTmp . '/storage/app/public',
    $vercelTmp . '/storage/framework',
    $vercelTmp . '/storage/framework/cache',
    $vercelTmp . '/storage/framework/cache/data',
    $vercelTmp . '/storage/framework/sessions',
    $vercelTmp . '/storage/framework/testing',
    $vercelTmp . '/storage/framework/views',
    $vercelTmp . '/storage/logs',
    $vercelTmp . '/bootstrap-cache',
];

foreach ($directories as $directory) {
    if (!is_dir($directory)) {
        @mkdir($directory, 0775, true);
    }
}

// Provide safe defaults for Vercel when env vars are not set.
$_ENV['VERCEL'] = '1';
$_SERVER['VERCEL'] = '1';
putenv('VERCEL=1');

$_ENV['APP_SERVICES_CACHE'] = $_ENV['APP_SERVICES_CACHE'] ?? ($vercelTmp . '/bootstrap-cache/services.php');
$_ENV['APP_PACKAGES_CACHE'] = $_ENV['APP_PACKAGES_CACHE'] ?? ($vercelTmp . '/bootstrap-cache/packages.php');
$_ENV['APP_CONFIG_CACHE'] = $_ENV['APP_CONFIG_CACHE'] ?? ($vercelTmp . '/bootstrap-cache/config.php');
$_ENV['APP_ROUTES_CACHE'] = $_ENV['APP_ROUTES_CACHE'] ?? ($vercelTmp . '/bootstrap-cache/routes-v7.php');
$_ENV['APP_EVENTS_CACHE'] = $_ENV['APP_EVENTS_CACHE'] ?? ($vercelTmp . '/bootstrap-cache/events.php');
$_ENV['VIEW_COMPILED_PATH'] = $_ENV['VIEW_COMPILED_PATH'] ?? ($vercelTmp . '/storage/framework/views');

$_SERVER['APP_SERVICES_CACHE'] = $_SERVER['APP_SERVICES_CACHE'] ?? $_ENV['APP_SERVICES_CACHE'];
$_SERVER['APP_PACKAGES_CACHE'] = $_SERVER['APP_PACKAGES_CACHE'] ?? $_ENV['APP_PACKAGES_CACHE'];
$_SERVER['APP_CONFIG_CACHE'] = $_SERVER['APP_CONFIG_CACHE'] ?? $_ENV['APP_CONFIG_CACHE'];
$_SERVER['APP_ROUTES_CACHE'] = $_SERVER['APP_ROUTES_CACHE'] ?? $_ENV['APP_ROUTES_CACHE'];
$_SERVER['APP_EVENTS_CACHE'] = $_SERVER['APP_EVENTS_CACHE'] ?? $_ENV['APP_EVENTS_CACHE'];
$_SERVER['VIEW_COMPILED_PATH'] = $_SERVER['VIEW_COMPILED_PATH'] ?? $_ENV['VIEW_COMPILED_PATH'];

if (!defined('LARAVEL_START')) {
    define('LARAVEL_START', microtime(true));
}

require __DIR__ . '/../vendor/autoload.php';

(require_once __DIR__ . '/../bootstrap/app.php')
    ->handleRequest(\Illuminate\Http\Request::capture());
