<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Validation Engine API',
        'status' => 'running',
        'version' => '1.0.0',
        'server' => 'Render.com',
        'timestamp' => now()->toDateTimeString()
    ]);
});

// Debug route to check environment
Route::get('/debug', function () {
    return response()->json([
        'app_env' => env('APP_ENV'),
        'app_debug' => env('APP_DEBUG'),
        'db_connection' => env('DB_CONNECTION'),
        'db_host' => env('DB_HOST'),
        'db_database' => env('DB_DATABASE'),
        'has_app_key' => !empty(env('APP_KEY')),
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version()
    ]);
});