<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
        $middleware->validateCsrfTokens(except: [
            'async/asyncactions'
        ]);
        $middleware->group('api', [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\ApiThrottle::class,
            \App\Http\Middleware\UserLanguage::class,
            \App\Http\Middleware\ApiGuard::class
        ]);
        $middleware->group('web', [
            Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\UserLanguage::class
        ]);
        $middleware->group('authenticate', [
            \App\Http\Middleware\Authentication::class,
        ]);
        $middleware->group('companyadmin', [
            \App\Http\Middleware\CompanyAdmin::class
        ]);
        $middleware->group('curriculum', [
            \App\Http\Middleware\MyCurriculum::class
        ]);
        $middleware->group('job', [
            \App\Http\Middleware\MyJob::class
        ]);
        $middleware->group('professional', [
            \App\Http\Middleware\ProfessionalProfile::class
        ]);
        $middleware->group('recruiter', [
            \App\Http\Middleware\RecruiterProfile::class
        ]);
        $middleware->group('company_recruiter', [
            \App\Http\Middleware\CompanyOrRecruiter::class
        ]);
        $middleware->group('async', [
            \App\Http\Middleware\AsynActions::class
        ]);
        $middleware->group('chat', [
            \App\Http\Middleware\ChatMessageHandler::class
        ]);
        $middleware->group('verify_email', [
            \App\Http\Middleware\VerifyEmail::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        
    })->create();
