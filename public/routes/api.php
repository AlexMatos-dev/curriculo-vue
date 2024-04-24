<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::prefix('auth')->middleware('authenticate')->group(function(){
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('profile', [AuthController::class, 'profile']);
    Route::post('requestchangepassword', [AuthController::class, 'requestChangePasswordCode']);
    Route::post('changepassword', [AuthController::class, 'changePassword']);
});