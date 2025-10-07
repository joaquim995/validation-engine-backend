<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    try {
        return response()->json([
            'message' => 'Validation Engine API',
            'status' => 'running',
            'version' => '1.0.0'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Route error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
});

// Database connection test
Route::get('/db-test', function () {
    try {
        $pdo = DB::connection()->getPdo();
        return response()->json([
            'status' => 'Database connected',
            'driver' => DB::connection()->getDriverName(),
            'database' => DB::connection()->getDatabaseName()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Database connection failed',
            'message' => $e->getMessage(),
            'db_connection' => env('DB_CONNECTION'),
            'db_host' => env('DB_HOST'),
            'db_database' => env('DB_DATABASE')
        ], 500);
    }
});