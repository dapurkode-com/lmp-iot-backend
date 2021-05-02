<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::apiResource('weight', App\Http\Controllers\Api\WeightController::class)->only(['index', 'show']);
Route::apiResource('calorie-expended', App\Http\Controllers\Api\CalorieExpendedController::class)->only(['index', 'show']);
Route::apiResource('calorie-intake', App\Http\Controllers\Api\CalorieIntakeController::class)->only(['index', 'show', 'store']);
Route::apiResource('heart-rate', App\Http\Controllers\Api\HeartRateController::class)->only(['index', 'show']);
Route::apiResource('ph', App\Http\Controllers\Api\PhController::class)->only(['index', 'show']);
Route::apiResource('ppm', App\Http\Controllers\Api\PpmController::class)->only(['index', 'show']);
Route::apiResource('sleep', App\Http\Controllers\Api\SleepController::class)->only(['index', 'show']);
