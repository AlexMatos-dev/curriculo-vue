<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\JobListController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\CommonCurrencyController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\CompanySocialNetworkController;
use App\Http\Controllers\JobAppliedController;
use App\Http\Controllers\JobModalityController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProfessionalController;
use App\Http\Controllers\ProficiencyController;
use App\Http\Controllers\RecruiterController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\TypeVisasController;
use App\Http\Controllers\VisaController;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('requestchangepassword', [AuthController::class, 'requestChangePasswordCode']);
Route::post('changepassword', [AuthController::class, 'changePassword']);
Route::prefix('auth')->middleware('auth:sanctum')->group(function ()
{
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('profile', [AuthController::class, 'profile']);
});

Route::prefix('curriculum')->middleware('auth:sanctum', 'curriculum')->group(function ()
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

Route::get('joblist/index', [JobListController::class, 'index']);
Route::middleware('auth:sanctum')->group(function ()
{
    Route::post('joblist', [JobListController::class, 'store']);
    Route::put('joblist/{joblist}', [JobListController::class, 'update']);
    Route::get('joblist/{joblist}', [JobListController::class, 'show']);
    Route::delete('joblist/{joblist}', [JobListController::class, 'destroy']);
    Route::prefix('joblist')->middleware('job')->group(function ()
    {
        Route::post('managelanguage/{joblist_id}', [JobListController::class, 'manageJobLanguages']);
        Route::post('manageskills/{joblist_id}', [JobListController::class, 'manageJobSkills']);
        Route::post('managevisas/{joblist_id}', [JobListController::class, 'manageJobVisas']);
    });
});

Route::prefix('person')->middleware('auth:sanctum')->group(function ()
{
    Route::post('update', [PersonController::class, 'update']);
});

Route::prefix('professional')->group(function ()
{
    Route::get('index', [ProfessionalController::class, 'index']);
    Route::middleware('auth:sanctum')->group(function ()
    {
        Route::post('update', [ProfessionalController::class, 'update']);
        Route::middleware('professional')->group(function ()
        {
            Route::post('updateprofessionalperson', [ProfessionalController::class, 'updateDataPerson']);
            Route::post('updateprofessionaljobmodality', [ProfessionalController::class, 'manageProfessionalJobModality']);
            Route::post('updateprofessionalprofession', [ProfessionalController::class, 'manageProfessionalProfessions']);
            Route::get('jobapplication', [JobAppliedController::class, 'professionalJobApplication']);
        });
    });
});

Route::prefix('company')->middleware('auth:sanctum')->group(function ()
{
    Route::post('update', [CompanyController::class, 'update']);
    Route::middleware('companyadmin')->group(function ()
    {
        Route::post('manageadmin', [CompanyController::class, 'manageCompanyAdmin']);
        Route::post('managerecruiter', [CompanyController::class, 'manageCompanyRecruiter']);
    });
});

Route::prefix('recruiter')->middleware('auth:sanctum', 'recruiter')->group(function ()
{
    Route::post('update', [RecruiterController::class, 'update']);
});

Route::prefix('social_network')->group(function ()
{
    Route::get('showByCompanyId/{company_id}', [CompanySocialNetworkController::class, 'showByCompanyId']);
    Route::middleware('auth:sanctum', 'companyadmin')->group(function ()
    {
        Route::post('store', [CompanySocialNetworkController::class, 'store']);
        Route::patch('update/{social_network_id}', [CompanySocialNetworkController::class, 'update']);
        Route::delete('destroy/{social_network_id}', [CompanySocialNetworkController::class, 'destroy']);
    });
});

Route::prefix('proficiency')->group(function ()
{
    Route::get('index', [ProficiencyController::class, 'index']);
});

Route::prefix('job_modality')->group(function ()
{
    Route::get('index', [JobModalityController::class, 'index']);
});

Route::prefix('type_visas')->group(function ()
{
    Route::get('index', [TypeVisasController::class, 'index']);
});

Route::prefix('common_currency')->group(function ()
{
    Route::get('index', [CommonCurrencyController::class, 'index']);
});

Route::prefix('job_applied')->middleware('auth:sanctum')->group(function ()
{
    Route::get('status', [JobAppliedController::class, 'listStatus']);

    Route::middleware('company_recruiter')->group(function ()
    {
        Route::get('index', [JobAppliedController::class, 'companyJobApplication']);
        Route::post('changejobappliedstatus', [JobAppliedController::class, 'changeJobAppliedStatus']);
    });

    Route::middleware('professional')->group(function ()
    {
        Route::post('applyforvacancy', [JobAppliedController::class, 'applyForVacancy']);
        Route::post('canceljobapplied', [JobAppliedController::class, 'cancelJobApplied']);
    });
});

Route::prefix('chat_message')->middleware(['auth:sanctum'])->group(function ()
{
    Route::middleware('chat')->prefix('{prefix}')->group(function(){
        Route::post('sendmessage', [ChatMessageController::class, 'sendMessage']);
        Route::get('list', [ChatMessageController::class, 'listMessages']);
        Route::delete('remove', [ChatMessageController::class, 'removeMessage']);
    });
});