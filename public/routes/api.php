<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\EducationController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::prefix('auth')->middleware('authenticate')->group(function(){
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('profile', [AuthController::class, 'profile']);
});
Route::resource('experience',ExperienceController::class)->middleware('authenticate');
Route::resource('education',EducationController::class)->middleware('authenticate');;
