<?php

use Illuminate\Http\Request;

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

Route::post('/login', 'LoginController@login');

Route::middleware('auth:api')->group(function () {
    Route::resource('posts', \App\Http\Controllers\PostController::class, ['only' => ['index', 'store', 'show']]);
    Route::resource('tags', 'TagController', ['only' => ['index']]);
    Route::resource('categories', 'CategoryController', ['only' => ['index']]);
});
