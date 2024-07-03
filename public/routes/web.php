<?php

use App\Http\Controllers\AsyncActionController;
use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/swagger', 'swagger');

Route::middleware('async')->prefix('async')->group(function(){
    Route::post('asyncactions', [AsyncActionController::class, 'handler']);

    Route::prefix('exports')->group(function(){
        Route::get('export_translation', [ExportController::class, 'exportTranslations']);
        Route::get('export_language', [ExportController::class, 'exportLanguages']);
    });
});