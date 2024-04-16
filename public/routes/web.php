<?php

use App\Models\User;
use Database\Seeders\CountrySeeder;
use Database\Seeders\GenderSeeder;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    Artisan::call('migrate:fresh');
    $e = Artisan::call('db:seed');

    // $o = Artisan::call('db:seed');

    // dd($e, $o);
    return view('welcome');
});
