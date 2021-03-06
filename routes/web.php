<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->to(config('l5-swagger.documentations.default.routes.api'));
});

Route::get('google-app/set-access-token', [App\Http\Controllers\GoogleApiTokenController::class, 'index'])->name('google-app.set-access-token');
