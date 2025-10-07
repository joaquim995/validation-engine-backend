<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ValidationRuleController;

Route::middleware('auth:sanctum')->get('/user', fn (Request $request) => $request->user());

Route::controller(ValidationRuleController::class)
    ->prefix('validation_rules')
    ->group(function () {
        Route::post('/evaluate', 'evaluate');
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::put('/{id}', 'update');
        Route::delete('/{id}', 'destroy');
    });
