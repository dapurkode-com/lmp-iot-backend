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
// HealthLink
Route::get('weight/today', [App\Http\Controllers\Api\WeightController::class, 'todayLatest'])->name('weight.today');
Route::get('step/today', [App\Http\Controllers\Api\StepController::class, 'todayLatest'])->name('step.today');
Route::get('calorie-expended/today', [App\Http\Controllers\Api\CalorieExpendedController::class, 'todayLatest'])->name('calorie-expended.today');
Route::get('heart-rate/today', [App\Http\Controllers\Api\HeartRateController::class, 'todayLatest'])->name('heart-rate.today');
Route::get('sleep/today', [App\Http\Controllers\Api\SleepController::class, 'todayLatest'])->name('sleep.today');

Route::apiResource('weight', App\Http\Controllers\Api\WeightController::class)->only(['index', 'show']);
Route::apiResource('calorie-expended', App\Http\Controllers\Api\CalorieExpendedController::class)->only(['index', 'show']);
Route::apiResource('heart-rate', App\Http\Controllers\Api\HeartRateController::class)->only(['index', 'show']);
Route::apiResource('sleep', App\Http\Controllers\Api\SleepController::class)->only(['index', 'show']);
Route::apiResource('step', App\Http\Controllers\Api\StepController::class)->only(['index', 'show']);

// Dieat
Route::get('calorie-intake/today', [App\Http\Controllers\Api\CalorieIntakeController::class, 'todayLatest'])->name('calorie-intake.today');

Route::apiResource('calorie-intake', App\Http\Controllers\Api\CalorieIntakeController::class)->only(['index', 'show', 'store']);

// Hydro
Route::get('ph/latest', [App\Http\Controllers\Api\PhController::class, 'latest'])->name('ph.latest');
Route::get('ppm/latest', [App\Http\Controllers\Api\PpmController::class, 'latest'])->name('ppm.latest');
Route::get('temperature/latest', [App\Http\Controllers\Api\TemperatureController::class, 'latest'])->name('temperature.latest');

Route::apiResource('ph', App\Http\Controllers\Api\PhController::class)->only(['index', 'show', 'store']);
Route::apiResource('ppm', App\Http\Controllers\Api\PpmController::class)->only(['index', 'show', 'store']);
Route::apiResource('temperature', App\Http\Controllers\Api\TemperatureController::class)->only(['index', 'show', 'store']);

// RAM Z
Route::get('stock/summary', [App\Http\Controllers\Api\StockController::class, 'summary'])->name('stock.summary');
Route::apiResource('stock', App\Http\Controllers\Api\StockController::class)->only(['index', 'show', 'store']);
