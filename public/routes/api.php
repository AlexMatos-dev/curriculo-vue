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
use App\Http\Controllers\CompanyTypeController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DrivingLicenseController;
use App\Http\Controllers\JobAppliedController;
use App\Http\Controllers\JobCertificationController;
use App\Http\Controllers\JobContractController;
use App\Http\Controllers\JobModalityController;
use App\Http\Controllers\JobPeriodController;
use App\Http\Controllers\ListLangueController;
use App\Http\Controllers\ListProfessionController;
use App\Http\Controllers\JobPaymentTypeController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProfessionalController;
use App\Http\Controllers\ProficiencyController;
use App\Http\Controllers\RecruiterController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TypeVisasController;
use App\Http\Controllers\VisaController;
use App\Http\Controllers\WorkingVisaController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('requestemailconfirmationcode', [AuthController::class, 'requestEmailConfirmationCode']);
Route::post('verifyemail', [AuthController::class, 'verifyEmail']);
Route::post('password/email', [ResetPasswordController::class, 'sendResetLinkEmail']);
Route::post('password/reset', [ResetPasswordController::class, 'reset']);

Route::middleware('auth:sanctum')->group(function ()
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
Route::get('joblist/{joblist}', [JobListController::class, 'show']);
Route::middleware('auth:sanctum')->group(function ()
{
    Route::post('joblist/store', [JobListController::class, 'store']);
    Route::put('joblist/{joblist}', [JobListController::class, 'update']);
    Route::delete('joblist/{joblist}', [JobListController::class, 'destroy']);
    Route::prefix('joblist')->middleware(['job', 'verify_email'])->group(function ()
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
    Route::get('find/{professional_slug}', [ProfessionalController::class, 'find']);
    Route::middleware('auth:sanctum')->group(function ()
    {
        Route::post('update', [ProfessionalController::class, 'update']);
        Route::middleware(['professional', 'verify_email'])->group(function ()
        {
            Route::post('updateprofessionalperson', [ProfessionalController::class, 'updateDataPerson']);
            Route::post('updateprofessionaljobmodality', [ProfessionalController::class, 'manageProfessionalJobModality']);
            Route::post('updateprofessionalprofession', [ProfessionalController::class, 'manageProfessionalProfessions']);
            Route::get('jobapplication', [JobAppliedController::class, 'professionalJobApplication']);
        });
    });
});

Route::prefix('company')->group(function ()
{
    Route::get('index', [CompanyController::class, 'index']);
    Route::middleware('auth:sanctum')->group(function(){
        Route::post('update', [CompanyController::class, 'update']);
        Route::middleware(['companyadmin',  'verify_email'])->group(function ()
        {
            Route::post('manageadmin', [CompanyController::class, 'manageCompanyAdmin']);
            Route::post('managerecruiter', [CompanyController::class, 'manageCompanyRecruiter']);
        });

        Route::middleware('company_recruiter')->group(function(){
            Route::get('job/search/{job_id}', [CompanyController::class, 'searchCompanyJob']);
            Route::get('jobs', [CompanyController::class, 'getMyCompanyJobs']);
            Route::post('postjob', [CompanyController::class, 'postJob']);
            Route::post('trashjob', [CompanyController::class, 'desactivateJob']);
            Route::post('untrashjob', [CompanyController::class, 'reactivateJob']);
        });
    });

    
    Route::get('{company_slug}', [CompanyController::class, 'show']);
});

Route::prefix('recruiter')->middleware('auth:sanctum', 'recruiter')->group(function ()
{
    Route::post('update', [RecruiterController::class, 'update']);
});

Route::prefix('social_network')->group(function ()
{
    Route::get('showByCompanyId/{company_id}', [CompanySocialNetworkController::class, 'showByCompanyId']);
    Route::middleware(['auth:sanctum', 'companyadmin', 'verify_email'])->group(function ()
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

Route::prefix('job_applied')->middleware(['auth:sanctum', 'verify_email'])->group(function ()
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

Route::prefix('chat_message')->middleware(['auth:sanctum', 'verify_email'])->group(function ()
{
    Route::middleware('chat')->prefix('{prefix}')->group(function ()
    {
        Route::post('sendmessage', [ChatMessageController::class, 'sendMessage']);
        Route::get('list', [ChatMessageController::class, 'listMessages']);
        Route::delete('remove', [ChatMessageController::class, 'removeMessage']);
    });
});

Route::prefix('company_types')->group(function ()
{
    Route::get('getcompanytypes', [CompanyTypeController::class, 'getCompanyTypes']);
});

Route::prefix('languages')->group(function ()
{
    Route::get('index', [ListLangueController::class, 'getLangue']);
});

Route::prefix('countries')->group(function ()
{
    Route::get('index', [CountryController::class, 'getCountries']);
});

Route::prefix('professions')->group(function ()
{
    Route::get('index', [ListProfessionController::class, 'getProfessions']);
    Route::get('list', [ListProfessionController::class, 'list']);
});

Route::prefix('tags')->group(function ()
{
    Route::get('search', [TagController::class, 'search']);
});

Route::prefix('job_payment_type')->group(function ()
{
    Route::get('index', [JobPaymentTypeController::class, 'index']);
});

Route::prefix('job_contract')->group(function ()
{
    Route::get('index', [JobContractController::class, 'index']);
});

Route::prefix('working_visa')->group(function ()
{
    Route::get('index', [WorkingVisaController::class, 'index']);
});

Route::prefix('job_period')->group(function ()
{
    Route::get('index', [JobPeriodController::class, 'index']);
});

Route::prefix('driving_license')->group(function ()
{
    Route::get('index', [DrivingLicenseController::class, 'index']);
});

Route::prefix('job_certification')->group(function ()
{
    Route::get('index', [JobCertificationController::class, 'index']);
});