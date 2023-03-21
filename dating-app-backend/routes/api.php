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
            Route::post("/upload",[UsersController::class,"uploadImage"]);
            Route::get('/user/{id}', [UsersController::class, "getuser"]);
            Route::get('/oppgender/{id}', [UsersController::class, "getoppgender"]);
            Route::post('/editprofile', [UsersController::class, "editprofile"]);
            Route::get('/messages/{sender_id}/{receiver_id}', [UsersController::class, "getmessage"]);
            Route::get('/blocks/{sender_id}/{receiver_id}', [UsersController::class, "getblocks"]);
            Route::get('/favorites/{sender_id}/{receiver_id}', [UsersController::class, "getfavorites"]);
            
        });
        Route::group(['prefix' => 'actions'], function () {
            Route::post('/likeuser/{sender_id}/{receiver_id}', [UsersController::class, "likeuser"]);
            Route::post('/blockuser/{sender_id}/{receiver_id}', [UsersController::class, "blockuser"]);
            Route::post('/sendmessage/{sender_id}/{receiver_id}', [UsersController::class, "sendmessage"]);        
        });
    
});
});