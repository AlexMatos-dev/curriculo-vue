<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\CertificationController;
use App\Http\Controllers\ReferenceController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProfessionalController;
use App\Http\Controllers\RecruiterController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('requestchangepassword', [AuthController::class, 'requestChangePasswordCode']);
Route::post('changepassword', [AuthController::class, 'changePassword']);
Route::prefix('auth')->middleware('authenticate')->group(function(){
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('profile', [AuthController::class, 'profile']);
});
Route::prefix('curriculum')->middleware('authenticate')->group(function(){
  Route::resource('experience',ExperienceController::class);
  Route::resource('education',EducationController::class);
  Route::resource('certification',CertificationController::class);
  Route::resource('reference',ReferenceController::class);
});

Route::prefix('person')->middleware('authenticate')->group(function(){
    Route::post('update', [PersonController::class, 'update']);
});

Route::prefix('professional')->middleware('authenticate')->group(function(){
    Route::post('update', [ProfessionalController::class, 'update']);
    Route::post('updateprofessionalperson', [ProfessionalController::class, 'updateDataPerson']);
});

Route::prefix('company')->middleware('authenticate')->group(function(){
    Route::post('update', [CompanyController::class, 'update']);
    Route::middleware('companyadmin')->group(function(){
        Route::post('manageadmin', [CompanyController::class, 'manageCompanyAdmin']);
        Route::post('managerecruiter', [CompanyController::class, 'manageCompanyRecruiter']);
    });
});

Route::prefix('recruiter')->middleware('authenticate')->group(function(){
    Route::post('update', [RecruiterController::class, 'update']);
});
