<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobListController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProfessionalController;
use App\Http\Controllers\RecruiterController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\VisaController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('requestchangepassword', [AuthController::class, 'requestChangePasswordCode']);
Route::post('changepassword', [AuthController::class, 'changePassword']);
Route::prefix('auth')->middleware('auth:sanctum')->group(function (){
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('profile', [AuthController::class, 'profile']);
});

Route::prefix('curriculum')->middleware('auth:sanctum', 'curriculum')->group(function(){
  Route::resource('experience',ExperienceController::class);
  Route::resource('education',EducationController::class);
  Route::resource('skill', SkillController::class);
  Route::resource('visa', VisaController::class);
  Route::resource('link', LinkController::class);
  Route::resource('curriculum', CurriculumController::class);
  Route::resource('experience', ExperienceController::class);
  Route::resource('education', EducationController::class);  
});

Route::get('joblist', [JobListController::class, 'index']);
Route::middleware('auth:sanctum')->group(function(){
    Route::post('joblist', [JobListController::class, 'store']);
    Route::put('joblist/{joblist}', [JobListController::class, 'update']);
    Route::get('joblist/{joblist}', [JobListController::class, 'show']);
    Route::delete('joblist/{joblist}', [JobListController::class, 'destroy']);
    Route::prefix('joblist')->middleware('job')->group(function(){
        Route::post('managelanguage/{joblist_id}', [JobListController::class, 'manageJobLanguages']);
        Route::post('manageskills/{joblist_id}', [JobListController::class, 'manageJobSkills']);
        Route::post('managevisas/{joblist_id}', [JobListController::class, 'manageJobVisas']);
    });
});

Route::prefix('person')->middleware('auth:sanctum')->group(function (){
    Route::post('update', [PersonController::class, 'update']);
});

Route::prefix('professional')->group(function (){
    Route::get('/', [ProfessionalController::class, 'index']);
    Route::middleware('auth:sanctum')->group(function(){
        Route::post('update', [ProfessionalController::class, 'update']);
        Route::post('updateprofessionalperson', [ProfessionalController::class, 'updateDataPerson']);
        Route::post('updateprofessionaljobmodality', [ProfessionalController::class, 'manageProfessionalJobModality']);
        Route::post('updateprofessionalprofession', [ProfessionalController::class, 'manageProfessionalProfessions']);
    });
});

Route::prefix('company')->middleware('auth:sanctum')->group(function (){
    Route::post('update', [CompanyController::class, 'update']);
    Route::middleware('companyadmin')->group(function (){
        Route::post('manageadmin', [CompanyController::class, 'manageCompanyAdmin']);
        Route::post('managerecruiter', [CompanyController::class, 'manageCompanyRecruiter']);
    });
});

Route::prefix('recruiter')->middleware('auth:sanctum')->group(function (){
    Route::post('update', [RecruiterController::class, 'update']);
});
