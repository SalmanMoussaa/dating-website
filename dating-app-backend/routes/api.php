<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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

Route::group(['middleware' => 'api'], function($router) {
    Route::group(['prefix' => 'v0.0.1'], function () {
        Route::group(['prefix' => 'auth'], function () {
            Route::post('/login', [JWTController::class, "login"]);
            Route::post('/register', [JWTController::class, "register"]);
            Route::post('/forgotpassword', [JWTController::class, "forgotpassword"]);
            Route::post('/addimage/{id}', [JWTController::class, "addimage"]);
          
        });
    });
});