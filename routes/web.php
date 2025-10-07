<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Validation Engine API',
        'status' => 'running',
        'version' => '1.0.0'
    ]);
});

// Test route to verify API is working
Route::get('/test', function () {
    return response()->json([
        'message' => 'Backend is working!',
        'timestamp' => now()->toDateTimeString()
    ]);
});