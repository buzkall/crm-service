<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:api')->namespace('App\Http\Controllers\Api')->group(function () {
    Route::post('logout', 'AuthController@logout');
    Route::get('user', 'AuthController@user');
});


