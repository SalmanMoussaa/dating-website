<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;


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
            Route::post('/login', [userscontroller::class, "login"]);
            Route::post('/register', [userscontroller::class, "register"]);
            Route::post('/forgotpassword', [userscontroller::class, "forgotpassword"]);
            Route::post('/addimage/{id}', [userscontroller::class, "addimage"]);
        });
        
        Route::group(['prefix' => 'user'], function () {
            Route::post("/upload",[UserController::class,"uploadImage"]);
            Route::get('/user/{id}', [UserController::class, "getuser"]);
            Route::get('/oppgender/{id}', [UserController::class, "getoppgender"]);
            Route::post('/editprofile', [UserController::class, "editprofile"]);
            Route::get('/messages/{sender_id}/{receiver_id}', [UserController::class, "getmessage"]);
            Route::get('/blocks/{sender_id}/{receiver_id}', [UserController::class, "getblocks"]);
            Route::get('/favorites/{sender_id}/{receiver_id}', [UserController::class, "getfavorites"]);
            
        });
        Route::group(['prefix' => 'actions'], function () {
            Route::post('/likeuser/{sender_id}/{receiver_id}', [ActionController::class, "likeuser"]);
            Route::post('/blockuser/{sender_id}/{receiver_id}', [ActionController::class, "blockuser"]);
            Route::post('/sendmessage/{sender_id}/{receiver_id}', [ActionController::class, "sendmessage"]);        
        });
    
});
});