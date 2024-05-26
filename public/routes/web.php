<?php

use App\Http\Controllers\AsyncActionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/swagger', 'swagger');

Route::middleware('async')->prefix('async')->group(function(){
    Route::post('asyncactions', [AsyncActionController::class, 'handler']);
});