<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:api')->group(function () {
    Route::get('logout', 'App\Http\Controllers\AuthController@logout');
    Route::get('user', 'App\Http\Controllers\AuthController@user');
});


