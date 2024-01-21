<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [AuthController::class, 'login'])->name('login');


Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::apiResource('clients', ClientController::class);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

});