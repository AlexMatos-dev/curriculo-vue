<?php

use App\Models\User;
use Database\Seeders\CountrySeeder;
use Database\Seeders\GenderSeeder;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
