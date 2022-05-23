<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Salesforce\HookController;
use App\Http\Controllers\AuthController;

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

Route::middleware('auth.jwt')->group(function () {

    Route::post('/hook/salesforce', [HookController::class, 'salesforce']);
});

Route::middleware('auth.uuid')->group(function () {

    Route::post('hook/amocrm', [HookController::class, 'amocrm']);
});

Route::post('login', [AuthController::class, 'login']);

