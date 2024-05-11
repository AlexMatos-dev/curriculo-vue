<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobListController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\CompanySocialNetworkController;
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
Route::prefix('auth')->middleware('authenticate')->group(function ()
{
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('profile', [AuthController::class, 'profile']);
});

Route::prefix('curriculum')->middleware('authenticate', 'curriculum')->group(function ()
{
    Route::resource('experience', ExperienceController::class);
    Route::resource('education', EducationController::class);
    Route::resource('skill', SkillController::class);
    Route::resource('visa', VisaController::class);
    Route::resource('link', LinkController::class);
    Route::resource('curriculum', CurriculumController::class);
    Route::resource('experience', ExperienceController::class);
    Route::resource('education', EducationController::class);
});

Route::middleware('authenticate')->apiResource('/joblist', JobListController::class);

Route::prefix('person')->middleware('authenticate')->group(function ()
{
    Route::post('update', [PersonController::class, 'update']);
});

Route::prefix('professional')->middleware('authenticate')->group(function ()
{
    Route::post('update', [ProfessionalController::class, 'update']);
    Route::post('updateprofessionalperson', [ProfessionalController::class, 'updateDataPerson']);
    Route::post('updateprofessionaljobmodality', [ProfessionalController::class, 'manageProfessionalJobModality']);
});

Route::prefix('company')->middleware('authenticate')->group(function ()
{
    Route::post('update', [CompanyController::class, 'update']);
    Route::middleware('companyadmin')->group(function ()
    {
        Route::post('manageadmin', [CompanyController::class, 'manageCompanyAdmin']);
        Route::post('managerecruiter', [CompanyController::class, 'manageCompanyRecruiter']);
    });
});

Route::prefix('recruiter')->middleware('authenticate')->group(function ()
{
    Route::post('update', [RecruiterController::class, 'update']);
});

Route::prefix('social_network')->middleware('authenticate')->group(function ()
{
    Route::get('showByCompanyId/{company_id}', [CompanySocialNetworkController::class, 'showByCompanyId']);
    Route::middleware('companyadmin')->group(function(){
        Route::post('store', [CompanySocialNetworkController::class, 'store']);
        Route::patch('update/{social_network_id}', [CompanySocialNetworkController::class, 'update']);
        Route::delete('destroy/{social_network_id}', [CompanySocialNetworkController::class, 'destroy']);
    });
});
