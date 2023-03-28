<?php

use Illuminate\Http\Request;
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

Route::post('/auth', [\App\Http\Controllers\AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
    Route::middleware('role:lawyer')->group(function () {
        Route::get('/test', function (Request $request) {
            return response()->json([
                "data" => "success"
            ]);
        });
    });
    Route::middleware('role:client')->group(function () {

    });
});
