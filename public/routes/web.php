<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    ini_set('max_execution_time', -1);
    $result = Artisan::call('migrate:fresh');
    dd($result);
    return view('welcome');
});
